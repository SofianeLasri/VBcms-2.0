<?php
if (isset($_GET["loadClientNavbar"])) {
	echo loadClientNavbar($_GET["loadClientNavbar"]);
} elseif (isset($_GET["loadLastNavItem"])) {
	echo loadLastNavItem($_GET["loadLastNavItem"]);
} elseif (isset($_GET["netAccess"]) && !empty($_GET["netAccess"])) {
	$decryption_iv = '1106737252181743';
	$ciphering = "AES-128-CTR";
	$iv_length = openssl_cipher_iv_length($ciphering);
	$options = 0;
    $decryption_key = $bdd->query("SELECT value FROM `vbcms-settings` WHERE name = 'encryptionKey'")->fetchColumn();
    $instructions= openssl_decrypt($_GET["netAccess"], $ciphering,  $decryption_key, $options, $decryption_iv);
    if (isJson($instructions)) {
    	$instructions = json_decode($instructions, true);
    	switch ($instructions["command"]) {
    		case 'getVersionInfo':
    			echo $bdd->query("SELECT value FROM `vbcms-settings` WHERE name = 'vbcmsVersion'")->fetchColumn();
    			break;

            case 'autoUpdate':
                $autoUpdate = $bdd->query("SELECT value FROM `vbcms-settings` WHERE name = 'autoUpdate'")->fetchColumn();
                if ($autoUpdate=="1") {
                    $updateState = json_decode(file_get_contents($websiteUrl."vbcms-admin/backTasks/updateVBcms"), true);
                    if ($updateState["success"]==true) {
                        file_get_contents($websiteUrl."update.php");
                    } else {
                        echo "Update failed with code: ".$updateState["code"];
                    }
                    
                } else {
                   echo "Auto update is not enabled";
                }
                break;
            
            case 'criticalUpdate':
                $response=$bdd->prepare("UPDATE `vbcms-settings` SET value = ? WHERE name = 'updateCanal'");
                $response->execute(["release"]);

                $updateState = json_decode(file_get_contents($websiteUrl."vbcms-admin/backTasks/updateVBcms"), true);
                if ($updateState["success"]==true) {
                    file_get_contents($websiteUrl."update.php");
                } else {
                    echo "Update failed with code: ".$updateState["code"];
                }   
                
                break;

    		default:
    			echo "unrecognized command";
    			break;
    	}
    }
    
} else {?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title><?=$websiteName?> | Tâches de fond</title>
</head>
<body>

</body>
</html>
<?php } ?>