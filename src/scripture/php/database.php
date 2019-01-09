<?php

function getAllScores()
{
	$mysqli = new mysqli( 'localhost', 'religiv3_admin', '1corinthians3:9', 'religiv3_overflow' );

	$scores = array();
	$result = $mysqli->query( "SELECT name, score FROM sc_highScores ORDER BY score DESC " );
	if ( $result && $result->num_rows > 0 )
	{
		while( $row = $result->fetch_array() )
		{
			$highScore = $row['name'] . " - " . $row['score'];
			array_push( $scores, $highScore );
		}
	}

    return $scores;
}

function getScores()
{
	$mysqli = new mysqli( 'localhost', 'religiv3_admin', '1corinthians3:9', 'religiv3_overflow' );

	$scores = array();
	$result = $mysqli->query( "SELECT name, score FROM sc_highScores ORDER BY score DESC LIMIT 10" );
	if ( $result && $result->num_rows > 0 )
	{
		while( $row = $result->fetch_array() )
		{
			$highScore = $row['name'] . " - " . $row['score'];
			array_push( $scores, $highScore );
		} 
	}

    return $scores;
}

function getLowScore()
{
	$mysqli = new mysqli( 'localhost', 'religiv3_admin', '1corinthians3:9', 'religiv3_overflow' );

	$lowScore = 0;
	$result = $mysqli->query( "SELECT score FROM sc_highScores ORDER BY score DESC LIMIT 9, 1 " );
    if ( $result && $result->num_rows > 0 )
   	{
   		while( $row = $result->fetch_array() )
   		{
            $lowScore = $row['score'];
   		}
   	}

    return $lowScore;
}

function getHighScoreInfo()
{
	$mysqli = new mysqli( 'localhost', 'religiv3_admin', '1corinthians3:9', 'religiv3_overflow' );

	$highScoreInfo = array();
	$result = $mysqli->query( "SELECT name, score, email 
                                     FROM sc_highScores
                                     WHERE email <> 'none' AND score = (SELECT MAX(score) FROM sc_highScores WHERE email <> 'none') 
                                     ORDER BY score DESC " );
    if ( $result && $result->num_rows > 0 )
   	{
   		while( $row = $result->fetch_array() )
   		{
            array_push( $highScoreInfo, $row );
   		}
   	}

   	return $highScoreInfo;
}

function saveScore( $name, $score, $email )
{
	$mysqli = new mysqli( 'localhost', 'religiv3_admin', '1corinthians3:9', 'religiv3_overflow' );

	$email = isset( $email ) ? $email : "";
	$mysqli->query( "INSERT INTO sc_highScores ( name, score, email ) VALUES ( '" . $name . "', " . $score . ", '" . $email . "')" );

	return "true";
}

function getBibles()
{
	return [
	    "kjv" => file("resources/KJV.txt"),
        "niv" => file("resources/NIV.txt")
	];
}

function getDuplicates()
{
	return file("resources/duplicates.txt");
}

function sendEmail( $emails )
{
    $addressList = json_decode($emails);

    $to = implode( ',', $addressList );
    $subject = "Scripture Challenge Game";
    $message = "<p>Hey, someone has matched or surpassed your high score! Go <a href='http://overflow.religionandstory.com/scripture/'>here</a> to defend your honor!</p>";
    $message = wordwrap($message, 70);
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From:  ReligionAndStory<noreply@religionandstory.com>" . "\r\n" .
                "Bcc:   danieltcrouch@gmail.com";

    mail($to, $subject, $message, $headers);
}

if ( isset( $_POST['action'] ) && function_exists( $_POST['action'] ) )
{
	$action = $_POST['action'];
    $result = null;

	if ( isset( $_POST['name'] ) && isset( $_POST['score'] ) )
	{
	    $email = isset( $_POST['email'] ) ? $_POST['email'] : null;
		$result = $action( $_POST['name'], $_POST['score'], $email );
	}
	elseif ( isset( $_POST['emails'] ) )
	{
		$result = $action( $_POST['emails'] );
	}
	else
	{
		$result = $action();
	}
	
	echo json_encode( $result );
}

?>