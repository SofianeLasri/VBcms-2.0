<?php
// Ici j'ai recréé la fonction que l'on appel pour les modules, car la flemme de faire autrement
// Et puis ça marche pareil donc bon...

// Une amélioration de la structure pourrait en revanche être une bonne idée, là c'est un peu le bazar
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
                    <?php if(verifyUserPermission($_SESSION['user_id'], "vbcms", 'access-manageUsersSettings')){ ?>
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
                if((!isset($params)||empty($params)||$params=="general") && verifyUserPermission($_SESSION['user_id'], "vbcms", 'access-generalSettings')){
            ?>
            
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
                        <div class="form-group">
                            <label>Icône du internet</label>
                            <div class="d-flex">
                                <input id="websiteLogo" type="text" class="form-control" value="<?=$GLOBALS['websiteLogo']?>" name="websiteLogo">
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
        <?php }elseif($params=="users" && verifyUserPermission($_SESSION['user_id'], "vbcms", 'manageUsersSettings')){ ?>
        
        <div class="d-flex">
            <div class="flex-grow-1 d-flex flex-column">
                <div class="mt-2">
                    <a href="#" class="btn btn-brown btn-sm"><i class="fas fa-envelope"></i> Inviter un utilisateur</a>
                    <a href="#" class="btn btn-outline-brown btn-sm"><i class="fas fa-user-plus"></i> Créer un utilisateur local</a>
                </div>
                <?php
                    $userGroups=$bdd->query("SELECT * FROM `vbcms-userGroups` ORDER BY `groupId` ASC")->fetchAll(PDO::FETCH_ASSOC);
                    foreach($userGroups as $userGroup){
                        echo ('<div class="d-flex flex-column p-4">
                            <div class="text-brown border-bottom">');

                        $usersCount = $bdd->prepare("SELECT COUNT(*) FROM `vbcms-users` WHERE groupId = ?");
                        $usersCount->execute([$userGroup['groupId']]);
                        $usersCount=$usersCount->fetchColumn();

                        echo translate($userGroup['groupName'])." (".$usersCount;

                        if($usersCount>1) echo " ".strtolower(translate("users")).")";
                        else echo " ".strtolower(translate("user")).")";

                        echo ('</div>
                        <div class="d-flex flex-wrap userList">');

                        $users = $bdd->prepare("SELECT * FROM `vbcms-users` WHERE groupId = ?");
                        $users->execute([$userGroup['groupId']]);
                        $users=$users->fetchAll(PDO::FETCH_ASSOC);

                        foreach($users as $user){
                            $userProfilPic = file_get_contents("https://api.vbcms.net/profiles/v1/".$user['netId']);
                            if(isJson($userProfilPic)){
                                $userProfilPic = json_decode($userProfilPic, true);
                                $userProfilPic = $userProfilPic['profilePic'];
                            } else {
                                // Ici on a soit pas trouvé l'utilisateur, soit les serveurs sont down
                                // Du coup on va check dans localAccounts
                                $userProfilPic = $bdd->prepare("SELECT * FROM `vbcms-localAccounts` WHERE netIdAssoc = ?");
                                $userProfilPic->execute([$user['netId']]);
                                $userProfilPic=$userProfilPic->fetch(PDO::FETCH_ASSOC);
                                if(!empty($userProfilPic)){
                                    $userProfilPic = $userProfilPic['profilePic'];
                                }else{
                                    // Ici l'utilisateur n'existe pas dans la liste des comptes locaux
                                    // Donc on va lui mettre une image placeholder
                                    $userProfilPic = $GLOBALS['websiteUrl']."vbcms-admin/images/misc/programmer.png";
                                }
                            }

                            $joinedDate = new DateTime($user['localJoinedDate']);

                            echo ('<div class="userCard d-flex">
                                <div class="userProfilPic" style="background-image:url(\''.$userProfilPic.'\')"></div>
                                <div class="ml-2">
                                    <h6 class="mb-n1">'.$user['username'].'</h6>
                                    <small class="text-muted">'.translate('joinedOn').': '. $joinedDate->format('l jS F').'</small><br>
                                    <small><a href="#" class="text-brown">'.translate("modifyUser").'</a></small>
                                </div>
                            </div>');
                        }
                        echo "</div></div>";
                    }
                ?>
                <!--
                <div class="d-flex flex-column p-4">
                    <div class="text-brown border-bottom">
                        Un groupe trop génial (1 utilisateur)
                    </div>
                    <div class="d-flex flex-wrap userList">
                        <div class="userCard d-flex">
                            <div class="userProfilPic" style="background-image:url('https://cdn.akamai.steamstatic.com/steamcommunity/public/images/avatars/ee/ee6f9c9ffd6bb2fd2114a378f3f03d997f79e4b9_full.jpg')"></div>
                            <div class="ml-2">
                                <h6 class="mb-n1">sofianelasri</h6>
                                <small class="text-muted">A rejoint le: </small><br>
                                <a href="#" class="text-brown"><?=translate("modifyUser")?></a>
                            </div>
                        </div>
                    </div>
                </div>
                -->
            </div>
            <div class="admin-tips" style="position: relative !important; ">
                <div class="tip">
                    <h5>Gérer les utilisateurs</h5>
                    <p>VBcms peut être utilisé par plusieurs peronnes en même temps. Ici tu peux gérer leur compte, mais également inviter d'autres personnes.<br><strong>Fais bien attention à qui aura accès au panneau d'administration.</strong></p>
                </div>
                
                
            </div>
            
        </div>
        

        <?php } ?>
    </div>
                    
    <?php if(verifyUserPermission($_SESSION['user_id'], "vbcms", 'editWebsiteIdentity')){ ?>

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
    <?php } ?>

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
                        message: '<?=translate("success-saving")?>',
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