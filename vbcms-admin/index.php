<?php
include 'includes/header.php';

$isUpToDate = $bdd->query("SELECT value FROM `vbcms-settings` WHERE name = 'upToDate'")->fetchColumn();
if ($isUpToDate == 1) {
	$updateMessage = $translation["isUpToDate"];
} else {
	$updateMessage = $translation["isNotUpToDate"];
}

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title><?=$websiteName?> | <?=$translation["dashboard"]?></title>
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
				<br><strong>Vous avez 3 notifications</strong></p>
			</div>
		</div>
	</div>
	<div class="page-content notTop" leftSidebar="240" rightSidebar="0">
		<h3>Tableau de bord</h3>
		<p>Bienvenu sur le paneau d’administration. Voici un bref résumé de l'activité de cette semaine.</p>

		<!-- Debut de la liste des cartes -->
		<div id="indexCards" class="row" style="">
			<div class="col-xl-6">
				<div id="indexCardOne" class="indexCard mb-3 overflow-hidden">
					<div style="position: absolute; z-index: 1;height: 100%; width: 100%; background-image: url(<?=$websiteUrl?>vbcms-admin/images/misc/mainIndexCardBg.png); background-position: right bottom; background-repeat: no-repeat; background-size: 100%;"></div>
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
								<img src="<?=$websiteUrl?>vbcms-admin/images/misc/globe.png">
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
								<img src="<?=$websiteUrl?>vbcms-admin/images/misc/ticket.png">
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
								<img src="<?=$websiteUrl?>vbcms-admin/images/misc/puzzle.png">
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
								<img src="<?=$websiteUrl?>vbcms-admin/images/misc/hourglass.png">
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
		<!-- Fin de la liste des cartes -->

		<div class="mt-3">
			<h3>Test</h3>
			<p>Mise à jour de test n°2</p>
		</div>
		
	</div>
</body>
</html>