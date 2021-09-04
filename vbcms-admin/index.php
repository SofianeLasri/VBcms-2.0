<?php
require_once '../vbcms-core/core.php';

$isUpToDate = $bdd->query("SELECT value FROM `vbcms-settings` WHERE name = 'upToDate'")->fetchColumn();
if ($isUpToDate == 1) {
	$updateMessage = translate("isUpToDate");
} else {
	$updateMessage = translate("isNotUpToDate");
}
if($_SESSION['auth']=='vbcms.net'){
	$userHasLocalAccount = $bdd->prepare("SELECT * FROM `vbcms-localAccounts` WHERE netIdAssoc = ?");
	$userHasLocalAccount->execute([$_SESSION['netId']]);
	$userHasLocalAccount = $userHasLocalAccount->fetch(PDO::FETCH_ASSOC);

	if(empty($userHasLocalAccount)){
		if(isset($_POST['localUserUsername']) && !empty($_POST['localUserUsername'])){
			$query = $bdd->prepare('INSERT INTO `vbcms-localAccounts` (`netIdAssoc`, `username`, `password`) VALUES (?,?,?)');
			$query->execute([$_SESSION['netId'], $_POST['localUserUsername'], password_hash($_POST['localUserPassword1'], PASSWORD_DEFAULT)]);

			$userHasLocalAccount = $bdd->prepare("SELECT * FROM `vbcms-localAccounts` WHERE netIdAssoc = ?");
			$userHasLocalAccount->execute([$_SESSION['netId']]);
			$userHasLocalAccount = $userHasLocalAccount->fetch(PDO::FETCH_ASSOC);
			if(empty($userHasLocalAccount)){
				$localAccountCreationSuccess=false;
			}else{
				$localAccountCreationSuccess=true;
			}
		} else {
			$showLocalAccountCreationModal = true;
		}
	}
}


?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title><?=VBcmsGetSetting("websiteName")?> | <?=translate("dashboard")?></title>
	<?php include 'includes/depedencies.php';?>
