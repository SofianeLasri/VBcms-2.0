<?php
if(!isset($vbcmsRootPath)){
	$vbcmsRootPath = getcwd();
	if(strpos($vbcmsRootPath, "/vbcms-admin") !== false){
	    $vbcmsRootPath = substr($vbcmsRootPath, 0, strpos($vbcmsRootPath, "/vbcms-admin"));
	}
}

require 'dbConnect.php'; // Primordiale pour l'usage de la base de donnée

// Vérifie le type de connexion
if(isset($_SERVER['HTTPS'])) $http = "https"; else $http = "http";

// Et dans quel dossier principal on se trouve ($folders[1])
$url = parse_url("$http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");	
$folders = explode("/", $url["path"]);

// On récupère l'ip
if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    $ip = $_SERVER['HTTP_CLIENT_IP'];
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
    $ip = $_SERVER['REMOTE_ADDR'];
}

// Concentre toutes les constantes/variables du site
require 'constants.php';

// Se charge de la session
require 'session.php'; // Permet l'usage des sessions

// Concentre toutes les fonctions du site
require 'functions.php';

// Switch pour la langue
// Doit être placé avant l'inclusion des pages
if(!isset($_SESSION["language"])){
	$geoPlugin_array = unserialize( file_get_contents('http://www.geoplugin.net/php.gp?ip=' . $_SERVER['REMOTE_ADDR']) );
	$language = $geoPlugin_array['geoplugin_countryCode'];
} else $language = $_SESSION["language"];
switch ($language) {
    case "FR":
        include $GLOBALS['vbcmsRootPath'].'/vbcms-content/translations/FR.php';
        break;
    case "EN":
        include $GLOBALS['vbcmsRootPath'].'/vbcms-content/translations/FR.php';
        break;
    default:
    	include $GLOBALS['vbcmsRootPath'].'/vbcms-content/translations/FR.php';
        break;
}

if ($folders[1]=="vbcms-admin") {// Ne s'éxecute que si l'on n'est sur le panneau admin
	if (!isset($_SESSION["user_id"])) { // Si l'utilisateur n'est pas connecté
		if (basename($_SERVER['PHP_SELF'])!="login.php" && !isset($jsonData) && !isset($jsonData->error)) { // Évite les boucles de redirection
			header("Location: https://vbcms.net/manager/login?from=".urlencode("$http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']));
		}
		
	} else {
		if (!in_array($_SESSION["user_role"], ["owner", "admin", "moderator"])){
			if ($_SERVER['HTTP_HOST'] != "vbcms.net") {
				session_destroy(); // Évite les boucles de redirection
			}
			header("Location: https://vbcms.net/manager/?error=".urlencode("Tu n'as pas le droit de te connecter à ce site <img height=\"16\" src=\"https://dev.vbcms.net/vbcms-content/uploads/emoji/oiseau-pas-content.png\">"));
		}

		// On inclu les scripts de fond des modules activés
		$response = $bdd->query("SELECT * FROM `vbcms-modules` WHERE activated=1"); // Je récupère l'id du dossier parent
	    $response = $response->fetchAll(PDO::FETCH_ASSOC);
	    if (!empty($response)) {
	    	foreach ($response as $module) {
	    		if(file_exists($GLOBALS['vbcmsRootPath'].'/vbcms-content/modules'.$module["path"]."/back.php"))
	    			include $GLOBALS['vbcmsRootPath'].'/vbcms-content/modules'.$module["path"]."/back.php"; //module.php a été remplacé par divers fichiers représentant les fonctions
	    	}
	    }

	    // On inclu les pages

		include 'adminPagesAssoc.php';
		if (array_key_exists($folders[2], $adminPagesAssoc)) {
			if($adminPagesAssoc[$folders[2]]!="")
				include $GLOBALS['vbcmsRootPath']."/vbcms-admin/".$adminPagesAssoc[$folders[2]]; // Charge la page admin
		} else {
			$moduleParams = array();
			for ($i=2; $i<count($folders); $i++) { 
				array_push($moduleParams, $folders[$i]);
			}
			loadModule("admin", $folders[2], $moduleParams); // Charge le module admin
		}

		// On check les mises à jour
		$lastUpdateCheck = $bdd->query("SELECT value FROM `vbcms-settings` WHERE name = 'lastUpdateCheck'")->fetchColumn();
		$lastUpdateCheck = DateTime::createFromFormat('Y-m-d H:i:s', $lastUpdateCheck);
		if ((abs($datetime->getTimestamp()-$lastUpdateCheck->getTimestamp())) > 1800){
			$serverId = $bdd->query("SELECT value FROM `vbcms-settings` WHERE name='serverId'")->fetchColumn();
			$key = $bdd->query("SELECT value FROM `vbcms-settings` WHERE name='encryptionKey'")->fetchColumn();
			$vbcmsVer = $bdd->query("SELECT value FROM `vbcms-settings` WHERE name='vbcmsVersion'")->fetchColumn();
			$curentUpdateCanal = $bdd->query("SELECT value FROM `vbcms-settings` WHERE name='updateCanal'")->fetchColumn();
			
			$json = file_get_contents("https://api.vbcms.net/updater/lastest?serverId=".$serverId."&key=".$key."&version=".$vbcmsVer."&canal=".$curentUpdateCanal);
			$jsonData = json_decode($json, true);

			if (!$jsonData["upToDate"]) {
				$response = $bdd->query("UPDATE `vbcms-settings` SET `value` = 0 WHERE `vbcms-settings`.`name` = 'upToDate'");

				$response = $bdd->query("SELECT COUNT(*) FROM `vbcms-notifications` WHERE origin = '[\"vbcms-updater\", \"notifyUpdate\"]'")->fetchColumn();
				if ($response!=1) {
					$response = $bdd->prepare("INSERT INTO `vbcms-notifications` (`id`, `origin`, `link`, `content`, `removable`, `date`, `userId`) VALUES (NULL, '[\"vbcms-updater\", \"notifyUpdate\"]', '/vbcms-admin/updater\"', ?, '0', ?, 0)");
					$response->execute([$translation["isNotUpToDate"], date("Y-m-d H:i:s")]);
				}
			}
			$response = $bdd->prepare("UPDATE `vbcms-settings` SET `value` = ? WHERE `vbcms-settings`.`name` = 'lastUpdateCheck'");
			$response->execute([date("Y-m-d H:i:s")]);
			
		}
		
	}
} else { // Ne s'éxecute que si l'on n'est PAS sur le panneau admin (donc la partie publique)

	if ($folders[1]!="vbcms-content") { // Permet aux scripts de communiquer tout en utilisant header
		if($folders[1]=="backTasks"){
			include $GLOBALS['vbcmsRootPath']."/backTasks.php";
		} else {
			// Je récupère l'ip du client pour les stats
			$response = $bdd->prepare("SELECT * FROM `vbcms-websiteStats` WHERE date LIKE ? AND ip = ?");
			$response->execute(['%'.date("Y-m-d").'%', $ip]);
			$response = $bdd->prepare("INSERT INTO `vbcms-websiteStats` (id, date, page, ip) VALUES (?,?,?,?)");
			$response->execute([null, date("Y-m-d H:i:s"), $_SERVER['REQUEST_URI'], $ip]);
			

			$moduleParams = array();
			for ($i=2; $i<count($folders); $i++) { 
				array_push($moduleParams, $folders[$i]);
			}
			loadModule("client", $folders[1], $moduleParams);


			
		}
	}

}

?>