<?php
// Ce fichier, malgré son nom, ne concentre pas uniquement les variables constantes mais également les fonctions du panel (admin ou pas).
// J'ai toujours fait comme ça, c'est pas aujourd'hui que ça va changer x) -> raisonnement de vieu con



function loadModule($type, $moduleAlias, $moduleParams){
	global $bdd, $http, $websiteUrl, $translation;
	if ($type=="client") {
		if ($moduleAlias !="") {
			// On cherche le module correspondant à l'alias clientAccess dans la liste des modules activés
			$response = $bdd->prepare("SELECT * FROM `vbcms-modules` WHERE clientAccess=? AND activated=1"); // Je récupère l'id du dossier parent
	        $response->execute([$moduleAlias]);
	        $response = $response->fetch(PDO::FETCH_ASSOC);

	        if (!empty($response)) {
	        	include $GLOBALS['vbcmsRootPath'].'/vbcms-content/modules'.$response["path"]."/moduleLoadPage.php"; //module.php a été remplacé par divers fichiers représentant les fonctions
	        	// moduleLoadPage($type, $moduleParams); // Plus d'appel de fonction du coup
	        } else {
	        	include 'clientPagesAssoc.php';

	        	// Check si la page n'est pas un module
	        	if (array_key_exists($moduleAlias, $clientPagesAssoc)) {
	        		loadThemePage($clientPagesAssoc[$moduleAlias]);
	        	} else {
	        		show404($type);
	        	}
	        }
		} else {
			include 'clientPagesAssoc.php';

        	// Check si la page n'est pas un module
        	if (array_key_exists($moduleAlias, $clientPagesAssoc)) {
        		loadThemePage($clientPagesAssoc[$moduleAlias]);
        	} else {
        		show404($type);
        	}
        }
        
	} elseif($type=="admin") {
		// On cherche le module correspondant à l'alias adminAccess dans la liste des modules activés
		$response = $bdd->prepare("SELECT * FROM `vbcms-modules` WHERE adminAccess=? AND activated=1"); // Je récupère l'id du dossier parent
        $response->execute([$moduleAlias]);
        $response = $response->fetch(PDO::FETCH_ASSOC);

        if (!empty($response)) {
        	include $GLOBALS['vbcmsRootPath'].'/vbcms-content/modules'.$response["path"]."/moduleLoadPage.php"; //module.php a été remplacé par divers fichiers représentant les fonctions
        	// moduleLoadPage($type, $moduleParams); // Plus d'appel de fonction du coup
        } else {
        	show404($type);
        }
        
	}
}

function show404($type){
	if ($type=="client") {
		// Affiche la page 404 du site client
		echo "404";
        
	} elseif($type=="admin") {
		global $bdd, $http, $websiteUrl, $translation, $websiteName, $websiteMetaColor, $websiteDescription, $websiteLogo;
		// Affiche la page 404 du panel admin
        include $GLOBALS['vbcmsRootPath']."/vbcms-admin/404.php";
	}
}

function createModulePage($title, $description, $depedencies, $pageToInclude, $mode){
	// Le mode 0 correspond à l'inclusion d'une page qui retourne du code HTML
	// Le mode 1 correspond à l'inclusion d'une page qui ne fait que passer des paramètres
	// Le mode 2 correspond à l'inclusion d'une page qui n'utilise pas la maquette du thème, qui renvoie sa propre page
	global $bdd, $http, $websiteUrl, $translation, $websiteName, $websiteMetaColor, $websiteDescription, $websiteLogo, $folders;
	if ($folders[1]=="vbcms-admin") {
		include 'moduleEmptyPage.php';
	} else{
		if ($mode == 2) {
			include $pageToInclude;
		} else {
			// On charge la page template du thème utilisé
			$response = $bdd->query("SELECT * FROM `vbcms-themes` WHERE activated = 1");
			$theme = $response->fetch(PDO::FETCH_ASSOC);
			include $GLOBALS['vbcmsRootPath'].'/vbcms-content/themes'.$theme["path"].'/pages/template.php';
		}
	}
	
}

