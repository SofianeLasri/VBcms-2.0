<?php
ini_set("allow_url_fopen", 1);
session_start();
error_reporting(E_ALL); //Désactive les erreur

if (isset($_SESSION["user_id"])) {
	$depedencies = 0;
}

// On récupère l'ip
if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    $ip = $_SERVER['HTTP_CLIENT_IP'];
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
    $ip = $_SERVER['REMOTE_ADDR'];
}

if (isset($_GET["session"])) {
	$json = file_get_contents("https://api.vbcms.net/auth/v1/checkToken/?token=".$_GET["session"]."&ip=".urlencode($ip));
	$jsonData = json_decode($json);
	foreach ($jsonData as $key => $value) {
		$_SESSION[$key] = $value;
	}
	header('Location: install.php');
} elseif (isset($_GET["testBdd"]) AND !empty($_GET["testBdd"])) {
	$bddInfos = json_decode($_GET["testBdd"]);
	$bddHost = $bddInfos[0]; //Adresse du serveur MySQL
	$bddName = $bddInfos[1]; //Nom de la base de donnée
	$bddUser = $bddInfos[2]; //Utilisateur
	$bddMdp = $bddInfos[3]; //Mot de passe

	$bddError = false;
	try {
	    $bddConn = new PDO("mysql:host=$bddHost;dbname=$bddName", $bddUser, $bddMdp); //Test de la connexion
	} catch (PDOException $e) {
		$bddError = true;
		print "Erreur !: " . $e->getMessage();
		die();
	}
	if (!$bddError) {
		$savedParameters = file_get_contents("tempInstallConfig");
		if ($savedParameters) {
			$savedParameters = json_decode($savedParameters);
			$savedParameters[0] = $bddInfos[0];
			$savedParameters[1] = $bddInfos[1];
			$savedParameters[2] = $bddInfos[2];
			$savedParameters[3] = $bddInfos[3];
			file_put_contents("tempInstallConfig", json_encode($savedParameters));
		} else {
			file_put_contents("tempInstallConfig", json_encode($bddInfos));
		}
	}
} elseif (isset($_GET["saveWebsiteConfig"]) AND !empty($_GET["saveWebsiteConfig"])){
	$websiteConfig = json_decode($_GET["saveWebsiteConfig"]);
	$savedParameters = file_get_contents("tempInstallConfig");
	$savedParameters = json_decode($savedParameters);
	$savedParameters[4] = $websiteConfig[0];
	$savedParameters[5] = $websiteConfig[1];
	$savedParameters[6] = $websiteConfig[2];
	$savedParameters[7] = $websiteConfig[3];
	file_put_contents("tempInstallConfig", json_encode($savedParameters));

} elseif (isset($_GET["createDatabase"])){
	$parameters = json_decode(file_get_contents("tempInstallConfig"));
	$bddHost = $parameters[0]; //Adresse du serveur MySQL
	$bddName = $parameters[1]; //Nom de la base de donnée
	$bddUser = $parameters[2]; //Utilisateur
	$bddMdp = $parameters[3]; //Mot de passe
	$bdd = new PDO("mysql:host=$bddHost;dbname=$bddName", $bddUser, $bddMdp);
	$bdd->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );

	// Création des tables
	$requete = $bdd->exec("CREATE TABLE IF NOT EXISTS `vbcms-blogCategories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shortName` varchar(64) NOT NULL,
  `showName` varchar(128) NOT NULL,
  `childOf` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

	$requete = $bdd->exec("CREATE TABLE IF NOT EXISTS `vbcms-blogDrafts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `randId` varchar(4) COLLATE utf8mb4_unicode_ci NOT NULL,
  `categoryId` int(11) NOT NULL,
  `authorId` bigint(255) NOT NULL,
  `slug` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `headerImage` varchar(255) CHARACTER SET utf8mb4 NOT NULL,
  `writtenOn` datetime NOT NULL,
  `modifiedOn` datetime NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `autosave` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

	$requete = $bdd->exec("CREATE TABLE IF NOT EXISTS `vbcms-blogPosts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `categoryId` int(11) NOT NULL,
  `authorId` bigint(255) NOT NULL,
  `slug` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` text COLLATE utf8_unicode_ci NOT NULL,
  `content` text COLLATE utf8_unicode_ci NOT NULL,
  `headerImage` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `writtenOn` datetime NOT NULL,
  `modifiedOn` datetime NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `views`int(11),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT;");

	$requete = $bdd->exec("CREATE TABLE IF NOT EXISTS `vbcms-files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 NOT NULL,
  `parentFolder` int(11) NOT NULL,
  `size` bigint(20) NOT NULL,
  `title` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `articles` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

	$requete = $bdd->exec("CREATE TABLE IF NOT EXISTS `vbcms-folders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `fullpath` varchar(128) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

	$requete = $bdd->exec("CREATE TABLE IF NOT EXISTS `vbcms-settings` (
		`name` VARCHAR(128) NOT NULL,
		`value` VARCHAR(3000) NOT NULL,
		PRIMARY KEY (`name`)
	) ENGINE = InnoDB;");

	$requete = $bdd->exec("CREATE TABLE IF NOT EXISTS `vbcms-modules`(
		`name` varchar(64) NOT NULL,
		`path` varchar(255) NOT NULL,
		`clientAccess` varchar(32) NOT NULL,
		`adminAccess` varchar(32) NOT NULL,
		`activated` tinyint(1) NOT NULL,
		`workshopId` bigint(20) NOT NULL,
		`version` VARCHAR(8) NOT NULL , PRIMARY KEY (`name`)
	) ENGINE = InnoDB;");

	$requete = $bdd->exec("CREATE TABLE IF NOT EXISTS `vbcms-adminNavbar` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parentId` int(11) NOT NULL,
  `position` int(11) NOT NULL,
  `parentPosition` int(11) NOT NULL,
  `value1` varchar(128) NOT NULL,
  `value2` varchar(128) NOT NULL,
  `value3` varchar(128) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

	$requete = $bdd->exec("CREATE TABLE IF NOT EXISTS `vbcms-modulesDepencies` (
		`moduleId` int(11) NOT NULL,
		`depedencyId` int(11) NOT NULL,
		`mandatory` BOOLEAN NOT NULL,
		PRIMARY KEY (`moduleId`, `depedencyId`)
	) ENGINE = InnoDB;");

	$requete = $bdd->exec("CREATE TABLE IF NOT EXISTS `vbcms-clientNavbar` (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`parentId` int(11) NOT NULL,
		`parentPosition` int(11) NOT NULL,
		`value1` VARCHAR(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
		`value2` VARCHAR(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
		`value3` VARCHAR(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
		PRIMARY KEY (`id`)
	) ENGINE = InnoDB;");

	$requete = $bdd->exec("CREATE TABLE IF NOT EXISTS `vbcms-themes` (
		`workshopId` bigint(255) NOT NULL,
		`name` varchar(128) NOT NULL,
		`path` varchar(255) NOT NULL,
		`version` varchar(32) NOT NULL,
		`activated` tinyint(1) NOT NULL,
		`designedFor` int(11) NOT NULL,
		PRIMARY KEY (`workshopId`)
	  ) ENGINE=InnoDB;");

	$requete = $bdd->exec("CREATE TABLE `vbcms-localAccounts` (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`username` VARCHAR(128) NOT NUL,
		`email` VARCHAR(256) NOT NULL,
		`password` VARCHAR(255) NOT NULL,
		`role` varchar(32) NOT NULL,
		PRIMARY KEY (`id`)
		) ENGINE = InnoDB;");
	
	$requete = $bdd->exec("CREATE TABLE `vbcms-websiteStats` (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`date` DATETIME NOT NULL,
		`page` VARCHAR(300) NOT NULL,
		`ip` VARCHAR(64) NOT NULL,
		PRIMARY KEY (`id`)
		) ENGINE = InnoDB;");

	$requete = $bdd->exec("CREATE TABLE `vbcms-loadingscreeens` (
		`id` INT(11) NOT NULL AUTO_INCREMENT,
		`themeId` INT(11) NOT NULL,
		`name` INT(255) NOT NULL,
		PRIMARY KEY (`id`)
	) ENGINE = InnoDB;");

	$requete = $bdd->exec("CREATE TABLE `vbcms-loadingscreensParameters` (
		`loadingScreenId` INT(11) NOT NULL,
	`name` VARCHAR(128) NOT NULL,
	`value` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
	PRIMARY KEY (`loadingScreenId`, `name`)
	) ENGINE = InnoDB;");

	$requete = $bdd->exec("CREATE TABLE `vbcms-permissions` (
		`accountId` INT(11) NOT NULL,
		`name` VARCHAR(128) NOT NULL,
		`value` VARCHAR(128) NOT NULL,
		PRIMARY KEY (`accountId`, `name`)
	) ENGINE = InnoDB;");

	/*
	$requete = $bdd->exec("");
	$requete = $bdd->exec("");
	$requete = $bdd->exec("");
	$requete = $bdd->exec("");
	$requete = $bdd->exec("");
	$requete = $bdd->exec("");
	$requete = $bdd->exec("");
	$requete = $bdd->exec("");
	$requete = $bdd->exec("");
	$requete = $bdd->exec("");
	*/

	// Insertions des données

	$response = $bdd->prepare("INSERT INTO `vbcms-settings` (name, value) VALUES (?,?)");
	$response->execute(["websiteName", $parameters[4]]);

	$response = $bdd->prepare("INSERT INTO `vbcms-settings` (name, value) VALUES (?,?)");
	$response->execute(["websiteDescription", $parameters[5]]);

	$response = $bdd->prepare("INSERT INTO `vbcms-settings` (name, value) VALUES (?,?)");
	$response->execute(["websiteMetaColor", $parameters[6]]);

	$response = $bdd->prepare("INSERT INTO `vbcms-settings` (name, value) VALUES (?,?)");
	$response->execute(["websiteLogo", "images/vbcms-logo/raccoon-in-box-512x.png"]);

	$response = $bdd->prepare("INSERT INTO `vbcms-settings` (name, value) VALUES (?,?)");
	$response->execute(["steamApiKey", $parameters[7]]);

	require "vbcms-content/translations/".$_SESSION["language"].".php";


	$navbarPosition=0;
	// Ajoute la catégorie
	$response = $bdd->prepare("INSERT INTO `vbcms-adminNavbar` (id, parentId, position, parentPosition, value1, value2, value3) VALUES (?,?,?,?,?,?,?)");
    $response->execute([null, 0, $navbarPosition, 0, "vbcms", 'workshop', ""]);
    $navbarPosition++;

    // Ajoute le lien
    $parentPosition = 1;
    $response = $bdd->prepare("INSERT INTO `vbcms-adminNavbar` (id, parentId, position, parentPosition, value1, value2, value3) VALUES (?,?,?,?,?,?,?)");
    $response->execute([null, 1, $navbarPosition, $parentPosition, "fa-shopping-cart", 'ws_visit', "/vbcms-admin/workshop/visit"]);
    $parentPosition++;
    $response = $bdd->prepare("INSERT INTO `vbcms-adminNavbar` (id, parentId, position, parentPosition, value1, value2, value3) VALUES (?,?,?,?,?,?,?)");
    $response->execute([null, 1, $navbarPosition, $parentPosition, "fa-list", 'ws_addonsLists_suscribed', "/vbcms-admin/workshop/suscribed"]);
    $parentPosition++;
    $response = $bdd->prepare("INSERT INTO `vbcms-adminNavbar` (id, parentId, position, parentPosition, value1, value2, value3) VALUES (?,?,?,?,?,?,?)");
    $response->execute([null, 1, $navbarPosition, $parentPosition, "fa-sync", 'ws_updatesAddons', "/vbcms-admin/workshop/updates"]);
    $parentPosition++;
    $response = $bdd->prepare("INSERT INTO `vbcms-adminNavbar` (id, parentId, position, parentPosition, value1, value2, value3) VALUES (?,?,?,?,?,?,?)");
    $response->execute([null, 1, $navbarPosition, $parentPosition, "fa-plus", 'ws_createAddon', "https://workshop.vbcms.net/create"]);
    $parentPosition++;
    $response = $bdd->prepare("INSERT INTO `vbcms-adminNavbar` (id, parentId, position, parentPosition, value1, value2, value3) VALUES (?,?,?,?,?,?,?)");
    $response->execute([null, 1, $navbarPosition, $parentPosition, "fa-wrench", 'ws_manage', "/vbcms-admin/workshop/manage"]);
    $parentPosition++;

    // Ajoute la catégorie
	$response = $bdd->prepare("INSERT INTO `vbcms-adminNavbar` (id, parentId, position, parentPosition, value1, value2, value3) VALUES (?,?,?,?,?,?,?)");
    $response->execute([null, 0, $navbarPosition, 0, "vbcms", 'websitePersonalize', ""]);
    $navbarPosition++;

    $response = $bdd->prepare("SELECT * FROM `vbcms-adminNavbar` WHERE value2 = ? AND parentPosition = 0"); // Je récupère l'id du dossier parent
    $response->execute(['websitePersonalize']);
    $parentInfos = $response->fetch(PDO::FETCH_ASSOC);

    // Ajoute le lien
    $parentPosition = 1;
    $response = $bdd->prepare("INSERT INTO `vbcms-adminNavbar` (id, parentId, position, parentPosition, value1, value2, value3) VALUES (?,?,?,?,?,?,?)");
    $response->execute([null, $parentInfos["id"], $navbarPosition, $parentPosition, "fa-paint-roller", 'changeTheme', "/vbcms-admin/personalize/changeTheme"]);
    $parentPosition++;
    $response = $bdd->prepare("INSERT INTO `vbcms-adminNavbar` (id, parentId, position, parentPosition, value1, value2, value3) VALUES (?,?,?,?,?,?,?)");
    $response->execute([null, $parentInfos["id"], $navbarPosition, $parentPosition, "fa-list", 'modifyNavbar', "/vbcms-admin/personalize/modifyNavbar"]);
    $parentPosition++;


	// Détecte l'adresse du site internet
	if(isset($_SERVER['HTTPS'])) $http = "https"; else $http = "http";
	$url = parse_url("$http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
	$websiteUrl = "$http://$_SERVER[HTTP_HOST]".str_replace("install.php", "", $url["path"]);

	$response = $bdd->prepare("INSERT INTO `vbcms-settings` (name, value) VALUES (?,?)");
	$response->execute(["websiteUrl", $websiteUrl]);

	echo "finished";


} else {
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>VBcms | Installation</title>
	<meta name="theme-color" content="#BF946F">
	<meta name="author" content="Sofiane Lasri">
	<link rel="icon" href="https://vbcms.net/vbcms-admin/images/vbcms-logo/raccoon-in-box-128x.png" type="image/png">

	<meta content="VBcms" property="og:title">
	<meta content="Installation de VBcms" property="og:description">
	<meta content='https://vbcms.net/vbcms-admin/images/vbcms-logo/raccoon-in-box-512x.png' property='og:image'>

	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
	<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
	<!-- Intégration de JS Snackbar -->
	<link rel="stylesheet" href="https://vbcms.net/vbcms-admin/vendors/js-snackbar/css/js-snackbar.css?v=2.0.0" />
	<script src="https://vbcms.net/vbcms-admin/vendors/js-snackbar/js/js-snackbar.js?v=1.2.0"></script>

	<link rel="stylesheet" href="https://vbcms.net/vbcms-admin/vendors/pick-a-color/css/pick-a-color-1.2.3.min.css">

	<link rel="stylesheet" type="text/css" href="https://vbcms.net/vbcms-admin/fonts/fonts.css">
</head>
<body>
	<style type="text/css">
		:root{
		  --mainBrown: #bf946f;
		  --secondaryBrown: #a77c58;
		  --darkBrown: #74492a;
		  --darkerBrown: #5c351f;

		  --lightMB: #d3ab89;
		}

		::-webkit-scrollbar {
		    width: 5px;
		    height: 7px;
		}
		::-webkit-scrollbar-button {
		    width: 0px;
		    height: 0px;
		}
		::-webkit-scrollbar-corner {
		    background: transparent;
		}
		::-webkit-scrollbar-thumb {
		    background: #525965;
		    border: 0px none #ffffff;
		    border-radius: 0px;
		}
		::-webkit-scrollbar-track {
		    background: transparent;
		    border: 0px none #ffffff;
		    border-radius: 50px;
		}
		html{
			height: 100%
		}
		body{
			font-size: 14px;
		    font-family: 'Inter', sans-serif;

			/*background-image: url("https://vbcms.net/vbcms-admin/images/general/vbcms-illustration1.jpg");*/
			background: rgb(167,124,88);
			background: -moz-linear-gradient(180deg, rgba(167,124,88,1) 0%, rgba(116,73,42,1) 100%);
			background: -webkit-linear-gradient(180deg, rgba(167,124,88,1) 0%, rgba(116,73,42,1) 100%);
			background: linear-gradient(180deg, rgba(167,124,88,1) 0%, rgba(116,73,42,1) 100%);
			background-size: cover;
			background-position: center;
		}
		ul{
			list-style: none;
		}
		.btn-brown {
		  color: #fff;
		  background-color: var(--mainBrown);
		  border-color: var(--mainBrown);
		}

		.btn-brown:hover {
		  color: #fff;
		  background-color: var(--lightMB);
		  border-color: var(--lightMB);
		}

		.btn-brown:focus, .btn-brown.focus {
		  color: #fff;
		  background-color: var(--darkerBrown);
		  border-color: var(--darkerBrown);
		  box-shadow: 0 0 0 0.2rem rgba(167, 124, 88, 0.5);
		}

		.btn-brown.disabled, .btn-brown:disabled {
		  color: #fff;
		  background-color: var(--secondaryBrown);
		  border-color: var(--secondaryBrown);
		}

		.btn-brown:not(:disabled):not(.disabled):active, .btn-brown:not(:disabled):not(.disabled).active,
		.show > .btn-brown.dropdown-toggle {
		  color: #fff;
		  background-color: var(--secondaryBrown);
		  border-color: var(--secondaryBrown);
		}

		.btn-brown:not(:disabled):not(.disabled):active:focus, .btn-brown:not(:disabled):not(.disabled).active:focus,
		.show > .btn-brown.dropdown-toggle:focus {
		  box-shadow: 0 0 0 0.2rem rgba(167, 124, 88, 0.5);
		}

		.installDiv{
			position: absolute;
			left: 50%;
			top: 50%;
			transform: translate(-50%, -50%);

			width: 1000px;
			height: 600px;
			background-color: white;
			border-radius: 5px;
			overflow: hidden;
			box-shadow: 0px 0px 5px 0px rgba(0,0,0,0.5);
		}
		.installDiv .header{
			position: relative;
			display: flex;
			width: 100%;
			height: 50px;
			background-color: var(--mainBrown);
			align-items: center;
		}
		.installDiv .header img{
			margin-left: 5px;
			margin-right: 5px;
		}
		.installDiv .header span{
			font-family: "Linotype Kaliber Bold";
			font-size: 1.2em;
			color: white;
		}
		#installStateTitle{
			position: absolute;
			width: 100%;
			text-align: center;
		}
		.installContent{
			height: 100%;
		}
		.centerItems{
			position: absolute;
			margin: 0;
			top: 50%;
			left: 50%;
			transform: translate(-50%, -50%);
			width: 100%;

			display: flex;
			flex-direction: column;
			align-items: center;
			justify-content: center;
			text-align: center;
		}
		.installNavButtons{
			position: absolute;
			bottom: 0;
			width: 100%;
		}
		.progress{
			height: 5px !important;
			border-radius: 0px!important;
			background-color: #e6e6e6
		}
		.progress-bar{
			background-color: var(--lightMB) !important;
		}
	</style>

	<div class="installDiv">
		<div class="header">
			<img height="45" src="https://vbcms.net/vbcms-admin/images/vbcms-logo/raccoon-in-box-128x.png" alt="vbcms-logo">
			<span>VBcms</span><span id="installStateTitle">Bienvenu</span>
		</div>
		<?php 
		if (!isset($_SESSION["user_id"])){ ?>
		<div id="install-step-0" title="" class="content" style="display: none;">
			<div class="centerItems">
				<img src="https://vbcms.net/vbcms-admin/images/vbcms-logo/raccoon-in-box-128x.png" alt="vbcms-logo">
				<h3>Bienvenu sur VBcms!</h3>
				<p>Merci de participer aux tests de pré-release! <br>Le panel actuel ne reflète qu'assez peu le travail final, de nombreuses fonctionnalités vont être ajouté dans les semaines à suivre.
				<br><br><?=$ip?><br>
				Pour te remercier de ton investissement, une liscence t'as été offerte. Tu as juste à te connecter pour procéder à l'installation. ^^</p>
			</div>
		</div>
		<div id="install-step-1" title="" class="content" style="display: none;">
			<div class="centerItems">
				<img src="https://vbcms.net/vbcms-admin/images/vbcms-logo/raccoon-in-box-128x.png" alt="vbcms-logo">
				<h3>Se connecter</h3>
				<a id="connectToVBcmsLink" class="btn btn-brown" href="">Connexion</a>
			</div>
		</div>

		<?php } elseif(isset($_SESSION["user_id"])&&in_array($_SESSION["user_role"], ["owner", "admin"])) { ?>

		<div id="install-step-0" title="Connexion réussie!" class="content" style="display: none;">
			<div class="centerItems">
				<img class="rounded-circle mb-3" src="<?=$_SESSION['user_profilePic']?>" alt="user-logo">
				<h3>Salut <?=$_SESSION['user_username']?>!</h3>
				<p>Nous allons maintenant pouvoir passer à l'installation du panel! :D</p>
			</div>
		</div>
		<div id="install-step-1" title="1. Dépendances" class="content" style="display: none;">
			<div class="p-2">
				<h3>1. Vérification des dépendances</h3>
				<p>Il est important de posséder toutes les dépendances requises afin d'assurer le bon fonctionnement de VBcms.
					<ul>
						<li class="<?php if(PHP_VERSION_ID>=70200) {echo "text-success"; $depedencies++; }else echo "text-danger"; ?>"><strong>PHP ⩾ 7.2</strong> <code><?=phpversion()?></code></li>
						<?php
						if (defined('PDO::ATTR_DRIVER_NAME')) {
							$depedencies++; 
							echo '<li class="text-success"><strong>PDO est installé</strong></li>';
						} else{
							echo '<li class="text-danger"><strong>PDO n\'est pas installé</strong></li>';
						}
						if (extension_loaded('session')) {
							$depedencies++; 
							echo '<li class="text-success"><strong>php-session est chargé</strong></li>';
						} else{
							echo '<li class="text-danger"><strong>php-session n\'est pas chargé</strong></li>';
						}
						?>
						<li></li>
					</ul>
				</p>
			</div>
		</div>

		<?php if ($depedencies==3){
			$savedParameters = file_get_contents("tempInstallConfig");
			if ($savedParameters) {
				$savedParameters = json_decode($savedParameters);
				$bddHost = $savedParameters[0]; //Adresse du serveur MySQL
				$bddName = $savedParameters[1]; //Nom de la base de donnée
				$bddUser = $savedParameters[2]; //Utilisateur
				$bddMdp = $savedParameters[3]; //Mot de passe
			} else {
				$bddHost = "";
				$bddUser = "";
				$bddMdp = "";
				$bddName = ""; 
			}
		?>
		<div id="install-step-2" title="2. Connexion à la base de donnée" class="content" style="display: none;">
			<div class="p-2">
				<h3>2. Connexion à la base de donnée</h3>
				<p>La connexion à la base de donnée n'est pas compliquée. Obtenir les informations de connexion peut cependant l'être un peu plus.</p>
				<form class="w-50" id="databaseConn" action="">
					<div class="form-group">
						<label>Hôte de la base de donnée</label>
						<input required class="form-control" value="<?=$bddHost?>" type="text" id="databaseHost">
						<small class="form-text text-muted">Souvent localhost sur les serveurs auto-hébergés</small>
					</div>
					<div class="form-group">
						<label>Nom de la base de donnée</label>
						<input required class="form-control" value="<?=$bddName?>" type="text" id="databaseName">
					</div>
					<div class="form-group">
						<label>Utilisateur</label>
						<input required class="form-control" value="<?=$bddUser?>" type="text" id="databaseUser">
					</div>
					<div class="form-group">
						<label>Mot de passe</label>
						<input class="form-control" value="<?=$bddMdp?>" type="password" id="databasePass">
					</div>
				</form>
				<button id="testDatabaseConn" onclick="databaseTest()" class="m-1 btn btn-brown float-left">Tester la connexion</button>				
			</div>
		</div>
		<div id="install-step-3" title="3. Configuration rapide du site" class="content" style="display: none;">
			<div class="p-2">
				<h3>3. Configuration rapide du site</h3>
				<p>Afin de gagner du temps, tu dois remplir ces quelques informations à propos de ton futur site. ^^</p>

				<div class="w-50">
					<div class="form-group">
						<label>Nom du site internet</label>
						<input required class="form-control" value="<?=$savedParameters[4]?>" type="text" id="websiteName">
					</div>
					<div class="form-group">
						<label>Brève description du site (pour le référencement)</label>
						<input class="form-control" value="<?=$savedParameters[5]?>" type="text" id="websiteDesc">
					</div>
					<div class="form-group">
						<label>Thème couleur de votre site</label>
						<input type="text" value="#bf946f" id="websiteColor" class="pick-a-color form-control">
					</div>
					<div class="form-group">
						<label>Clé API Steam</label>
						<input class="form-control" value="<?=$savedParameters[7]?>" type="text" id="steamApiKey">
						<small class="form-text text-muted">Non obligatoire selon les utilisations</small>
					</div>
				</div>
			</div>
		</div>
		<div id="install-step-4" title="4. Téléchargement de VBcms" class="content" style="display: none;">
			<div class="p-2">
				<h3>4. Téléchargement de VBcms</h3>
				<p>Comme tu as pu le remarquer, tu n'a téléchargé que cette petite page internet. ^^'<br>
				Mais ne t'inquiète pas c'est normal, c'est pour que tu puisse bénéficier de la dernière version du panel sans que tu ai à le mettre à jour! :D</p>
			</div>
		</div>
		<div id="install-step-5" title="5. Création de la base de donnée" class="content" style="display: none;">
			<div class="p-2">
				<h3>5. Création de la base de donnée</h3>
				<p>Veuillez patienter...</p>
			</div>
		</div>
		<div id="install-step-6" title="6. Vérifications additionnelles" class="content" style="display: none;">
			<div class="p-2">
				<h3>6. Vérifications additionnelles</h3>
				<p>On sait jamais, ça peut ne pas marcher x)</p>
			</div>
		</div>
		<?php } ?>
		<?php } ?>

		<div class="installNavButtons">
			<div class="progress">
			  <div id="progress-bar" class="progress-bar" role="progressbar" style="width: 1%"></div>
			</div>
			<div class="progressBar">
				<div class="actualProgress"></div>
				<div class="finalProgress"></div>
			</div>
			<div class="navBtn">
				<button id="prevBtn" onclick="previousStep()" class="m-1 btn btn-brown float-left">Précédent</button>
				<button id="nextBtn" onclick="nextStep()" class="m-1 btn btn-brown float-right">Suivant</button>
			</div>
		</div>
	</div>
	<script src="https://vbcms.net/vbcms-admin/vendors/pick-a-color/js/tinycolor-0.9.15.min.js"></script>
	<script src="https://vbcms.net/vbcms-admin/vendors/pick-a-color/js/pick-a-color-1.2.3.min.js"></script>
	<script type="text/javascript">
		$( document ).ready(function() {
			var url = new URL(window.location.href);
			var search_params = url.searchParams;
			if(search_params.get('step')==null){
				search_params.append('step', '0');
				var new_url = url.toString();
				window.history.replaceState({}, '',new_url);
				showStep(0);
			} else {
				showStep(search_params.get('step'));
			}

			$("#connectToVBcmsLink").attr("href", "https://vbcms.net/manager/login?from="+encodeURIComponent(window.location.href)+"&firstInstall");
		});

		function nextStep(){
			var url = new URL(window.location.href);
			var search_params = url.searchParams;
			id = parseInt(search_params.get('step'), 10);
			$("#install-step-"+(id)).css("display", "none");

			var url = new URL(window.location.href);
			var search_params = url.searchParams;
			search_params.set('step', id+1);
			var new_url = url.toString();
			window.history.replaceState({}, '',new_url);

			showStep(id+1);
		}
		function previousStep(){
			var url = new URL(window.location.href);
			var search_params = url.searchParams;
			id = parseInt(search_params.get('step'), 10);
			$("#install-step-"+(id)).css("display", "none");

			var url = new URL(window.location.href);
			var search_params = url.searchParams;
			search_params.set('step', id-1);
			var new_url = url.toString();
			window.history.replaceState({}, '',new_url);

			showStep(id-1);
		}
		async function showStep(id){
			id = parseInt(id, 10);

			if(!$("#install-step-"+(id-1)).length){
				$("#prevBtn").css("display", "none");
			}else{
				$("#prevBtn").css("display", "block");
			}

			if(!$("#install-step-"+(id+1)).length){
				$("#nextBtn").css("display", "none");
			}else{
				$("#nextBtn").css("display", "block");
			}

			if ($("#install-step-"+(id)).attr("title").length) {
				$("#installStateTitle").html($("#install-step-"+(id)).attr("title"));
			}

			if (id==0) {
				$("#progress-bar").css("width", "1%");
			} else if (id==1) {
				$("#progress-bar").css("width", "2%");
			} else if (id==2) {
				$("#progress-bar").css("width", "5%");
				$("#nextBtn").css("display", "none");
			} else if (id==3) {
				$("#progress-bar").css("width", "15%");
				$(".pick-a-color").pickAColor();
			} else if (id==4) {
				var websiteConfig = [];
				websiteConfig.push($("#websiteName").val(), $("#websiteDesc").val(), $("#websiteColor").val(), $("#steamApiKey").val());
				$.get("install.php?saveWebsiteConfig="+JSON.stringify(websiteConfig), function(data) {});
				$("#progress-bar").css("width", "25%");
			} else if (id==5) {
				$("#progress-bar").css("width", "80%");
				$.get("install.php?createDatabase", function(data) {
					console.log("data="+data);
					if (data!="finished") {
						SnackBar({
	                        message: "Echec lors de la création de la base de donnée: "+data,
	                        status: "danger",
	                        timeout: false
	                    });
					}else{
						nextStep();
					}
				});
			} else if (id==6) {
				$("#progress-bar").css("width", "95%");;
			}

			$("#install-step-"+id).css("display", "block");
		}

		function databaseTest(){
			var databaseInfos = [];
			databaseInfos.push($("#databaseHost").val(), $("#databaseName").val(), $("#databaseUser").val(), $("#databasePass").val());
			$.get("install.php?testBdd="+JSON.stringify(databaseInfos), function(data) {
				if (data=="") {
					$("#nextBtn").css("display", "block");
					SnackBar({
                        message: "Connexion réussie!",
                        status: "success"
                    });
				} else {
					$("#nextBtn").css("display", "none");
					SnackBar({
                        message: "Echec lors de la connexion: "+data,
                        status: "danger",
                        timeout: false
                    });
				}
			});
		}
	</script>
</body>
</html>
<?php } ?>