<?php
include_once("omdb.php");

function getColumns( $firstRow )
{
    $result['iIndex'] = array_search( "ID", $firstRow, true );
    $result['tIndex'] = array_search( "Title", $firstRow, true );
    $result['aIndex'] = array_search( "Author", $firstRow, true );
    $result['yIndex'] = array_search( "Year", $firstRow, true );
    $result['cIndex'] = array_search( "Review", $firstRow, true );
    $result['rIndex'] = array_search( "Rating", $firstRow, true );
    $result['pIndex'] = array_search( "Image", $firstRow, true );
    $result['uIndex'] = array_search( "URL", $firstRow, true );

    //todo
    $result['isTitlePresent'] = $result['tIndex'] !== false;
    $result['isRatingPresent'] = $result['rIndex'] !== false;
    $result['isIdPresent'] = $result['iIndex'] !== false;

    return $result;
}

function isSafe( $fileName )
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

    return $isSafe;
}

function getSearchTitle( $title )
{
    $result = preg_replace( '/\'/', '%27', $title );
    $result = preg_replace( '/\s+/', '+', $result );
    return $result;
}

function getResponse( $url )
{
    $INTERNAL_ERROR = "Something went wrong!";
    $result = null;
    $attempts = 0;
    do
    {
        $responseObj = json_decode( file_get_contents( $url ) );
        $result['content'] = $responseObj;
        $result['isSuccess'] = $responseObj->Response === "True";
        //Possible Errors: "Movie not found!" | "Request limit reached!" | "Something went wrong!"
        $result['message'] = !$result['isSuccess'] ? (isset($responseObj->Error) ? $responseObj->Error : "Something went wrong!") : "";
        $attempts++;
    } while ( $result['message'] === $INTERNAL_ERROR && $attempts < 5 );
    return $result;
}

function getMessage( $index, $response, $movie )
{
    $result = $response['message'];

    if ( $response['isSuccess'] )
    {
        if ( strcasecmp( $movie['title'], $response['content']->Title ) != 0 )
        {
            $result = array( "index" => $index, "title" => $movie['title'], "newTitle" => $response['content']->Title, "id" => $response['content']->imdbID, "poster" => $response['content']->Poster );
        }
    }
    elseif ( $response['message'] === "Movie not found!" )
    {
        $result = array( "index" => $index, "title" => $movie['title'], "newTitle" => "", "id" => $response['message'], "poster" => "" );
    }

    return $result;
}

function makeCSV( $data )
{
    return implode( ",", array( $data['id'], json_encode( $data[title] ), $data['rating'] ) ) . "\n";
}

function parseFile( $originalName )
{
    $originalFile = fopen( $originalName, "r" );
    $result = isSafe( $originalName );

    if ( $result['isSuccess'] )
    {
        ini_set( "allow_url_fopen", 1 );
        ini_set("default_socket_timeout", 1);

        $index = 1;
        $limitReached = false;
        $result['message'] = [];
        $columns = getColumns( fgetcsv( $originalFile ) );

        $row = fgetcsv( $originalFile, 200 );
        while ( $row !== false && $columns['isTitlePresent'] && !$limitReached )
        {
            $movie['title'] = trim( $row[$columns['tIndex']] );
            $movie['year'] = trim( $row[$columns['yIndex']] );
            $movie['rating'] = $columns['isRatingPresent'] ? trim( $row[$columns['rIndex']] ) : "--";
            $movie['id'] = $columns['isIdPresent'] ? trim( $row[$columns['iIndex']] ) : "";

            if ( !empty( $movie['title'] ) )
            {
                if ( empty( $movie['id'] ) )
                {
                    $searchTitle = urlencode( $movie['title'] );
                    $searchYear = $movie['year'] ?? "";
                    $url = "http://www.omdbapi.com/?t=$searchTitle&y=$searchYear&plot=short&r=json&apikey=$apiKey";
                    $response = getResponse( $url );

                    $limitReached = $response['message'] === "Request limit reached!";
                    array_push( $result['message'], getMessage( $index, $response, $movie ) );

                    if ( $response['isSuccess'] )
                    {
                        //If I change my mind and don't want the user's title overwritten, just remove this line
                        $movie['title'] = $response['content']->Title;
                        $movie['id'] = $response['content']->imdbID;
                    }
                }

                $result['contents'] .= makeCSV( $movie );
                $index++;
            }

            $row = fgetcsv( $originalFile, 200 );
        }
    }

    fclose( $originalFile );
    unlink( $originalName );

    return $result;
}

function makeUTF8( $mixed )
{
    if ( is_array( $mixed ) )
    {
        foreach ( $mixed as $key => $value )
        {
            $mixed[$key] = makeUTF8( $value );
        }
    }
    else if ( is_string( $mixed ) )
    {
        return utf8_encode( $mixed );
    }
    return $mixed;
}

$file = $_FILES['file'];
move_uploaded_file( $file['tmp_name'], $file['name'] );
echo json_encode( makeUTF8( parseFile( $file['name'] ) ), JSON_PARTIAL_OUTPUT_ON_ERROR, 2048 );
?>