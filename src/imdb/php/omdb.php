<?php
$INTERNAL_ERROR = "Something went wrong!";
$LIMIT_ERROR    = "Request limit reached!";
$SEARCH_ERROR   = "Movie not found!";
$apiKey = '522c6900';

function requestOMDB( array $params = array() )
{
    global $apiKey;
    $result = null;
    if ( !empty($params) )
    {
        $paramString = http_build_query($params, "", "&");
        $url = "http://www.omdbapi.com/?$paramString&plot=short&r=json&apikey=$apiKey"; //OMDB API requires API Key -> go to their site if this one stops working
        $response = json_decode(file_get_contents($url));
        $result = getDataFromResponse($response);
    }
    return $result;
}

function getDataFromResponse( $response )
{
    $result['isSuccess'] = false;
    if ( $response && $response->Response === "True" )
    {
        $result['isSuccess'] = true;
        $result['id'] = $response->imdbID;
        $result['title'] = $response->Title;
        $result['year'] = $response->Year;
        $result['image'] = $response->Poster;
    }
    else
    {
        global $INTERNAL_ERROR;
        $result['message'] = $response->Error ?? $INTERNAL_ERROR;
    }
    return $result;
}

function getMovieByTitle( $title, $year = null )
{
    $title = trim( $title );
    $searchTitle = preg_replace( '/[^A-Za-z0-9 ]/', '', $title );
    $result = requestOMDB( [ 't' => $searchTitle, 'y' => $year ] );
    $result['search'] = $title;
    return $result;
}

function getMovieById( $id )
{
    return requestOMDB( [ 'i' => $id ] );
}


/********************FILE PARSING********************/


function getColumns( $firstRow )
{
    array_walk( $firstRow, function(&$value) {
        $value = preg_replace( '/[^A-Za-z0-9 ]/', '', $value );
    });

    $result['iIndex'] = array_search( "ID", $firstRow, true );
    $result['tIndex'] = array_search( "Title", $firstRow, true );
    $result['aIndex'] = array_search( "Author", $firstRow, true );
    $result['yIndex'] = array_search( "Year", $firstRow, true );
    $result['cIndex'] = array_search( "Review", $firstRow, true );
    $result['rIndex'] = array_search( "Rating", $firstRow, true );
    $result['pIndex'] = array_search( "Image", $firstRow, true );
    $result['uIndex'] = array_search( "URL", $firstRow, true );

    return $result;
}

function isSafe( $fileName, $columns )
{
    $isSafe['isSuccess'] = true;

    if ( filesize( $fileName ) > 250000 )
    {
        $isSafe['message'] = "Sorry, your file is too large.";
        $isSafe['isSuccess'] = false;
    }
    if ( strtolower( pathinfo( $fileName, PATHINFO_EXTENSION ) ) != "csv" )
    {
        $isSafe['message'] = "Sorry, only CSV files are allowed.";
        $isSafe['isSuccess'] = false;
    }
    if ( $columns['tIndex'] === false && $columns['iIndex'] === false )
    {
        $isSafe['message'] = "Sorry, no Title or ID columns found .";
        $isSafe['isSuccess'] = false;
    }

    return $isSafe;
}

function getMovieWithAttempts( $id, $title, $year )
{
    global $INTERNAL_ERROR;
    global $LIMIT_ERROR;

    $result = null;
    $attempts = 0;
    do
    {
        if ( !empty( $title ) )
        {
            $result = getMovieByTitle( $title, $year );
        }
        else
        {
            $result = getMovieById( $id );
        }

        $attempts++;
    } while ( $result['message'] === $INTERNAL_ERROR && $attempts < 3 );

    if ( $result['message'] === $LIMIT_ERROR )
    {
        $result = false;
    }

    return $result;
}

function getMessage( $index, $results, $searchTitle )
{
    global $INTERNAL_ERROR;
    global $SEARCH_ERROR;
    $result = "";

    if ( $results )
    {
        if ( $results['isSuccess'] )
        {
            if ( strcasecmp( $results['title'], $searchTitle ) != 0 )
            {
                $result = array( "index" => $index, "title" => $searchTitle, "newTitle" => $results['title'], "id" => $results['id'], "poster" => $results['image'] );
            }
        }
        else
        {
            if ( $results['message'] === $SEARCH_ERROR )
            {
                $result = array( "index" => $index, "title" => $searchTitle, "newTitle" => "", "id" => $SEARCH_ERROR, "poster" => "" );
            }
            else
            {
                $result = $index . ". " . $results['message'];
            }
        }
    }
    else
    {
        $result = "<i>$INTERNAL_ERROR</i>";
    }

    return $result;
}

function parseFile( $originalName )
{
    $resultName = "Results-" . time() . ".csv";
    $resultFile = fopen( $resultName, "w" );
    $originalFile = fopen( $originalName, "r" );
    $columns = getColumns( fgetcsv( $originalFile ) );
    $result = isSafe( $originalName, $columns );

    if ( $result['isSuccess'] )
    {
        ini_set( "allow_url_fopen", 1 );
        ini_set("default_socket_timeout", 1);

        $index = 1;
        $limitReached = false;
        $result['content'] = "";
        $result['message'] = [];

        $row = fgetcsv( $originalFile );
        while ( $row !== false && !$limitReached )
        {
            $id     = $columns['iIndex'] !== false ? trim( $row[$columns['iIndex']] ) : "";
            $title  = $columns['tIndex'] !== false ? trim( $row[$columns['tIndex']] ) : "";
            $year   = $columns['yIndex'] !== false ? trim( $row[$columns['yIndex']] ) : "";

            if ( isset( $title ) || isset( $id ) ) //not a blank line
            {
                $movie = [ "id" => "", "title" => "", "year" => "" ];
                if ( empty( $title ) || empty( $id ) ) //not both already there
                {
                    $movie = getMovieWithAttempts( $id, $title, $year );
                    $limitReached = $movie === false;

                    array_push( $result['message'], getMessage( $index, $movie, $title ) );
                }

                array_unshift( $row, $movie['id'], $movie['title'], $movie['year'], "" );
                fputcsv( $resultFile, $row );
                $index++;
            }

            $row = fgetcsv( $originalFile );
        }
    }

    fclose( $resultFile );
    $result['content'] = file_get_contents( $resultName );
    unlink( $resultName );

    fclose( $originalFile );
    unlink( $originalName );

    return $result;
}

function convert( $file )
{
    move_uploaded_file( $file['tmp_name'], $file['name'] );

    $result = parseFile( $file['name'] );
    return $result;
}

?>