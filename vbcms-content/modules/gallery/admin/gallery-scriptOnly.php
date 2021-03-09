<?php
// Je le remet car l'inclusion de header.php va causer plus de problèmes qu'autre chose
if(isset($_SERVER['HTTPS'])) $http = "https"; else $http = "http";
?>
<script src="<?=$http?>://<?=$_SERVER['HTTP_HOST']?>/vbcms-admin/js/md5.js"></script>
<script type="text/javascript">

	/////////////////////////////
	// INITIALISE L'EXPLORATEUR
	/////////////////////////////

	const basePath = "";
	const basePathId = "basePath";
	
	///////////
	// VBcms Manager
	/////////

	// Par Sofiane Lasri - https://sl-projects.com

	const pageFileName = window.location.pathname;
	const cdnUrl = "<?=$http?>://<?=$_SERVER['HTTP_HOST']?>/vbcms-content/uploads/";
	if (pageFileName=="/vbcms-admin/gallery/browse") {
		mode = "full-gallery"; // Ça signifie qu'il s'agit de la page de la galerie
	} else{
		mode = "selector"; // Ça signifie que la galerie est incluse comme simple selecteur d'images
	}

	// Initialation n°2 de la galerie

	// Va se charger de récupérer les informations de l'url et de lancer la galerie
	$( document ).ready(function() {
		// Se charge d'attribuer le chemin de base au lien "maison"
		$("#a"+basePathId).attr("onclick", "getChildDirectory('"+basePath+"', 'basePath', 'no')");

		// Se charge de récupérer/créer le chemin d'accès du dossier courant dans l'url de la page
		var url = new URL(window.location.href);
		var search_params = url.searchParams;
		if(search_params.get('path')==null || search_params.get('path')==basePath){
			getChildDirectory(basePath, basePathId, 'yes');
		} else {
			getChildDirectory(search_params.get('path'), "url", 'yes');
		}

		// Se charge de récupérer la taille des items désirées depuis l'url de la page
		if(search_params.get('size')==null){
			search_params.append('size', 'gridBig');
			var new_url = url.toString();
			window.history.replaceState({}, '',new_url);
		} else {
			$("#"+search_params.get('size')).trigger("click");
		}

		if(mode=="full-gallery"){ // Ne fonctionne que si l'on est sur la galerie

			// Se charge d'ouvrir le fichier spécifié
			if(search_params.get('openFileId')!=null){
				openFileId(search_params.get('openFileId'));
			}
			console.log("La taille maximale d'envoie est de <?=(int)(ini_get('upload_max_filesize'))?> MB")
		}
	});


	/////////////////////////////
	// SCRIPT DE l'EXPLORATEUR
	/////////////////////////////

	async function getChildDirectory(path, parent, genSidebar){
		// Permet de ne pas aller sur le dossier parent au dossier de base
		// TODO: Le faire en PHP car JS niveau sécu ça craint du cul
		if (path != basePath) {
			var parentFolder = path.substr(0, path.lastIndexOf("/"));
			$("#parentFolder").attr("onclick", "getChildDirectory('"+parentFolder+"','parentFolder', 'no')");
		}

		// Permet de récupérer le paramètre dans l'url
		// Mais aussi de reécrire l'url avec le dossier courant
		var url = new URL(window.location.href);
		var search_params = url.searchParams;
		if(search_params.get('path')==null){
			search_params.append('path', path);
			url.search = search_params.toString();
			var new_url = url.toString();
		} else {
			search_params.set('path', path);
			url.search = search_params.toString();
			var new_url = url.toString();
		}
		window.history.replaceState({}, '',new_url);

		$("#galleryContent").html(""); // Supprime tout le contenu de la balise

		// Si parent = url ça veut dire que l'url contient un chemin d'accès
		if (parent == "url") {
			var structureCurent = path;
			var subfolderList = [];
			var parent2 = basePathId;
			var nextParent = "";
			subfolderList.unshift(structureCurent.match(/([^\/]*)\/*$/)[1]); //Permet de capter le premier dossier?

			//Génère la liste des dossier dans l'ordre croissant
			while (structureCurent != basePath){
				structureCurent = structureCurent.substr(0, structureCurent.lastIndexOf("/"));//Supprime le dernier /
				if (structureCurent != basePath) {//Si le chemin est égale au chemin de base
					subfolderList.unshift(structureCurent.match(/([^\/]*)\/*$/)[1]);//Ne récupère que le nom du dossier
				}else{//Permet de ne pas avoir le chemin de base pour éviter la répétition
					subfolderList.unshift("");
				}
			}

			for (let i=0; i<subfolderList.length;i++){//Pour chaque dossier, faire...

				if (subfolderList[i]!="") {//S'il s'agit du dossier de base
					structureCurent = structureCurent + "/" + subfolderList[i];
				}

				await $.get("<?=$http?>://<?=$_SERVER['HTTP_HOST']?>/vbcms-admin/gallery/backTasks/?folderContent="+structureCurent+"&folderOnly",async function(data) {// Affiche uniquement les dossiers
					var folders = JSON.parse(data);
					
					if (nextParent != "") {//Détecte si on a trouvé le dossier parent
						parent2 = nextParent;
					}
					for (let b=0; b<folders.length;b++){
						var ml = (parseInt($("#"+parent2).css("margin-left").replace('px','')) + 10)+"px";
						var name = folders[b].match(/([^\/]*)\/*$/)[1];
						var id = hex_md5(structureCurent);

						// Ajoute à la barre latérale de naviguation
						if (parent2 == basePathId) {
							$("#"+parent2).append('<div class="folder-item mt-2" id="'+id+'" path="'+path+'/'+name+'" style="margin-left:0px;"><a id="a'+id+'" href="#" onclick="getChildDirectory(\''+folders[b]+'\', \''+id+'\', \'no\')" class="text-dark"><span class="menu-icon"><i class="fas fa-folder-open"></i></span><span class="menu-text">'+name+'</span></a></div>');
						} else {
							$("#"+parent2).append('<div class="folder-item mt-2" id="'+id+'" path="'+path+'/'+name+'" style="margin-left:'+ml+';"><a id="a'+id+'" href="#" onclick="getChildDirectory(\''+folders[b]+'\', \''+id+'\', \'no\')" class="text-dark"><span class="menu-icon">↳<i class="fas fa-folder-open"></i></span><span class="menu-text">'+name+'</span></a></div>');
							/*$('<div class="folder-item mt-2" id="'+id+'" style="margin-left:'+ml+';"><a href="#" onclick="getChildDirectory(\''+folders[b]+'\', \''+id+'\')" class="text-dark"><span class="menu-icon">↳<i class="fas fa-folder-open"></i></span><span class="menu-text">'+name+'</span></a></div>').insertAfter("#"+parent);*/
						}
						if (folders[b] != basePath) {
							if (name==subfolderList[i+1]) {
								nextParent = id;
							}
						}

						//Ajoute a conteneur principale
						if (i+1 == subfolderList.length) {
							$("#galleryContent").append('<div id="c'+id+'" type="folder" path="'+path+'/'+name+'" onclick="getChildDirectory(\''+folders[b]+'\', \''+id+'\', \'yes\')" class="col galleryItem"><div class="galleryItemIcon" style="background:url(\'<?=$http?>://<?=$_SERVER['HTTP_HOST']?>/vbcms-content/modules/gallery/assets/images/manager/folder.png\')"></div><div id="fileName">'+name+'</div></div>');
						}
						
						
					}
				});
			}

		} else {
			await $.get("<?=$http?>://<?=$_SERVER['HTTP_HOST']?>/vbcms-admin/gallery/backTasks/?folderContent="+path+"&folderOnly", function(data) {// Affiche uniquement les dossiers
				jQuery.each(JSON.parse(data), function(index, val){ 
					var ml = (parseInt($("#"+parent).css("margin-left").replace('px','')) + 10)+"px";
					var name = val.match(/([^\/]*)\/*$/)[1];
					var id = hex_md5(path);

					if (genSidebar == 'yes') {
						if (parent == basePathId) {
							$("#"+parent).append('<div class="folder-item mt-2" id="'+id+'" path="'+path+'/'+name+'" unselectable="on" onselectstart="return false;" onmousedown="return false;" style="margin-left:0px;"><a id="a'+id+'" href="#" onclick="getChildDirectory(\''+val+'\', \''+id+'\', \'yes\')" class="text-dark"><span class="menu-icon"><i class="fas fa-folder-open"></i></span><span class="menu-text">'+name+'</span></a></div>');
						} else {
							$("#"+parent).append('<div class="folder-item mt-2" id="'+id+'" path="'+path+'/'+name+'" unselectable="on" onselectstart="return false;" onmousedown="return false;" style="margin-left:'+ml+';"><a id="a'+id+'" href="#" onclick="getChildDirectory(\''+val+'\', \''+id+'\', \'yes\')" class="text-dark"><span class="menu-icon">↳<i class="fas fa-folder-open"></i></span><span class="menu-text">'+name+'</span></a></div>');
							/*$('<div class="folder-item mt-2" id="'+id+'" style="margin-left:'+ml+';"><a href="#" onclick="getChildDirectory(\''+val+'\', \''+id+'\')" class="text-dark"><span class="menu-icon">↳<i class="fas fa-folder-open"></i></span><span class="menu-text">'+name+'</span></a></div>').insertAfter("#"+parent);*/
						}
					}
					
					$("#galleryContent").append('<div id="c'+id+'" type="folder" path="'+path+'/'+name+'" unselectable="on" onselectstart="return false;" onmousedown="return false;" onclick="getChildDirectory(\''+val+'\', \''+id+'\', \'yes\')" class="col galleryItem"><div class="galleryItemIcon" style="background:url(\'<?=$http?>://<?=$_SERVER['HTTP_HOST']?>/vbcms-content/modules/gallery/assets/images/manager/folder.png\')"></div><div id="fileName">'+name+'</div>');
					
				});
			});
		}
		
		await $.get("<?=$http?>://<?=$_SERVER['HTTP_HOST']?>/vbcms-admin/gallery/backTasks/?folderContent="+path+"&filesOnly", function(data) {// Affiche uniquement les fichiers
			jQuery.each(JSON.parse(data), function(index, val){
				var url = cdnUrl+val;
				var name = val.match(/([^\/]*)\/*$/)[1];
				var id = hex_md5(path+name);
				if (name.endsWith(".png")||name.endsWith(".jpg")||name.endsWith(".gif")) {
					$("#galleryContent").append('<div type="image" id="'+id+'" path="'+path+'/'+name+'" onclick="openViewer(\''+path+'/'+name+'\')" class="col galleryItem" unselectable="on" onselectstart="return false;" onmousedown="return false;"><div class="galleryItemIcon" style="background:url(\''+url+'\'),linear-gradient(180deg, rgba(65,65,65,1) 0%, rgba(1,1,1,1) 100%);border-radius:5px;"></div><div id="fileName">'+name+'</div>');
				} else if (name.endsWith(".mp4")||name.endsWith(".webm")||name.endsWith(".avi")) {
					$("#galleryContent").append('<div type="image" id="'+id+'" path="'+path+'/'+name+'" onclick="openVideo(\''+path+'/'+name+'\')" class="col galleryItem" unselectable="on" onselectstart="return false;" onmousedown="return false;"><div class="galleryItemIcon" style="background:url(\'<?=$http?>://<?=$_SERVER['HTTP_HOST']?>/vbcms-content/modules/gallery/assets//images/manager/video-camera.png\')"></div><div id="fileName">'+name+'</div>');
				} else {
					$("#galleryContent").append('<div type="file" id="'+id+'" path="'+path+'/'+name+'" onclick="download(\''+path+'/'+name+'\')" class="col galleryItem" unselectable="on" onselectstart="return false;" onmousedown="return false;"><div class="galleryItemIcon" style="background:url(\'<?=$http?>://<?=$_SERVER['HTTP_HOST']?>/vbcms-content/modules/gallery/assets//images/manager/document.png\')"></div><div id="fileName">'+name+'</div>');
				}
			});
		});
		//changeDirectory(path, "no", parent);
		$("#a"+parent).attr("onclick", "getChildDirectory('"+path+"','"+parent+"', 'no')");
		$("#c"+parent).attr("onclick", "getChildDirectory('"+path+"','"+parent+"', 'no')");

		$("#"+search_params.get('size')).trigger("click");

		$("#galleryContent").attr("oldParent", parent);
	}

	//////////////////////////
	// DRAG'N DROP
	//////////////////////////

	// Le Dra'N Drop n'est disponible que sur la page de la galerie (pour éviter des problèmes)
	if(mode=="full-gallery"){

		function dropHandler(ev) {
			console.log('File(s) dropped');

			// Prevent default behavior (Prevent file from being opened)
			ev.preventDefault();

			if (ev.dataTransfer.items) {
				// Use DataTransferItemList interface to access the file(s)
				for (var i = 0; i < ev.dataTransfer.items.length; i++) {
					// If dropped items aren't files, reject them
					if (ev.dataTransfer.items[i].kind === 'file') {
						var file = ev.dataTransfer.items[i].getAsFile();
						console.log('... file[' + i + '].name = ' + file.name);
						uploadFile(file);
					}
				}
			} else {
				// Use DataTransfer interface to access the file(s)
				for (var i = 0; i < ev.dataTransfer.files.length; i++) {
					console.log('... file[' + i + '].name = ' + ev.dataTransfer.files[i].name);
					uploadFile(ev.dataTransfer.files[i]);
				}
			}
		}

		function dragOverHandler(ev) {
			console.log('File(s) in drop zone'); 
			$("#galleryContent").css("background-color", "red");

			// Prevent default behavior (Prevent file from being opened)
			ev.preventDefault();
		}

		function dragLeaveHandler(ev) {
			$("#galleryContent").css("background-color", "white");

			ev.preventDefault();
		}

		function uploadFile(file) {
			console.log(file);
		    var xhr = new XMLHttpRequest();
		    var fd = new FormData();

		    var url = new URL(window.location.href);
			var search_params = url.searchParams;

		    xhr.open("POST", "<?=$http?>://<?=$_SERVER['HTTP_HOST']?>/vbcms-admin/gallery/backTasks", true);
		    xhr.onreadystatechange = function() {
		        if (xhr.readyState == 4 && xhr.status == 200 && xhr.responseText != "") {
		            // Handle response.
		            SnackBar({
                        message: "Erreur lors de l'envoie des fichiers: "+xhr.responseText,
                        status: "danger",
                        timeout: false
                    });
                    $("#galleryContent").css("background-color", "white");
		        } else {
		        	document.location.reload();
		        	$("#galleryContent").css("background-color", "white");
		        }
		    };
		    fd.append('uploadFile', file);
		    fd.append('path', search_params.get('path'));

		    // Initiate a multipart/form-data upload
		    xhr.send(fd);
		} 

	}

	//////////////////////////
	// BOUTONS DE NAVIGUATION
	//////////////////////////

	$("#gridBig").click(function() {
		$("#galleryContent").css("flex-direction", "row");
		$(".galleryItem").css("flex-direction", "column");
		$(".galleryItem").css("max-width", "15em");
		$(".galleryItem").css("margin-top", "0px");
		$(".galleryItemIcon").css("width", "96px");
		$(".galleryItemIcon").css("height", "96px");
		$(".galleryItemIcon").css("margin-right", "0px");
		$(".galleryItem i").css("font-size", "5em");
		$(".galleryItem i").css("margin-right", "0px");
		var url = new URL(window.location.href);
		var search_params = url.searchParams;
		search_params.set('size', 'gridBig');
		url.search = search_params.toString();
		var new_url = url.toString();
		window.history.replaceState({}, '',new_url);
	});
	$("#gridLittle").click(function() {
		$("#galleryContent").css("flex-direction", "row");
		$(".galleryItem").css("flex-direction", "column");
		$(".galleryItem").css("max-width", "10em");
		$(".galleryItem").css("margin-top", "0px");
		$(".galleryItemIcon").css("width", "64px");
		$(".galleryItemIcon").css("height", "64px");
		$(".galleryItem i").css("font-size", "3em");
		$(".galleryItemIcon").css("margin-right", "0px");
		$(".galleryItem i").css("margin-right", "0px");
		var url = new URL(window.location.href);
		var search_params = url.searchParams;
		search_params.set('size', 'gridLittle');
		url.search = search_params.toString();
		var new_url = url.toString();
		window.history.replaceState({}, '',new_url);
	});
	$("#gridList").click(function() {
		$("#galleryContent").css("flex-direction", "column");
		$(".galleryItem").css("flex-direction", "row");
		$(".galleryItem").css("max-width", "100%");
		$(".galleryItem").css("margin-top", "5px");
		$(".galleryItemIcon").css("width", "32px");
		$(".galleryItemIcon").css("height", "32px");
		$(".galleryItemIcon").css("margin-right", "5px");
		$(".galleryItem i").css("margin-right", "5px");
		$(".galleryItem i").css("font-size", "2em");
		var url = new URL(window.location.href);
		var search_params = url.searchParams;
		search_params.set('size', 'gridList');
		url.search = search_params.toString();
		var new_url = url.toString();
		window.history.replaceState({}, '',new_url);
	});

	///////////
	// A partir d'ici, les fonctionnalités ne doivent pas être accessible en mode sélecteur (prévention de bugs et problèmes divers)
	///////////

	if(mode=="full-gallery"){

		$("[data-toggle=createFolder]").popover({
		    container: 'body',
		    html: true,
		    placement: 'bottom',
		    sanitize: false,
		    content: 
		    `<div>
		      <div class="input-group">
		        <input id="createFolderName" type="text" class="form-control" placeholder="Nom du dossier">
		        <div class="input-group-append" id="button-addon1">
		          <button onclick="createFolder()" class="btn btn-outline-brown" type="button" data-toggle="popover" data-placement="bottom"
		              data-html="true" data-title="Search">
		            <i class="fas fa-folder-plus"></i>
		          </button>
		        </div>
		      </div>
		    </div>`
		});

		function createFolder(){
			folderName = $('input[id^="createFolderName"]').val();
			console.log(folderName);
			var url = new URL(window.location.href);
			var search_params = url.searchParams;
			path = search_params.get('path');
			$.get("<?=$http?>://<?=$_SERVER['HTTP_HOST']?>/vbcms-admin/gallery/backTasks/?createFolder="+encodeURIComponent(path+"/"+folderName), function(data) {
				if (data != "") {
					console.log(data);
				} else {
					document.location.reload();
				}
			})
		}

		/////////////////////////////
		// ICI C'EST LE CLIQUE DROIT
		/////////////////////////////

		document.onclick = hideMenu;
		$("[id^=galleryContent]").on("mouseenter", ".galleryItem", function() {
			document.oncontextmenu = rightClick; // Permet de remplacer le context menu par le miens

			if ($(this).attr("type") == "folder") {
				//console.log("c'est un dossier :D");
			} else if ($(this).attr("type") == "file") {
				//console.log("c'est un fichier :D");
			} else if ($(this).attr("type") == "image") {
				//console.log("c'est une image :D");
			}			
		});

		$("#galleryContent").on("mouseleave", ".galleryItem", function() {
			document.oncontextmenu = ""; //Remet le context menu par défaut
		});

		function hideMenu() { 
			document.getElementById("contextMenu").style.display = "none";
		} 

		function rightClick(e) { 
			e.preventDefault();
			if ($(e.target).attr("id")!=null) {
				$("#contextMenu").attr("itemId", $(e.target).attr("id"));
			} else {
				$("#contextMenu").attr("itemId", $(e.target).parent().attr("id"));
			}
			/*if ($(e.target).attr("path")!=null) {
				$("contextMenu").attr("itemId")=$(e.target).attr("id");
			} else {
				$("contextMenu").attr("itemId")=$(e.target).parent().attr("id");
			}*/

			if (document.getElementById("contextMenu").style.display == "block")hideMenu(); 
			else { 
				var menu = document.getElementById("contextMenu") 
				menu.style.display = 'block'; 
				menu.style.left = e.pageX + "px"; 
				menu.style.top = e.pageY + "px";
			} 
		}

		///////////////
		// EVENTS
		//////////////

		// Détails ////////////

		$("#explorerDetail").click(async function() {			
			//console.log("l'id de l'item en question est:"+$("#contextMenu").attr("itemId"));
			detailsDiv("details");
		});

		$("#saveFileDetails").click(function() {
			path = $("#fileDetailsDiv").attr("path");
			var details = [path, $("#fileTitle").val(), $("#fileDescription").val()];
			$.ajax({
			  url: "<?=$http?>://<?=$_SERVER['HTTP_HOST']?>/vbcms-admin/gallery/backTasks/?updateFileDetails="+encodeURIComponent(JSON.stringify(details)),
			});
			
		});

		$("#closeFileDetailsDiv").click(function() {
			$("#fileDetailsDiv").css("display", "none");
			resizePageContent(0, "right");
		});


		$("#explorerRename").click(function() {
			detailsDiv("rename");
		});

		$("#explorerMove").click(async function() {
			path = $("#"+$("#contextMenu").attr("itemId")).attr("path");
			$("#galleryNaviguationButtons").append("<div class=\"mx-2\"><a id=\"moveFile\" onclick href=\"#\" class=\"text-dark\">Déplacer ici <i class=\"fas fa-external-link-alt\"></i></a></div>");
			$("#galleryNaviguationButtons").append("<div class=\"mx-2\"><a id=\"copyFile\" onclick href=\"#\" class=\"text-dark\">Copier ici <i class=\"fas fa-copy\"></i></a></div>");
			$("#moveFile").attr("onclick", "copyMoveFile(\""+path+"\",\""+path+"\",\"move\")");
			$("#copyFile").attr("onclick", "copyMoveFile(\""+path+"\",\""+path+"\",\"copy\")");
		});

		$("#explorerDelete").click(async function() {
			path = $("#fileDetailsDiv").attr("path");
			$.ajax({
			  url: "<?=$http?>://<?=$_SERVER['HTTP_HOST']?>/vbcms-admin/gallery/backTasks/?deleteFileFolder="+encodeURIComponent(path),
			});
		});

		$(".fileDetailsDiv").on('click', "#saveFileRename", function(){
		    console.log("j'ai été cliqué");
			path = $("#fileDetailsDiv").attr("path");
			var details = [path, $("#fileRenameValue").val()];
			$.ajax({
			  url: "<?=$http?>://<?=$_SERVER['HTTP_HOST']?>/vbcms-admin/gallery/backTasks/?renameFile="+encodeURIComponent(JSON.stringify(details)),
			});
		});

		async function detailsDiv(action){
			if ($("#"+$("#contextMenu").attr("itemId")).attr("type") == "folder") {
				$(".fileDetailsDiv .imageBackground").css("background-image", "url()");
				$(".titleDescription").css("display", "none");
				console.log("c'est un dossier :D");
			} else if ($("#"+$("#contextMenu").attr("itemId")).attr("type") == "file") {
				$(".fileDetailsDiv .imageBackground").css("background-image", "url()");
				$(".titleDescription").css("display", "block");
				console.log("c'est un fichier :D");
			} else if ($("#"+$("#contextMenu").attr("itemId")).attr("type") == "image") {
				$(".fileDetailsDiv .imageBackground").css("background-image", $("#"+$("#contextMenu").attr("itemId")).children().css("background-image"));
				$(".titleDescription").css("display", "block");
				console.log("c'est une image :D");
			}
			
			path = $("#"+$("#contextMenu").attr("itemId")).attr("path");
			
			if ($("#"+$("#contextMenu").attr("itemId")).attr("type") == "folder"){
				await $.get("<?=$http?>://<?=$_SERVER['HTTP_HOST']?>/vbcms-admin/gallery/backTasks/?folderDetails="+path, function(data) {// Affiche uniquement les fichiers
					var details = JSON.parse(data);
					if (action == "rename") {
						$(".detailHeader").html("<h5>Renommer</h5><div class=\"d-flex align-items-center\"><input type=\"text\" class=\"form-control\" id=\"fileRenameValue\" value=\""+details[0]+"\" required=\"\"><button id=\"saveFileRename\" type=\"button\" class=\"btn btn-brown float-sm-right ml-2\"><i class=\"fas fa-save\"></i></button></div>");
					} else {
						$(".detailHeader").html("<h3>"+details[0]+"</h3><span>"+details[1]+" - "+details[2]+"</span>");
					}
					$("#fileTitle").val(details[3]);
					$("#fileDescription").val(details[4]);
				});
			} else {
				await $.get("<?=$http?>://<?=$_SERVER['HTTP_HOST']?>/vbcms-admin/gallery/backTasks/?fileDetails="+path, function(data) {// Affiche uniquement les fichiers
					var details = JSON.parse(data);
					if (action == "rename") {
						$(".detailHeader").html("<h5>Renommer</h5><div class=\"d-flex align-items-center\"><input type=\"text\" class=\"form-control\" id=\"fileRenameValue\" value=\""+details[0]+"\" required=\"\"><button id=\"saveFileRename\" type=\"button\" class=\"btn btn-brown float-sm-right ml-2\"><i class=\"fas fa-save\"></i></button></div>");
					} else {
						$(".detailHeader").html("<h3>"+details[0]+"</h3><span>"+details[1]+" - "+details[2]+"</span>");
					}
					$("#fileTitle").val(details[3]);
					$("#fileDescription").val(details[4]);
				});
			}
			

			$("#fileDetailsDiv").attr("path", path);
			$("#fileDetailsDiv").css("display", "block");
			resizePageContent(500, "right");
		}

		function copyMoveFile(path, dest, action){
			var url = new URL(window.location.href);
			var search_params = url.searchParams;
			var name = path.replace(/^.*[\\\/]/, '');
			var parametres = [path, search_params.get('path')+"/"+name, action];
			$.ajax({
			  url: "<?=$http?>://<?=$_SERVER['HTTP_HOST']?>/vbcms-admin/gallery/backTasks/?copyMoveFile="+encodeURIComponent(JSON.stringify(parametres)),
			});
			$("#moveFile").css("display", "none");
			$("#copyFile").css("display", "none");
			getChildDirectory(search_params.get('path'),$("#galleryContent").attr("oldParent"), 'no');
		}
		/////////////
		// Events Explorer

		$("#explorerDelete").click(function() {
			path = $("#"+$("#contextMenu").attr("itemId")).attr("path");
			$.get("<?=$http?>://<?=$_SERVER['HTTP_HOST']?>/vbcms-admin/gallery/backTasks/?deleteFileFolder="+encodeURIComponent(path), function(data) {
				if (data != "") {
					console.log(data);
				} else {
					document.location.reload();
				}
			})
			
		})

		function openFileId(pathId){ // Check le type de fichier que c'est
			$.get("<?=$http?>://<?=$_SERVER['HTTP_HOST']?>/vbcms-admin/gallery/backTasks/?fileDetails="+pathId, function(data) {
				var details = JSON.parse(data);
				name = details[0];
				if (name.endsWith(".png")||name.endsWith(".jpg")||name.endsWith(".gif")) {
					openViewer(pathId);
				} else if (name.endsWith(".mp4")||name.endsWith(".webm")||name.endsWith(".avi")) {
					openVideo(pathId);
				} else {
					console.log("fichier non pris en charge :(");
				}
			});
		}

		function openViewer(pathId){
			if ($("#galleryModal").attr("type")!="image") {
				$("#galleryModal").attr("type", "image");
				$(".galleryModalContent").html('<a id="viewerPrevious" class="viewerPrevious">\
							<div class="icon"><i class="far fa-chevron-left"></i></div>\
						</a>\
						<div id="viewerImage" class="viewerImage">\
							<img src="" alt="">\
						</div>\
						<a id="viewerNext" class="viewerNext">\
							<div class="icon"><i class="far fa-chevron-right"></i></div>\
						</a>');
			}
			$.get("<?=$http?>://<?=$_SERVER['HTTP_HOST']?>/vbcms-admin/gallery/backTasks/?fileDetails="+pathId, function(data) {
				var details = JSON.parse(data);
				var url = new URL(window.location.href);
				var search_params = url.searchParams;
				if(search_params.get('openFileId')==null){
					search_params.append('openFileId', details[5]);
				} else{
					search_params.set('openFileId', details[5]);
				}
					
				var new_url = url.toString();
				window.history.replaceState({}, '',new_url);

				$("#viewerImage > img").attr("src", cdnUrl+details[6]);
				$("#galleryModalTitle").html(details[0]);
				$("#galleryModal").attr("path", details[6]);

				path = details[6].substring(0,details[6].lastIndexOf("\/")+1);
				$.get("<?=$http?>://<?=$_SERVER['HTTP_HOST']?>/vbcms-admin/gallery/backTasks/?folderContent="+path+"&filesOnly", function(data) {
					var fichiers = JSON.parse(data);
					var i = 0;
					var indexCorrect = 0;
					fichiers.forEach(element =>{
						i++;
						if(element == details[6]){
							indexCorrect = i-1;
						}
					});
					if (fichiers[indexCorrect-1]!= null) {
						$("#viewerPrevious").attr("onclick", "openViewer(\""+fichiers[indexCorrect-1]+"\")");
					}
					if (fichiers[indexCorrect+1]!= null) {
						$("#viewerNext").attr("onclick", "openViewer(\""+fichiers[indexCorrect+1]+"\")");
					}
				});
				
			});


			$("#galleryModal").css("display", "block");
		}

		function openVideo(pathId){
			if ($("#galleryModal").attr("type")!="video") {
				$("#galleryModal").attr("type", "video");
				$(".galleryModalContent").html('<video id="videoPlayer" class="video-player" autoplay controls preload="auto" ></video>');
			}
			$.get("<?=$http?>://<?=$_SERVER['HTTP_HOST']?>/vbcms-admin/gallery/backTasks/?fileDetails="+pathId, function(data) {
				var details = JSON.parse(data);
				var url = new URL(window.location.href);
				var search_params = url.searchParams;
				if(search_params.get('openFileId')==null){
					search_params.append('openFileId', details[5]);
				} else{
					search_params.set('openFileId', details[5]);
				}
					
				var new_url = url.toString();
				window.history.replaceState({}, '',new_url);

				$("#videoPlayer").html("<source src=\""+cdnUrl+details[6]+"\" type=\"video/"+details[6].substr((details[6].lastIndexOf('.') +1) )+"\"/>");
				$("#galleryModalTitle").html(details[0]);
				$("#galleryModal").attr("path", details[6]);	
			});


			$("#galleryModal").css("display", "block");
		}

		function closeGalleryModal(){
			var url = new URL(window.location.href);
			var search_params = url.searchParams;
			search_params.delete('openFileId');
			
			var new_url = url.toString();
			window.history.replaceState({}, '',new_url);
			if($('#videoPlayer').length){
				document.querySelector("#videoPlayer").pause();
				$(".galleryModalContent").html("");
				$("#galleryModal").attr("type", "");
			}
			
			$('#galleryModal').css('display', 'none');
		}

	}
</script>