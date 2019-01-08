<?php
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

function getColumns( $firstRow )
{
    $result['tIndex'] = array_search( "Title", $firstRow, true );
    $result['rIndex'] = array_search( "Rating", $firstRow, true );
    $result['yIndex'] = array_search( "Year", $firstRow, true );
    $result['iIndex'] = array_search( "ID", $firstRow, true );

    $result['isTitlePresent'] = $result['tIndex'] !== false;
    $result['isRatingPresent'] = $result['rIndex'] !== false;
    $result['isYearPresent'] = $result['yIndex'] !== false;
    $result['isIdPresent'] = $result['iIndex'] !== false;

    return $result;
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
        if ( strcasecmp( $movie['title'], $response['content']->Title ) != 0 ||
             strcasecmp( $movie['year'], $response['content']->Year ) != 0 )
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
        $result['contents'] = [];
        $columns = getColumns( fgetcsv( $originalFile, 200, "," ) );

        $row = fgetcsv( $originalFile, 200 );
        while ( $row !== false && $columns['isTitlePresent'] && !$limitReached )
        {
            $movie['title'] = trim( $row[$columns['tIndex']] );
            $movie['rating'] = $columns['isRatingPresent'] ? trim( $row[$columns['rIndex']] ) : "--";
            $movie['year'] = $columns['isYearPresent'] ? trim( $row[$columns['yIndex']] ) : "--";
            $movie['id'] = $columns['isIdPresent'] ? trim( $row[$columns['iIndex']] ) : "";

            if ( !empty( $movie['id'] ) )
            {
                $url = "http://www.omdbapi.com/?i=$movie[id]&y=&plot=short&r=json&apikey=8f0ce8a6";
                $response = getResponse( $url );

                $limitReached = $response['message'] === "Request limit reached!";
                array_push( $result['message'], getMessage( $index, $response, $movie ) );

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
            $mixed[$key] = utf8ize( $value );
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