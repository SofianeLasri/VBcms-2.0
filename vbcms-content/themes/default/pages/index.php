<!DOCTYPE html>
<html lang="<?=$_SESSION['language']?>">
<head>
	<meta charset="utf-8">
	<title><?=$websiteName?></title>
	<?php include $_SERVER['DOCUMENT_ROOT'].'/vbcms-content/themes/default/includes/depedencies.php';?>
</head>
<body>
	<header class="container">
		<div class="navbar navbar-expand-lg navbar-light">
			<a class="navbar-brand" href="<?=$_SERVER['DOCUMENT_ROOT']?>">
				<img src="#">
			</a>
			<div class="collapse navbar-collapse" id="navbarNav">
				<ul class="navbar-nav">
					<li class="nav-item">
						<a href="#">Test</a>
					</li>
				</ul>
			</div>
		</div>
	</header>
	<div class="index-presentationTop">
		<h3>Merci d'avoir choisi VBcms</h3>
		<p>Ouai c'est en dev, et alors?</p>
	</div>
</body>
</html>