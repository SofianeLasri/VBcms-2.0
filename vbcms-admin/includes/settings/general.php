<form id="form" method="post">
        <?php if(verifyUserPermission($_SESSION['user_id'], "vbcms", 'updatePanel')){ ?>
        <h5 class="mt-2">Paramètres de mises à jour</h5>
        <div class="row">
            <div class="col-sm">
                <div class="form-group">
                    <label>Canal de mise à jour</label>
                    <select class="form-control" name="updateCanal">
                        <?php
                        $updateCanals = ["release", "nightly", "dev"];
                        foreach($updateCanals as $updateCanal){
                            if($updateCanal == $curentUpdateCanal) $selected = 'selected';
                            else $selected = '';
                            echo '<option value="'.$updateCanal.'" '.$selected.'>'.$updateCanal.'</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>

            <div class="col-sm">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="" name="autoUpdatesSearch" <?=$autoUpdatesSearch?>>
                    <label class="form-check-label">Recherche de mises à jour automatiques</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="" name="autoUpdatesInstall" <?=$autoUpdatesInstall?>>
                    <label class="form-check-label">Installation automatique des mises à jour</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="" name="autoInstallCriticalUpdates" <?=$autoInstallCriticalUpdates?>>
                    <label class="form-check-label text-danger" data-toggle="tooltip" data-placement="top" title="⚠️Désactiver cette option n'est pas recommandé!⚠️"><i class="fas fa-exclamation-triangle"></i> <strong>Installation automatique des mises à jour critiques</strong></label>
                </div>

            </div>
        </div>
        <?php } ?>
        
        <?php if(verifyUserPermission($_SESSION['user_id'], "vbcms", 'editWebsiteIdentity')){ ?>
        <h5>Identité de l'installation <i class="fas fa-question-circle" data-toggle="tooltip" data-placement="top" title="Nom du site, sa méta-couleur, description, etc."></i></h5>
        <div class="row">
            <div class="col-sm">
                <div class="form-group">
                    <label>Nom du site internet</label>
                    <input required type="text" value="<?=VBcmsGetSetting("websiteName")?>" class="form-control" name="websiteName">
                </div>
                <div class="form-group">
                    <label>Couleur globale du site</label>
                    <input required type="text" value="<?=VBcmsGetSetting("websiteMetaColor")?>" name="websiteMetaColor" class="pick-a-color form-control">
                </div>
            </div>

            <div class="col-sm">
                <div class="form-group">
                    <label>Courte description du internet</label>
                    <input required type="text" class="form-control" value="<?=VBcmsGetSetting("websiteDescription")?>" name="websiteDescription">
                </div>
                <div class="form-group">
                    <label>Icône du internet</label>
                    <div class="d-flex">
                        <input id="websiteLogo" type="text" class="form-control" value="<?=VBcmsGetSetting("websiteLogo")?>" name="websiteLogo">
                        <button type="button" class="btn btn-sm btn-brown ml-2" data-toggle="modal" data-target="#websiteLogoPicker"><i class="fas fa-image"></i></button>
                    </div>
                    
                </div>

            </div>
        </div>
        <?php } ?>
        
        <?php if(verifyUserPermission($_SESSION['user_id'], "vbcms", 'editOtherSettings')){ ?>
        <h5>Autres paramètres</h5>
        <div class="row">
            <div class="col-sm">
                <div class="form-group">
                    <label>Steam API Key</label>
                    <input type="text" value="<?=VBcmsGetSetting("steamApiKey")?>" class="form-control" name="steamApiKey">
                </div>
            </div>

            <div class="col-sm">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="debugMode" <?=$debugModeChecked?>>
                    <label class="form-check-label"><i class="fas fa-bug"></i> Debug mode</label>
                </div>

            </div>
        </div>
        <?php } ?>
    </form>

    <button type="button" class="btn btn-brown" onclick="saveChanges()">Sauvegarder</button>
</div>
<div class="admin-tips" style="position: relative !important; ">
    <?php if(verifyUserPermission($_SESSION['user_id'], "vbcms", 'editWebsiteIdentity')){ ?>
    <div class="tip">
        <h5>Qu'est-ce que l'identité de l'installation?</h5>
        <p><b>Ces paramètre permettent de donner une identité à votre site.</b> Ils permettent aux moteurs de recherches ainsi qu'aux applications de le reconnaître.<br><br>
        <b>Il n'est pas primordial de les remplir si vous n'utilisez pas la fonction de site internet</b>, mais il reste préférable d'au moins renseigner le nom du serveur ainsi que son logo.</p>
    </div>
    <?php } ?>
    <?php if(verifyUserPermission($_SESSION['user_id'], "vbcms", 'editOtherSettings')){ ?>
    <div class="tip">
        <h5>À quoi sert la clé Steam API?</h5>
        <p>La clé API Steam permet à certains addons de communiquer avec votre serveur, mais également à certaines interractions la nécessitant.<br><br>
        <b>Vous pouvez l'obtenir ici:</b> <a href="https://steamcommunity.com/dev/apikey" class="text-brown" target="_blank">Clé API Steam Web</a></p>
    </div>
    <?php } ?>
    <?php if(verifyUserPermission($_SESSION['user_id'], "vbcms", 'updatePanel')){ ?>
    <div class="tip">
        <h5>Qu'est-ce que l'installation automatique de mises à jour critiques?</h5>
        <p><b>VBcms dispose d'un système d'envoie de commande à distance sécurisé, permettant au serveur principal d'effectuer certaines opérations de maintenances d'extrême urgence.</b><br><br>
        Cela signifie que si une faille de sécurité critique a été découvert dans le coeur du cms, VBcms sera en mesure d'automatiquement se mettre à jour dans les plus brefs délais.</p>
        <p class="text-danger"><b>Il n'est pas pas recommandé de désactiver cette fonctionnalité, VBcms n'est pas encore à un stade de développement mature.</b></p>
    </div>
    <?php } ?>
</div>

<!-- MODAL POUR LOGO DU SITE -->
<div class="modal fade" id="websiteLogoPicker">
    <div class="modal-dialog" style="max-width: 50em;">
        <div class="modal-content">
            <div class="modal-header bg-brown text-white">
                <h5 class="modal-title"><?=translate("chooseAPicture")?></h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body d-flex">
                <iframe class="flex-grow-1" style="height: 25em;" src="<?=openFilemanager('admin', array('field_id' => 'websiteLogo', 'type' => 1))?>"></iframe>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
function saveChanges(){
    $.post( "<?=VBcmsGetSetting("websiteUrl")?>vbcms-admin/backTasks?saveSettings", $( "#form" ).serialize() )
    .done(function( data ) {
        if(data!=""){
            SnackBar({
                message: data,
                status: "danger",
                timeout: false
            });
        } else {
            SnackBar({
                message: '<?=translate("success-saving")?>',
                status: "success"
            });
            // On peut reload le contenu de la page avec cette fonction
            setSettingsContent();
        }
    });
}
</script>