<?php
if (isset($_GET["getNotifications"])) {
	$response = $bdd->prepare("SELECT * FROM `vbcms-notifications` WHERE userId='0' OR userId=?");
	$response->execute([$_SESSION["user_id"]]);
	$response = $response->fetchAll(PDO::FETCH_ASSOC);
	echo json_encode($response);
} elseif (isset($_GET["updateVBcms"])) {
	$curentUpdateCanal = $bdd->query("SELECT value FROM `vbcms-settings` WHERE name='updateCanal'")->fetchColumn();
	$serverId = $bdd->query("SELECT value FROM `vbcms-settings` WHERE name='serverId'")->fetchColumn();
	$key = $bdd->query("SELECT value FROM `vbcms-settings` WHERE name='encryptionKey'")->fetchColumn();
	$vbcmsVer = $bdd->query("SELECT value FROM `vbcms-settings` WHERE name='vbcmsVersion'")->fetchColumn();
	$curentUpdateCanal = $bdd->query("SELECT value FROM `vbcms-settings` WHERE name='updateCanal'")->fetchColumn();

	$updateInfos = file_get_contents("https://api.vbcms.net/updater/lastest?serverId=".$serverId."&key=".$key."&version=".$vbcmsVer."&canal=".$curentUpdateCanal);
	$updateInfosData = json_decode($updateInfos, true);

	$updateFilename = $GLOBALS['vbcmsRootPath']."/vbcms-content/updates/vbcms-update-v".$updateInfosData['version']."_from-".$vbcmsVer.".zip";
	if (!file_exists($GLOBALS['vbcmsRootPath']."/vbcms-content/updates")) mkdir($GLOBALS['vbcmsRootPath']."/vbcms-content/updates", 0755);
	//echo $updateInfosData["downloadLink"]."?serverId=".$serverId."&key=".$key;
	file_put_contents($updateFilename, file_get_contents($updateInfosData["downloadLink"]."?serverId=".$serverId."&key=".$key));
	if (file_exists($updateFilename)) {
		$zip = new ZipArchive;
		if ($zip->open($updateFilename) === TRUE) {
		    $zip->extractTo($GLOBALS['vbcmsRootPath']);
		    $zip->close();

		    $response["success"] = true;
		    $response["link"] = $websiteUrl."update.php";
		} else {
			$response["success"] = false;
			$response["code"] = 1; // Impossible d'ouvrir l'archive
		}
	} else {
		$response["success"] = false;
		$response["code"] = 0; // Impossible de télécharger la màj
	}
	echo json_encode($response);
} else {?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title><?=$websiteName?> | Tâches de fond</title>
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
				<img src="https://cdn.vbcms.net/images/vbcms-logo/raccoon-512x.png">
				<h1 class="mt-5">Tâches de fond</h1>
				<p>Cette page n'est pas accessible en tant que telle. Seuls les addons peuvent communiquer avec. :D</p>
			</div>
		</div>
	</div>
</body>
</html>
<?php } ?>