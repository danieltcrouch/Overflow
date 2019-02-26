<?php
include_once("omdb.php");

if ( isset( $_FILES['file'] ) || ( isset( $_POST['action'] ) && function_exists( $_POST['action'] ) ) )
{
	$action = $_POST['action'];
    $result = null;

	if ( isset( $_FILES['file'] ) )
	{
        $result = convert( $_FILES['file'] );
	}
	elseif ( isset( $_POST['title'] ) )
	{
		$result = $action( $_POST['title'] );
	}
	else
	{
		$result = $action();
	}

	echo json_encode( $result, JSON_PARTIAL_OUTPUT_ON_ERROR, 2048 );
}

?>