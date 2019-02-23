<?php
function getMovie( $title )
{
    $result['isSuccess'] = false;
    $result['search'] = $title;

    $title = trim( $title );
    $searchTitle = urlencode( $title );
    //OMDB API now requires an API Key (&apikey=522c6900) -> go to their site if this one stops working
    $url = "http://www.omdbapi.com/?t=$searchTitle&y=&plot=short&r=json&apikey=522c6900";
    $response = json_decode( file_get_contents( $url ) );

    if ( $response->Response === "True" )
    {
        $result['isSuccess'] = true;
        $result['title'] = $response->Title;
        $result['id'] = $response->imdbID;
        $result['poster'] = $response->Poster;
    }

    return $result;
}

echo json_encode( getMovie( $_POST['title'] ) );
?>