<?php
if (isset($_GET["updateVBcms"])&&!empty($_GET["updateVBcms"])) {
	$updateKey=$bdd->query("SELECT * FROM `vbcms-settings` WHERE name = 'updateKey'")->fetch(PDO::FETCH_ASSOC);
	if($_GET["updateVBcms"]==$updateKey['value']){
		$newUpdateInfos = checkVBcmsUpdates();

		$updateFilename = $GLOBALS['vbcmsRootPath']."/vbcms-content/updates/".basename($newUpdateInfos['zip']);
		if (!file_exists($GLOBALS['vbcmsRootPath']."/vbcms-content/updates")) mkdir($GLOBALS['vbcmsRootPath']."/vbcms-content/updates", 0755);
		//echo $updateInfosData["downloadLink"]."?serverId=".VBcmsGetSetting("serverId")."&key=".$key;
		$options  = array('http' => array('user_agent' => 'VBcms Updater'));
    	$context  = stream_context_create($options);

		file_put_contents($updateFilename, file_get_contents($newUpdateInfos["zip"], true, $context));
		if (file_exists($updateFilename)) {
			$zip = new ZipArchive;
			if ($zip->open($updateFilename) === TRUE) {
				$zip->extractTo($GLOBALS['vbcmsRootPath']);
				$zip->close();
	
				$response["success"] = true;
				$response["link"] = VBcmsGetSetting("websiteUrl")."update.php";
			} else {
				$response["success"] = false;
				$response["code"] = "CANT_OPEN_ARCHIVE"; // Impossible d'ouvrir l'archive
			}
		} else {
			$response["success"] = false;
			$response["code"] = "CANT_DOWNLOAD_UPDATE"; // Impossible de télécharger la màj
		}
	}else{
		$response["success"] = false;
		$response["code"] = "WRONG_CODE"; // Impossible d'ouvrir l'archive
	}
	echo json_encode($response);
	/*
	$updateInfos = file_get_contents("https://api.vbcms.net/updater/lastest?serverId=".VBcmsGetSetting("serverId")."&key=".VBcmsGetSetting('encryptionKey')."&version=".VBcmsGetSetting('vbcmsVersion')."&canal=".VBcmsGetSetting('updateCanal'));
	if(isJson($updateInfos)){
		$updateInfosData = json_decode($updateInfos, true);

		$updateFilename = $GLOBALS['vbcmsRootPath']."/vbcms-content/updates/vbcms-update-v".$updateInfosData['version']."_from-".$vbcmsVer.".zip";
		if (!file_exists($GLOBALS['vbcmsRootPath']."/vbcms-content/updates")) mkdir($GLOBALS['vbcmsRootPath']."/vbcms-content/updates", 0755);
		//echo $updateInfosData["downloadLink"]."?serverId=".VBcmsGetSetting("serverId")."&key=".$key;
		file_put_contents($updateFilename, file_get_contents($updateInfosData["downloadLink"]."?serverId=".VBcmsGetSetting("serverId")."&key=".$key));
		if (file_exists($updateFilename)) {
			$zip = new ZipArchive;
			if ($zip->open($updateFilename) === TRUE) {
				$zip->extractTo($GLOBALS['vbcmsRootPath']);
				$zip->close();
	
				$response["success"] = true;
				$response["link"] = VBcmsGetSetting("websiteUrl")."update.php";
			} else {
				$response["success"] = false;
				$response["code"] = 2; // Impossible d'ouvrir l'archive
			}
		} else {
			$response["success"] = false;
			$response["code"] = 1; // Impossible de télécharger la màj
		}
		echo json_encode($response);
	} else {
		$response["success"] = false;
		$response["code"] = 0; // Impossible de lire la réponse -> !JSON
	}*/
    
} 