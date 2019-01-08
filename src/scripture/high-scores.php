<?php include_once($_SERVER["DOCUMENT_ROOT"] . "/rns/common/php/startup.php"); ?>
<!DOCTYPE html>
<html>
<head>
	<title>Scripture Challenge</title>
    <?php include($BASE_PATH . "/common/html/head.html"); ?>
</head>

<body>

	<!--Header-->
    <?php include($BASE_PATH . "/common/html/header.html"); ?>
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
   			"utility/database.php",
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
<?php include($BASE_PATH . "/common/html/modal.html"); ?>
<?php include($BASE_PATH . "/common/html/toaster.html"); ?>
</html>