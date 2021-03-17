<?php
session_start();

if (isset($_GET["session"]) && !empty($_GET["session"])) {
	if (!isset($_SESSION["user_id"])){
		$json = file_get_contents("https://api.vbcms.net/auth.php?session=".$_GET["session"]);
		$jsonData = json_decode($json);
		foreach ($jsonData as $key => $value) {
			$_SESSION[$key] = $value;
		}
		// Je récupère l'adresse de la page
		$url = parse_url("$websiteUrl$_SERVER[REQUEST_URI]"); // Puis ses composantes
		parse_str($url["query"], $newUrl); // Puis ses paramètres
		unset($newUrl["session"]); // Je vide session
		$newUrl = http_build_query($newUrl); // J'encode les nouveaux paramètres
		header("Location: ".$url["scheme"]."://".$url["host"].$url["path"]."?$newUrl"); // Et je renvoie vers la nouvelle url
	
	} else {
		// Utilisateur déjà connecté
		$json = file_get_contents("https://api.vbcms.net/auth.php?session=".$_GET["session"]); // Je get l'url juste pour détruire le token

		// Je récupère l'adresse de la page
		$url = parse_url("$websiteUrl$_SERVER[REQUEST_URI]"); // Puis ses composantes
		parse_str($url["query"], $newUrl); // Puis ses paramètres
		unset($newUrl["session"]); // Je vide session
		$newUrl = http_build_query($newUrl); // J'encode les nouveaux paramètres
		header("Location: ".$url["scheme"]."://".$url["host"].$url["path"]."?$newUrl"); // On le redirige vers sa destination
	}
} elseif (isset($_GET["logout"]) && isset($_SESSION["user_id"])){
	// Ne s'éxecutera que si l'utilisateur est déjà connecté
	session_destroy();
	header("Location: ".$url["scheme"]."://".$url["host"]); // On le redirige vers la page de connexion
}
?>