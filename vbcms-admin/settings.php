<?php
$steamApiKey = $bdd->query("SELECT value FROM `vbcms-settings` WHERE name='steamApiKey'")->fetchColumn();

if (isset($_POST["submit"])) {
	$response = $bdd->prepare("UPDATE `vbcms-settings` SET value=? WHERE name='websiteName'");
	$response->execute([$_POST["websiteName"]]);

	$response = $bdd->prepare("UPDATE `vbcms-settings` SET value=? WHERE name='websiteDescription'");
	$response->execute([$_POST["websiteDescription"]]);

	$response = $bdd->prepare("UPDATE `vbcms-settings` SET value=? WHERE name='websiteMetaColor'");
	$response->execute([$_POST["websiteMetaColor"]]);

	$response = $bdd->prepare("UPDATE `vbcms-settings` SET value=? WHERE name='websiteLogo'");
	$response->execute([$_POST["websiteLogo"]]);

	$response = $bdd->prepare("UPDATE `vbcms-settings` SET value=? WHERE name='steamApiKey'");
	$response->execute([$_POST["steamApiKey"]]);


	header("Refresh:0");
}


?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title><?=$websiteName?></title>
	<?php include 'includes/depedencies.php';?>
	<link rel="stylesheet" href="https://cdn.vbcms.net/vendors/pick-a-color/css/pick-a-color-1.2.3.min.css">
</head>
<body>
	<?php 
	include ('includes/navbar.php');
	?>

	<!-- Contenu -->
	<div class="page-content" leftSidebar="240" rightSidebar="0">
		<h3><?=$translation["settings"]?></h3>
		
		<h5 class="mt-4"><?=$translation["website"]?></h5>
		<form class="width-50em" action="settings" method="POST">
			<div class="form-group">
				<label>Nom du site internet</label>
				<input required type="text" value="<?=$websiteName?>" class="form-control" name="websiteName">
			</div>
			<div class="form-group">
				<label>Courte description internet</label>
				<input required type="text" class="form-control" value="<?=$websiteDescription?>" name="websiteDescription">
			</div>
			<div class="form-group">
				<label>Couleur globale du site</label>
				<input required type="text" value="<?=$websiteMetaColor?>" name="websiteMetaColor" class="pick-a-color form-control">
			</div>
			<div class="form-group">
				<label>Logo du site internet</label>
				<div class="d-flex">
					<div id="websiteLogoPreview" class="settingsFormPicture mr-2" style="background:url('<?=$websiteLogo?>'),linear-gradient(180deg, rgba(65,65,65,1) 0%, rgba(1,1,1,1) 100%);border-radius:5px; width: 64px;"></div>
					<div class="d-flex flex-column flex-grow-1">
						<input required type="text" class="form-control" value="<?=$websiteLogo?>" name="websiteLogo" id="websiteLogo">
						<a class="btn btn-brown" onclick="openGallery()" href="#">Choisir une image</a>
					</div>
				</div>
			</div>

			<h5 class="mt-4">Steam</h5>
			<div class="form-group">
				<label>Steam API Key</label>
				<input type="text" value="<?=$steamApiKey?>" class="form-control" name="steamApiKey">
			</div>

			<button class="btn btn-brown" type="submit" name="submit">Sauvegarder les changements</button>
		</form>
	</div>

	<div class="modal fade" id="galleryModal" tabindex="-1">
		<div class="modal-dialog galleryIncludeModal">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">Choisir une image</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>

				<!-- C'est ici que la galerie est incluse -->
				<div class="modal-body">
					<div id="alertContainer">
						
					</div>
					<div id="includeGallery"></div>
				</div>
			</div>
		</div>
	</div>

	<script src="https://cdn.vbcms.net/vendors/pick-a-color/js/tinycolor-0.9.15.min.js"></script>
	<script src="https://cdn.vbcms.net/vendors/pick-a-color/js/pick-a-color-1.2.3.min.js"></script>
	<script type="text/javascript">
		$(".pick-a-color").pickAColor();

		function openGallery(){
			$('#galleryModal').modal('toggle');
			if ($('#includeGallery').html()=="")
				$('#includeGallery').load('<?=$http?>://<?=$_SERVER['HTTP_HOST']?>/vbcms-content/modules/gallery/admin/gallery-include.php');
		}

		// Contournement pour ne sélectionner que les images
		function openViewer(path){
			$("#websiteLogoPreview").css("background", "url(\"<?=$http?>://<?=$_SERVER['HTTP_HOST']?>/vbcms-content/uploads"+path+"\"),linear-gradient(180deg, rgba(65,65,65,1) 0%, rgba(1,1,1,1) 100%)");
			$("#websiteLogo").val("<?=$http?>://<?=$_SERVER['HTTP_HOST']?>/vbcms-content/uploads"+path);
			$('#galleryModal').modal('hide');
		}
		function openVideo(path){
			$('#alertContainer').html("\
				<div id=\"fileChooseAlert\" class=\"alert alert-warning alert-dismissible fade show\" role=\"alert\">\
							<strong>Désolé,</strong> mais le fichier sélectionné ne semble pas être une image. :/\
							<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\
								<span aria-hidden=\"true\">&times;</span>\
							</button>\
						</div>\
				");
		}
	</script>
</body>
</html>