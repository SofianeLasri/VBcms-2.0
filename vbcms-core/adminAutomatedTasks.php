<?php
// Ici on est sur le script des tâches automatisées


// On check les mises à jour
$datetime = new DateTime(date("Y-m-d H:i:s"));

$lastUpdateCheck = $bdd->query("SELECT value FROM `vbcms-settings` WHERE name = 'lastUpdateCheck'")->fetchColumn();
$lastUpdateCheck = DateTime::createFromFormat('Y-m-d H:i:s', $lastUpdateCheck);
if ((abs($datetime->getTimestamp()-$lastUpdateCheck->getTimestamp())) > 1800){
    $serverId = $bdd->query("SELECT value FROM `vbcms-settings` WHERE name='serverId'")->fetchColumn();
    $key = $bdd->query("SELECT value FROM `vbcms-settings` WHERE name='encryptionKey'")->fetchColumn();
    $vbcmsVer = $bdd->query("SELECT value FROM `vbcms-settings` WHERE name='vbcmsVersion'")->fetchColumn();
    $curentUpdateCanal = $bdd->query("SELECT value FROM `vbcms-settings` WHERE name='updateCanal'")->fetchColumn();
    
    $json = file_get_contents("https://api.vbcms.net/updater/lastest?serverId=".$serverId."&key=".$key."&version=".$vbcmsVer."&canal=".$curentUpdateCanal);
    $jsonData = json_decode($json, true);

    if (!empty($jsonData) && !$jsonData["upToDate"]) {
        $response = $bdd->query("UPDATE `vbcms-settings` SET `value` = 0 WHERE `vbcms-settings`.`name` = 'upToDate'");

        $response = $bdd->query("SELECT COUNT(*) FROM `vbcms-notifications` WHERE origin = '[\"vbcms-updater\", \"notifyUpdate\"]'")->fetchColumn();
        if ($response!=1) {
            $response = $bdd->prepare("INSERT INTO `vbcms-notifications` (`id`, `origin`, `link`, `content`, `dismissible`, `date`, `userId`) VALUES (NULL, '[\"vbcms-updater\", \"notifyUpdate\"]', '/vbcms-admin/updater\"', ?, '0', ?, 0)");
            $response->execute([translate("isNotUpToDate"), date("Y-m-d H:i:s")]);
        }
    }
    $response = $bdd->prepare("UPDATE `vbcms-settings` SET `value` = ? WHERE `vbcms-settings`.`name` = 'lastUpdateCheck'");
    $response->execute([date("Y-m-d H:i:s")]);
}