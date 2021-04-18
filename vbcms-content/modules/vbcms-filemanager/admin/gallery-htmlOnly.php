<div class="row">
	<div class="gallerySidebar">
		<div id="gallerySidebar" style="margin: 1em;">
			<h5>Arborescence</h5>
			<div id="basePath" class="folder-item mt-2">
				<a id="abasePath" href="#" class="text-dark">
					<span class="menu-icon"><i class="fas fa-home"></i></span>
					<span class="menu-text">Racine</span>
				</a>
			</div>
		</div>
	</div>
	<div class="galleryPageContent">
		<div class="galleryTopBar d-flex align-items-center">
			<div id="galleryNaviguationButtons" class="d-flex mr-auto">
				<div class="mx-2">
					<a id="parentFolder" href="#" class="text-dark"><i class="fas fa-undo-alt"></i></a>
				</div>
				<div class="mx-2">
					<a id="parentFolder" href="#" class="text-dark"><i class="fas fa-cloud-upload-alt"></i> Envoyer une image</a>
				</div>
				<div class="mx-2">
					<button type="button" class="btn-invisible text-dark" data-toggle="createFolder" title="Créer un dossier"><i class="fas fa-folder-plus"></i></button>
				</div>
			</div>
			<div class="mx-2">
				<a id="gridBig" href="#" class="text-dark"><i class="fas fa-th-large"></i></a>
			</div>
			<div class="mx-2">
				<a id="gridLittle" href="#" class="text-dark"><i class="fas fa-th"></i></a>
			</div>
			<div class="mx-2">
				<a id="gridList" href="#" class="text-dark"><i class="fas fa-list"></i></a>
			</div>

		</div>
		<div id="galleryContent" class="m-2" ondrop="dropHandler(event);" ondragover="dragOverHandler(event);" ondragleave="dragLeaveHandler(event);">
			<!--<div id="dejaécrit" class="col galleryItem"><i class="fas fa-folder"></i>Déjà écrit</div>-->
		</div>
	</div>
</div>

<!-- Modal de la galerie-->
<div id="galleryModal" class="galleryModal" type>
	<div class="galleryModalHeader">
		<div id="galleryModalTitle" class="galleryModalTitle">
			Chargement...
		</div>
		<div class="galleryModalMenu">
			<button onclick="closeGalleryModal()" class="rButton-white"><i class="fas fa-times"></i></button>
		</div>
	</div>
	<div class="galleryModalContent">
		<!-- Visionneuse de photos
		<a id="viewerPrevious" class="viewerPrevious">
			<div class="icon"><i class="far fa-chevron-left"></i></div>
		</a>
		<div class="viewerImage">
			<img src="" alt="">
		</div>
		<a id="viewerNext" class="viewerNext">
			<div class="icon"><i class="far fa-chevron-right"></i></div>
		</a>
		-->
	</div>
</div>