<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title><?=VBcmsGetSetting("websiteName")?> | 404 :/</title>
	<?php include 'includes/depedencies.php';?>
</head>
<body>
	<?php 
	include ('includes/navbar.php');
	?>

	<!-- Contenu -->
	<div class="page-content" leftSidebar="240" rightSidebar="0">
		<div class="d-flex flex-column">
			<div class="align-self-center text-center">
				<img src="<?=VBcmsGetSetting("websiteUrl")?>/vbcms-admin/images/vbcms-logo/raccoon-512x.png">
				<h1 class="mt-5"><?=translate("404oups")?></h1>
				<p><?=translate("404message")?></p>
			</div>
		</div>
	</div>
</body>
</html>