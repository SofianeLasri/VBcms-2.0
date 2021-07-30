<?php
$loadingScreenId = $bdd->query("SELECT COUNT(*) FROM `vbcms-loadingscreeens`")->fetchColumn() + 1;
?>
<!-- Contenu -->
<div class="page-content" leftSidebar="240" rightSidebar="0">
	<h3><?=$translation["loadingscreen_create"]?></h3>
	<p>Vous pouvez créer plusieurs écrans de chargement.</p>
	<div class="d-flex flex-column width-50em" id="page-content">
		<h5>Prévisualisation</h5>
		<div class="loadsingScreenPreviewTop rounded d-flex p-2 justify-content-center">
				<div class="loadingScreenPreview rounded" style="background-image: url('https://api.apiflash.com/v1/urltoimage?access_key=65e037cb81b44087ba537b58dd19e4ff&format=jpeg&height=1080&quality=80&response_type=image&url=<?php echo urlencode($websiteUrl."loadingscreen/".$loadingScreenId."?preview"); ?>&width=1920');"></div>
		</div>

		<h5 class="mt-5">Paramètres</h5>
		
		<form class="needs-validation" novalidate>
			<div class="form-group">
				<label>Nom de l'écran de chargement</label>
				<input type="text" name="name" class="form-control" value="<?= $_GET["name"] ?? '' ?>" placeholder="Un super écran de chargement" required>
				<div class="valid-feedback">
					Nickel!
				</div>
				<div class="invalid-feedback">
					Veuillez donner un nom à l'écran de chargement.
				</div>
			</div>

			<label>Images de fond</label>

			<div class="form-row">

				<div class="col-md-6 mb-3">
					<input type="text" id="backgroundPics" name="backgroundPics" class="form-control" value="<?= $_GET["backgroundPics"] ?? '[]' ?>" required readonly>
					<div class="valid-feedback">
						Nickel!
					</div>
					<div class="invalid-feedback">
						Veuillez choisir des images de fond
					</div>
				</div>
				<div class="col-md-6 mb-3">
					<button type="button" onclick="openGallery('backgroundPics')" id="chooseBackgroundPics" class="btn btn-brown">Choisir des images</button>
				</div>
					
				
			</div>
			<button type="submit" name="submit" class="btn btn-brown">Créer l'écran de chargement</button>
		</form>

	</div>
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

<script type="text/javascript">
	var element = "";
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

	function openGallery(element){
		$('#galleryModal').modal('toggle');
		if ($('#includeGallery').html()=="")
			$('#includeGallery').load('<?=$websiteUrl?>vbcms-content/modules/vbcms-filemanager/admin/gallery-include.php');
		element = this.element;
	}

	// Contournement pour ne sélectionner que les images
	function openViewer(path){
		$("#websiteLogoPreview").css("background", "url(\"<?=$websiteUrl?>vbcms-content/uploads"+path+"\"),linear-gradient(180deg, rgba(65,65,65,1) 0%, rgba(1,1,1,1) 100%)");
		$("#websiteLogo").val("<?=$websiteUrl?>vbcms-content/uploads"+path);
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