<?php
function getSettingsHTML($params){
    global $bdd;
    $curentUpdateCanal = $bdd->query("SELECT value FROM `vbcms-settings` WHERE name='updateCanal'")->fetchColumn();
    $steamApiKey = $bdd->query("SELECT value FROM `vbcms-settings` WHERE name='steamApiKey'")->fetchColumn();
    ?>
    <div class="d-flex">
        <div class="flex-grow-1">
            <h5>Paramètres de mises à jour</h5>
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
                        <input class="form-check-input" type="checkbox" value="" id="autoUpdates">
                        <label class="form-check-label">Recherche de mises à jour automatiques</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="" id="autoUpdates">
                        <label class="form-check-label">Mises à jour automatiques</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="" id="autoUpdates">
                        <label class="form-check-label text-danger" data-toggle="tooltip" data-placement="top" title="⚠️Désactiver cette option n'est pas recommandé!⚠️"><i class="fas fa-exclamation-triangle"></i> <strong>Désactiver l'installation automatique de mises à jour critiques</strong></label>
                    </div>

                </div>
            </div>

            <h5>Identité de l'installation <i class="fas fa-question-circle" data-toggle="tooltip" data-placement="top" title="Nom du site, sa méta-couleur, description, etc."></i></h5>
            <div class="row">
                <div class="col-sm">
                    <div class="form-group">
                        <label>Nom du site internet</label>
                        <input required type="text" value="<?=$GLOBALS['websiteName']?>" class="form-control" name="websiteName">
                    </div>
                    <div class="form-group">
                        <label>Couleur globale du site</label>
                        <input required type="text" value="<?=$GLOBALS['websiteMetaColor']?>" name="websiteMetaColor" class="pick-a-color form-control">
                    </div>
                </div>

                <div class="col-sm">
                    <div class="form-group">
                        <label>Courte description du internet</label>
                        <input required type="text" class="form-control" value="<?=$GLOBALS['websiteDescription']?>" name="websiteDescription">
                    </div>

                </div>
            </div>

            <h5>Autres paramètres</h5>
            <div class="row">
                <div class="col-sm">
                    <div class="form-group">
                        <label>Steam API Key</label>
                        <input type="text" value="<?=$steamApiKey?>" class="form-control" name="steamApiKey">
                    </div>
                </div>

                <div class="col-sm">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="debugMode">
                        <label class="form-check-label">Debug mode</label>
                    </div>

                </div>
            </div>
            
            
        </div>
        <div class="admin-tips" style="position: relative !important;">
            <div class="tip">
                <h5>À quoi servent ces paramètres?</h5>
                <p><b>Ces paramètre permettent de donner une identité à votre site.</b> Ils permettent aux moteurs de recherches ainsi qu'aux applications de le reconnaître.<br><br>
                <b>Il n'est pas primordial de les remplir si vous n'utilisez pas la fonction de site internet</b>, mais il reste préférable d'au moins renseigner le nom du serveur ainsi que son logo.</p>
            </div>
            <div class="tip">
                <h5>À quoi sert la clé Steam API?</h5>
                <p>La clé API Steam permet à certains addons de communiquer avec votre serveur, mais également à certaines interractions la nécessitant.<br><br>
                <b>Vous pouvez l'obtenir ici:</b> <a href="https://steamcommunity.com/dev/apikey" class="text-brown">Clé API Steam Web</a></p>
            </div>
            <div class="tip">
                <h5>Qu'est-ce que l'installation automatique de mises à jour critiques?</h5>
                <p><b>VBcms dispose d'un système d'envoie de commande à distance sécurisé, permettant au serveur principal d'effectuer certaines opérations de maintenances d'extrême urgence.</b><br><br>
                Cela signifie que si une faille de sécurité critique a été découvert dans le coeur du cms, VBcms sera en mesure d'automatiquement se mettre à jour dans les plus brefs délais.</p>
                <p class="text-danger"><b>Il n'est pas pas recommandé de désactiver cette fonctionnalité, VBcms n'est pas encore à un stade de développement mature.</b></p>
            </div>
        </div>
        
    </div>
    <?php
}