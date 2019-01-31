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
            The IMDb Helper associates movie titles with the unique IDs provided by IMDb.
            This can aid in uploading personal movie ratings to <a class="link" href="https://www.criticker.com/">Criticker</a> or <a class="link" href="https://letterboxd.com/">Letterboxd</a>.
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
<?php includeModals(); ?>
</html>