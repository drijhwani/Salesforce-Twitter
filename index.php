<!doctype HTML>
<html lang="en">
<head>
	<title>Twitter Timeline</title>
	<meta charset="utf-8"/>
	<meta http-equiv="X-UA-Compatible" content="chrome=1,IE=edge">
	<meta http-equiv="refresh" content = "60" />
	<meta name="description" content="">
	<meta name="keywords" content="">
	<meta name="robots" content="index,follow">
	<link rel="stylesheet" href="css/style.css">
</head>

<body>
	<h1>Salesforce Twitter Timeline</h1>
	
	<form id="live-search" action="" class="styled" method="post">
    	<b>Search : </b>
        <input type="text" class="text-input" id="filter" value="" placeholder="Search something..." size="40"/>
        <span id="filter-count"></span>
	</form>

	<?php include_once("_includes/gettweets.php"); ?>

	<footer>
		<h1>Salesforce-twitter-timeline</h1>
	</footer>
	
	<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
	<script type="text/javascript" src="//platform.twitter.com/widgets.js"></script>	
	<script type="text/javascript" src="javascript/twitter.js"></script>
	<script type="text/javascript" src="javascript/search.js"></script>

</body>
</html>

