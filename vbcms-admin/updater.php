<?php
$curentUpdateCanal = VBcmsGetSetting('updateCanal');
$serverId = VBcmsGetSetting('serverId');
$key = VBcmsGetSetting('encryptionKey');
$vbcmsVer = VBcmsGetSetting('vbcmsVersion');
$curentUpdateCanal = VBcmsGetSetting('updateCanal');

$updateInfos = file_get_contents("https://api.vbcms.net/updater/lastest?serverId=".$serverId."&key=".$key."&version=".$vbcmsVer."&canal=".$curentUpdateCanal);
if(isJson($updateInfos)){
    $updateInfosData = json_decode($updateInfos, true);
    if (!$updateInfosData["upToDate"]) {
        $response = $bdd->query("UPDATE `vbcms-settings` SET `value` = 0 WHERE `vbcms-settings`.`name` = 'upToDate'");
    
        $response = $bdd->query("SELECT COUNT(*) FROM `vbcms-notifications` WHERE origin = '[\"vbcms-updater\", \"notifyUpdate\"]'")->fetchColumn();
        if ($response!=1) {
            $response = $bdd->prepare("INSERT INTO `vbcms-notifications` (`id`, `origin`, `link`, `content`, `removable`, `date`, `userId`) VALUES (NULL, '[\"vbcms-updater\", \"notifyUpdate\"]', '/vbcms-admin/updater\"', ?, '0', ?, 0)");
            $response->execute([translate("isNotUpToDate"), date("Y-m-d H:i:s")]);
        }
    } else{
        $response = $bdd->query("UPDATE `vbcms-settings` SET `value` = 1 WHERE `vbcms-settings`.`name` = 'upToDate'");
        $bdd->query("DELETE FROM `vbcms-notifications` WHERE origin = '[\"vbcms-updater\", \"notifyUpdate\"]'");
    } 
} else {
    $error = "Impossible de vérifier les mises à jour:".$updateInfos;
    $updateInfosData['version'] = translate("unknownF");
}

$response = $bdd->prepare("UPDATE `vbcms-settings` SET `value` = ? WHERE `vbcms-settings`.`name` = 'lastUpdateCheck'");
$response->execute([date("Y-m-d H:i:s")]);

$isUpToDate = $bdd->query("SELECT value FROM `vbcms-settings` WHERE name = 'upToDate'")->fetchColumn();
$lastUpdateCheck = $bdd->query("SELECT value FROM `vbcms-settings` WHERE name = 'lastUpdateCheck'")->fetchColumn();
if ($isUpToDate == 1) {
    $updateMessage = translate("isUpToDate");
    $textColor = "success";
} else {
    $updateMessage = translate("isNotUpToDate");
    $textColor = "danger";
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?=VBcmsGetSetting("websiteName")?> | <?=translate("update")?></title>
    <?php include 'includes/depedencies.php';?>
</head>
<body>
    <?php 
    include ('includes/navbar.php');
    ?>

    <!-- Contenu -->
    <div class="dashboardTopCard" leftSidebar="240" rightSidebar="0">
        <h3><?=translate("updateVBcms")?></h3>
        <div class="d-flex mt-5">
            <div class="vbcms-logo">
                <img src="<?=VBcmsGetSetting("websiteUrl")?>vbcms-admin/images/vbcms-logo/raccoon-in-box-512x.png">
            </div>
            <div class="ml-5">
                <h4>VBcms <small><?=$vbcmsVer?></small></h4>
                <p><strong><?=$updateMessage?></strong><br>
                    <?=translate("lastChecked")?>: <?=$lastUpdateCheck?></p>

                <?php
                    if ($isUpToDate == 1) {
                        #
                    } else {
                        echo '<p>Test</p>';
                        echo '<button type="button" onclick="$(\'#updateModal\').modal(\'toggle\');" class="btn btn-light">'.translate("downloadAndInstall").'</button>';
                    }
                    
                ?>
            </div>
        </div>
    </div>

    <div class="page-content notTop" leftSidebar="240" rightSidebar="0">
        <div class="row">
            <div class="col">
                <h5>Détail de la mise à jour</h5>
                <p><span class="text-muted">Installée: </span><span class="text-<?=$textColor?>"><?=$vbcmsVer?></span>
                <?php
                if ($isUpToDate == 0) echo '<br><span class="text-muted">Disponible: </span><span class="text-success">'.$updateInfosData["version"].'</span>';
                ?>
                <br><span class="text-muted">Canal de mise à jour: </span>
                <?php
                if ($curentUpdateCanal == "release") {
                    echo '<span class="text-success">Release</span>';
                } elseif ($curentUpdateCanal == "dev") {
                    echo '<span class="text-danger">Développement</span>';
                } elseif ($curentUpdateCanal == "nightly") {
                    echo '<span class="text-warning">Bêta</span>';
                }
                
                ?></p>


            </div>
            <div class="col-8"">
                <h4>Détail de la mise à jour</h4>
            </div>
            <div class="col">
                <h5>Obtenir de l'aide</h5>
                <a class="text-dark" target="_blank" href="https://vbcms.net/doc"><i class="fas fa-book"></i> Documentation</a><br>
                <a class="text-dark" target="_blank" href="https://vbcms.net/doc/faq"><i class="fas fa-question-circle"></i> Questions réponses</a><br>
                <a class="text-dark" target="_blank" href="https://vbcms.net/manager/support"><i class="fas fa-life-ring"></i> Support</a><br>
                <a class="text-dark" target="_blank" href="https://discord.gg/DpfF8Kz"><i class="fab fa-discord"></i> Notre discord</a>
            </div>
        </div>

        <div class="modal fade" id="updateModal" tabindex="-1">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<h5>Mettre à jour VBcms</h5>
	      			</div>
			      	<div class="modal-body">
			        	<p>Vous êtes sur le point de télécharger et d'installer une mise à jour. Tout se fera automatiquement, vous serez automatiquement redirigé après l'installation effectuée.</p>
			        	<p><strong>Note : Il se peut que d'autres mises à jours suivent celle-ci, référez-vous à notre documentation pour en savoir plus.</strong></p>
			      	</div>
	      			<div class="modal-footer">
				        <button type="button" class="btn btn-secondary" data-dismiss="modal">Peut-être plus-tard</button>
				        <button type="button" data-dismiss="modal" onclick="updateVBcms()" class="btn btn-success">Faire la mise à jour</button>
	      			</div>
	    		</div>
	  		</div>
		</div>

    </div>
    <script type="text/javascript">
    $( document ).ready(function() {
        <?php
        if(isset($error)&&!empty($error)){
            echo('SnackBar({
                message: "Check la console",
                status: "danger",
                timeout: false
            });');
            echo('console.log(atob(\''.base64_encode($error).'\'));');
        }
        ?>
    });
    	async function updateVBcms(){
    		$.get("<?=VBcmsGetSetting("websiteUrl")?>backTasks?updateVBcms", function(data) {
				if (data=="") {
					SnackBar({
                        message: "backTasks ne retourne rien: "+data,
                        status: "danger",
                        timeout: false
                    });
				}else{
					details = JSON.parse(data);
                    if (details.success == true) {
                        window.location.replace(details.link);
                    } else {
                        if (details.code == 0) {
                            SnackBar({
                                message: "Impossible de télécharger la mise à jour",
                                status: "danger",
                                timeout: false
                            });
                        } else if(details.code == 1) {
                            SnackBar({
                                message: "Impossible d'ouvrir l'archive de la mise à jour",
                                status: "danger",
                                timeout: false
                            });
                        }
                    }
				}
			});
    	}
    </script>
</body>
</html>