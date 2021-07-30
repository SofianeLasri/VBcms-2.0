<?php
function loadModule($type, $moduleAlias, $moduleParams){
	global $bdd, $http, $websiteUrl, $translation;

	// On va enregistrer l'évènement
	$response = $bdd->prepare("INSERT INTO `vbcms-events` (id,date,module,content,url,ip) VALUES (?,?,?,?,?,?)");
	$response->execute([null, date("Y-m-d H:i:s"), $moduleAlias, "loadModule($type, $moduleAlias, ".json_encode($moduleParams).")", $GLOBALS['http']."://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]", $GLOBALS['ip']]);

	if ($type=="client") {
        if($moduleAlias != "none"){
            // On cherche le module correspondant à l'alias clientAccess dans la liste des modules activés
            $response = $bdd->prepare("SELECT * FROM `vbcms-activatedExtensions` WHERE clientAccess=? AND type='module'");
            $response->execute([$moduleAlias]);
            $response = $response->fetch(PDO::FETCH_ASSOC);

            if (!empty($response)) {
                //include $GLOBALS['vbcmsRootPath'].'/vbcms-content/modules'.$response["path"]."/pageHandler.php"; // Le module appelé va se charger du reste
                $calledmodule = new module($response["name"]);
                $calledmodule->call($moduleParams, $type);
            } else {
                // Aucun module d'activé ne se charge de ce chemin
                if (empty($moduleAlias)) {
                    // Ici on est donc sur l'index du site
                    // Si aucun module ne s'en charge, on va afficher la page par défaut
                    include $GLOBALS['vbcmsRootPath'].'/vbcms-core/defaultPages/index.php';
                } else {
                    // Il s'agit peut-être d'une page du module gérant l'index du site Internet
                    // Nous allons donc vérifier si un module gère l'index, puis on va l'éxecuter
                    $response = $bdd->query("SELECT * FROM `vbcms-activatedExtensions` WHERE clientAccess='' AND type='module'")->fetch(PDO::FETCH_ASSOC);
            
                    if (!empty($response)) {
                        // On a trouvé un module qui gère l'index, au cas où cet alias n'existe pas, ce sera ce module qui gérera la page 404
                        $calledmodule = new module($response["name"]);
                        $calledmodule->call($moduleParams, $type);
                        //include $GLOBALS['vbcmsRootPath'].'/vbcms-content/modules'.$response["path"]."/pageHandler.php"; // Le module appelé va se charger du reste
                    } else {
                        // Si on arrive ici c'est qu'il n'y a vraiment aucun module qui gère cet alias
                        show404($type);
                    }
                    
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

// Cette fonction est appelée par les modules afin de créer des pages selon 3 modes (pas vraiment besoin de l'appeler pour le 3ème)
function extensionCreatePage($panelMode, $creationMode, $pageToInclude, $title, $description, $depedencies){
    // Le mode 0 correspond à l'inclusion d'une page qui retourne du code HTML
	// Le mode 1 correspond à l'inclusion d'une page qui ne fait que passer des paramètres
	// Le mode 2 correspond à l'inclusion d'une page qui n'utilise pas la maquette du thème, qui renvoie sa propre page
	global $bdd, $http, $websiteUrl, $translation, $websiteName, $websiteMetaColor, $websiteDescription, $websiteLogo, $paths;
    
    if($creationMode == 0){
        if($panelMode == "admin"){
            $vbcmsRequest = true;
            require $GLOBALS['vbcmsRootPath']."/vbcms-admin/includes/emptyPage.php";
        }
    } elseif($creationMode == 1){

    } elseif($creationMode == 2){
        require $pageToInclude;
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
        
		global $bdd, $http, $websiteUrl, $translation, $websiteName, $websiteMetaColor, $websiteDescription, $websiteLogo;
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
function verifyUserPermission($userId, $extensionName, $action){
    global $bdd;    
    // On va récupérer les infos de l'utilisateur
    $userInfos = $bdd->prepare("SELECT * FROM `vbcms-users` WHERE netId=?");
    $userInfos->execute([$userId]);
    $userInfos = $userInfos->fetch(PDO::FETCH_ASSOC);

    // On va vérifier s'il a des perms à part
    $usersPerms = $bdd->prepare("SELECT * FROM `vbcms-usersPerms` WHERE userId=? AND extensionName=?");
    $usersPerms->execute([$userId, $extensionName]);
    $perms = $usersPerms->fetch(PDO::FETCH_ASSOC);
    
    if(empty($perms)){
        // Il n'a pas de perms à part
        // On va maintenant récupérer les infos de son groupe
        $groupInfos = $bdd->prepare("SELECT * FROM `vbcms-userGroups` WHERE groupId=?");
        $groupInfos->execute([$userInfos['groupId']]);
        $groupInfos = $groupInfos->fetch(PDO::FETCH_ASSOC);

         // Et maintenant les perms
        if(!empty($groupInfos)){
            if($groupInfos['groupName'] == "superadmins") return true; // Les superadmins ont tous les droits, pas besoin de spécifier leur perms

            $groupsPerms = $bdd->prepare("SELECT * FROM `vbcms-groupsPerms` WHERE netId=? AND extensionName=?");
            $groupsPerms->execute([$groupInfos['groupId'], $extensionName]);
            $perms = $groupsPerms->fetch(PDO::FETCH_ASSOC);
        }
    }

    if(!empty($perms)){
        $perms = json_decode($perms, true);
        if($perms[$action]) return true;
        else return false;
    } else {
        return false;
    }
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
        return $GLOBALS['websiteUrl'].'/vbcms-core/defaultPages/ext404.php';
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