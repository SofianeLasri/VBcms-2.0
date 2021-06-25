<?php
require_once '../vbcms-core/core.php';

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
		-->
		<!-- Fin de la liste des cartes -->
		
		<div class="mt-3">
			<h3>Merci de participer aux tests!</h3>
			<p>Salut et merci d'avoir accepté de participer aux tests de pré-sortie. Tu es là sur une version très primaire de ce que sera VBcms 2.0 à la fin, et comme tu l'as peut-être déjà remarqué, il n'y a pas grand chose de terminé.</p><br>

			<h5>Pourquoi avoir sorti publiquement une application non terminée?</h5>
			<p>Cela fait maintenant près de 7 mois que je travail activement sur le développement de ce cms. Je vous ai tenu informé de l'avancement du projet depuis le début, et j'ai souhaité vous faire part d'un aperçu fonctionnel de que j'ai pu réaliser durant ces longs mois. <strong>VBcms 1 étant vraiment médiocre</strong>, je me suis dit qu'il serait bon de le remplacer en commençant par vous mettre à disposition une nouvelle solution. Pas forcément plus stable vous verrez, mais une alternative très prometeuse qui vous proposera bien plus de fonctionnalités dans le temps.</p>
			<br>
			<h5>Qu'en est-il de la suite? Quand arriveront les nouveautés?</h5>
			<p>VBcms dispose d'un système de mise à jour automatique, vous permettant de ne pas avoir à vous soucier de ce problème. Les prochaînes mises à jours du panel arriveront dans les jours qui suivent. Elles apporteront diverses modifications comme des corrections de bugs, de nouvelles fonctionnalités, mais aussi et surtout une amélioration du coeur du panel. Étant donné que nous sommes là sur une version très primitive dans l'état de développement, beaucoup de choses vont changer dans les semaines à venir.<br>
			Une documentation détaillée verra le jours le plus tôt possible, le workshop suivra sa sortie pour vous permetre la création d'addons. (on peut déjà en créer mais l'api n'est encore établie. Il est trop dangereux de créer un addon dans cet état puisque les addons possèdent un accès total à la base de donnée)</p>
			<br>
			<h5>Reste informé!</h5>
			<p>Reste sur notre serveur Discord pour être informé de l'avancement du cms! :D <a href="https://discord.gg/DpfF8Kz">https://discord.gg/DpfF8Kz</a></p>
		</div>
		
	</div>
</body>
</html>