<?php
if (isset($_GET["getNotifications"])) {
	$response = $bdd->prepare("SELECT * FROM `vbcms-notifications` WHERE userId='0' OR userId=?");
	$response->execute([$_SESSION["user_id"]]);
	$response = $response->fetchAll(PDO::FETCH_ASSOC);
	echo json_encode($response);
} elseif (isset($_GET["updateVBcms"])) {/*
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
    */
} elseif (isset($_GET["checkModulesAliases"])&&!empty($_GET["checkModulesAliases"])){
	$aliases = json_decode($_GET["checkModulesAliases"],true);
	$aliasesAlreadyUsed = array();
	if(isset($aliases['adminAccess'])){
		$response = $bdd->prepare("SELECT * FROM `vbcms-activatedExtensions` WHERE adminAccess=?");
		$response->execute([$aliases['adminAccess']]);
		if(!empty($response->fetch())) $aliasesAlreadyUsed['adminAccess'] = true;
		else $aliasesAlreadyUsed['adminAccess'] = false;
	}
	if(isset($aliases['clientAccess'])){
		$response = $bdd->prepare("SELECT * FROM `vbcms-activatedExtensions` WHERE clientAccess=?");
		$response->execute([$aliases['clientAccess']]);
		if(!empty($response->fetch())) $aliasesAlreadyUsed['clientAccess'] = true;
		else $aliasesAlreadyUsed['clientAccess'] = false;
	}
	echo json_encode($aliasesAlreadyUsed);
	
} elseif (isset($_GET["enableExtension"])&&!empty($_GET["enableExtension"])){
	$extensionToEnable = json_decode($_GET["enableExtension"],true);
	
	// On va scanner le dossier des extensions pour les afficher dans la page
	$extensionsFolder = $GLOBALS['vbcmsRootPath'].'/vbcms-content/extensions/';
	$extensionsFolderContent = scandir($extensionsFolder);
	foreach ($extensionsFolderContent as $extensionFolder){
		if(!in_array($extensionFolder,[".", ".."]) && is_dir($extensionsFolder.$extensionFolder)){ // Ici on check qu'il s'agisse bien d'un dossier
			if(file_exists($extensionsFolder.$extensionFolder.'/extensionInfos.json')){
				unset($extensionInfos);
				$extensionInfos = json_decode(file_get_contents($extensionsFolder.$extensionFolder.'/extensionInfos.json'),true);
				if($extensionInfos["name"]==$extensionToEnable["name"]){
					$extensionInfos["path"] = $extensionFolder;
					break;
				}
			}
		}
	}

	// Maintenant on va créer l'instance de l'extension et l'activer
	if($extensionInfos["type"]=="module"){
		$calledmodule = new VBcms\module($extensionInfos["name"]);
    	$calledmodule->initModule($extensionInfos["name"], $extensionInfos["path"], $extensionInfos["adminAccess"], $extensionInfos["clientAccess"], $extensionInfos["compatible"], $extensionInfos["workshopId"]);
	}
	
} elseif (isset($_GET["disableExtension"])&&!empty($_GET["disableExtension"])){
	$extensionToDisable = json_decode($_GET["disableExtension"],true);
	
	// On va récupérer les informations de l'extension en question
	$extensionInfos = $bdd->prepare("SELECT * FROM `vbcms-activatedExtensions` WHERE name = ?");
	$extensionInfos->execute([$extensionToDisable['name']]);
	$extensionInfos = $extensionInfos->fetch(PDO::FETCH_ASSOC);

	if(empty($extensionInfos)) echo "L'extension ".$extensionToDisable['name']." n'a pas été trouvée dans la base de données.";
	else {
		// Maintenant on va créer l'instance de l'extension et la désactiver
		if($extensionInfos["type"]=="module"){
			$calledmodule = new VBcms\module($extensionInfos["name"]);
			$calledmodule->disableModule($extensionToDisable['deleteData']);
		}
	}	
	
} elseif (isset($_GET["checkModulesAliases"])&&!empty($_GET["checkModulesAliases"])){
	$aliases = json_decode($_GET["checkModulesAliases"],true);
	$aliasesAlreadyUsed = array();
	if(isset($aliases['adminAccess'])){
		$response = $bdd->prepare("SELECT * FROM `vbcms-activatedExtensions` WHERE adminAccess=?");
		$response->execute([$aliases['adminAccess']]);
		if(!empty($response->fetch())) $aliasesAlreadyUsed['adminAccess'] = true;
		else $aliasesAlreadyUsed['adminAccess'] = false;
	}
	if(isset($aliases['clientAccess'])){
		$response = $bdd->prepare("SELECT * FROM `vbcms-activatedExtensions` WHERE clientAccess=?");
		$response->execute([$aliases['clientAccess']]);
		if(!empty($response->fetch())) $aliasesAlreadyUsed['clientAccess'] = true;
		else $aliasesAlreadyUsed['clientAccess'] = false;
	}
	echo json_encode($aliasesAlreadyUsed);
	
} elseif (isset($_GET["getSettingsHTML"])&&!empty($_GET["getSettingsHTML"])){
	$moduleToCall = json_decode($_GET["getSettingsHTML"],true);
	if($moduleToCall['moduleName']=="VBcms"){
		require_once $GLOBALS['vbcmsRootPath']."/vbcms-admin/includes/settingsPage.php";
		getSettingsHTML($moduleToCall['parameters']);
	} else {
		$moduleExist = $bdd->prepare("SELECT * FROM `vbcms-activatedExtensions` WHERE name=?");
		$moduleExist->execute([$moduleToCall['moduleName']]);
		$moduleExist=$moduleExist->fetch(PDO::FETCH_ASSOC);

		if(!empty($moduleExist)){
			$extensionsFolder = $GLOBALS['vbcmsRootPath'].'/vbcms-content/extensions/';
			$calledModule = new VBcms\module($moduleToCall['moduleName']);
			$calledModule->getSettingsPage($moduleToCall['parameters']);
		}else{
			echo "<h5>Impossible d'afficher la page</h5><p>L'extension <code>".$moduleToCall['moduleName']."</code> n'a pas été trouvée. 😢</p>";
		}
	}
	
		
} elseif (isset($_GET["saveSettings"])&& (isset($_POST)&&!empty($_POST))){	
	$response = $bdd->prepare("UPDATE `vbcms-settings` SET value=? WHERE name='websiteName'");
	$response->execute([$_POST["websiteName"]]);

	$response = $bdd->prepare("UPDATE `vbcms-settings` SET value=? WHERE name='websiteDescription'");
	$response->execute([$_POST["websiteDescription"]]);

	$response = $bdd->prepare("UPDATE `vbcms-settings` SET value=? WHERE name='websiteMetaColor'");
	$response->execute([$_POST["websiteMetaColor"]]);

	//$response = $bdd->prepare("UPDATE `vbcms-settings` SET value=? WHERE name='websiteLogo'");
	//$response->execute([$_POST["websiteLogo"]]);

	$response = $bdd->prepare("UPDATE `vbcms-settings` SET value=? WHERE name='steamApiKey'");
	$response->execute([$_POST["steamApiKey"]]);

	$response = $bdd->prepare("UPDATE `vbcms-settings` SET value=? WHERE name='updateCanal'");
	$response->execute([$_POST["updateCanal"]]);

	if (isset($_POST["debugMode"])) $response = $bdd->query("UPDATE `vbcms-settings` SET value='1' WHERE name='debugMode'");
	else $response = $bdd->query("UPDATE `vbcms-settings` SET value='0' WHERE name='debugMode'");

	if (isset($_POST["autoUpdatesSearch"])) $response = $bdd->query("UPDATE `vbcms-settings` SET value='1' WHERE name='autoUpdatesSearch'");
	else $response = $bdd->query("UPDATE `vbcms-settings` SET value='0' WHERE name='autoUpdatesSearch'");
	if (isset($_POST["autoUpdatesInstall"])) $response = $bdd->query("UPDATE `vbcms-settings` SET value='1' WHERE name='autoUpdatesInstall'");
	else $response = $bdd->query("UPDATE `vbcms-settings` SET value='0' WHERE name='autoUpdatesInstall'");
	if (isset($_POST["autoInstallCriticalUpdates"])) $response = $bdd->query("UPDATE `vbcms-settings` SET value='1' WHERE name='autoInstallCriticalUpdates'");
	else $response = $bdd->query("UPDATE `vbcms-settings` SET value='0' WHERE name='autoInstallCriticalUpdates'");
	
} elseif(isset($_GET)&&!empty($_GET)){
	echo "Commande \"".array_key_first($_GET)."(".$_GET[array_key_first($_GET)].")\" non reconnue.";
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
				<img src="<?=$websiteUrl?>vbcms-admin/images/vbcms-logo/raccoon-512x.png">
				<h1 class="mt-5">Tâches de fond</h1>
				<p>Cette page n'est pas accessible en tant que telle. Seuls les addons peuvent communiquer avec. :D</p>
			</div>
		</div>
	</div>
</body>
</html>
<?php } ?>