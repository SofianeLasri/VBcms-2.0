<?php
function loadModule($type, $moduleAlias, $moduleParams){
	global $bdd, $http, $websiteUrl, $translation;

	// On va enregistrer l'évènement
	$response = $bdd->prepare("INSERT INTO `vbcms-events` (id,date,module,content,url,ip) VALUES (?,?,?,?,?,?)");
	$response->execute([null, date("Y-m-d H:i:s"), $moduleAlias, "loadModule($type, $moduleAlias, ".json_encode($moduleParams).")", $GLOBALS['http']."://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]", $GLOBALS['ip']]);

	if ($type=="client") {
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
        
	} elseif($type=="admin") {
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
        
	}
}

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
function modifyNavItem($item){
    global $bdd;
    $item = json_decode($item);
    $response = $bdd->prepare("UPDATE `vbcms-clientNavbar` SET value1=?, value2=? WHERE id=?");
    $response->execute([$item[1], $item[2], $item[0]]);
}