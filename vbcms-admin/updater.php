<?php
$hasNewUpdate = checkVBcmsUpdates();

if (!$hasNewUpdate) {
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
                <h4>VBcms <small><?=VBcmsGetSetting("vbcmsVersion")?></small></h4>
                <p><strong><?=$updateMessage?></strong><br>
                    <?=translate("lastChecked")?>: <?=VBcmsGetSetting("lastUpdateCheck")?></p>

                <?php
                    if ($hasNewUpdate) {
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
                <p><span class="text-muted">Installée: </span><span class="text-<?=$textColor?>"><?=VBcmsGetSetting("vbcmsVersion")?></span>
                <?php
                if ($hasNewUpdate) echo '<br><span class="text-muted">Disponible: </span><span class="text-success">'.$hasNewUpdate["name"].'</span>';
                ?>
                <br><span class="text-muted">Canal de mise à jour: </span>
                <?php
                if (VBcmsGetSetting("updateCanal") == "release") {
                    echo '<span class="text-success">Release</span>';
                } elseif (VBcmsGetSetting("updateCanal") == "dev") {
                    echo '<span class="text-danger">Développement</span>';
                } elseif (VBcmsGetSetting("updateCanal") == "nightly") {
                    echo '<span class="text-warning">Bêta</span>';
                }
                
                ?></p>


            </div>
            <div class="col-8"">
                <h4>Détail de la mise à jour</h4>
                <p><?=$hasNewUpdate["description"] ?? ''?></p>
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