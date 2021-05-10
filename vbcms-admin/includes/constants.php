<?php
// Variables ne nécessitant pas d'être connectés
$websiteUrl = $bdd->query("SELECT value FROM `vbcms-settings` WHERE name='websiteUrl'")->fetchColumn();
$websiteName = $bdd->query("SELECT value FROM `vbcms-settings` WHERE name='websiteName'")->fetchColumn();
$websiteDescription = $bdd->query("SELECT value FROM `vbcms-settings` WHERE name='websiteDescription'")->fetchColumn();
$websiteMetaColor = $bdd->query("SELECT value FROM `vbcms-settings` WHERE name='websiteMetaColor'")->fetchColumn();
$websiteLogo = $bdd->query("SELECT value FROM `vbcms-settings` WHERE name='websiteLogo'")->fetchColumn();

$uploadFolderPath = $_SERVER['DOCUMENT_ROOT']."/vbcms-content/uploads/"; // Hérité du manager de vbcms.net -> cette variable était utilisée dans le fichier de config
$datetime = new DateTime(date("Y-m-d H:i:s"));