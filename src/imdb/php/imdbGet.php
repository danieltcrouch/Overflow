<?php
include_once("omdb.php");

function getMovie( $title )
{
    $result['isSuccess'] = false;
    $result['search'] = $title;

    $title = trim( $title );
    $searchTitle = urlencode( $title );
    //OMDB API requires API Key -> go to their site if this one stops working
    global $apiKey;
    $url = "http://www.omdbapi.com/?t=$searchTitle&y=&plot=short&r=json&apikey=$apiKey";
    $response = json_decode( file_get_contents( $url ) );

    if ( $response->Response === "True" )
    {
        $result['isSuccess'] = true;
        $result['title'] = $response->Title;
        $result['year'] = $response->Year;
        $result['id'] = $response->imdbID;
        $result['poster'] = $response->Poster;
    }

    return $result;
}

echo json_encode( getMovie( $_POST['title'] ) );
?>