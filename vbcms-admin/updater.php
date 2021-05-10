<?php
$curentUpdateCanal = $bdd->query("SELECT value FROM `vbcms-settings` WHERE name='updateCanal'")->fetchColumn();
$serverId = $bdd->query("SELECT value FROM `vbcms-settings` WHERE name='serverId'")->fetchColumn();
$key = $bdd->query("SELECT value FROM `vbcms-settings` WHERE name='encryptionKey'")->fetchColumn();
$vbcmsVer = $bdd->query("SELECT value FROM `vbcms-settings` WHERE name='vbcmsVersion'")->fetchColumn();
$curentUpdateCanal = $bdd->query("SELECT value FROM `vbcms-settings` WHERE name='updateCanal'")->fetchColumn();

$updateInfos = file_get_contents("https://api.vbcms.net/updater/lastest?serverId=".$serverId."&key=".$key."&version=".$vbcmsVer."&canal=".$curentUpdateCanal);
$updateInfosData = json_decode($updateInfos, true);

if (!$updateInfosData["upToDate"]) {
    $response = $bdd->query("UPDATE `vbcms-settings` SET `value` = 0 WHERE `vbcms-settings`.`name` = 'upToDate'");

    $response = $bdd->query("SELECT COUNT(*) FROM `vbcms-notifications` WHERE origin = '[\"vbcms-updater\", \"notifyUpdate\"]'")->fetchColumn();
    if ($response!=1) {
        $response = $bdd->prepare("INSERT INTO `vbcms-notifications` (`id`, `origin`, `link`, `content`, `removable`, `date`, `userId`) VALUES (NULL, '[\"vbcms-updater\", \"notifyUpdate\"]', '/vbcms-admin/updater\"', ?, '0', ?, 0)");
        $response->execute([$translation["isNotUpToDate"], date("Y-m-d H:i:s")]);
    }
} else{
    $response = $bdd->query("UPDATE `vbcms-settings` SET `value` = 1 WHERE `vbcms-settings`.`name` = 'upToDate'");
    $bdd->query("DELETE FROM `vbcms-notifications` WHERE origin = '[\"vbcms-updater\", \"notifyUpdate\"]'");
} 
$response = $bdd->prepare("UPDATE `vbcms-settings` SET `value` = ? WHERE `vbcms-settings`.`name` = 'lastUpdateCheck'");
$response->execute([date("Y-m-d H:i:s")]);

$isUpToDate = $bdd->query("SELECT value FROM `vbcms-settings` WHERE name = 'upToDate'")->fetchColumn();
$lastUpdateCheck = $bdd->query("SELECT value FROM `vbcms-settings` WHERE name = 'lastUpdateCheck'")->fetchColumn();
if ($isUpToDate == 1) {
    $updateMessage = $translation["isUpToDate"];
} else {
    $updateMessage = $translation["isNotUpToDate"];
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?=$websiteName?> | <?=$translation["update"]?></title>
    <?php include 'includes/depedencies.php';?>
</head>
<body>
    <?php 
    include ('includes/navbar.php');
    ?>

    <!-- Contenu -->
    <div class="dashboardTopCard" leftSidebar="240" rightSidebar="0">
        <h3><?=$translation["updateVBcms"]?></h3>
        <div class="d-flex mt-5">
            <div class="vbcms-logo">
                <img src="<?=$websiteUrl?>vbcms-admin/images/vbcms-logo/raccoon-in-box-512x.png">
            </div>
            <div class="ml-5">
                <h4>VBcms <small><?=$vbcmsVer?></small></h4>
                <p><strong><?=$updateMessage?></strong><br>
                    <?=$translation["lastChecked"]?>: <?=$lastUpdateCheck?></p>

                <?php
                    if ($isUpToDate == 1) {
                        #
                    } else {
                        echo '<p>Test</p>';
                        echo '<button type="button" class="btn btn-light">'.$translation["downloadAndInstall"].'</button>';
                    }
                    
                ?>
            </div>
        </div>
    </div>

    <div class="page-content notTop" leftSidebar="240" rightSidebar="0">
        <h3><?=$translation["update"]?></h3>

    </div>
</body>
</html>