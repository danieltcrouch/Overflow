<?php include("$_SERVER[DOCUMENT_ROOT]/common/php/startup.php"); ?>
<!DOCTYPE html>
<html>
<head>
    <?php
    $pageTitle  = "Scripture Challenge";
    $image      = "https://overflow.religionandstory.com/scripture/images/scripture.jpg";
    $description= "This is a game to test your knowledge of the Bible. Enter the Book, Chapter, and Verse for the quotes that appear.";
    $keywords   = "Scripture,Bible,Word of God,book,chapter,verse,game,quiz,quote";
    includeHeadInfo();
    ?>
    <script src="javascript/game.js"></script>
</head>

<body>

	<!--Header-->
    <?php includeHeader(); ?>
    <div class="col-10 header">
        <div class="title center"><span class="clickable">
                Scripture Challenge
                <img style="width: .5em; padding-bottom: .25em" src="<?php getHelpImage() ?>" alt="help">
        </span></div>
        <div id="instructions" style="display: none">
            <div style="text-align: left">
                <strong>Rules:</strong>
                <ul>
                    <li>Enter the Book, Chapter, and Verse for the quotes that appear. If you enter the wrong book, the game will end.</li>
                    <li>Books are worth 3pts, Chapters worth 1pt, and Verses 1pt. A bonus of 3pts is awarded if all fields are correct.</li>
                </ul>
                <strong>Notes:</strong>
                <ul>
                    <li>Acceptable book names can be found <a class="link" href="http://mbhumanistsatheists.ca/wp-content/uploads/bible-books.png">here</a>. Only the first 3 letters of a book name are checked. </li>
                    <li>Over 200 verses in the Bible are duplicates. Any citation for a duplicated verse should be acceptable. </li>
                    <li>For more variety in game-play, the following books appear 25% less than they would naturally: 1 & 2 Chronicles, Job, Psalms, Isaiah, Jeremiah, and Ezekiel. </li>
                    <li>On average, verses will be evenly distributed between the Old and New Testaments. </li>
                    <li>Players may switch between the NIV and KJV until points have been gained, after which point, players will lose their game by switching. </li>
                    <li>Previously, to prevent cheating, if the screen lost focus (so a player could look up a verse), the game ended. This feature has been disabled. </li>
                    <!--To prevent cheating, you will lose if you click off the screen unless you <em>pause</em>.-->
                    <li>All previous high scores can be found <a class="link" href="https://overflow.religionandstory.com/scripture/high-scores.php">here</a>. </li>
                    <li>Also, this game is <em>really</em> hard.</li>
                </ul>
            </div>
        </div>
    </div>

    <!--Main-->
    <div id="game" class="col-10 main">
        <div class="col-10 center">
            <button id="niv" name="version" class="button selectedButton" style="width: 5em; margin: .25em;">NIV</button>
            <button id="kjv" name="version" class="button inverseButton" style="width: 5em; margin: .25em;">KJV</button>
        </div>

        <div class="col-10 center hide-able">
        	<div class="subtitle">Verse:</div>
        	<div id="verseDisplay">Loading First Verse...</div>
        </div>

        <input id="book" type="text" class="input" placeholder="Book">
        <input id="chapter" type="number" class="input" placeholder="Chapter">
        <input id="verse" type="number" class="input" placeholder="Verse">
        <div class="col-10 center">
            <button id="submitButton" class="button" style="width: 10em; margin: 1em auto;">Submit</button>
        </div>

        <div class="col-10 center">
        	<div id="answerDisplay"></div>
        	<span id="scoreSection" class="subtitle link" style="font-weight: normal">
        		Score: <span id="scoreDisplay">0</span>
        	</span>
        </div>
    </div>

</body>
<script>
    $( document ).ready( startGame );

    function startGame()
    {
        var elements = {
            bookInput: $('#book'),
            chapterInput: $('#chapter'),
            verseInput: $('#verse'),
            verseDisplay: $('#verseDisplay'),
            answerDisplay: $('#answerDisplay'),
            scoreDisplay: $('#scoreDisplay'),
			scoreSection: $('#scoreSection'),
			submitButton: $('#submitButton'),
			rulesSpan: $('#rulesSpan')
        };
        startScriptureChallenge( elements );
    }
</script>
<?php includeModals(); ?>
</html>
