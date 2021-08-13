<?php
if (isset($_GET["netAccess"]) && !empty($_GET["netAccess"])) {
	$decryption_iv = '1106737252181743';
	$ciphering = "AES-128-CTR";
	$iv_length = openssl_cipher_iv_length($ciphering);
	$options = 0;
    $decryption_key = VBcmsGetSetting('encryptionKey');
    $instructions= openssl_decrypt($_GET["netAccess"], $ciphering,  $decryption_key, $options, $decryption_iv);

    // On a réussi la connexion à distance, on va créer une session superadmin

    if (isJson($instructions)) {
    	$instructions = json_decode($instructions, true);
    	switch ($instructions["command"]) {
    		case 'getVersionInfo':
    			echo VBcmsGetSetting('vbcmsVersion');
    			break;

            case 'autoUpdate':
                $autoUpdate = $bdd->query("SELECT value FROM `vbcms-settings` WHERE name = 'autoUpdate'")->fetchColumn();
                if ($autoUpdate=="1") {
                    $updateState = json_decode(file_get_contents(VBcmsGetSetting("websiteUrl")."backTasks/?updateVBcms"), true);
                    if ($updateState["success"]==true) {
                        file_get_contents(VBcmsGetSetting("websiteUrl")."update.php?silentUpdate");
                        $result["result"] = "success";
                        echo json_encode($result);
                    } else {
                        $result["result"] = "error";
                        $result["code"] = $updateState["code"];
                        $result["message"] = $updateState["error"];
                        echo json_encode($result);
                    }
                    
                } else {
                   echo "Auto update is not enabled";
                }
                break;
            
            case 'criticalUpdate':
                $response=$bdd->prepare("UPDATE `vbcms-settings` SET value = ? WHERE name = 'updateCanal'");
                $response->execute(["release"]);

                $updateState = json_decode(file_get_contents(VBcmsGetSetting("websiteUrl")."backTasks/?updateVBcms"), true);
                if ($updateState["success"]==true) {
                    file_get_contents(VBcmsGetSetting("websiteUrl")."update.php?silentUpdate");
                    $result["result"] = "success";
                    echo json_encode($result);
                } else {
                    $result["result"] = "error";
                    $result["code"] = $updateState["code"];
                    $result["message"] = $updateState["error"];
                    echo json_encode($result);
                } 
                
                break;

    		default:
    			echo "unrecognized command";
    			break;
    	}
    }
} elseif (isset($_GET["updateVBcms"])) {
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
	}
    
} 