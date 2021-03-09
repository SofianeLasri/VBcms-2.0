<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title><?=$websiteName?> | <?=$title?></title>
	<?php include 'depedencies.php';?>
	<?=$depedencies?>
</head>
<body>
	<?php 
	include ('navbar.php');
	include $pageToInclude;
	?>
</body>
</html>