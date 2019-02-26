<?php include("$_SERVER[DOCUMENT_ROOT]/common/php/startup.php"); ?>
<!DOCTYPE html>
<html>
<head>
    <?php
    $pageTitle  = "IMDb Converter";
    $image      = "https://overflow.religionandstory.com/imdb/images/imdb.jpg";
    $description= "Convert CSV files with movie titles to IMDBb movie lists.";
    $keywords   = "IMDb,Criticker,spreadsheet,csv,conversion";
    includeHeadInfo();
    ?>
    <script src="javascript/imdb.js"></script>
</head>

<body>

    <!--Header-->
    <?php includeHeader(); ?>
    <div class="col-10 header">
        <div class="title center"><span class="clickable">
            IMDb Converter
            <img style="width: .5em; padding-bottom: .25em" src="<?php getHelpImage() ?>" alt="help">
        </span></div>
        <div id="instructions" style="display: none">
            The IMDb Converter associates movie titles with the unique IDs provided by IMDb.
            This can aid in uploading personal movie ratings to <a class="link" href="https://www.criticker.com/">Criticker</a> or <a class="link" href="https://letterboxd.com/">Letterboxd</a>.
            <br /><br />
            To use, upload a CSV file with the column headers &ldquo;Title&rdquo; and/or &ldquo;ID&rdquo; (may also include &ldquo;Year&rdquo;).
            Movie IDs will be fetched using their title and year or <i>vice versa</i>. Results that do not match exactly will be printed for your review.
            Beware that movies may not match as intended, especially if the year is not included.
            If both an ID and title are provided, that row will be skipped.
            Files with over 500 movies have a hard time completing; to ensure completion, include IDs or break the file into multiple parts.
            When the conversion is complete, a download should start with the IDs, titles, and years in one file.
            <br /><br />
            Contact <a class="link" href="mailto:dcrouch1@harding.edu?Subject=IMDb%20Converter" target="_top">Daniel Crouch</a> regarding any questions or issues.<br/>
            This tool utilizes the OMDb Api which requires a small fee; please consider <a class="link" href="https://paypal.me/danieltcrouch?locale.x=en_US">donating</a> a few dollars to keep this tool available.
        </div>
    </div>

    <!--Main-->
    <div class="col-10 main">
        <div class="center">
            <input id="getMovie" type="button" class="button" style="width: 10em; margin-bottom: 1em;" onclick="getMovie()" value="Find Movie">
        </div>
        <div class="center">
            <input id="submit" type="button" class="button" style="width: 10em; margin-bottom: 1em;" onclick="uploadFile()" value="Upload Movies">
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
<?php includeModals(); ?>
</html>