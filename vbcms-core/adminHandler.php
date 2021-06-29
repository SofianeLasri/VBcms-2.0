<?php
// Dans un premier temps, on va vérifier que l'utilisateur est connecté
// Puis on va vérifier qu'il a bien le droit d'être ici

if (!isset($_SESSION["user_id"])) { // Si l'utilisateur n'est pas connecté
    if (basename($_SERVER['PHP_SELF'])!="login.php" && !isset($sessionData) && !isset($sessionData['error'])) { // On check que la page actuelle n'est pas la page de login, mais également de rediriger pendant la création de la session
        // On le renvoie vers la page de connexion
        header("Location: https://vbcms.net/manager/login?from=".urlencode("$http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']));
    }
} else {
	// On va vérifier qu'il a un rôle
	if (!in_array($_SESSION["user_role"], ["owner", "admin", "moderator"])){
		if ($_SERVER['HTTP_HOST'] != "vbcms.net") {
			session_destroy(); // On détruit la session
		}
		header("Location: https://vbcms.net/manager/?error=".urlencode("Tu n'as pas le droit de te connecter à ce site <img height=\"16\" src=\"https://vbcms.net/vbcms-content/uploads/emoji/oiseau-pas-content.png\">"));
	}

	//
	// A CHANGER CAR NON COMPATIBLE AVEC LE NOUVEAU SYSTEM DE SECURITE
	// 
	// On inclu les scripts de fond des modules activés
	/*
	$response = $bdd->query("SELECT * FROM `vbcms-activatedExtensions` WHERE type='module'");
	$response = $response->fetchAll(PDO::FETCH_ASSOC);
	if (!empty($response)) {
		foreach ($response as $module) {
			if(file_exists($GLOBALS['vbcmsRootPath'].'/vbcms-content/modules'.$module["path"]."/back.php"))
				include $GLOBALS['vbcmsRootPath'].'/vbcms-content/modules'.$module["path"]."/back.php";
		}
	}
	*/
	//
	//
	//	

	// On inclue les associations de page admin
	require_once 'adminPagesAssoc.php';

	// Maintenant on va vérifier la présence du chemin demandé dans cette page d'associations
	if (array_key_exists($paths[2], $adminPagesAssoc)) {
		if($adminPagesAssoc[$paths[2]]!="") // On évite "" même si adminPagesAssoc renvoie un lien vide
			include $GLOBALS['vbcmsRootPath']."/vbcms-admin/".$adminPagesAssoc[$paths[2]]; // Charge la page admin
	} else {
		// Si le chemin demandé n'y est pas, alors on va appeler le module qui gère ce chemin
		$moduleParams = array();
		for ($i=2; $i<count($paths); $i++) { 
			array_push($moduleParams, $paths[$i]);
		}
		loadModule("admin", $paths[2], $moduleParams); // Charge le module admin
	}
}
