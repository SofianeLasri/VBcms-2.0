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
            include $GLOBALS['vbcmsRootPath'].'/vbcms-content/modules'.$response["path"]."/moduleLoadPage.php"; // Le module appelé va se charger du reste
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
                    include $GLOBALS['vbcmsRootPath'].'/vbcms-content/modules'.$response["path"]."/moduleLoadPage.php"; // Le module appelé va se charger du reste
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
        	//include $GLOBALS['vbcmsRootPath'].'/vbcms-content/modules'.$response["path"]."/moduleLoadPage.php"; // Le module appelé va se charger du reste
            $calledmodule = new VBcms\module($response["name"],$response["path"],$response["adminAccess"],$response["clientAccess"],$response["vbcmsVerId"]);
            $calledmodule->call($moduleParams, $type);
        } else {
        	show404($type);
        }
        
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