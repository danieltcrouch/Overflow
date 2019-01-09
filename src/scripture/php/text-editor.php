<?php
/*fixKJV();
function fixKJV()
{
	$in = fopen("KJV.txt","r");
	$out = fopen("newKJV.txt","w");

	$book = "";
	$verse = "";
	while( $line = fgets($in) )
	{
		if ( strpos($line, "#") === 0 )
		{
			$book = trim( substr($line, 1) );
		}
		else if ( strpos($line, ":") === 1 || strpos($line, ":") === 2 || strpos($line, ":") === 3  )
		{
			fputs( $out, $verse . "\n" );  

			$verse = $book . "|" . getFormattedLine( $line ) . " ";
		}
		else if ( !empty( trim( $line ) ) )
		{
			$verse .= trim( $line ) . " ";
		}
	}
	
	fclose($in); 
	fclose($out);
}

function getFormattedLine( $line )
{
	preg_match("#:#", $line, $matches, PREG_OFFSET_CAPTURE);
	$colIndex = $matches[0][1];
	$temp = substr( $line, $colIndex );
	preg_match("#\s+#", $line, $matches, PREG_OFFSET_CAPTURE);
	$wsIndex = $matches[0][1];
	$line = substr_replace($line, '|', $colIndex, 1);
	$line = substr_replace($line, '@', $wsIndex, 1);
	
	return trim($line);
}

function fixKJV()
{
	$in = fopen("KJV.txt","r");
	$out = fopen("newKJV.txt","w");

	$book = "";
	while( $line = fgets($in) )
	{
		$verse = explode("@", $line);
		$ref = explode("|", $verse[0]);
		$book = $ref[0];
		
		if ( preg_match_all("#\d+:\d+#", $verse[1], $matches, PREG_OFFSET_CAPTURE) )
		{
			fputs( $out, substr($line, 0, $matches[0][0][1] + strlen($verse[0])) . "\n" );
			
			foreach ( $matches[0] as $newVerse )
			{
				$ref = $newVerse[0];
				$index = $newVerse[1];
				
				preg_match("#:#", $ref, $refMatches, PREG_OFFSET_CAPTURE);
				$colIndex = $refMatches[0][1];
				$ref = substr_replace($ref, '|', $colIndex, 1);
				
				$subVerse = substr($verse[1], $index + strlen($ref));
				preg_match("#\d+:\d+#", $subVerse, $nextMatch, PREG_OFFSET_CAPTURE);
				if ( $nextMatch[0][1] > 1 )
				{
					$subVerse = substr($subVerse, 0, $nextMatch[0][1]);
				}
				$subVerse = trim( $subVerse );
				fputs( $out, $book . "|" . $ref . "@" . $subVerse . "\n" );
			}
		}
		else
		{
			fputs( $out, $line );  
		}
	}
	
	fclose($in); 
	fclose($out);
}

function fixKJV()
{
	$in = fopen("KJV.txt","r");
	$out = fopen("newKJV.txt","w");

	$book = "";
	$verse = "";
	while( $line = fgets($in) )
	{
		if ( preg_match("#\|[A-z]+\|#", $line, $matches, PREG_OFFSET_CAPTURE) )
		{
			$pipeIndex = strpos($line, "|");
			$pipeIndex2 = strpos($line, "|", $pipeIndex + 1);
			$atIndex = strpos($line, "@");
		
			$word = substr( $line, $pipeIndex + 1, $pipeIndex2 - $pipeIndex - 1 );
			$after = substr( $line, $atIndex + 1 );
			
			$verse = substr($verse, 0, strlen($verse) - 1);
			$verse .= " " . $word . ": " . $after;
		}
		else
		{
			fputs( $out, $verse );  
			$verse = $line;
		}
	}
	
	fputs( $out, $verse );  
	
	fclose($in); 
	fclose($out);
}

function fixNIV()
{
	$in = fopen("NIV.txt","r");
	$out = fopen("newNIV.txt","w");

	$verse = "";
	$book = "";
	$chapter = 0;
	$verseNum = -1;
	while( $line = fgets($in) )
	{
		$leadingDigits;
		if ( preg_match('/^\d+/', $line, $match) )
		{
			$leadingDigits = $match[0];
		}
		
		if ( strpos($line, "#") === 0 )
		{
			$book = trim( substr($line, 1) );
			$chapter = 0;
			$verseNum = -1;
		}
		else if ( isset( $leadingDigits ) )
		{
			if ( strlen($verse) > 0 )
			{
				fputs( $out, $verse . "\n" );  
			}
			
			if ( $leadingDigits == ($chapter + 1) && $leadingDigits <> ($verseNum + 1) )
			{
				$chapter = $leadingDigits;
				$verseNum = 1;
			}
			else if ( $leadingDigits > $verseNum && $leadingDigits < ($verseNum + 10))
			{
				$verseNum = $leadingDigits;
			}
			else if ( $verseNum == ($chapter + 1) && $leadingDigits < $verseNum )
			{
				fputs( $out, "****************************************************\n" ); 
				$chapter = $verseNum;
				$verseNum = $leadingDigits;
			}
			
			$verse = $book . "|" . $chapter . "|" . $verseNum . "@" . substr( trim( $line ), strlen( $leadingDigits ) ) . " ";
		}
		else if ( !empty( trim( $line ) ) )
		{
			$verse .= trim( $line ) . " ";
		}
		
		unset( $leadingDigits );
	}
	
	fclose($in); 
	fclose($out);
}

function fixNIV()
{
	//PSALMs
	$in = fopen("NIV.txt","r");
	$out = fopen("newNIV.txt","w");

	$verse = "";
	$book = "";
	$chapter = 0;
	$verseNum = -1;
	while( $line = fgets($in) )
	{
		$leadingDigits;
		if ( preg_match('/^\d+/', $line, $match) )
		{
			$leadingDigits = $match[0];
		}
		
		if ( strpos($line, "#") === 0 )
		{
			$book = trim( substr($line, 1) );
			$chapter = 0;
			$verseNum = -1;
		}
		else if ( isset( $leadingDigits ) )
		{
			if ( strlen($verse) > 0 )
			{
				fputs( $out, $verse . "\n" );  
			}
			
			if ( $leadingDigits == 1 )
			{
				$chapter = $chapter + 1;
				$verseNum = 1;
			}
			else if ( $leadingDigits > $verseNum && $leadingDigits < ($verseNum + 2))
			{
				$verseNum = $leadingDigits;
			}
			
			$verse = $book . "|" . $chapter . "|" . $verseNum . "@" . substr( trim( $line ), strlen( $leadingDigits ) ) . " ";
		}
		else if ( !empty( trim( $line ) ) && strpos(trim( $line ), "PSALM") === false )
		{
			$verse .= trim( $line ) . " ";
		}
		
		unset( $leadingDigits );
	}
	
	fclose($in); 
	fclose($out);
}
*/?>