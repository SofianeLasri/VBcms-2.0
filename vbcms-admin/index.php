<?php
include 'includes/header.php';
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title><?=$websiteName?></title>
	<?php include 'includes/depedencies.php';?>
</head>
<body>
	<?php 
	include ('includes/navbar.php');
	?>

	<!-- Contenu -->
	<div class="page-content" leftSidebar="240" rightSidebar="0">
		<h3>Tableau de bord</h3>
		<p>Bienvenu sur le paneau d’administration. Voici un bref résumé de l'activité de cette semaine.</p>

		<!-- Debut de la liste des cartes -->
		<div id="indexCards" class="row" style="">
			<div class="col-xl-6">
				<div id="indexCardOne" class="indexCard mb-3 overflow-hidden">
					<div style="position: absolute; z-index: 1;height: 100%; width: 100%; background-image: url(https://cdn.vbcms.net/images/manager/mainIndexCardBg.png); background-position: right bottom; background-repeat: no-repeat; background-size: 100%;"></div>
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
								<img src="https://cdn.vbcms.net/images/manager/globe.png">
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
								<img src="https://cdn.vbcms.net/images/manager/ticket.png">
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
								<img src="https://cdn.vbcms.net/images/manager/puzzle.png">
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
								<img src="https://cdn.vbcms.net/images/manager/hourglass.png">
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
		</div>
		
	</div>
</body>
</html>