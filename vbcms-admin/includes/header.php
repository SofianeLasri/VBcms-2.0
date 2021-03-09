<?php
require 'dbConnect.php'; // Primordiale pour l'usage de la base de donnée

// Vérifie le type de connexion
if(isset($_SERVER['HTTPS'])) $http = "https"; else $http = "http";

// Et dans quel dossier principal on se trouve ($folders[1])
$url = parse_url("$http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");	
$folders = explode("/", $url["path"]);

// Se charge de la session
require 'session.php'; // Permet l'usage des sessions

// Concentre toutes les constantes/variables du site
require 'constants.php';

if ($folders[1]=="vbcms-admin") {// Ne s'éxecute que si l'on n'est sur le panneau admin
	if (!isset($_SESSION["user_id"])) { // Si l'utilisateur n'est pas connecté
		if (basename($_SERVER['PHP_SELF'])!="login.php") { // Évite les boucles de redirection
			header("Location: $http://$_SERVER[HTTP_HOST]/vbcms-admin/login.php?from=".urlencode("$http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']));
		}
		
	} else {
		// Switch pour la langue
		// Doit être placé avant l'inclusion des pages
		switch ($_SESSION["language"]) {
		    case "FR":
		        include $_SERVER['DOCUMENT_ROOT'].'/vbcms-content/translations/FR.php';
		        break;
		    case "EN":
		        include $_SERVER['DOCUMENT_ROOT'].'/vbcms-content/translations/FR.php';
		        break;
		    default:
		    	include $_SERVER['DOCUMENT_ROOT'].'/vbcms-content/translations/FR.php';
		        break;
		}

		// On inclu les scripts de fond des modules activés
		$response = $bdd->query("SELECT * FROM `vbcms-modules` WHERE activated=1"); // Je récupère l'id du dossier parent
	    $response = $response->fetchAll(PDO::FETCH_ASSOC);
	    if (!empty($response)) {
	    	foreach ($response as $module) {
	    		include $_SERVER['DOCUMENT_ROOT'].'/vbcms-content/modules'.$module["path"]."/back.php"; //module.php a été remplacé par divers fichiers représentant les fonctions
	    	}
	    }

	    // On inclu les pages

		include 'adminPagesAssoc.php';
		if (array_key_exists($folders[2], $adminPagesAssoc)) {
			if($adminPagesAssoc[$folders[2]]!="")
				include $_SERVER['DOCUMENT_ROOT']."/vbcms-admin/".$adminPagesAssoc[$folders[2]]; // Charge la page admin
		} else {
			$moduleParams = array();
			for ($i=2; $i<count($folders); $i++) { 
				array_push($moduleParams, $folders[$i]);
			}
			loadModule("admin", $folders[2], $moduleParams); // Charge le module admin
		}
		
	}
} else { // Ne s'éxecute que si l'on n'est PAS sur le panneau admin (donc la partie publique)
	if ($folders[1]!="vbcms-content") { // Permet aux scripts de communiquer tout en utilisant header
		$moduleParams = array();
		for ($i=2; $i<count($folders); $i++) { 
			array_push($moduleParams, $folders[$i]);
		}
		loadModule("client", $folders[1], $moduleParams);
	}

}

?>