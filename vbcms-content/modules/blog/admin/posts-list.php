<!-- Contenu -->
<div class="page-content" leftSidebar="240" rightSidebar="0">
	<h3>Liste des articles</h3>

	<div class="d-flex flex-column">
		<div class="posts-list-options">
			<a id="allPosts" onclick="listPosts('allPosts')" href="#" class="text-primary">Tous (<?php echo $bdd->query("SELECT COUNT(*) FROM `vbcms-blogPosts`")->fetchColumn()+$bdd->query("SELECT COUNT(*) FROM `vbcms-blogDrafts`")->fetchColumn();?>)</a> | <a id="publishedOnly" onclick="listPosts('publishedOnly')" href="#" class="text-primary">Publiés (<?php echo $bdd->query("SELECT COUNT(*) FROM `vbcms-blogPosts`")->fetchColumn();?>)</a> | <a id="draftsOnly" onclick="listPosts('draftsOnly')" href="#" class="text-primary">Brouillons (<?php echo $bdd->query("SELECT COUNT(*) FROM `vbcms-blogDrafts`")->fetchColumn();?>)</a>
		</div>
		
		<div id="postsDiv" class="d-flex flex-wrap mt-2">

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
				Cette action est irréversible, sois certain que c'est ce que tu veux faire. :p
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
				<button id="confirmDelete" type="button" class="btn btn-danger" data-dismiss="modal">Supprimer</button>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">

	$( document ).ready(function() {
		var url = new URL(window.location.href);
		var search_params = url.searchParams;
		if(search_params.get('list')==null){
			search_params.append('list', 'allPosts');
			var new_url = url.toString();
			window.history.replaceState({}, '',new_url);

			listPosts("allPosts");
		} else {
			listPosts(search_params.get('list'));
		}
	});

	async function listPosts(view){
		var params = [];
		params.push(view);
		params.push("ASC");
		$("#"+view).removeClass("text-primary");
		$("#"+view).addClass("current text-dark");
		$("#postsDiv").html("");

		var url = new URL(window.location.href);
		var search_params = url.searchParams;
		search_params.set('list', view);
		var new_url = url.toString();
		window.history.replaceState({}, '',new_url);

		console.log("<?=$http?>://<?=$_SERVER['HTTP_HOST']?>/blog/backTasks/?getPostsList="+JSON.stringify(params));

		await $.get("<?=$http?>://<?=$_SERVER['HTTP_HOST']?>/blog/backTasks/?getPostsList="+JSON.stringify(params), function(data) {
			var postList = JSON.parse(data);
			jQuery.each(JSON.parse(data), function(index){
				if(postList[index]["type"]=="post"){
					$("#postsDiv").append('\
					<div id="'+postList[index]["id"]+'" class="card mr-2 mb-2" style="width: 18rem;">\
						<div style="background: url(\''+postList[index]["headerImage"]+'\'), linear-gradient(rgb(65, 65, 65) 0%, rgb(1, 1, 1) 100%);" class="card-img-top">\
							<span class="badge badge-success position-absolute m-1">'+postList[index]["type"]+'</span>\
						</div>\
						<div class="card-body">\
							<h5 class="card-title">'+postList[index]["title"]+'</h5>\
							<p class="card-subtitle mb-2 text-muted">Par '+postList[index]["author"]+' - '+postList[index]["category"][1]+'</p>\
							<p class="card-text">'+postList[index]["description"]+'</p>\
							<a href="/vbcms-admin/blog/post-new?modifyPost='+postList[index]["id"]+'" class="btn btn-brown btn-sm"><i class="fas fa-pencil-alt"></i> Modifier l\'article</a>\
							<a href="<?=$http?>://<?=$_SERVER['HTTP_HOST']?>/blog/'+postList[index]["slug"]+'" target="_blank"  class="btn btn-success btn-sm"><i class="fas fa-eye"></i></a>\
							<a href="#" onclick="deletePostDraft(\''+postList[index]["type"]+'\', \''+postList[index]["id"]+'\', false)" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></a>\
						</div>\
					</div>');
				} else {
					$("#postsDiv").append('\
					<div id="'+postList[index]["randId"]+'" class="card mr-2 mb-2" style="width: 18rem;">\
						<div style="background: url(\''+postList[index]["headerImage"]+'\'), linear-gradient(rgb(65, 65, 65) 0%, rgb(1, 1, 1) 100%);" class="card-img-top">\
							<span class="badge badge-warning position-absolute m-1">'+postList[index]["type"]+'</span>\
						</div>\
						<div class="card-body">\
							<h5 class="card-title">'+postList[index]["title"]+'</h5>\
							<p class="card-subtitle mb-2 text-muted">Par '+postList[index]["author"]+' - '+postList[index]["category"][1]+'</p>\
							<p class="card-text">'+postList[index]["description"]+'</p>\
							<a href="/vbcms-admin/blog/post-new?modifyDraft='+postList[index]["randId"]+'" class="btn btn-brown btn-sm"><i class="fas fa-pencil-alt"></i> Modifier le brouillon</a>\
							<a href="#" onclick="deletePostDraft(\''+postList[index]["type"]+'\', \''+postList[index]["randId"]+'\', false)" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></a>\
						</div>\
					</div>');
				}
			});
		});
	}

	function deletePostDraft(type, id, confirm){
		if (!confirm) {
			$("#confirmDelete").attr("onclick", "deletePostDraft(\'"+type+"\', \'"+id+"\', true)");
			$("#deleteModal").modal("show");
		} else {
			$("#confirmDelete").attr("onclick", "");
			var params = [];
			params.push(type);
			params.push(id);
			$.get("<?=$http?>://<?=$_SERVER['HTTP_HOST']?>/vbcms-admin/blog/backTasks/?deletePostDraft="+JSON.stringify(params), function(data) {
				if (data=="") {
					SnackBar({
						message: "Suprression réussie!",
						status: "success"
					});
					$("#"+id).remove();
				} else {
					SnackBar({
						message: "Impossible de supprimer le "+type+": "+data,
						status: "danger",
						timeout: false
					});
				}
			});
		}
	}
</script>