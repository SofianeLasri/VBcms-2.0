<?php
// Pour éviter que quelqu'un y accède depuis l'éxtérieur
if(isset($vbcmsRequest)){
?>

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

<?php } ?>