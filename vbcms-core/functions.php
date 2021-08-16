<?php
// Cette fonctions permettra de récupérer des variables propres au panel
function VBcmsGetSetting($setting){
    global $bdd;
    
    $response = $bdd->prepare("SELECT value FROM `vbcms-settings` WHERE name = ?");
    $response->execute([$setting]);
    return $response->fetchColumn();
}

function loadModule($type, $moduleAlias, $moduleParams){
	global $bdd;

	// On va enregistrer l'évènement
	$response = $bdd->prepare("INSERT INTO `vbcms-events` (id,date,module,content,url,ip) VALUES (?,?,?,?,?,?)");
	$response->execute([null, date("Y-m-d H:i:s"), $moduleAlias, "loadModule($type, $moduleAlias, ".json_encode($moduleParams).")", $GLOBALS['http']."://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]", $GLOBALS['ip']]);

	if ($type=="client") {
        if($moduleAlias != "none"){
            // Dans un premier temps on va regarder si un module gère l'index
            $isExistAModuleHandleClientRoot = $bdd->query("SELECT * FROM `vbcms-activatedExtensions` WHERE clientAccess='' AND type='module'")->fetch(PDO::FETCH_ASSOC);

            if(!empty($isExistAModuleHandleClientRoot)){
                // Un modèle gère l'index
                // On va donc l'appeler pour savoir s'il gère cette url

                $rootModuleParameters = $moduleParams; // Je duplique la liste des paramètres pour ce module
                array_unshift($rootModuleParameters,$moduleAlias); // Et je lui ajoute en premier ce que cette fonction considère comme un alias
                // J'ai fais ça car vu que ce module gère la racine de l'url, elle n'a pas d'alias. Donc le premier paramètre n'est pas un alias.

                $calledmodule = new module($isExistAModuleHandleClientRoot["name"]);
                $calledmodule->call($rootModuleParameters, $type); // Ressort simplement 404 s'il ne gère pas cette page
                $modulePageContent = ob_get_clean(); // Je récupère la sortie pour ensuite la comparer

                if($modulePageContent == "404"){
                    // Ce module ne gère donc pas l'url

                    // On va donc vérifier s'il existe un module qui gère cet alias
                    $response = $bdd->prepare("SELECT * FROM `vbcms-activatedExtensions` WHERE clientAccess=? AND type='module'");
                    $response->execute([$moduleAlias]);
                    $response = $response->fetch(PDO::FETCH_ASSOC);
                    
                    if(!empty($response)){
                        // Il existe, on va donc l'appeler
                        $calledmodule = new module($response["name"]);
                        $calledmodule->call($moduleParams, $type);
                        $modulePageContent = ob_get_clean(); // Pour récupérer la sortie
                        if($modulePageContent == "404"){
                            // On va afficher sa page 404 si l'extension renvoie 404
                            array_unshift($moduleParams, "404"); // On ajoute 404 en premier afin que l'ext affiche la page 404
                            $calledmodule->call($moduleParams, $type);
                        } else {
                            echo $modulePageContent;
                        }
                    } else {
                        // Aucune extension ne gère ce lien, on va donc appeler la page 404 de l'extension gérant cet alias
                        array_unshift($moduleParams, "404"); // On ajoute 404 en premier afin que l'ext affiche la page 404
                        $calledmodule->call($moduleParams, $type);
                    }
                } else {
                    echo $modulePageContent;
                }
            } else {
                // Aucune extension ne gère l'index
                // On va donc vérifier s'il existe un module qui gère cet alias
                $response = $bdd->prepare("SELECT * FROM `vbcms-activatedExtensions` WHERE clientAccess=? AND type='module'");
                $response->execute([$moduleAlias]);
                $response = $response->fetch(PDO::FETCH_ASSOC);
                
                if(!empty($response)){
                    // Il existe, on va donc l'appeler
                    $calledmodule = new module($response["name"]);
                    $modulePageContent = $calledmodule->call($moduleParams, $type);
                    if($modulePageContent == "404"){
                        // On va afficher sa page 404 si l'extension renvoie 404
                        array_unshift($moduleParams, "404"); // On ajoute 404 en premier afin que l'ext affiche la page 404
                        $calledmodule->call($moduleParams, $type);
                    } else {
                        echo $modulePageContent;
                    }
                } else {
                    // Aucune extension ne gère cet alias
                    show404($type);
                }
            }
        } else show404($type);
        
	} elseif($type=="admin") {
        if($moduleAlias != "none"){
            // On cherche le module correspondant à l'alias adminAccess dans la liste des modules activés
            $response = $bdd->prepare("SELECT * FROM `vbcms-activatedExtensions` WHERE adminAccess=? AND type='module'");
            $response->execute([$moduleAlias]);
            $response = $response->fetch(PDO::FETCH_ASSOC);

            if (!empty($response)) {
                //include $GLOBALS['vbcmsRootPath'].'/vbcms-content/modules'.$response["path"]."/pageHandler.php"; // Le module appelé va se charger du reste
                $calledmodule = new module($response["name"]);
                $calledmodule->call($moduleParams, $type);
            } else {
                show404($type);
            }
        } else show404($type);
	}
}



