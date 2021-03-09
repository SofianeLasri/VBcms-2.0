<?php
if(isset($_GET["modifyDraft"]) && !empty($_GET["modifyDraft"])){
	$response = $bdd->prepare("SELECT * FROM `vbcms-blogDrafts` WHERE randId = ?"); // Je récupère le contenu du brouillon
    $response->execute([$_GET["modifyDraft"]]);
    $postContent = $response->fetch(PDO::FETCH_ASSOC);
} elseif(isset($_GET["modifyPost"]) && !empty($_GET["modifyPost"])){
	$response = $bdd->prepare("SELECT * FROM `vbcms-blogPosts` WHERE id = ?"); // Je récupère le contenu du brouillon
    $response->execute([$_GET["modifyPost"]]);
    $postContent = $response->fetch(PDO::FETCH_ASSOC);
} else {
	$postContent = false;
}
?>
<!-- Contenu -->
<div class="page-content" leftSidebar="240" rightSidebar="0">
	<h3>Créer un nouvel article</h3>

	<div class="row">
		<div class="col-sm-6 col-editor-content">
			<input type="text" class="form-control my-2" id="articleTitle" <?php if($postContent) echo 'value="'.utf8_decode($postContent["title"]).'"'; ?> placeholder="Titre du nouvel article">
			<div id="permalink-div" slug="<?php if($postContent) echo $postContent["slug"]; ?>" class="my-2"><strong>Permalien : </strong><a href="<?=$http?>://<?=$_SERVER['HTTP_HOST']?>/blog/<?php if($postContent) echo $postContent["slug"]; ?>" target="_blank"><?=$http?>://<?=$_SERVER['HTTP_HOST']?>/blog/<?php if($postContent) echo $postContent["slug"]; ?></a></div>
			<textarea required id="summernote" name="articleContent"><?php if($postContent) echo utf8_decode($postContent["content"]); ?></textarea>
		</div>
		<div class="col-6 col-editor-sidebar">
			<div class="card vbcard">
				<h6 class="card-header">Publier</h6>
				<div class="card-body">
					<button type="button" onclick="autoSave(0)" class="btn btn-primary btn-sm"><i class="fas fa-save"></i> Brouillon</button>
					<button type="button" onclick="preview()" class="btn btn-secondary btn-sm"><i class="fas fa-eye"></i> Prévisualiser</button>
					<p class="card-text mt-2"><strong>Choisir une image d'entête</strong></p>
					<a href="#" onclick="openGallery()" class="text-dark"><div id="editor-headerPic" path="<?php if($postContent) echo $postContent["headerImage"]; ?>" <?php if($postContent) echo 'style="background-image: url(\''.$postContent["headerImage"].'\'");"'; ?> class="editor-headerPic border rounded"><i class="fas fa-image"></i>Ajouter</div></a>
					<?php
					if(isset($_GET["modifyPost"])){
						echo '<a href="#" onclick="update()" class="btn btn-primary" style="margin-top: .75rem;">Mettre à jour</a>';
					} else {
						echo '<a href="#" onclick="publish()" class="btn btn-primary" style="margin-top: .75rem;">Publier</a>';
					}
					?>
			    	
				</div>
			</div>

			<div class="card vbcard mt-3">
				<h6 class="card-header">Description</h6>
				<div class="card-body">
					<p class="card-text"><strong>Entrez une courte description de l'article</strong></p>
					<textarea id="articleDescription" class="form-control"><?php if($postContent) echo utf8_decode($postContent["description"]); ?></textarea>
				</div>
			</div>

			<div class="card vbcard mt-3">
				<h6 class="card-header">Catégorie</h6>
				<div class="card-body">
					<div class="form-group">
					    <select multiple class="form-control" id="articleCategory">
						    <option value="0">(aucune)</option>
						    <?php
						  	$result = $bdd->query("SELECT * FROM `vbcms-blogCategories`");
						  	foreach ($result as $row) {
						  		$response = $bdd->prepare("SELECT showName FROM `vbcms-blogCategories` WHERE id = ?");
								$response->execute([$row['childOf']]);
								$response = $response->fetch(PDO::FETCH_ASSOC); 
								$parentName = $response["showName"];

								if (isset($postContent) && $postContent["categoryId"]==$row['id']) {
									$selected = "selected";
								} else {
									$selected = "";
								}
								

						  		echo ('<option '.$selected.' value="'.$row['id'].'">'.$row['showName']."</option>");
						  	}
						  	?>
					    </select>
					</div>
					<a href="<?=$http?>://<?=$_SERVER['HTTP_HOST']?>/vbcms-admin/blog/categories">Ajouter une catégorie</a>
				</div>
			</div>
		</div>
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
<script src="<?=$http?>://<?=$_SERVER['HTTP_HOST']?>/vbcms-admin/js/md5.js";?>"></script>
<script type="text/javascript">


	// Initialise l'editeur
	document.emojiButton = 'far fa-smile'; // default: fa fa-smile-o
	document.emojiType = 'unicode'; // default: image
	document.emojiSource = '<?=$http?>://<?=$_SERVER['HTTP_HOST']?>/vbcms-content/modules/blog/assets/vendors/tam-emoji/img';

	// ID unique du brouillon
	draftId = getRandomString(4);
	// Permet de détecter les modifs
	hash = hex_md5($("#summernote").val());

	var url = new URL(window.location.href);
	var search_params = url.searchParams;
	if((!search_params.get('modifyDraft') || (search_params.get('modifyDraft')==null)) && !search_params.get('modifyPost')){
		search_params.append('modifyDraft', draftId);
		url.search = search_params.toString();
		var new_url = url.toString();
		window.history.replaceState({}, '',new_url);

		var writtenDate = getDateTime();
	} else if(search_params.get('modifyPost')!=null && (!search_params.get('modifyDraft') || (search_params.get('modifyDraft')==null))){
		var id = search_params.get('modifyPost');
		var writtenDate = "<?php if($postContent) echo $postContent["writtenOn"]; ?>";
	} else if(search_params.get('modifyDraft')!=null && (!search_params.get('modifyPost') || (search_params.get('modifyPost')==null))) {
		draftId = search_params.get('modifyDraft');
		var writtenDate = "<?php if($postContent) echo $postContent["writtenOn"]; ?>";
	}
	


	$(document).ready(function() {
		$('#summernote').summernote({
        placeholder: "Ceci est un texte.",
        tabsize: 2,
        height: 300,
        toolbar: [
        	['style', ['style', 'bold', 'italic', 'underline', 'clear']],
		    ['fontsize', ['fontsize', 'height', 'color']],
		    ['para', ['ul', 'ol', 'paragraph']],
		    ['font', ['strikethrough', 'superscript', 'subscript']],
		    ['insert', ['link', 'picture', 'video', 'emoji']],
         	['view', ['fullscreen', 'codeview', 'undo', 'redo', 'help']]
        ]
      });
	});

	$("#articleTitle").on("change paste keyup", function() { 
		var slug = $(this).val()
		slug = slug.toLowerCase().replace(/ /g,'-').replace(/[^\w-]+/g,'');
		$('#permalink-div>a').attr("href", "<?=$http?>://<?=$_SERVER['HTTP_HOST']?>/blog/"+slug);
		$('#permalink-div>a').html("<?=$http?>://<?=$_SERVER['HTTP_HOST']?>/blog/"+slug);
		$('#permalink-div').attr("slug", slug);
	});

	function openGallery(){
		$('#galleryModal').modal('toggle');
		if ($('#includeGallery').html()=="")
			$('#includeGallery').load('<?=$http?>://<?=$_SERVER['HTTP_HOST']?>/vbcms-content/modules/gallery/admin/gallery-include.php');
	}

	// Contournement pour ne sélectionner que les images
	function openViewer(path){
		$("#editor-headerPic").css("background-image", "url(\"<?=$http?>://<?=$_SERVER['HTTP_HOST']?>/vbcms-content/uploads"+path+"\")");
		$("#editor-headerPic").attr("path", "<?=$http?>://<?=$_SERVER['HTTP_HOST']?>/vbcms-content/uploads"+path);
		$("#editor-headerPic").html('<i class="fas fa-pencil-alt"></i> Modifier');
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

	function autoSave(autoOrNot){
        if($("#summernote").val() != hash){
        	SnackBar({
                message: "Sauvegarde automatique...",
                status: "info"
            });

            // Je fais une liste des éléments du post
            var modifyDate = getDateTime();

            var draftContent = [];
            draftContent.push(draftId, $("#articleCategory option:selected").val(), $('#permalink-div').attr("slug"), $("#articleTitle").val(), $("#summernote").val(), $('#editor-headerPic').attr("path"), writtenDate, modifyDate, $("#articleDescription").val(), autoOrNot);
            
            var xhr = new XMLHttpRequest();
		    var fd = new FormData();

		    var url = new URL(window.location.href);
			var search_params = url.searchParams;

		    xhr.open("POST", "<?=$http?>://<?=$_SERVER['HTTP_HOST']?>/vbcms-admin/blog/backTasks", true);
		    xhr.onreadystatechange = function() {
		        if (xhr.readyState == 4 && xhr.status == 200) {
		        	if(xhr.responseText!=""){
		        		SnackBar({
	                        message: "Impossible de sauvegarder: "+xhr.responseText,
	                        status: "danger",
	                        timeout: false
	                    });
		        	} else {
		        		SnackBar({
	                        message: "Sauvegarde réussie!",
	                        status: "success"
	                    });
		        	}
		        }
		        
		    };
		    fd.append('saveDraft', JSON.stringify(draftContent));

		    // Initiate a multipart/form-data upload
		    xhr.send(fd);
		}
	}

	const autoSaveTimer = setInterval( function() { autoSave(1); }, 60000);

	function publish(autoOrNot){
        if($('#permalink-div').attr("slug") != "" || $("#summernote").val() != ""){
        	SnackBar({
                message: "Publication de l'article...",
                status: "info"
            });

            // Je fais une liste des éléments du post
            var modifyDate = getDateTime();

            var postContent = [];
            postContent.push($("#articleCategory option:selected").val(), $('#permalink-div').attr("slug"), $("#articleTitle").val(), $("#summernote").val(), $('#editor-headerPic').attr("path"), writtenDate, modifyDate, $("#articleDescription").val(), );
            
            var xhr = new XMLHttpRequest();
		    var fd = new FormData();

		    var url = new URL(window.location.href);
			var search_params = url.searchParams;

		    xhr.open("POST", "<?=$http?>://<?=$_SERVER['HTTP_HOST']?>/vbcms-admin/blog/backTasks", true);
		    xhr.onreadystatechange = function() {
		        if (xhr.readyState == 4 && xhr.status == 200) {
		        	if(xhr.responseText!=""){
		        		SnackBar({
	                        message: "Impossible de sauvegarder: "+xhr.responseText,
	                        status: "danger",
	                        timeout: false
	                    });
		        	} else {
		        		SnackBar({
	                        message: "Sauvegarde réussie!",
	                        status: "success"
	                    });
		        	}
		        }
		        
		    };
		    fd.append('savePost', JSON.stringify(postContent));

		    // Initiate a multipart/form-data upload
		    xhr.send(fd);
		}
	}

	function update(){
        if($('#permalink-div').attr("slug") != "" || $("#summernote").val() != ""){
        	SnackBar({
                message: "Mise à jour de l'article...",
                status: "info"
            });

            // Je fais une liste des éléments du post
            var modifyDate = getDateTime();

            var postContent = [];
            postContent.push($("#articleCategory option:selected").val(), $('#permalink-div').attr("slug"), $("#articleTitle").val(), $("#summernote").val(), $('#editor-headerPic').attr("path"), writtenDate, modifyDate, $("#articleDescription").val(), id);
            
            var xhr = new XMLHttpRequest();
		    var fd = new FormData();

		    var url = new URL(window.location.href);
			var search_params = url.searchParams;

		    xhr.open("POST", "<?=$http?>://<?=$_SERVER['HTTP_HOST']?>/vbcms-admin/blog/backTasks", true);
		    xhr.onreadystatechange = function() {
		        if (xhr.readyState == 4 && xhr.status == 200) {
		        	if(xhr.responseText!=""){
		        		SnackBar({
	                        message: "Impossible de sauvegarder: "+xhr.responseText,
	                        status: "danger",
	                        timeout: false
	                    });
		        	} else {
		        		SnackBar({
	                        message: "Sauvegarde réussie!",
	                        status: "success"
	                    });
		        	}
		        }
		        
		    };
		    fd.append('updatePost', JSON.stringify(postContent));

		    // Initiate a multipart/form-data upload
		    xhr.send(fd);
		}
	}
</script>
