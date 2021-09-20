<?php
// Ici on est sur le script des tâches automatisées


// On check les mises à jour
$datetime = new DateTime(date("Y-m-d H:i:s"));

$lastUpdateCheck = $bdd->query("SELECT value FROM `vbcms-settings` WHERE name = 'lastUpdateCheck'")->fetchColumn();
$lastUpdateCheck = DateTime::createFromFormat('Y-m-d H:i:s', $lastUpdateCheck);
if ((abs($datetime->getTimestamp()-$lastUpdateCheck->getTimestamp())) > 1800){
    checkVBcmsUpdates();
}