</head>
<body>
	<?php 
	include ('includes/navbar.php');
	?>

	<!-- Contenu -->
	<div class="dashboardTopCard" leftSidebar="240" rightSidebar="0">
		<div class="d-flex">
			<div class="userLogo" style="background-image: url('<?=$_SESSION['user_profilePic']?>');"></div>
			<div class="ml-5">
				<h3>Bienvenue <?=$_SESSION["user_username"]?>!</h3>
				<p><?=$updateMessage?>
				<br><strong>Vous êtes sur une version de développement de VBcms.</strong></p>
			</div>
		</div>
	</div>
	<div class="page-content notTop" leftSidebar="240" rightSidebar="0">
		<h3>Tableau de bord</h3>
		<p>Bienvenu sur le paneau d’administration. Voici un bref résumé de l'activité de cette semaine.</p>

		<!-- Debut de la liste des cartes -->
		<!--
		<div id="indexCards" class="row" style="">
			<div class="col-xl-6">
				<div id="indexCardOne" class="indexCard mb-3 overflow-hidden">
					<div style="position: absolute; z-index: 1;height: 100%; width: 100%; background-image: url(<?=VBcmsGetSetting("websiteUrl")?>vbcms-admin/images/misc/mainIndexCardBg.png); background-position: right bottom; background-repeat: no-repeat; background-size: 100%;"></div>
					<div class="indexCardBody">
						<h5>Nouvelles licences</h5>
						<h3>15</h3>
						<p class="subText">+15% que la semaine dernière</p>
						<a href="#" class="btn btn-light">Voir les liscences</a>
					</div>
				</div>
			</div>
			<div class="col-xl-6">
				<div class="row">
					<div class="col-sm-6">
						<div id="indexCardTwo" class="indexCard overflow-hidden">
							<div class="indexCardImg">
								<img src="<?=VBcmsGetSetting("websiteUrl")?>vbcms-admin/images/misc/globe.png">
							</div>
							<div class="indexCardBody">
								<h5>Visites</h5>
								<h3>20</h3>
								<p class="subText">+5% que la semaine dernière</p>
								<a href="#" class="text-light">Voir les statistiques <i class="fas fa-arrow-right"></i></a>
							</div>
						</div>
						<div id="indexCardThree" class="indexCard overflow-hidden">
							<div class="indexCardImg">
								<img src="<?=VBcmsGetSetting("websiteUrl")?>vbcms-admin/images/misc/ticket.png">
							</div>
							<div class="indexCardBody">
								<h5>Tickets support</h5>
								<h3>4</h3>
								<p class="subText">14 depuis le début de la semaine</p>
								<a href="#" class="text-light">Voir les tickets <i class="fas fa-arrow-right"></i></a>
							</div>
						</div>
					</div>
					<div class="col-sm-6">
						<div id="indexCardFour" class="indexCard overflow-hidden">
							<div class="indexCardImg">
								<img src="<?=VBcmsGetSetting("websiteUrl")?>vbcms-admin/images/misc/puzzle.png">
							</div>
							<div class="indexCardBody">
								<h5>Nouveaux addons</h5>
								<h3>15</h3>
								<p class="subText">+15% que la semaine dernière</p>
								<a href="#" class="text-light">Voir les addons <i class="fas fa-arrow-right"></i></a>
							</div>
						</div>
						<div id="indexCardFive" class="indexCard overflow-hidden">
							<div class="indexCardImg">
								<img src="<?=VBcmsGetSetting("websiteUrl")?>vbcms-admin/images/misc/hourglass.png">
							</div>
							<div class="indexCardBody">
								<h5>Inscriptions en attente</h5>
								<h3>15</h3>
								<p class="subText">+15% que la semaine dernière</p>
								<a href="#" class="text-light">Voir les inscriptions <i class="fas fa-arrow-right"></i></a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		-->
		<!-- Fin de la liste des cartes -->
		
		<div class="mt-3">
			<h3>Merci de participer aux tests!</h3>
			<p>Salut et merci d'avoir accepté de participer aux tests de pré-sortie. Tu es là sur une version très primaire de ce que sera VBcms 2.0 à la fin, et comme tu l'as peut-être déjà remarqué, il n'y a pas grand chose de terminé.</p><br>
			<?php print_r($_SESSION); ?>
			
		</div>

		
		
	</div>

	<?php
		if(isset($showLocalAccountCreationModal) && $showLocalAccountCreationModal){ ?>

		<div class="modal fade" id="localAccountCreationModal" data-backdrop="static">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header bg-brown text-white">
						<h5 id="extensionActivationModalTitle" class="modal-title"><?=translate('localAccountCreation')?></h5>
					</div>
					<div class="modal-body">
						<form method="post" class="needs-validation" novalidate>
							<div class="form-group">
								<label><?=translate('username')?></label>
								<input type="text" class="form-control" name="localUserUsername" placeholder="<?=$_SESSION['user_username']?>" value="<?=$_SESSION['user_username']?>" required>
								<small class="form-text text-muted"><?=translate("localAccountCreation_loginCanBeDifferent")?></small>
								<div class="invalid-feedback"><?=translate("localAccountCreation_pleaseEnterLogin")?></div>
							</div>
							<div class="form-group">
								<label><?=translate('password')?></label>
								<input type="password" class="form-control" name="localUserPassword1" id="localUserPassword1" placeholder="" required>
								<div class="invalid-feedback" id="localUserPassword1Alert">
									<?=translate('localAccountCreation_youCreateAnAccountWithoutPassword')?> <img height="16" src="<?=VBcmsGetSetting("websiteUrl")?>vbcms-admin/images/emojis/thinkingHard.png">
								</div>
							</div>
							<div class="form-group">
								<label><?=translate('repeatPassword')?></label>
								<input type="password" class="form-control" name="localUserPassword2" id="localUserPassword2" placeholder="" required>
								<div class="invalid-feedback" id="localUserPassword2Alert"><?=translate("localAccountCreation_pleaseRewriteYourPassword")?></div>
							</div>

							<div>
								<h5><?=translate("whyCreateALocalAccount")?></h5>
								<p>Autant le dire tout de suite, les serveurs de VBcms ne sont pas réputés pour être très fiables... Il sera assez fréquent de les voir inaccessibles, surtout à ce stade du développement.<br><br><strong>Le compte local te permettera d'accéder au panneau d'administration, même en cas de panne générale.</strong> Tu ne pourras pas télécharger d'extensions ni mettre VBcms à jour, mais au moins tu pourras continuer à gérer ton site. :D</p>
							</div>
						</div>
						<div class="modal-footer">
							<button id="registerBtn" type="submit" class="btn btn-brown"><?=translate("create")?></button>
						</div>
					</form>
				</div>
			</div>
		</div>

		<script type="text/javascript">
			(function() {
			'use strict';
			window.addEventListener('load', function() {
				// Fetch all the forms we want to apply custom Bootstrap validation styles to
				var forms = document.getElementsByClassName('needs-validation');
				// Loop over them and prevent submission
				var validation = Array.prototype.filter.call(forms, function(form) {
				form.addEventListener('submit', function(event) {
					if (form.checkValidity() === false) {
					event.preventDefault();
					event.stopPropagation();
					}
					form.classList.add('was-validated');
				}, false);
				});
			}, false);
			})();

			$( document ).ready(function() {
				$('#localAccountCreationModal').modal('show');
			});

			$("#localUserPassword1").change(function() {
				checkPassword();
			});
			$("#localUserPassword2").change(function() {
				checkPassword();
			});

			function checkPassword(){
				if ($("#localUserPassword1").val()!=$("#localUserPassword2").val()) {
					$("#localUserPassword1Alert").html("<?=translate("localAccountCreation_passwordsDontMatches")?>");
					$("#localUserPassword1Alert").css("display","block");
					$("#localUserPassword2Alert").html("<?=translate("localAccountCreation_passwordsDontMatches")?>");
					$("#localUserPassword2Alert").css("display","block");
					$("#registerBtn").attr("disabled", "");
				} else {
					var passw = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,32}$/;
					if($("#localUserPassword1").val().match(passw)) { 
						$("#localUserPassword1Alert").html('<?=translate('localAccountCreation_youCreateAnAccountWithoutPassword')?> <img height="16" src="<?=VBcmsGetSetting("websiteUrl")?>vbcms-admin/images/emojis/thinkingHard.png">');
						$("#localUserPassword1Alert").css("display","");
						$("#localUserPassword2Alert").html('<?=translate("localAccountCreation_pleaseRewriteYourPassword")?>');
						$("#localUserPassword2Alert").css("display","");
						$("#registerBtn").removeAttr("disabled");
					} else { 
						$("#localUserPassword1Alert").html("<?=translate("localAccountCreation_yourPasswordIsTooWeak")?>");
						$("#localUserPassword1Alert").css("display","block");
						$("#localUserPassword2Alert").html("<?=translate("localAccountCreation_yourPasswordIsTooWeak")?>");
						$("#localUserPassword2Alert").css("display","block");
						$("#registerBtn").attr("disabled", "");
					}
					
				}
			}
		</script>
		<?php }
		
		if(isset($localAccountCreationSuccess) && $localAccountCreationSuccess){ ?>
		<script type="text/javascript">
			SnackBar({
				message: "<?=translate('localAccountCreation_success')?>",
				status: "success"
			});
		</script>
		<?php }if(isset($localAccountCreationSuccess) && !$localAccountCreationSuccess){ ?>
		<script type="text/javascript">
			SnackBar({
				message: "<?=translate('localAccountCreation_error')?>",
				status: "danger",
				timeout: false
			});
		</script>
		<?php } ?>
</body>
</html>