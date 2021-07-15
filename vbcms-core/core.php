<?php
// Ce qu'on fait ici c'est récupérer le dossier racine de VBcms
// Sur la pre2.0 toute la partie logique se passait dans /vbcms-admin
// Ce raisonnement était donc logique car toujours valable.

// Il faudra en revanche, voir si cela fonctionnera toujours avec la nouvelle architecture.
if(!isset($vbcmsRootPath)){
	$vbcmsRootPath = getcwd();
	if(strpos($vbcmsRootPath, "/vbcms-core") !== false){
	    $vbcmsRootPath = substr($vbcmsRootPath, 0, strpos($vbcmsRootPath, "/vbcms-core"));
	}elseif(strpos($vbcmsRootPath, "/vbcms-admin") !== false){
	    $vbcmsRootPath = substr($vbcmsRootPath, 0, strpos($vbcmsRootPath, "/vbcms-admin"));
	}elseif(strpos($vbcmsRootPath, "/vbcms-content") !== false){
	    $vbcmsRootPath = substr($vbcmsRootPath, 0, strpos($vbcmsRootPath, "/vbcms-content"));
	}
}
// On utilisera $GLOBALS['vbcmsRootPath']

// Connexion à la base de donnée
require_once 'dbConnect.php';

// On inclue le fichier des variables (constantes ou non)
require_once 'variables.php';

// On inclue le fichier des fonctions
require_once 'functions.php';

// On inclue le fichier des classes
require_once 'classes.php';

// On inclue les classes et fonctions du Namespace VBcms
//require_once 'ns-VBcms.php';

// On inclue le fichier responsable de la session utilisateur
require_once 'sessionHandler.php';

/*
// Switch pour la langue
if(!isset($_SESSION["language"])){
	$geoPlugin_array = unserialize( file_get_contents('http://www.geoplugin.net/php.gp?ip=' . $_SERVER['REMOTE_ADDR']) );
	$language = $geoPlugin_array['geoplugin_countryCode'];
} else $language = $_SESSION["language"];
switch ($language) {
    case "FR":
        require_once $GLOBALS['vbcmsRootPath'].'/vbcms-content/translations/FR.php';
        break;
    case "EN":
        require_once $GLOBALS['vbcmsRootPath'].'/vbcms-content/translations/EN.php';
        break;
    default:
    	require_once $GLOBALS['vbcmsRootPath'].'/vbcms-content/translations/FR.php';
        break;
}
*/

// Maintenant on va gérer l'affichage des pages selon l'url
if($paths[1]=="vbcms-admin"){
	// L'utilisateur est sur le panel admin du cms
	require_once "adminHandler.php";
	// On va executer les tâches de fond du panel admin
	require_once "adminAutomatedTasks.php";
}else{
	// L'utilisateur est sur la partie publique du cms
	require_once "clientHandler.php";
}