function loadThemePage($page){
	global $bdd, $http, $websiteUrl, $translation, $websiteName, $websiteMetaColor, $websiteDescription, $websiteLogo, $folders; //pour Barcha17 <3
	$response = $bdd->query("SELECT * FROM `vbcms-themes` WHERE activated = 1");
	$theme = $response->fetch(PDO::FETCH_ASSOC);

	include $GLOBALS['vbcmsRootPath'].'/vbcms-content/themes'.$theme["path"]."/pages/".$page;
}

function loadClientNavbar($parentId){
	global $bdd;
	if ($parentId=="all") {
		$navbarItems = $bdd->query("SELECT * FROM `vbcms-clientNavbar` ORDER BY parentId, parentPosition")->fetchAll(PDO::FETCH_ASSOC);
	} else {
		$response = $bdd->prepare("SELECT * FROM `vbcms-clientNavbar` WHERE parentId=? ORDER BY parentPosition");
		$response->execute([$parentId]);
		$navbarItems = $response->fetchAll(PDO::FETCH_ASSOC);
	}
	
	return json_encode($navbarItems);
}

function loadLastNavItem(){
	global $bdd;
	$response = $bdd->query("SELECT * FROM `vbcms-clientNavbar` ORDER BY id DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
	return json_encode($response);
}

function getUserNameById($id){
	global $bdd;
	$response = $bdd->prepare("SELECT username FROM `vbcms-localAccounts` WHERE id = ?");
	$response->execute([$id]);
	return $response->fetchColumn();
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

function isJson($string) {
	json_decode($string);
	return (json_last_error() == JSON_ERROR_NONE);
}

// Variables nécessitant la connexion
if (isset($_SESSION["user_id"])){

	function enableAddon($workshopId){
		global $bdd;
		// On cherche dans les différentes table l'addon (voir s'il s'agit d'un module, thème ou autre)
		$response = $bdd->prepare("SELECT * FROM `vbcms-modules` WHERE workshopId=?");
		$response->execute([$workshopId]);
		if (!empty($response->fetch())) {
			//il s'agit d'un module
			$response = $bdd->prepare("UPDATE `vbcms-modules` SET activated=1 WHERE workshopId=?"); // Je change l'état du module
			$response->execute([$workshopId]);

			$response = $bdd->prepare("SELECT * FROM `vbcms-modules` WHERE workshopId=?");
			$response->execute([$workshopId]);
			$response = $response->fetch(PDO::FETCH_ASSOC);
			include $GLOBALS['vbcmsRootPath'].'/vbcms-content/modules'.$response["path"]."/init.php";
		} else {
			//il s'agit d'un thème
			$response = $bdd->prepare("SELECT * FROM `vbcms-themes` WHERE workshopId=?");
			$response->execute([$workshopId]);
			$response = $response->fetch(PDO::FETCH_ASSOC);
			if (!empty($response)){
				$activatedTheme = $bdd->prepare("SELECT * FROM `vbcms-themes` WHERE activated=1 AND designedFor=?");
				$activatedTheme->execute([$response["designedFor"]]);
				$activatedTheme = $activatedTheme->fetch(PDO::FETCH_ASSOC);
				if (!empty($activatedTheme)) {
					disableAddon($activatedTheme["workshopId"]);
				}

				$response = $bdd->prepare("UPDATE `vbcms-themes` SET activated=1 WHERE workshopId=?"); // Je change l'état du module
				$response->execute([$workshopId]);

				$response = $bdd->prepare("SELECT * FROM `vbcms-themes` WHERE workshopId=?");
				$response->execute([$workshopId]);
				$response = $response->fetch(PDO::FETCH_ASSOC);
				include $GLOBALS['vbcmsRootPath'].'/vbcms-content/themes'.$response["path"]."/init.php";
			}
		}
	}

	function disableAddon($workshopId){
		global $bdd;
		// On cherche dans les différentes table l'addon (voir s'il s'agit d'un module, thème ou autre)
		$response = $bdd->prepare("SELECT * FROM `vbcms-modules` WHERE workshopId=?");
		$response->execute([$workshopId]);
		if (!empty($response->fetch())){
			//il s'agit d'un module
			$response = $bdd->prepare("UPDATE `vbcms-modules` SET activated=0 WHERE workshopId=?"); // Je change l'état du module
			$response->execute([$workshopId]);

			$response = $bdd->prepare("SELECT * FROM `vbcms-modules` WHERE workshopId=?"); // Je le sélectionne
			$response->execute([$workshopId]);
			$moduleInfos = $response->fetch(PDO::FETCH_ASSOC);

			$response = $bdd->prepare("SELECT * FROM `vbcms-adminNavbar` WHERE value1=?"); // Je sélectionne l'id parent de la ligne de la navbar
			$response->execute([$moduleInfos["name"]]);
			$navbarInfos = $response->fetch(PDO::FETCH_ASSOC);
			if(!empty($navbarInfos)){
				$response = $bdd->prepare("DELETE FROM `vbcms-adminNavbar` WHERE id=? OR parentId=?"); // Je supprime tous ses liens
				$response->execute([$navbarInfos["id"], $navbarInfos["id"]]);
			}
		} else {
			//il s'agit d'un thème
			$response = $bdd->prepare("SELECT * FROM `vbcms-themes` WHERE workshopId=?");
			$response->execute([$workshopId]);
			if (!empty($response->fetch())){
				$response = $bdd->prepare("UPDATE `vbcms-themes` SET activated=0 WHERE workshopId=?"); // Je change l'état du module
				$response->execute([$workshopId]);

				$response = $bdd->prepare("SELECT * FROM `vbcms-themes` WHERE workshopId=?"); // Je le sélectionne
				$response->execute([$workshopId]);
				$moduleInfos = $response->fetch(PDO::FETCH_ASSOC);

				$response = $bdd->prepare("SELECT * FROM `vbcms-adminNavbar` WHERE value1=?"); // Je sélectionne l'id parent de la ligne de la navbar
				$response->execute([$moduleInfos["name"]]);
				$navbarInfos = $response->fetch(PDO::FETCH_ASSOC);
				if(!empty($navbarInfos)){
					$response = $bdd->prepare("DELETE FROM `vbcms-adminNavbar` WHERE id=? OR parentId=?"); // Je supprime tous ses liens
					$response->execute([$navbarInfos["id"], $navbarInfos["id"]]);
				}
			}
			$response = $bdd->prepare("DELETE FROM `vbcms-modulesDepencies` WHERE moduleId=?"); // Je supprime tous ses liens
			$response->execute([$workshopId]);
		}

		
	}

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
	function recreateClientNav($data){
		global $bdd;
		$data = json_decode($data, true);

		$bdd->query("TRUNCATE `vbcms-clientNavbar`");

		//print_r($data);
		$index = 0;
		$oldIndex = array();
		$zeroPos = 0;
		foreach ($data as $key) {
			$index++;
			$key["id"] = str_replace("item-", "", $key["id"]);
			
			$oldIndex[$key["id"]] = $index;

			if(!isset($key["parentId"])){
				$key["parentId"] = 0;
				$zeroPos++;
			} else {
				$key["parentId"] = str_replace("item-", "", $key["parentId"]);
				$key["parentId"] = $oldIndex[$key["parentId"]];
			}

			$response = $bdd->prepare("INSERT INTO `vbcms-clientNavbar` (id, parentId, parentPosition, value1, value2, value3) VALUES (?,?,?,?,?,?)");
			$response->execute([$index, $key["parentId"], $key["order"], $key["value1"], $key["value2"], ""]);
		}
	}
	function deleteNavItem($item){
		global $bdd;
		$response = $bdd->prepare("DELETE FROM `vbcms-clientNavbar` WHERE id=? OR parentId=?");
		$response->execute([$item, $item]);
	}
}


?>