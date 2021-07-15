<?php
// Ce qu'on fait ici c'est récupérer le dossier racine de VBcms
// On pourra récupérer cette variable sur l'ensemble du cms

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

// On inclue le fichier responsable de la session utilisateur
// L'API de la pre 2.0 est encore UP, mais il faudra revoir ce fichier dès que la pre 2.1 sera terminée
require_once 'sessionHandler.php';

// Maintenant on va gérer l'affichage des pages selon l'url
if($urlPath[1]=="vbcms-admin"){
	// L'utilisateur est sur le panel admin du cms
	require_once "adminHandler.php";
	// On va executer les tâches de fond du panel admin
	require_once "adminAutomatedTasks.php";
}else{
	// L'utilisateur est sur la partie publique du cms
	require_once "clientHandler.php";
}