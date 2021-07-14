<?php
function getSettingsHTML($params){
    global $bdd, $translation;
    $curentUpdateCanal = $bdd->query("SELECT value FROM `vbcms-settings` WHERE name='updateCanal'")->fetchColumn();
    $steamApiKey = $bdd->query("SELECT value FROM `vbcms-settings` WHERE name='steamApiKey'")->fetchColumn();

    $autoUpdatesSearch = $bdd->query("SELECT value FROM `vbcms-settings` WHERE name='autoUpdatesSearch'")->fetchColumn();
    $autoUpdatesInstall = $bdd->query("SELECT value FROM `vbcms-settings` WHERE name='autoUpdatesInstall'")->fetchColumn();
    $autoInstallCriticalUpdates = $bdd->query("SELECT value FROM `vbcms-settings` WHERE name='autoInstallCriticalUpdates'")->fetchColumn();

    $debugMode = $bdd->query("SELECT value FROM `vbcms-settings` WHERE name='debugMode'")->fetchColumn();

    if($autoUpdatesSearch == 1) $autoUpdatesSearch = "checked";
    else $autoUpdatesSearch = null;
    if($autoUpdatesInstall == 1) $autoUpdatesInstall = "checked";
    else $autoUpdatesInstall = null;
    if($autoInstallCriticalUpdates == 1) $autoInstallCriticalUpdates = "checked";
    else $autoInstallCriticalUpdates = null;

    if($debugMode == 1) $debugMode = "checked";
    else $debugMode = null;
    ?>
    <div class="d-flex">
        <div class="flex-grow-1" >
            <div class="tabs">
                <ul id="tabVBcmsSettingsLinks">
                    <?php if(verifyUserPermission($_SESSION['user_id'], "vbcms", 'access-generalSettings')){ ?>
                    <li id="tab-general">
                        <a href="#" onclick="changeTab('general')">Paramètres généraux</a>
                    </li>
                    <?php } ?>
                    <?php if(verifyUserPermission($_SESSION['user_id'], "vbcms", 'access-usersSettings')){ ?>
                    <li id="tab-users">
                        <a href="#" onclick="changeTab('users')">Utilisateurs</a>
                    </li>
                    <?php } ?>
                    <?php if(verifyUserPermission($_SESSION['user_id'], "vbcms", 'access-userGroupsSettings')){ ?>
                    <li id="tab-userGroups">
                        <a href="#" onclick="changeTab('userGroups')">Groupes d'utilisateurs</a>
                    </li>
                    <?php } ?>
                    <?php if(verifyUserPermission($_SESSION['user_id'], "vbcms", 'access-permissionsSettings')){ ?>
                    <li id="tab-permissions">
                        <a href="#" onclick="changeTab('permissions')">Permissions</a>
                    </li>
                    <?php } ?>
                    <?php if(verifyUserPermission($_SESSION['user_id'], "vbcms", 'access-extAndWsSettings')){ ?>
                    <li id="tab-extAndWs">
                        <a href="#" onclick="changeTab('extAndWs')">Extensions & Worshop</a>
                    </li>
                    <?php } ?>
                    <!-- Modèle
                    <li id="tab-nomTab">
                        <a href="#" class="active">Onglet</a>
                    </li>
                    -->
                </ul>
            </div>
            <?php 
                if(!isset($params)||empty($params)||$params=="general"){
            ?>
            <h5 class="mt-2">Paramètres de mises à jour</h5>
            <form id="form" method="post">
                <?php if(verifyUserPermission($_SESSION['user_id'], "vbcms", 'updatePanel')){ ?>
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
                <?php } ?>
                
                <?php if(verifyUserPermission($_SESSION['user_id'], "vbcms", 'editOtherSettings')){ ?>
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
                            <input class="form-check-input" type="checkbox" name="debugMode" <?=$debugMode?>>
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
        <?php }elseif($params=="users"){ ?>
        
        <div class="d-flex">
            <div class="flex-grow-1 d-flex flex-column">
                <div>
                    
                </div>
            </div>
            <div class="admin-tips" style="position: relative !important; ">
                <div class="tip">
                    <h5>Gérer les utilisateurs</h5>
                    <p>VBcms peut être utilisé par plusieurs peronnes en même temps. Ici tu peux gérer leur compte utilisateur, fais bien attention à qui à accès au panel admin.</p>
                </div>
                
                
            </div>
            
        </div>
        

        <?php } ?>
    </div>
    <script type="text/javascript">
        // S'éxecute une fois la page chargée
        $( document ).ready(function() {
            // On va récupérer l'url et ses paramètres
            let url = new URL(window.location.href);
		    let search_params = url.searchParams;

            // On récupère les infos de la requête
            var extSettingsQuery = JSON.parse(search_params.get('p'));
            if(extSettingsQuery.parameters == ""){
                changeTab("general");
            } else {
                // Et on surligne le lien qui correspond à l'extension souhaitée
                $("#tab-"+extSettingsQuery.parameters).addClass("active");
            }
            
        });

        function changeTab(pageName){
            // Cette fonction permet de charger une autre page

            // On va récupérer l'url et ses paramètres
            let url = new URL(window.location.href);
		    let search_params = url.searchParams;
            // On recréé la requête
            let array = {};
            array.moduleName="VBcms";
            array.parameters=pageName;

            // Et on modifie le paramètre p
            search_params.set('p', JSON.stringify(array));
            let newUrl = url.toString();
            window.history.replaceState({}, '', newUrl);

            // Enfin on lance la fonction qui affiche la page
            setSettingsContent();
        }

        function saveChanges(){
            $.post( "<?=$GLOBALS['websiteUrl']?>vbcms-admin/backTasks?saveSettings", $( "#form" ).serialize() )
            .done(function( data ) {
                if(data!=""){
                    SnackBar({
                        message: data,
                        status: "danger",
                        timeout: false
                    });
                } else {
                    SnackBar({
                        message: '<?=$translation["success-saving"]?>',
                        status: "success"
                    });
                    // On peut reload le contenu de la page avec cette fonction
                    setSettingsContent();
                }
            });
        }
    </script>
    <?php
}