<?php
if (isset($_GET["updateVBcms"])&&!empty($_GET["updateVBcms"])) {
	// On va récupérer la clé pour être sûr que ce n'est pas une action mal intentionnée
	$updateKey=$bdd->query("SELECT * FROM `vbcms-settings` WHERE name = 'updateKey'")->fetch(PDO::FETCH_ASSOC);
	if($_GET["updateVBcms"]==$updateKey['value']){
		// On vérifie les mises à jour et on récupère les informations
		$newUpdateInfos = checkVBcmsUpdates();

		// On génère le nom du fichier de màj
		$updateFilename = $GLOBALS['vbcmsRootPath']."/vbcms-content/updates/".$newUpdateInfos['name'].".zip";
		if (!file_exists($GLOBALS['vbcmsRootPath']."/vbcms-content/updates")) mkdir($GLOBALS['vbcmsRootPath']."/vbcms-content/updates", 0755);
		//On créé le contexte
		$options  = array('http' => array('user_agent' => 'VBcms Updater'));
    	$context  = stream_context_create($options);

		// Puis on télécharge
		file_put_contents($updateFilename, file_get_contents($newUpdateInfos["zip"], true, $context));
		if (file_exists($updateFilename)) {
			$zip = new ZipArchive;
			if ($zip->open($updateFilename) === TRUE) {
				$updateFolder = $GLOBALS['vbcmsRootPath']."/vbcms-content/updates/".$newUpdateInfos['name'];
				$zip->extractTo($updateFolder);
				$zip->close();

				// On vérifie si la mise à jour est dans le dossier racine
				if(file_exists($updateFolder."/index.php")){
					recursive_copy_if_different($updateFolder, $GLOBALS['vbcmsRootPath']);
				}else{
					// La mise a jour est peut-être dans un sous-dossier, on va vérifier
					$subfolder = scandir($updateFolder);
					$foundFolder = false;
					foreach($subfolder as $file){
						if (( $file != '.' ) && ( $file != '..' )){
							if(file_exists($updateFolder."/".$file."/index.php?update")){
								$foundFolder = true;
								$updateFolder = $updateFolder."/".$file; // À ce compte là il ne faut pas qu'il y ai 2 dossiers avec des index.php
							}
						}
					}
					if($foundFolder) recursive_copy_if_different($updateFolder, $GLOBALS['vbcmsRootPath']);
				}
				
				$response["success"] = true;
				if(file_exists($GLOBALS['vbcmsRootPath']."/install.php"))
					$response["link"] = VBcmsGetSetting("websiteUrl")."install.php";
				else
					$response["link"] = VBcmsGetSetting("websiteUrl")."vbcms-admin";
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