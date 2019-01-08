<?php include_once($_SERVER["DOCUMENT_ROOT"] . "/utility/common/php/startup.php"); ?>
<!DOCTYPE html>
<html>
<head>
	<title>IMDb Helper</title>
    <?php include($BASE_UTL_PATH . "/common/html/head.html"); ?>
    <script src="http://religionandstory.webutu.com/utility/imdb/javascript/imdb.js"></script>
</head>

<body>

    <!--Header-->
    <?php include($BASE_UTL_PATH . "/common/html/header.html"); ?>
    <div class="col-10 header">
        <div class="title center"><span class="clickable">
            IMDb Helper
            <img style="width: .5em; padding-bottom: .25em" src="http://religionandstory.webutu.com/utility/common/images/question-mark.png" alt="help">
        </span></div>
        <div id="instructions" style="display: none">
            The IMDb Helper associates movie titles with the unique IDs provided by IMDb.
            This can aid in uploading personal movie ratings to <a class="link" href="https://www.criticker.com/">Criticker</a>.
            <br /><br />
            To use, upload a CSV file with the column header &ldquo;Title&rdquo; (may also include &ldquo;Rating&rdquo; and &ldquo;ID&rdquo;).
            Movie IDs will be fetched using their title. Results that do not match exactly will be printed for your review.
            Beware that movies may not match as intended (try searching for 1998&rsquo;s <i>Twilight</i>).
            If an ID column is included and a given title has an ID, that movie will be skipped.
            Files with over 500 movies have a hard time completing; to ensure completion, include IDs or break file into multiple parts.
            When the conversion is complete, a download should start with the IDs, titles, and ratings in one file.
        </div>
    </div>

    <!--Main-->
    <div class="col-10 main">
        <div class="center">
            <input id="getMovie" class="button" style="width: 10em; margin-bottom: 1em;" onclick="getMovie()" value="Find Movie">
        </div>
        <div class="center">
            <input id="submit" class="button" style="width: 10em; margin-bottom: 1em;" onclick="uploadFile()" value="Upload Movies">
        </div>
        <div class="center">
            <!-- Hidden Input -->
            <input id="file" name="file" type="file" style="position: absolute; left: 0; top: 0; opacity: 0;">
        </div>
        <div class="center">
            <span id="info">Choose a File</span>
        </div>
    </div>

</body>
<?php include($BASE_UTL_PATH . "/common/html/modal.html"); ?>
<?php include($BASE_UTL_PATH . "/common/html/toaster.html"); ?>
</html>