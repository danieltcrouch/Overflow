<?php include(getSubPath() . "common/php/startup.php"); ?>
<!DOCTYPE html>
<html>
<head>
	<title>Scripture Challenge</title>
    <?php includeHeadInfo(); ?>
</head>

<body>

	<!--Header-->
    <?php includeHeader(); ?>
    <div class="col-10 main">
        <div class="title center">Scripture Challenge</div>
    </div>

    <!--Main-->
	<div class="col-10 main">
        <div class="subtitle center">All High Scores</div>
    </div>
    <div id="highScores" class="col-10 main center">
    </div>

</body>
<script>
    function showAllScores()
   	{
   		$.post(
   			"php/database.php",
   			{ action: "getAllScores" },
            showAllScoresCallback
   		);
   	}

   	function showAllScoresCallback( response )
   	{
   		var scores = JSON.parse( response ).join( "<br />" );
   		$('#highScores').html( scores );
   	}

    showAllScores();
</script>
<?php includeModals(); ?>
</html>