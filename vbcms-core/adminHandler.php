<?php
// Dans un premier temps, on va vérifier que l'utilisateur est connecté
// Puis on va vérifier qu'il a bien le droit d'être ici

if (!isset($_SESSION["user_id"])) { // Si l'utilisateur n'est pas connecté

	// On check que la page actuelle n'est pas la page de login, mais également que le panel n'est pas en train de traiter une connexion
    if (basename($_SERVER['PHP_SELF'])!="login.php" && !isset($sessionData) && !isset($sessionData['error'])) { 
        // On le renvoie vers la page de connexion
        header("Location: https://vbcms.net/manager/login?from=".urlencode("$http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']));
    }
} else {
	// On va vérifier qu'il a accès au panel admin

	if ($_SESSION['accessAdmin']!= 1){
		if ($_SERVER['HTTP_HOST'] != "vbcms.net") {
			session_destroy(); // On détruit la session
		}

		// Le message d'erreur sera à changer, je ne l'ai pas encore fait car je dois refaire le drm
		header("Location: https://vbcms.net/manager/?error=".urlencode("Tu n'as pas le droit de te connecter à ce site <img height=\"16\" src=\"https://vbcms.net/vbcms-content/uploads/emoji/oiseau-pas-content.png\">"));
		exit(); // Pour être sûr qu'il n'y ai pas de problèmes
	}

	// On inclu les scripts de fond des modules activés

	// NOTE : On pourrait le faire en instanciant la classe module
	$response = $bdd->query("SELECT * FROM `vbcms-activatedExtensions` WHERE type='module'");
	$response = $response->fetchAll(PDO::FETCH_ASSOC);
	if (!empty($response)) {
		foreach ($response as $module) {
			if(file_exists($GLOBALS['vbcmsRootPath'].'/vbcms-content/extensions/'.$module["path"]."/back.php"))
				include $GLOBALS['vbcmsRootPath'].'/vbcms-content/extensions/'.$module["path"]."/back.php";
		}
	}


	// On inclue les associations de page admin
	require_once 'adminPagesAssoc.php';

	// Maintenant on va vérifier la présence du chemin demandé dans cette page d'associations
	if (array_key_exists($urlPath[2], $adminPagesAssoc)) {
		if($adminPagesAssoc[$urlPath[2]]!="") // On évite "" même si adminPagesAssoc renvoie un lien vide
			include $GLOBALS['vbcmsRootPath']."/vbcms-admin/".$adminPagesAssoc[$urlPath[2]]; // Charge la page admin
			// On a pas de page 404 pour les inclusions menant à rien du tout, on considère qu'elle doivent toujours mener à quelque chsoe
	} else {
		// Si le chemin demandé n'y est pas, alors on va appeler le module qui gère ce chemin
		$moduleParams = array();
		for ($i=2; $i<count($urlPath); $i++) { 
			array_push($moduleParams, $urlPath[$i]);
		}
		loadModule("admin", $urlPath[2], $moduleParams); // Charge le module admin
	}
}
// Le reste est géré par le module ou la page admin
