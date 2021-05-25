<?php
session_start();

if (isset($_GET["session"]) && !empty($_GET["session"])) {
	if (isset($_SESSION["user_id"])){
		// Utilisateur déjà connecté -> je réinitialise les variables de la session
		session_unset();
	}

	$encryptionKey = $bdd->query("SELECT value FROM `vbcms-settings` WHERE name='encryptionKey'")->fetchColumn();
	$serverId = $bdd->query("SELECT value FROM `vbcms-settings` WHERE name='serverId'")->fetchColumn();
	
	$json = file_get_contents("https://api.vbcms.net/auth/v1/checkToken/?token=".$_GET["session"]."&ip=".urlencode($ip)."&serverId=".$serverId);
	//echo $json;
	$jsonData = json_decode($json, true);
	if (isset($jsonData) && !isset($jsonData['error'])) {
		if ($jsonData["user_id"] ==0) {
			//Connexion par vbcms
			$_SESSION["user_id"] = 0;
	    	$_SESSION["user_role"] = "owner";
		}else{
			foreach ($jsonData as $key => $value) {
				$_SESSION[$key] = $value;
			}
			// Je rappel que header donne $url
			parse_str($url["query"], $newUrl); // Puis ses paramètres
			unset($newUrl["session"]); // Je vide session
			$newUrl = http_build_query($newUrl); // J'encode les nouveaux paramètres
			header("Location: ".$url["scheme"]."://".$url["host"].$url["path"]."?$newUrl"); // Et je renvoie vers la nouvelle url
		}
	} elseif(isset($jsonData) && isset($jsonData['error'])){
		echo $json;
	}
	

	
} elseif (isset($_GET["logout"]) && isset($_SESSION["user_id"])){
	// Ne s'éxecutera que si l'utilisateur est déjà connecté
	session_destroy();
	header("Location: ".$url["scheme"]."://".$url["host"]); // On le redirige vers la page de connexion
}
?>