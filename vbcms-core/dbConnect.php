<?php
require_once $GLOBALS['vbcmsRootPath']."/vbcms-config.php";
try {
    $bddConn = new PDO("mysql:host=$bddHost;dbname=$bddName", $bddUser, $bddMdp); //Test de la connexion
    $bddConn = null;
} catch (PDOException $e) { 
	$bddError = true;
	die();
}
if (!isset($bddError)){
	$bdd = new PDO("mysql:host=$bddHost;dbname=$bddName", $bddUser, $bddMdp);
	$bdd->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING ); // Debug
}