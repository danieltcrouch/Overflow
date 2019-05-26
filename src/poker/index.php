<?php include(getSubPath() . "common/php/startup.php"); ?>
<!DOCTYPE html>
<html>
<head>
    <?php
    $pageTitle  = "Poker Simulator";
    $image      = "https://overflow.religionandstory.com/poker/images/poker.jpg";
    $description= "This is a Texas Hold'em poker simulator (currently in development) designed to help you improve your poker skills.";
    $keywords   = "Poker,Texas Hold'em,card game,simulator,statistics";
    includeHeadInfo();
    ?>
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
        <br/>
        <div class="textBlock center">
            Contact <a class="link" href="mailto:dcrouch1@harding.edu?Subject=Seven%20Dimensions" target="_top">Daniel Crouch</a> for any questions regarding the game.
        </div>
        <div class="center"><img src="<?php getConstructionImage(); ?>" width="300px"></div>
    </div>

</body>
<?php includeModals(); ?>
</html>