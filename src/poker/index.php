<?php include("$_SERVER[DOCUMENT_ROOT]/common/php/startup.php"); ?>
<!DOCTYPE html>
<html>
<head>
	<title>Poker</title>
    <?php includeHeadInfo(); ?>
</head>

<body>

	<!--Header-->
    <?php includeHeader(); ?>
    <div class="col-10 header">
        <div class="title center"><span class="clickable">
                Texas Hold&rsquo;em
            <img style="width: .5em; padding-bottom: .25em" src="<?php getHelpImage() ?>" alt="help">
        </span></div>
        <div id="instructions" style="display: none">
            ...
        </div>
    </div>

    <!--Main-->
    <div class="col-10 main">
        <div class="title center">This Page is under construction</div>
        <br />
        <div class="center"><img src="<?php getConstructionImage() ?>" width="300px"></div>
    </div>

</body>
<?php includeModals(); ?>
</html>