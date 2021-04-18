<!-- Contenu -->
<div class="page-content" leftSidebar="240" rightSidebar="0">
	<h3><?=$translation["modifyNavbarFull"]?></h3>
	<p><?=$translation["modifyNavbarDesc"]?></p>
	<div class="width-50em d-flex flex-column" id="page-content">
		<ul class="sortableLists navbarModify" id="parent-0">
			<!--<li id="item-7">
				<div class="modifyNavbarItemList">
		      		<span class="mx-2">Super text</span>
		      		<button onclick="modifyItem(7)" class="ml-auto mr-2 clickable btn btn-sm btn-brown">Modifier</button>
		      	</div>
		      	<div class="modifyNavbarItemEdit p-2">
		      		<div class="form-group">
		      			<label>Modifier le nom</label>
		      			<input class="form-control form-control-sm clickable" type="text" placeholder="Nom du lien">
		      		</div>
		      		<div class="form-group">
		      			<label>Modifier le lien</label>
		      			<input class="form-control form-control-sm clickable" type="text" placeholder="Lien">
		      		</div>
		      	</div>
		      	<ul>
		         	<li id="item-8">
		         		<div class="modifyNavbarItemList">
				      		<span class="mx-2">Super text</span>
				      		<button onclick="modifyItem(8)" class="ml-auto mr-2 clickable btn btn-sm btn-brown">Modifier</button>
				      	</div>
		         	</li>
		        	<li id="item-9">
		         		<div class="modifyNavbarItemList">
				      		<span class="mx-2">Super text</span>
				      		<button onclick="modifyItem(9)" class="ml-auto mr-2 clickable btn btn-sm btn-brown">Modifier</button>
				      	</div>
		         	</li>
		      	</ul>
		   </li>-->
		</ul>
		<div class="d-flex">
			<button class="btn btn-sm btn-brown" onclick="addNavItem()"><i class="fas fa-plus"></i> Ajouter un lien</button>
			<button class="btn btn-sm btn-brown mx-2" id="saveBtn">Enregistrer</button>
		</div>
	</div>
</div>

