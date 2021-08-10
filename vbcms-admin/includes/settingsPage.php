<?php
// Ici j'ai recréé la fonction que l'on appel pour les modules, car la flemme de faire autrement
// Et puis ça marche pareil donc bon...

// Une amélioration de la structure pourrait en revanche être une bonne idée, là c'est un peu le bazar
function getSettingsHTML($params){
    global $bdd, $translation;
    $curentUpdateCanal = $bdd->query("SELECT value FROM `vbcms-settings` WHERE name='updateCanal'")->fetchColumn();

    $autoUpdatesSearch = $bdd->query("SELECT value FROM `vbcms-settings` WHERE name='autoUpdatesSearch'")->fetchColumn();
    $autoUpdatesInstall = $bdd->query("SELECT value FROM `vbcms-settings` WHERE name='autoUpdatesInstall'")->fetchColumn();
    $autoInstallCriticalUpdates = $bdd->query("SELECT value FROM `vbcms-settings` WHERE name='autoInstallCriticalUpdates'")->fetchColumn();

    if($autoUpdatesSearch == 1) $autoUpdatesSearch = "checked";
    else $autoUpdatesSearch = null;
    if($autoUpdatesInstall == 1) $autoUpdatesInstall = "checked";
    else $autoUpdatesInstall = null;
    if($autoInstallCriticalUpdates == 1) $autoInstallCriticalUpdates = "checked";
    else $autoInstallCriticalUpdates = null;

    if(VBcmsGetSetting("debugMode") == 1) $debugModeChecked = "checked";
    else $debugModeChecked = null;
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
                    include "settings/general.php";    
                }elseif($params=="users" && verifyUserPermission($_SESSION['user_id'], "vbcms", 'manageUsersSettings')){ 
                    include "settings/users.php"; 
                }elseif($params=="userGroups" && verifyUserPermission($_SESSION['user_id'], "vbcms", 'manageuserGroupsSettings')){ 
                    include "settings/groups.php"; 
                } ?>
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
    </script>
    <?php
}