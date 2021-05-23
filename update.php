
if (isset($_GET["silentUpdate"])) {
	// Ici on met à jour la base de donnée
	include 'vbcms-config.php';
	$bdd = new PDO("mysql:host=$bddHost;dbname=$bddName", $bddUser, $bddMdp);

	$response = $bdd->prepare("INSERT INTO `vbcms-settings` (name, value) VALUES (?,?)");
	$response->execute(["autoUpdate","1"]);
	$response = $bdd->prepare("INSERT INTO `vbcms-settings` (name, value) VALUES (?,?)");
	$response->execute(["debugMode","0"]);
	
	$response=$bdd->prepare("UPDATE `vbcms-settings` SET value = ? WHERE name = 'vbcmsVersion'");
	$response->execute([$vbcmsVer]);
} elseif(isset($_GET["deleteUpdateFile"])){
	unlink('update.php');
}else{

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>VBcms | Mise à jour</title>
	<meta name="theme-color" content="#BF946F">
	<meta name="author" content="Sofiane Lasri">
	<link rel="icon" href="https://vbcms.net/vbcms-admin/images/vbcms-logo/raccoon-in-box-128x.png" type="image/png">

	<meta content="VBcms" property="og:title">
	<meta content="Mise à jour de VBcms" property="og:description">
	<meta content='https://vbcms.net/vbcms-admin/images/vbcms-logo/raccoon-in-box-512x.png' property='og:image'>

	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
	<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
	<!-- Intégration de JS Snackbar -->
	<link rel="stylesheet" href="https://vbcms.net/vbcms-admin/vendors/js-snackbar/css/js-snackbar.css?v=2.0.0" />
	<script src="https://vbcms.net/vbcms-admin/vendors/js-snackbar/js/js-snackbar.js?v=1.2.0"></script>

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
	</style>
	<div class="installDiv">
		<div class="header">
			<img height="45" src="https://vbcms.net/vbcms-admin/images/vbcms-logo/raccoon-in-box-128x.png" alt="vbcms-logo">
			<span>VBcms</span><span id="installStateTitle">Mise à jour en cours</span>
		</div>
		<div class="content">
			<div class="p-2">
				<h3>Nous configurons votre installation</h3>
				<p>Veuillez patienter quelques instants, vous serez automatiquement redirigé une fois la configuration terminée. :D</p>
			</div>
		</div>
	</div>

	<script type="text/javascript">
		$( document ).ready(function() {
			$.get("update.php?silentUpdate", function(data) {
				console.log("data="+data);
				if (data!="") {
					SnackBar({
                        message: "Echec lors de la configuration du site: "+data,
                        status: "danger",
                        timeout: false
                    });
				}else{
					$.get("update.php?deleteUpdateFile", function(data) {
						console.log("data="+data);
						if (data!="") {
							SnackBar({
		                        message: "La mise à jour a réussie, mais nous n'avons pas réussi à supprimer update.php: "+data,
		                        status: "danger",
		                        timeout: false
		                    });
						}else{
							window.location.replace("/vbcms-admin");
						}
					});
					
				}
			});
		});
	</script>
</body>
</html>
<?php } ?>