// Cette fonction permet la traduction des textures en fcontion de la langue utilisée par l'utilisateur
function translate($index){
    global $bdd;
    // Peut probable, mais il se peut que la langue n'ai pas été définie plus tôt
    if(!isset($_SESSION["language"])){
        $geoPlugin_array = unserialize( file_get_contents('http://www.geoplugin.net/php.gp?ip=' . $_SERVER['REMOTE_ADDR']) );
        $_SESSION["language"] = $geoPlugin_array['geoplugin_countryCode'];
    }

    $language = $_SESSION["language"];
    switch ($language) {
        case "FR":
            include $GLOBALS['vbcmsRootPath'].'/vbcms-content/translations/FR.php';
            break;
        case "EN":
            include $GLOBALS['vbcmsRootPath'].'/vbcms-content/translations/EN.php';
            break;
        default:
            include $GLOBALS['vbcmsRootPath'].'/vbcms-content/translations/FR.php';
            break;
    }

    // En gros, s'il existe une traduction, on la récupère
    if(isset($translation[$index])) $response = $translation[$index];
    else {
        // Sinon on suppose que c'est une traduction d'un module
        // Et donc on va la chercher
        $response = $bdd->query("SELECT * FROM `vbcms-activatedExtensions`");
        $activatedExtensions = $response->fetchAll(PDO::FETCH_ASSOC);
        foreach ($activatedExtensions as $activatedExtension){
            $ext = new module($activatedExtension["name"]);
            $extTrsl = $ext->getTranslationFile($language);
            if(!empty($extTrsl)) include $extTrsl;
            if(isset($translation[$index])) return $translation[$index];
        }
    }
    
    // S'il n'y a vraiment rien....
    if(!isset($translation[$index])) $response = $translation["unknownTranslation"];
    return $response;
}

function show404($type){
	if ($type=="client") {
		// Affiche la page 404 du site client
        // A savoir que les pages 404 des modules sont gérées par ces derniers
		include $GLOBALS['vbcmsRootPath'].'/vbcms-core/defaultPages/404.php';
        
	} elseif($type=="admin") {
        // A REFAIRE
        
		global $bdd;
		// Affiche la page 404 du panel admin
        include $GLOBALS['vbcmsRootPath']."/vbcms-admin/404.php";
        
	}
}

// Petites fonctions utiles
function isJson($string) {
	json_decode($string);
	return (json_last_error() == JSON_ERROR_NONE);
}

function getRandomString($length) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function tableExist($tableName){
    global $bdd;
    try{
        $response = $bdd->query("SELECT 1 FROM $tableName LIMIT 1"); // Aîe les injections SQL, ça va qu'ici c'est interne
    } catch(Exception $e){
        return false;
    }
    return $response !== FALSE; // Retourne l'objet si != faux
}

