<?php
if (isset($_GET["getNotifications"])) {
	$response = $bdd->prepare("SELECT * FROM `vbcms-notifications` WHERE userId='0' OR userId=?");
	$response->execute([$_SESSION["user_id"]]);
	$response = $response->fetchAll(PDO::FETCH_ASSOC);
	echo json_encode($response);
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

	// Maintenant on va cr??er l'instance de l'extension et l'activer
	if($extensionInfos["type"]=="module"){
		$calledmodule = new module($extensionInfos["name"]);
    	$calledmodule->initModule($extensionInfos["name"], $extensionInfos["path"], $extensionToEnable["adminAccess"], $extensionToEnable["clientAccess"], $extensionInfos["compatible"], $extensionInfos["workshopId"]);
	}
	
} elseif (isset($_GET["disableExtension"])&&!empty($_GET["disableExtension"])){
	$extensionToDisable = json_decode($_GET["disableExtension"],true);
	
	// On va r??cup??rer les informations de l'extension en question
	$extensionInfos = $bdd->prepare("SELECT * FROM `vbcms-activatedExtensions` WHERE name = ?");
	$extensionInfos->execute([$extensionToDisable['name']]);
	$extensionInfos = $extensionInfos->fetch(PDO::FETCH_ASSOC);

	if(empty($extensionInfos)) echo "L'extension ".$extensionToDisable['name']." n'a pas ??t?? trouv??e dans la base de donn??es.";
	else {
		// Maintenant on va cr??er l'instance de l'extension et la d??sactiver
		if($extensionInfos["type"]=="module"){
			$calledmodule = new module($extensionInfos["name"]);
			$calledmodule->disableModule($extensionToDisable['deleteData']);
		}
	}

	// Elle peut ??tre utilis??e comme fonction de base
	$baseFunctions = $bdd->prepare("SELECT * FROM `vbcms-baseModulesAssoc` WHERE extensionName = ?");
	$baseFunctions->execute([$extensionToDisable['name']]);
	foreach($baseFunctions as $baseFunction){
		$fixedAssoc = $bdd->prepare("UPDATE `vbcms-baseModulesAssoc` SET extensionName = '' WHERE name = ?");
		$fixedAssoc->execute([$baseFunction['name']]);
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
			$calledModule = new module($moduleToCall['moduleName']);
			$calledModule->getSettingsPage($moduleToCall['parameters']);
		}else{
			echo "<h5>Impossible d'afficher la page</h5><p>L'extension <code>".$moduleToCall['moduleName']."</code> n'a pas ??t?? trouv??e. ????</p>";
		}
	}
	
		
} elseif (isset($_GET["saveSettings"])&& (isset($_POST)&&!empty($_POST))){	
	$response = $bdd->prepare("UPDATE `vbcms-settings` SET value=? WHERE name='websiteName'");
	$response->execute([$_POST["websiteName"]]);

	$response = $bdd->prepare("UPDATE `vbcms-settings` SET value=? WHERE name='websiteDescription'");
	$response->execute([$_POST["websiteDescription"]]);

	$response = $bdd->prepare("UPDATE `vbcms-settings` SET value=? WHERE name='websiteMetaColor'");
	$response->execute([$_POST["websiteMetaColor"]]);

	if(isset($_POST["websiteLogo"])){
		$response = $bdd->prepare("UPDATE `vbcms-settings` SET value=? WHERE name='websiteLogo'");
		$response->execute([$_POST["websiteLogo"]]);
	}

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
	
}elseif (isset($_GET["fixBaseFunctionAssoc"])&& (isset($_POST)&&!empty($_POST))) {
	foreach ($_POST as $assocName => $extName){
		$fixedAssoc = $bdd->prepare("UPDATE `vbcms-baseModulesAssoc` SET extensionName = ? WHERE name = ?");
		$fixedAssoc->execute([$extName, $assocName]);
	}
} elseif (isset($_GET["getLocalAccount"])&&!empty($_GET["getLocalAccount"]) && verifyUserPermission($_SESSION['user_id'], "vbcms", 'manageUsersSettings')){
	$localAccountExist = $bdd->prepare("SELECT * FROM `vbcms-localAccounts` WHERE userIdAssoc = ?");
	$localAccountExist->execute([$_GET["getLocalAccount"]]);
	$localAccountExist = $localAccountExist->fetch(PDO::FETCH_ASSOC);
	echo json_encode($localAccountExist);
	
} elseif (isset($_GET["changeUserGroup"])&&!empty($_GET["changeUserGroup"]) && verifyUserPermission($_SESSION['user_id'], "vbcms", 'manageUsersSettings')){
	$modificationDetail = json_decode($_GET["changeUserGroup"], true);
	$query = $bdd->prepare("UPDATE `vbcms-users` SET `groupId` = ? WHERE `vbcms-users`.`id` = ?");
	$query->execute([$modificationDetail['groupId'], $modificationDetail['id']]);
	
} elseif (isset($_GET["getPermissions"]) && verifyUserPermission($_SESSION['user_id'], "vbcms", 'viewPermissions')){
	if(!empty($_GET["getPermissions"])){
		if(isJson(urldecode($_GET["getPermissions"]))){
			$requestDetails = json_decode($_GET["getPermissions"], true);

		}
	}
	$permissions = array();
	$activatedExtensions = $bdd->query("SELECT * FROM `vbcms-activatedExtensions`")->fetchAll(PDO::FETCH_ASSOC);

	// On va ajouter vbcms au d??but
	$vbcmsExt['name'] = 'vbcms';
	array_unshift($activatedExtensions, $vbcmsExt);
	foreach ($activatedExtensions as $activatedExtension){
		$permissions[$activatedExtension['name']] = array();

		if($activatedExtension['name'] == 'vbcms'){
			$extPermissions = getVBcmsPermissions();
		} else {
			$ext = new module($activatedExtension['name']);
			$extPermissions = $ext->getPermissions();
		}

		foreach ($extPermissions as $permission){
			$permissionDetail['name'] = $permission;
			if(isset($requestDetails['type'])&&isset($requestDetails['id'])){
				if($requestDetails['type']=="user"){
					$permissionDetail['access'] = verifyUserPermission($requestDetails['id'], $activatedExtension['name'], $permission);
				}elseif($requestDetails['type']=="group"){
					$permissionDetail['access'] = verifyGroupPermission($requestDetails['id'], $activatedExtension['name'], $permission);
				}
			}
			array_push($permissions[$activatedExtension['name']], $permissionDetail);
		}
	}
	echo json_encode($permissions);
	
} elseif (isset($_GET["editPermissions"])&&!empty($_GET["editPermissions"]) && verifyUserPermission($_SESSION['user_id'], "vbcms", 'editPermissions')) {
	if(isJson(urldecode($_GET["editPermissions"]))){
		$requestDetails = json_decode($_GET["editPermissions"], true);

		if($requestDetails['type'] == 'group' && $requestDetails['id']!=1){ // Le groupe n??1 ??tant celui des superadmins, ils auront tj tous les droits
			$query = $bdd->prepare('DELETE FROM `vbcms-groupsPerms` WHERE groupId=?'); // On vide les perms du groupe
			$query->execute([$requestDetails['id']]);
			foreach($_POST as $permissionJson => $checked) { // Puis on les recr????es
				$permissionDetail = json_decode(urldecode($permissionJson), true);
				$query = $bdd->prepare('INSERT INTO `vbcms-groupsPerms` (`groupId`, `extensionName`, `permission`) VALUES (?,?,?)');
				$query->execute([$requestDetails['id'], $permissionDetail['extension'], $permissionDetail['permission']]);
			}
		}elseif($requestDetails['type'] == 'user'){
			$query = $bdd->prepare('DELETE FROM `vbcms-usersPerms` WHERE userId=?'); // On vide les perms du groupe
			$query->execute([$requestDetails['id']]);
			foreach($_POST as $permissionJson => $checked) { // Puis on les recr????es
				$permissionDetail = json_decode(urldecode($permissionJson), true);
				$query = $bdd->prepare('INSERT INTO `vbcms-usersPerms` (`userId`, `extensionName`, `permission`) VALUES (?,?,?)');
				$query->execute([$requestDetails['id'], $permissionDetail['extension'], $permissionDetail['permission']]);
			}
		}else{
			echo 'Param??tre non reconnu.';
		}
		
	} else {
		echo translate('error').': '.translate('thisIsNotJSON');
	}
} elseif (isset($_GET["setLocalAccount"]) && (isset($_POST)&&!empty($_POST)) && verifyUserPermission($_SESSION['user_id'], "vbcms", 'manageUsersSettings')) {
	if($_GET["setLocalAccount"]!=0 || !empty($_GET["setLocalAccount"])){
		$localAccountExist = $bdd->prepare("SELECT * FROM `vbcms-localAccounts` WHERE userIdAssoc = ?");
		$localAccountExist->execute([$_GET["setLocalAccount"]]);
		$localAccountExist = $localAccountExist->fetch(PDO::FETCH_ASSOC);
		$accountId = $_GET["setLocalAccount"];
	}else{
		$accountId = $bdd->query("SELECT id FROM `vbcms-users` ORDER BY id DESC LIMIT 1;")->fetchColumn() + 1;
	}

	// Si on update un compte alors...
	if(isset($localAccountExist) && !empty($localAccountExist)){
		$modify = $bdd->prepare("UPDATE `vbcms-localAccounts` SET username = ?, password = ? WHERE userIdAssoc = ?");
		$modify->execute([$_POST['localUserUsername'], password_hash($_POST['localUserPassword1'], PASSWORD_DEFAULT), $accountId]);
	}else{
		// Si on en cr???? un...
		// default profil pic = VBcmsGetSetting("websiteUrl")."vbcms-admin/images/misc/programmer.png"
		$query = $bdd->prepare('INSERT INTO `vbcms-localAccounts` (`userIdAssoc`, `username`, `password`) VALUES (?,?,?)');
		$query->execute([$accountId, $_POST['localUserUsername'], password_hash($_POST['localUserPassword1'], PASSWORD_DEFAULT)]);

		$userGroup = $bdd->query("SELECT groupId FROM `vbcms-userGroups` WHERE groupName = 'users'")->fetch(PDO::FETCH_ASSOC);

		$query = $bdd->prepare('INSERT INTO `vbcms-users` (`id`, `auth`, `authId`, `username`, `groupId`) VALUES (NULL,?,?,?,?)');
		$query->execute(["vbcms", $accountId, $_POST['localUserUsername'], $userGroup["groupId"]]);

		// On renseigne les param??tres
		$insertSettings = $bdd->prepare("INSERT INTO `vbcms-usersSettings` (`userId`, `name`, `value`) VALUES (?,?,?)");
		$insertSettings->execute([$accountId, 'profilPic', VBcmsGetSetting("websiteUrl")."vbcms-admin/images/misc/programmer.png"]);
		$insertSettings = $bdd->prepare("INSERT INTO `vbcms-usersSettings` (`userId`, `name`, `value`) VALUES (?,?,?)");
		$insertSettings->execute([$accountId, 'joinedDate', date("Y-m-d H:i:s")]);
		$insertSettings = $bdd->prepare("INSERT INTO `vbcms-usersSettings` (`userId`, `name`, `value`) VALUES (?,?,?)");
		$insertSettings->execute([$accountId, 'language', "FR"]);
	}
	
} elseif(isset($_GET)&&!empty($_GET)){
	echo "Commande \"".array_key_first($_GET)."(".$_GET[array_key_first($_GET)].")\" non reconnue.";
} else {?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title><?=VBcmsGetSetting("websiteName")?> | T??ches de fond</title>
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
				<img src="<?=VBcmsGetSetting("websiteUrl")?>vbcms-admin/images/vbcms-logo/raccoon-512x.png">
				<h1 class="mt-5">T??ches de fond</h1>
				<p>Cette page n'est pas accessible en tant que telle. Seuls les addons peuvent communiquer avec. :D</p>
			</div>
		</div>
	</div>
</body>
</html>
<?php } ?>