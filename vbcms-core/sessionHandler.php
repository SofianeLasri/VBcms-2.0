<?php
session_start();

if (isset($_GET["session"]) && !empty($_GET["session"])){
    // Ici le panel reçoie une requête de connexion via l'id de session unique, communiqué par le manager

    if (isset($_SESSION["user_id"])) session_unset(); // Utilisateur déjà connecté -> je réinitialise les variables de la session

	$sessionJson = file_get_contents("https://api.vbcms.net/auth/v1/checkToken/?token=".$_GET["session"]."&ip=".urlencode($ip)."&serverId=".$serverId);
	//echo $json;
	$sessionData = json_decode($sessionJson, true);

    if (isset($sessionData) && !isset($sessionData['error'])) {
		if ($sessionData["user_id"] ==0) {
			//Connexion par vbcms.net
			$_SESSION["user_id"] = 0;
	    	$_SESSION["user_role"] = "owner";
		}else{
			foreach ($sessionData as $key => $value) {
				$_SESSION[$key] = $value;
			}
			
			parse_str($url["query"], $newUrl); // Puis ses paramètres
			unset($newUrl["session"]); // Je vide session
			$newUrl = http_build_query($newUrl); // J'encode les nouveaux paramètres

            if(!empty($newUrl)) $newUrl = "?".$newUrl;
			header("Location: ".$url["scheme"]."://".$url["host"].$url["path"]."$newUrl"); // Et je renvoie vers la nouvelle url
		}
	} elseif(isset($sessionData) && isset($sessionData['error'])){
		header("Location: https://vbcms.net/manager/login?error=".urlencode($sessionData['error'])); // On renvoie l'utilisateur vers la page de connexion avec un msg d'erreur
	}

} elseif (isset($_GET["logout"]) && isset($_SESSION["user_id"])){
	// Ne s'éxecutera que si l'utilisateur est déjà connecté
	session_destroy();
	header("Location: ".$url["scheme"]."://".$url["host"]); // On le redirige vers la page de connexion
}