<div class="modal fade" id="depedenciesModal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 id="depedenciesModalTitle" class="modal-title"><?=$translation['ws_requireddependecies']?></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<p id="depedenciesModalDesc"><?=$translation['ws_enableRequireddependecies']?></p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
				<button id="depedenciesModalBtn" onclick="" type="button" class="btn btn-primary">Activer</button>
			</div>
		</div>
	</div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" data-backdrop="static" data-keyboard="false" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="staticBackdropLabel">Confirmer la suppression?</h5>
			</div>
			<div class="modal-body">
				Cette action est irréversible, sois certain que c'est ce que tu veux faire. :p<br>
				<b>Il se peut que d'autres liens soient dépendants de celui-ci.</b>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
				<button id="confirmDelete" type="button" class="btn btn-danger" data-dismiss="modal">Supprimer</button>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	var options = {
		opener: {
			active: true,
			as: 'html',
			close: '<i class="fa fa-minus red mx-2"></i>',
			open: '<i class="fa fa-plus mx-2"></i>',
		},

	    ignoreClass: 'clickable',

		placeholderClass: 'loadingBack',

		hintClass: 'hintClass',

		hintCss: {'background-color':'#d3ab89', 'border':'1px dashed white'},
	}

	$( document ).ready(function() {
		loadNavbar(0,0);
	});
	/* Ancienne fonction 
	function loadNavbar(parentId,state){
		$.get("<?=$websiteUrl?>vbcms-admin/backTasks/?loadClientNavbar="+parentId, function(data) {
			var navbarItems = JSON.parse(data);
			if (parentId!=0 && data!="[]") {
				$("#item-"+parentId).append("<ul id='parent-"+parentId+"'></ul>");
				//$("#item-"+parentId).appendTo("#parent-"+parentId);
			}
			jQuery.each(JSON.parse(data), function(index){
				$("#parent-"+parentId).append('\
					<li id="item-'+navbarItems[index]["id"]+'">\
		         		<div class="modifyNavbarItemList">\
				      		<span class="mx-2" id="itemName-'+navbarItems[index]["id"]+'">'+navbarItems[index]["value1"]+'</span>\
				      		<button id="btn-'+navbarItems[index]["id"]+'" onclick="modifyItem('+navbarItems[index]["id"]+')" class="ml-auto mr-2 clickable btn btn-sm btn-brown">Modifier</button>\
				      	</div>\
				      	<div id="modify-'+navbarItems[index]["id"]+'" class="modifyNavbarItemEdit p-2">\
				      		<div class="form-group">\
				      			<label>Modifier le nom</label>\
				      			<input id="modifyName-'+navbarItems[index]["id"]+'" class="form-control form-control-sm clickable" type="text" placeholder="Nom du lien" value="'+navbarItems[index]["value1"]+'">\
				      		</div>\
				      		<div class="form-group">\
				      			<label>Modifier le lien</label>\
				      			<input id="modifyLink-'+navbarItems[index]["id"]+'" class="form-control form-control-sm clickable" type="text" placeholder="Lien" value="'+navbarItems[index]["value2"]+'">\
				      		</div>\
				      		<span><a href="#" class="text-primary mt-n2 clickable" onclick="cancelEdit('+navbarItems[index]["id"]+')">Annuler</a> - \
				      		<a href="#" class="text-danger mt-n2 clickable" onclick="deleteItem('+navbarItems[index]["id"]+', false)"><i class="fas fa-trash-alt"></i> Supprimer</a></span>\
				      	</div>\
		         	</li>');
				loadNavbar(navbarItems[index]["id"],1);
			});
		});
		if (state===0) {
			setTimeout(() => {
				$('#parent-0').sortableLists( options );
		    }, 2000);
		}
	} */

	async function loadNavbar(parentId,state){
		await $.get("<?=$websiteUrl?>vbcms-admin/backTasks/?loadClientNavbar=all", function(data) {
			var navbarItems = JSON.parse(data);
			jQuery.each(JSON.parse(data), function(index){
				if (!$("#parent-"+navbarItems[index]["parentId"]).length) {
					$("#item-"+navbarItems[index]["parentId"]).append("<ul id='parent-"+navbarItems[index]["parentId"]+"'></ul>");
					//$("#item-"+parentId).appendTo("#parent-"+parentId);
				}
				$("#parent-"+navbarItems[index]["parentId"]).append('\
					<li id="item-'+navbarItems[index]["id"]+'">\
		         		<div class="modifyNavbarItemList">\
				      		<span class="mx-2" id="itemName-'+navbarItems[index]["id"]+'">'+navbarItems[index]["value1"]+'</span>\
				      		<button id="btn-'+navbarItems[index]["id"]+'" onclick="modifyItem('+navbarItems[index]["id"]+')" class="ml-auto mr-2 clickable btn btn-sm btn-brown">Modifier</button>\
				      	</div>\
				      	<div id="modify-'+navbarItems[index]["id"]+'" class="modifyNavbarItemEdit p-2">\
				      		<div class="form-group">\
				      			<label>Modifier le nom</label>\
				      			<input id="modifyName-'+navbarItems[index]["id"]+'" class="form-control form-control-sm clickable" type="text" placeholder="Nom du lien" value="'+navbarItems[index]["value1"]+'">\
				      		</div>\
				      		<div class="form-group">\
				      			<label>Modifier le lien</label>\
				      			<input id="modifyLink-'+navbarItems[index]["id"]+'" class="form-control form-control-sm clickable" type="text" placeholder="Lien" value="'+navbarItems[index]["value2"]+'">\
				      		</div>\
				      		<span><a href="#" class="text-primary mt-n2 clickable" onclick="cancelEdit('+navbarItems[index]["id"]+')">Annuler</a> - \
				      		<a href="#" class="text-danger mt-n2 clickable" onclick="deleteItem('+navbarItems[index]["id"]+', false)"><i class="fas fa-trash-alt"></i> Supprimer</a></span>\
				      	</div>\
		         	</li>');
			});
		});
		$('#parent-0').sortableLists( options );
	}

	function modifyItem(item){
		$("#modify-"+item).css("display", "flex");
		$("#btn-"+item).html("Enregistrer");
		$("#btn-"+item).attr("onclick", "saveItem("+item+")");
	}

	function saveItem(itemId){
		var item = [];
        item.push(itemId, $("#modifyName-"+itemId).val(), $("#modifyLink-"+itemId).val());
        console.log(item);

        var xhr = new XMLHttpRequest();
	    var fd = new FormData();

		xhr.open("POST", "<?=$websiteUrl?>vbcms-admin/backTasks", true);
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
                    $("#itemName-"+itemId).html($("#modifyName-"+itemId).val());
                    cancelEdit(itemId);
	        	}
	        }
	        
	    };
	    fd.append('modifyNavItem', JSON.stringify(item));

	    // Initiate a multipart/form-data upload
	    xhr.send(fd);
	}

	function cancelEdit(item){
		$("#modify-"+item).css("display", "none");
		$("#btn-"+item).html("Modifier");
		$("#btn-"+item).attr("onclick", "modifyItem("+item+")");
	}
	
	$('#saveBtn').on('click', function(){
		var data = $('#parent-0').sortableListsToArray();

        var xhr = new XMLHttpRequest();
	    var fd = new FormData();

		xhr.open("POST", "<?=$websiteUrl?>vbcms-admin/backTasks", true);
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
	    console.log($('#parent-0').sortableListsToArray());
	    fd.append('recreateClientNav', JSON.stringify(data));

	    // Initiate a multipart/form-data upload
	    xhr.send(fd);
	});

	function deleteItem(itemId, confirm){
		if (!confirm) {
			$("#confirmDelete").attr("onclick", "deleteItem(\'"+itemId+"\', true)");
			$("#deleteModal").modal("show");
		} else {
			$("#confirmDelete").attr("onclick", "");
			$.get("<?=$websiteUrl?>vbcms-admin/backTasks/?deleteNavItem="+itemId, function(data) {
				if (data=="") {
					SnackBar({
						message: "Suprression réussie!",
						status: "success"
					});
					$("#item-"+itemId).remove();
				} else {
					SnackBar({
						message: "Impossible de supprimer le lien: "+data,
						status: "danger",
						timeout: false
					});
				}
			});
		}
	}

	async function addNavItem(){
		var newId = parseInt(await giveMeNewId(1), 10);
		$("#parent-0").append('\
		<li id="item-'+newId+'">\
			<div class="modifyNavbarItemList">\
				<span class="mx-2" id="itemName-'+newId+'">Nouveau Lien</span>\
				<button id="btn-'+newId+'" onclick="modifyItem('+newId+')" class="ml-auto mr-2 clickable btn btn-sm btn-brown">Modifier</button>\
			</div>\
			<div id="modify-'+newId+'" class="modifyNavbarItemEdit p-2">\
				<div class="form-group">\
					<label>Modifier le nom</label>\
					<input id="modifyName-'+newId+'" class="form-control form-control-sm clickable" type="text" placeholder="Nom du lien" value="Nouveau Lien">\
				</div>\
				<div class="form-group">\
					<label>Modifier le lien</label>\
					<input id="modifyLink-'+newId+'" class="form-control form-control-sm clickable" type="text" placeholder="Lien" value="#">\
				</div>\
				<span><a href="#" class="text-primary mt-n2 clickable" onclick="cancelEdit('+newId+')">Annuler</a> - \
				<a href="#" class="text-danger mt-n2 clickable" onclick="deleteItem('+newId+', false)"><i class="fas fa-trash-alt"></i> Supprimer</a></span>\
			</div>\
		</li>');
	}

	function giveMeNewId(id){
		while($("#item-"+id).length){
			id=parseInt(id,10)+1;
		}
		return id;
	}
</script>