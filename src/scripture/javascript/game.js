function startScriptureChallenge( elements )
{
	var scriptureChallenge = new ScriptureChallenge( elements );
	scriptureChallenge.initialize();
}

var ScriptureChallenge = function( elements )
{
	const KJV_OT_VERSES = 23145;
	const KJV_NT_VERSES = 7956;
	const NIV_OT_VERSES = 23188;
	const NIV_NT_VERSES = 7942;
	var KJV = [];
	var NIV = [];
	var DUPLICATES = [];

	var answerBookFull;
	var answerBook;
	var answerChapter;
	var answerVerse;

	var isNiv = true;

	var currentScore = 0;
	var lowScore = 0;
	var highestScore = 0;
	var highScoreEmails = [];

	//var timeToRead = false;
	//var paused = false;

	this.initialize = function()
	{
		setBibles();
		setDuplicates();
		setLowScore();
		setHighestScore();
		setHandlers();
	};

	function setBibles()
	{
		$.post(
			"utility/database.php",
			{ action: "getBibles" },
			function ( response ) {
				NIV = JSON.parse( response ).niv;
				KJV = JSON.parse( response ).kjv;
				nextRound();
			}
		);
	}

	function setDuplicates()
	{
		$.post(
			"utility/database.php",
			{ action: "getDuplicates" },
			function ( response ) {
				DUPLICATES = [];
				JSON.parse( response ).forEach( function(elements) {
					var citationSet = elements.trim().split("|").map( function(element) {
						return {
                            book:		element.split("@")[0].substring(0,3).toUpperCase(),
                            chapter:	element.split("@")[1].split(":")[0],
                            verse:		element.split("@")[1].split(":")[1]
                        }
                    } );
			        DUPLICATES.push( citationSet );
			    });
			}
		);
	}

	function setLowScore()
	{
		$.post(
			"utility/database.php",
			{ action: "getLowScore" },
			function ( response ) {
				lowScore = parseInt( JSON.parse( response ) );
			}
		);
	}

	function setHighestScore()
	{
		$.post(
			"utility/database.php",
			{ action: "getHighScoreInfo" },
			function ( response ) {
				var highScoreInfos = JSON.parse( response );
				highestScore = parseInt( highScoreInfos[0].score );
				highScoreEmails = highScoreInfos.filter( function(highScoreInfo) {
					return highScoreInfo.email;
				} ).map(
					highScoreInfo => highScoreInfo.email
				);
			}
		);
	}

	function setHandlers()
	{
		elements.verseInput.on( "keyup", function ( e ) {
			if ( e.keyCode === 13 ) {
				submitAnswer();
			}
		});
		elements.submitButton.on( "click", function () {
			submitAnswer();
		});
		elements.scoreSection.on( "click", function () {
			showScores();
		});
		setRadioCallback( "version", function () {
			if ( isNowNiv() !== isNiv ) {
				( currentScore === 0 ) ? nextRound() : submitAnswer();
				isNiv = !isNiv;
			}
		});
	}

	function isNowNiv()
	{
		return getSelectedRadioButton( "version" ).id === "niv";
	}

	function nextRound()
	{
		//timeToRead = false;
		//setTimeout( function() { timeToRead = true; }, 1000 );

	    var verse = getRandomVerse();

	    elements.verseDisplay.text( verse.text );
		setAnswer( verse.book, verse.chapter, verse.verse );
		clearInputs();
	}

	function getRandomVerse()
	{
	    var verse;
	    var book;

	    do
	    {
	        var isOT = Math.random() < 0.5; //Verses will evenly distribute between Old and New Testaments
	        var range = isOT ? (isNiv ? NIV_OT_VERSES : KJV_OT_VERSES) : (isNiv ? NIV_NT_VERSES : KJV_NT_VERSES);
	        var minimum = isOT ? 0 : (isNiv ? NIV_OT_VERSES : KJV_OT_VERSES);
	        var randomIndex = Math.floor( Math.random() * range ) + minimum;

	        var verseString = isNiv ? NIV[randomIndex] : KJV[randomIndex];
	        verse = {
	        	text:    verseString.split( "@" )[1],
	        	book:    verseString.split( "@" )[0].split( "|" )[0],
	        	chapter: verseString.split( "@" )[0].split( "|" )[1],
	        	verse:   verseString.split( "@" )[0].split( "|" )[2]
	        };
	        book = verse.book;
	    }
	    while ( isGrayListBook( book ) && Math.random() < 0.25 ); //Certain Books will appear 25% less

	    return verse;
	}

	function isGrayListBook( book )
	{
		return ( book === "1 Chronicles" ||
				 book === "2 Chronicles" ||
				 book === "Job" ||
				 book === "Psalms" ||
				 book === "Isaiah" ||
				 book === "Jeremiah" ||
				 book === "Ezekiel" );
	}

	function setAnswer( book, chapter, verse )
	{
	    answerBookFull = book;
	    answerBook = book.substring(0,3).toUpperCase();
	    answerChapter = chapter;
	    answerVerse = verse;
	}

	function submitAnswer()
	{
		var points = getPoints();
		var answer = answerBookFull + " " + answerChapter + ":" + answerVerse;
        currentScore = currentScore + points;

		if ( points > 0 )
		{
			elements.scoreDisplay.text( currentScore + "" );
			elements.answerDisplay.text( answer );
		}
		else if ( currentScore > lowScore )
		{
			setHighScore( answer );
		}
		else
		{
			var message = answer + "<br />You&rsquo;ve lost with a score of " + currentScore;
			showMessage( "Game Over", message );
			clear();
		}
		nextRound();
	}

	function getPoints()
	{
		var points = 0;
		var possibleAnswers = getPossibleAnswers();
		var correct = getCorrectBinary( possibleAnswers );

		if ( correct.charAt(0) === "1" ) //correct book
		{
			points += 3;
			var isChapterCorrect = false;
			if ( correct.charAt(1) === "1" ) //correct chapter
			{
				points += 1;
				isChapterCorrect = true;
			}
			if ( correct.charAt(2) === "1" ) //correct verse
			{
				points += 1;
				if ( isChapterCorrect )
				{
					points += 3;
				}
			}
		}

		if ( points > 0 && possibleAnswers.length > 1 )
		{
			showToaster("This was a duplicate verse so there were multiple correct answers.");
		}

		return points;
	}

	function getPossibleAnswers()
	{
	    var possibleAnswers = [];

		DUPLICATES.forEach( function(citationSet) {
		    return citationSet.forEach( function(citation) {
		    	if ( answerBook 	=== citation.book &&
					 answerChapter	=== citation.chapter &&
					 answerVerse	=== citation.verse )
				{
				    possibleAnswers = citationSet;
					return true;
				}
		    });
		});

		if ( possibleAnswers.length === 0 )
		{
		    possibleAnswers.push( { book: answerBook, chapter: answerChapter, verse: answerVerse } );
		}

		return possibleAnswers;
	}

	function getCorrectBinary( possibleAnswers )
	{
	    var result = "";
	    var highScore = 0;

	    var inputBook		= elements.bookInput.val().substring( 0, 3 ).toUpperCase().trim();
	    var inputChapter	= elements.chapterInput.val().toUpperCase().trim();
	    var inputVerse		= elements.verseInput.val().toUpperCase().trim();

		possibleAnswers.forEach( function(citation) {
			var binary = "";
		    binary += ( inputBook       === citation.book )     ? "1" : "0";
			binary += ( inputChapter    === citation.chapter )  ? "1" : "0";
			binary += ( inputVerse      === citation.verse )    ? "1" : "0";

			var score = parseInt(binary, 2);
			if ( score > highScore )
			{
			    highScore = score;
			    result = binary;
			}
		});

		return result;
	}

	function setHighScore( answer )
	{
		if ( currentScore )
        {
    		showPrompt(
    			"High Score",
    			answer + "<br />You&rsquo;ve set a new High Score of <strong>" + currentScore + "</strong>!<br />Enter your name:",
    			setHighScoreCallback,
    			"Your Name",
				true
    		);
        }
	}

	function setHighScoreCallback( response )
	{
	    var name = response || "Anonymous";
        if ( currentScore >= highestScore )
        {
    		var message = "<span>Since you set the new highest score, enter your email to receive notifications if you&rsquo;re dethroned:</span>";
    		showPrompt(
    			"Highest Score",
    			message,
    			function (response) {
    			    saveScore( name, response );
    			},
    			"Email Address",
				true
    		);

    		sendNotificationEmail();
        }
        else
        {
			saveScore( name );
        }
	}

	function saveScore( name, email )
	{
	    $.post(
			"utility/database.php",
			{
				name: name,
				email: email,
				score: currentScore,
				action: "saveScore"
			},
			function() {
				showScores();
				clear();
			}
		);
	}

	function sendNotificationEmail()
	{
		$.post(
			"utility/database.php",
			{
				emails: JSON.stringify(highScoreEmails),
				action: "sendEmail"
			},
			function (response) {}
		);
	}

	function showScores()
	{
		$.post(
			"utility/database.php",
			{ action: "getScores" },
			showScoresCallback
		);
	}

	function showScoresCallback( response )
	{
		var message = JSON.parse( response ).join( "<br />" );
		showMessage( "High Scores", message );
	}

	function clear()
	{
		currentScore = 0;
		elements.scoreDisplay.text( currentScore );
		elements.answerDisplay.text( "" );
		clearInputs();
	}

	function clearInputs()
	{
		elements.bookInput.val( "" );
		elements.chapterInput.val( "" );
		elements.verseInput.val( "" );
	}

	// window.onblur = function() {
	// 	if ( timeToRead && !paused )
	// 	{
	// 		//submitAnswer();
	// 	}
	// };

	// function pause()
	// {
	// 	paused = !paused;
	// 	$( "#pauseDiv" ).toggle();
	// }
};
