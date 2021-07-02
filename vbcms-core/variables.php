<?php
// On récupère l'ip
if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    $ip = $_SERVER['HTTP_CLIENT_IP'];
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
    $ip = $_SERVER['REMOTE_ADDR'];
}

// Vérifie le type de connexion
if(isset($_SERVER['HTTPS'])) $http = "https"; else $http = "http";

// Variables permettant la gestion des pages à afficher
$url = parse_url("$http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");	
$paths = explode("/", $url["path"]);

// Variables propres à l'installation
$encryptionKey = $bdd->query("SELECT value FROM `vbcms-settings` WHERE name='encryptionKey'")->fetchColumn();
$serverId = $bdd->query("SELECT value FROM `vbcms-settings` WHERE name='serverId'")->fetchColumn();

// Variables pour le site
$websiteUrl = $bdd->query("SELECT value FROM `vbcms-settings` WHERE name='websiteUrl'")->fetchColumn();
$websiteName = $bdd->query("SELECT value FROM `vbcms-settings` WHERE name='websiteName'")->fetchColumn();
$websiteDescription = $bdd->query("SELECT value FROM `vbcms-settings` WHERE name='websiteDescription'")->fetchColumn();
$websiteMetaColor = $bdd->query("SELECT value FROM `vbcms-settings` WHERE name='websiteMetaColor'")->fetchColumn();
$websiteLogo = $bdd->query("SELECT value FROM `vbcms-settings` WHERE name='websiteLogo'")->fetchColumn();

$debugMode = $bdd->query("SELECT value FROM `vbcms-settings` WHERE name='debugMode'")->fetchColumn();