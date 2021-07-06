<?php
session_start();

if (isset($_GET["session"]) && !empty($_GET["session"])){
    // Ici le panel reçoie une requête de connexion via l'id de session unique, communiqué par le manager

    if (isset($_SESSION["user_id"])) session_unset(); // Utilisateur déjà connecté -> je réinitialise les variables de la session

	$sessionJson = file_get_contents("https://api.vbcms.net/auth/v1/checkToken/?token=".$_GET["session"]."&ip=".urlencode($ip)."&serverId=".$serverId);
	//echo $json;
	$sessionData = json_decode($sessionJson, true);

    if (isset($sessionData) && !isset($sessionData['error'])) {
		// On va rechercher dans la liste des utilisateurs si cet user est présent ou non
		$userExistInDB = $bdd->prepare("SELECT * FROM `vbcms-users` WHERE netId=?");
		$userExistInDB->execute([$sessionData["user_id"]]);
		$userExistInDB = $userExistInDB->fetch(PDO::FETCH_ASSOC);
		if(empty($userExistInDB)){
			// On va l'insérer
			if($sessionData["user_role"]=="owner")
				$userGroupID = $bdd->query("SELECT groupId FROM `vbcms-userGroups` WHERE groupName = 'superadmins'")->fetchColumn();
			elseif($sessionData["user_role"]=="admin")
				$userGroupID = $bdd->query("SELECT groupId FROM `vbcms-userGroups` WHERE groupName = 'admins'")->fetchColumn();
			else
				$userGroupID = $bdd->query("SELECT groupId FROM `vbcms-userGroups` WHERE groupName = 'users'")->fetchColumn();

			$userExistInDB = $bdd->prepare("INSERT INTO `vbcms-users` (`netId`, `username`, `localLanguage`, `localJoinedDate`, `groupId`) VALUES (?,?,?,?,?)");
			$userExistInDB->execute([$sessionData["user_id"], $sessionData["user_username"], $sessionData["language"], date("Y-m-d H:i:s"), $userGroupID]);

			// Maintenant on va revérifier
			$userExistInDB = $bdd->prepare("SELECT * FROM `vbcms-users` WHERE netId=?");
			$userExistInDB->execute([$sessionData["user_id"]]);
			$userExistInDB = $userExistInDB->fetch(PDO::FETCH_ASSOC);
		}

		// On va chercher le groupe auquel il appartient
		$userGroup = $bdd->prepare("SELECT * FROM `vbcms-userGroups` WHERE groupId=?");
		$userGroup->execute([$userExistInDB["groupId"]]);
		$userGroup = $userGroup->fetch(PDO::FETCH_ASSOC);
		if(empty($userGroup)){
			// il sera un client si le groupe n'existe pas/plus
			$userGroup = $bdd->query("SELECT groupId FROM `vbcms-userGroups` WHERE groupName = 'users'")->fetch(PDO::FETCH_ASSOC);
		}

		// On va appliquer les variables session
		$_SESSION['groupName'] = $userGroup['groupName'];
		$_SESSION['accessAdmin'] = $userGroup['accessAdmin'];


		foreach ($sessionData as $key => $value) {
			if($key != "user_role") $_SESSION[$key] = $value; // La gestion par les roles de .net n'est plus supporté, le nouveau système de groupe l'a remplacé
		}
		
		parse_str($url["query"], $newUrl); // Puis ses paramètres
		unset($newUrl["session"]); // Je vide session
		$newUrl = http_build_query($newUrl); // J'encode les nouveaux paramètres

		if(!empty($newUrl)) $newUrl = "?".$newUrl;
		header("Location: ".$url["scheme"]."://".$url["host"].$url["path"]."$newUrl"); // Et je renvoie vers la nouvelle url
	} elseif(isset($sessionData) && isset($sessionData['error'])){
		header("Location: https://vbcms.net/manager/login?error=".urlencode($sessionData['error'])); // On renvoie l'utilisateur vers la page de connexion avec un msg d'erreur
	}

} elseif (isset($_GET["logout"]) && isset($_SESSION["user_id"])){
	// Ne s'éxecutera que si l'utilisateur est déjà connecté
	session_destroy();
	header("Location: ".$url["scheme"]."://".$url["host"]); // On le redirige vers la page de connexion
}