<?php
require_once '../vbcms-core/core.php';

if (isset($_GET["from"])) {
	$redirect = urlencode($_GET["from"]);
} else {
	$redirect = urlencode("$http://$_SERVER[HTTP_HOST]/vbcms-admin");
}

// Traite les informations du formulaire de connexion
if (isset($_POST['login'])) { // Ne s'éxécute que si le formulaire de connexion est utilisé
    if ((isset($_POST['username']) AND !empty($_POST['username'])) AND (isset($_POST['password']) AND !empty($_POST['password']))){

		// Permet de savoir si l'utilisateur s'est connecté avec une adresse mail
		if(strpos($_POST['username'], '@') !== false) {
			$response = $bdd->prepare("SELECT * FROM `vbcms-localAccounts` WHERE email=?");
		} else {
			$response = $bdd->prepare("SELECT * FROM `vbcms-localAccounts` WHERE username=?");
		}
		$response->execute([$_POST['username']]);
		$user = $response->fetch(PDO::FETCH_ASSOC);
		if(!empty($user)){
			if(hash_equals($user["password"], crypt($_POST["password"], $user["password"]))){
				$_SESSION['loginType'] = "local";
				$_SESSION['user_id'] = $user['id'];
				$_SESSION['user_username'] = $user['username'];
				$_SESSION['user_role'] = $user['role'];
				$_SESSION['user_profilePic'] = "VBcmsGetSetting("websiteUrl")/vbcms-admin/images/misc/programmer.png";
				$geoPlugin_array = unserialize( file_get_contents('http://www.geoplugin.net/php.gp?ip=' . $_SERVER['REMOTE_ADDR']) );
				$_SESSION['language'] = $geoPlugin_array['geoplugin_countryCode'];
				header('Location: '.urldecode($redirect));
			} else {
				$error = "Vous avez renseigné un mauvais couple identifiant/mot de passe.";
			}
		} else {
			$error = "Vous avez renseigné un mauvais couple identifiant/mot de passe.";
		}
        
    } else {
        $error = "Remplis tous les champs stp";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Connexion</title>
	<?php include 'includes/depedencies.php';?>
	<link rel="stylesheet" type="text/css" href="css/login.css">
</head>
<body>
	<body>
	<div class="loginForm d-flex flex-column p-2 rounded animate__animated animate__fadeIn">
		<div class="brand-name align-self-center">
			<img src="images/vbcms-logo/raccoon-in-box-128x.png">
			VBcms
		</div>
		<div class="align-self-center text-center mt-3">
			<p>Veuillez vous connecter pour accéder à l'administration</p>
			<a id="connectToVBcmsLink" class="btn btn-brown" href="https://api.vbcms.net/auth.php?login&from=<?=$redirect?>">Connexion</a>
			<a href="#" id="showLogin" class="text-brown">Utiliser un compte local</a>
			<div id="loginDiv" class="mt-3" style="display: none;">
			<?php
			if(isset($error) && !empty($error)){
				echo '<div class="alert alert-danger mt-2" role="alert">'.$error.'</div>';
			}
			?>
				<form id="form" method="post" action="login.php?from=<?=$redirect?>">
					<div class="form-group mt">
						<input class="form-control" type="text" name="username" placeholder="Nom d'utilisateur/Email" required>
					</div>
					<div class="form-group">
						<input class="form-control" type="password" name="password" placeholder="Mot de passe" required>
					</div>
					<button type="submit" name="login" class="btn btn-brown">Se connecter</button>
				</form>
			</div>
		</div>
	</div>
	<script type="text/javascript">
		$("#showLogin").on("click", function(){
			$("#loginDiv").css("display","block");
		});
	</script>
</body>
</html>