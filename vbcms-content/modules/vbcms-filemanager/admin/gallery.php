<!-- Contenu -->
<div class="page-content" leftSidebar="240" rightSidebar="0">
	<h3><?=$translation["gallery_filemanager"]?></h3>
	<p>La taille maximale d'envoie est de <code><?=(int)(ini_get('upload_max_filesize'))?> MB</code>.</p>

	<div id="includeGallery">
		<!-- ICI la galerie est incluse lors du chargement de la page -->
		<?php require $_SERVER['DOCUMENT_ROOT']."/vbcms-content/modules/vbcms-filemanager/admin/gallery-htmlOnly.php"; ?>
	</div>

	<div class="width-50em mt-4">
		<div class="alert alert-danger alert-dismissible fade show" role="alert">
			<strong>Attention!</strong> La galerie n'est pas terminée, elle possède encore de nombreux bugs et oublis. N'effectuez que de simples tâches comme l'envoi de fichiers.
		  	<button type="button" class="close" data-dismiss="alert" aria-label="Close">
		    	<span aria-hidden="true">&times;</span>
		  	</button>
		</div>
	</div>
</div>

<!-- Barre de détail des fichiers -->
<div id="fileDetailsDiv" class="fileDetailsDiv" path="">
	<div class="imageBackground">
		<button id="closeFileDetailsDiv" class="rButton-white gallery-close-btn"><i class="fas fa-times"></i></button>
	</div>
	<div class="detailContent">
		<div class="detailHeader d-flex flex-column">
			<h3>Veuillez patientez...</h3>
			<span>On cherche les détails! :D</span>
		</div>
		<div class="titleDescription">
			<input id="fileTitle" type="text" class="form-control form-control-sm mt-3" placeholder="Titre du fichier">
			<textarea id="fileDescription" class="form-control mt-1" placeholder="Description du fichier"></textarea>
			<button id="saveFileDetails" type="button" class="btn btn-brown float-sm-right mt-1">Enregistrer</button>
		</div>
		<button onclick="copyFileUrl()" class="rButton-black"><i class="fas fa-link"></i></button>
	</div>
</div>


<!-- context menu -->
<div id="contextMenu" itemId="" class="context-menu" style="display:none"> 
	<ul>
		<li id="explorerDetail"><a href="#"><i class="fas fa-info"></i> Détails</a></li>
		<li id="explorerRename"><a href="#"><i class="fas fa-pencil-alt"></i> Renommer</a></li>
		<li id="explorerMove"><a href="#"><i class="fas fa-external-link-alt"></i> Déplacer ou copier</a></li>
		<li id="explorerDelete"><a href="#"><i class="fas fa-trash"></i> Supprimer</a></li>
	</ul>
</div>
<?php require $_SERVER['DOCUMENT_ROOT']."/vbcms-content/modules/vbcms-filemanager/admin/gallery-scriptOnly.php"; ?>