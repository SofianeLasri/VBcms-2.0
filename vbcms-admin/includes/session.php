<?php
session_start();

if (isset($_GET["session"])) {
	$json = file_get_contents("https://api.vbcms.net/auth.php?session=".$_GET["session"]);
	$jsonData = json_decode($json);
	foreach ($jsonData as $key => $value) {
		$_SESSION[$key] = $value;
	}
	// Je récupère l'adresse de la page
	$url = parse_url("$http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"); // Puis ses composantes
	parse_str($url["query"], $newUrl); // Puis ses paramètres
	unset($newUrl["session"]); // Je vide session
	$newUrl = http_build_query($newUrl); // J'encode les nouveaux paramètres
	header("Location: ".$url["scheme"]."://".$url["host"].$url["path"]."?$newUrl"); // Et je renvoie vers la nouvelle url
}
?>