// Fonctions pour la barre de naviguation admin
function adminNavbarAddCategory($moduleName, $title){
    global $bdd;
    $position = 1+($bdd->query("SELECT position FROM `vbcms-adminNavbar` ORDER BY position DESC LIMIT 1")->fetchColumn());
    $response = $bdd->prepare("INSERT INTO `vbcms-adminNavbar` (id, parentId, position, parentPosition, value1, value2, value3) VALUES (?,?,?,?,?,?,?)");
    $response->execute([null, 0, $position, 0, $moduleName, $title, ""]);
}
function adminNavbarAddItem($moduleName, $icon, $name, $link){
    global $bdd;
    $response = $bdd->prepare("SELECT * FROM `vbcms-adminNavbar` WHERE value1 = ? AND parentPosition = 0"); // Je récupère l'id du dossier parent
    $response->execute([$moduleName]);
    $parentInfos = $response->fetch(PDO::FETCH_ASSOC);

    $response = $bdd->prepare("SELECT parentPosition FROM `vbcms-adminNavbar` WHERE parentId = ? ORDER BY parentPosition DESC LIMIT 1"); // Je récupère l'id du dossier parent
    $response->execute([$moduleName]);
    $parentPosition = 1+ $response->fetchColumn();


    $response = $bdd->prepare("INSERT INTO `vbcms-adminNavbar` (id, parentId, position, parentPosition, value1, value2, value3) VALUES (?,?,?,?,?,?,?)");
    $response->execute([null, $parentInfos["id"], $parentInfos["position"], $parentPosition, $icon, $name, $link]);
}

// Permet de vérifier qu'un utilisateur a bien les permissions pour visualiser ou effectuer une tâche
function verifyUserPermission($userId, $extensionName, $permission){
    global $bdd;    
    // On va récupérer les infos de l'utilisateur
    $userInfos = $bdd->prepare("SELECT * FROM `vbcms-users` WHERE id=?");
    $userInfos->execute([$userId]);
    $userInfos = $userInfos->fetch(PDO::FETCH_ASSOC);

    // On va vérifier s'il a des perms à part
    $usersPerms = $bdd->prepare("SELECT * FROM `vbcms-usersPerms` WHERE userId=? AND extensionName=? AND permission=?");
    $usersPerms->execute([$userId, $extensionName, $permission]);
    $hasPerm = $usersPerms->fetch(PDO::FETCH_ASSOC);
    
    if(empty($hasPerm)){
        // Il n'a pas de perms à part
        // On va maintenant vérifier avec le groupe
        return verifyGroupPermission($userInfos['groupId'], $extensionName, $permission);
    }else{
        $perms = json_decode($perms, true);
        if($perms[$action]) return true;
        else return false;
    }
}

function verifyGroupPermission($groupId, $extensionName, $permission){
    global $bdd; 
    // On va récupérer les infos du groupe
    $groupInfos = $bdd->prepare("SELECT * FROM `vbcms-userGroups` WHERE groupId=?");
    $groupInfos->execute([$groupId]);
    $groupInfos = $groupInfos->fetch(PDO::FETCH_ASSOC);

    // Et maintenant les perms
    if(!empty($groupInfos)){
        if($groupInfos['groupName'] == "superadmins") return true; // Les superadmins ont tous les droits, pas besoin de spécifier leur perms

        $groupsPerms = $bdd->prepare("SELECT * FROM `vbcms-groupsPerms` WHERE groupId=? AND extensionName=? AND permission=?");
        $groupsPerms->execute([$groupInfos['groupId'], $extensionName, $permission]);
        $hasPerm = $groupsPerms->fetch(PDO::FETCH_ASSOC);
        if(!empty($hasPerm)) return true;
        else return false; // N'a pas la perm
    } else return false; // Le groupe n'existe pas, donc pas de perm
}

function getVBcmsPermissions(){
    include $GLOBALS['vbcmsRootPath'].'/vbcms-core/permissions.php';
    return $permissions;
}

/////////////////////////////////
// FONCTIONS DES MODULES DE BASES
/////////////////////////////////

function openFilemanager($mode, $parameters = array()){
    // Mode pour client ou admin, mais c'est pas encore prévu pour les clients
    // Parameters est une liste
    global $bdd;
    $filemanagerAssoc = $bdd->query("SELECT extensionName FROM `vbcms-baseModulesAssoc`")->fetchColumn();
    if(empty($filemanagerAssoc)){
        return VBcmsGetSetting("websiteUrl").'/vbcms-core/defaultPages/ext404.php';
    }else{
        $filemanagerExt = $bdd->prepare("SELECT * FROM `vbcms-activatedExtensions` WHERE name = ?");
        $filemanagerExt->execute([$filemanagerAssoc]);
        $filemanagerExt=$filemanagerExt->fetch(PDO::FETCH_ASSOC);

        $callParams[1] = "openFilemanager";
        $callParams[2] = json_encode($parameters);

        $filemanagerModule = new module($filemanagerExt["name"]);
        $filemanagerModule->call($callParams, 'admin');

    }
}