<!DOCTYPE html>
<html lang="<?=$_SESSION['language']?>">
<head>
	<meta charset="utf-8">
	<title><?=$websiteName?></title>
	<?php include $_SERVER['DOCUMENT_ROOT'].'/vbcms-content/themes/default/includes/depedencies.php';?>
</head>
<body>
	<?php include $pageToInclude; ?>
</body>
</html>