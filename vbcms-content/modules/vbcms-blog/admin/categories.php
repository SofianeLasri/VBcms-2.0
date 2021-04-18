<?php
if (isset($_POST["submit"])) {
	$response = $bdd->prepare("INSERT INTO `vbcms-blogCategories` (id, shortName, showName, childOf) VALUES (?,?,?,?)");
	$response->execute([null, $_POST["catogorySlug"], $_POST["catogoryName"], $_POST["categoryParent"]]);

} else if (isset($_GET["delete"]) AND (!empty($_GET["delete"]) OR $_GET["delete"]!=0)) {
	$response = $bdd->prepare("SELECT * FROM `vbcms-blogCategories` WHERE id = ?");
	$response->execute([$_GET["delete"]]);
	if (!empty($response->fetch())) {
		$response = $bdd->prepare("DELETE FROM `vbcms-blogCategories` WHERE id = ?");
		$response->execute([$_GET["delete"]]);
	} else {
		$errorMessage = ($translation["category_message_1"].$_GET["delete"]." ".$translation["dont_exist"].".");
	}
}
?>
<!-- Contenu -->
<div class="page-content" leftSidebar="240" rightSidebar="0">
	<h3><?=$translation["blog_categories"]?></h3>

	<div class="row mt-5">
		<div class="col-6 md-4">
			<h5><?=$translation["category_add"]?></h5>
			<form method="post" action="categories" class="mt-3">
				<div class="form-group">
					<label for="catogoryName"><?=$translation["category_name"]?></label>
					<input type="text" class="form-control" id="catogoryName" name="catogoryName" placeholder="<?=$translation["category_asupercategory"]?>" required>
				</div>
				<div class="form-group">
					<label for="catogorySlug"><?=$translation["slug"]?></label>
					<input type="text" class="form-control" id="catogorySlug" name="catogorySlug" required>
				</div>
				<div class="form-group">
					<label for="categoryParent"><?=$translation["category_parent"]?></label>
				    <select class="form-control" id="categoryParent" name="categoryParent">
				    	<option value="0"><?=$translation["none-f"]?></option>
				    	<?php
					  	$result = $bdd->query("SELECT * FROM `vbcms-blogCategories`");
					  	foreach ($result as $row) {
					  		$response = $bdd->prepare("SELECT showName FROM `vbcms-blogCategories` WHERE id = ?");
							$response->execute([$row['childOf']]);
							$response = $response->fetch(PDO::FETCH_ASSOC); 
							$parentName = $response["showName"];

					  		echo ('<option value="'.$row['id'].'">'.$row['showName']."</option>");
					  	}
					  	?>
				    </select>
				</div>
				<button type="submit" name="submit" class="btn btn-brown"><?=$translation["category_add"]?></button>
			</form>
			
		</div>
		<div class="col-6 md-8">
			<?php
			if (isset($errorMessage)) {
				echo ('<div class="alert alert-danger" role="alert">'.$errorMessage.'</div>');
			}
			?>
			<table class="table">
			  <thead class="thead-dark">
			    <tr>
			      <th scope="col"><?=$translation["name"]?></th>
			      <th scope="col"><?=$translation["slug"]?></th>
			      <th scope="col"><?=$translation["parent"]?></th>
			    </tr>
			  </thead>
			  <tbody>
			  	<?php
			  	$result = $bdd->query("SELECT * FROM `vbcms-blogCategories`");
			  	foreach ($result as $row) {
			  		$response = $bdd->prepare("SELECT showName FROM `vbcms-blogCategories` WHERE id = ?");
					$response->execute([$row['childOf']]);
					$response = $response->fetch(PDO::FETCH_ASSOC); 
					$parentName = $response["showName"];

			  		echo ("<tr id='row".$row['id']."'><td><span id='showName".$row['id']."'>".$row['showName']."</span><br><div class='rowSubText'><a href='#' onclick='modifyButton(".$row['id'].", \"".$row['showName']."\", \"".$row['shortName']."\", \"".$parentName."\", false)'>".$translation["modify"]."</a> | <a class='text-danger' href='?delete=".$row['id']."'>".$translation["delete"]."</a></div></td><td><span id='shortName".$row['id']."'>".$row['shortName']."</span></td><td><span id='parentName".$row['id']."'>".$parentName."</span></td></tr>");
			  	}
			  	?>
			  </tbody>
			</table>
		</div>
	</div>
</div>

<script type="text/javascript">
	$("#catogoryName").on("change paste keyup", function() {
		//console.log($(this).val()); 
		var slug = $(this).val()
		slug = slug.toLowerCase().replace(/ /g,'-').replace(/[^\w-]+/g,'');
		$('#catogorySlug').val(slug);
	});

  	function modifyButton(id, showName, shortName, parentName, close){
  		if (!close) {
  			$('#row'+id).html('<td><input type="text" class="form-control" id="changeCategoryName'+id+'" value="'+showName+'" required><br><div class="rowSubText"><a href="#" id="modify'+id+'" onclick="modifyButton('+id+', \'update\', \''+$('#changeCategorySlug'+id).val()+'\', \''+$('#categoryParent'+id+' option:selected').text()+'\', true)">Appliquer</a> | <a class="text-danger" href="#" id="cancel'+id+'" onclick="modifyButton('+id+', \''+showName+'\', \''+shortName+'\', \''+parentName+'\', true)">Annuler</a></div></td><td><input type="text" class="form-control" id="changeCategorySlug'+id+'" value="'+shortName+'" required></td><td><select class="form-control" id="categoryParent'+id+'" name="categoryParent"><option value="0">Aucune</option><?php
  						//En fait c'est sur une ligne, c'est pour ça que c'est aussi illisible
					  	$result = $bdd->query("SELECT * FROM `vbcms-blogCategories`");
					  	foreach ($result as $row) {
					  		$response = $bdd->prepare("SELECT showName FROM `vbcms-blogCategories` WHERE id = ?");
							$response->execute([$row['childOf']]);
							$response = $response->fetch(PDO::FETCH_ASSOC); 
							$parentName = $response["showName"];

					  		echo ('<option value="'.$row['id'].'">'.$row['showName']."</option>");
					  	}
					  	?></select></td>');
  		} else if(showName != "update"){
  			$('#row'+id).html('<td><span id="showName'+id+'">'+showName+'</span><br><div class="rowSubText"><a href="#" id="modify'+id+'" onclick="modifyButton('+id+', \''+showName+'\', \''+shortName+'\', \''+parentName+'\', false)">Modifier</a> | <a class="text-danger" href="?delete='+id+'">Supprimer</a></div></td><td><span id="shortName'+id+'">'+shortName+'</span></td><td><span id="parentName'+id+'">'+parentName+'</span></td>');
  		} else {//Met à jour
  			$.ajax({
			  url: "<?=$http?>://<?=$_SERVER['HTTP_HOST']?>/vbcms-admin/blog/backTask?updateCategory="+id+"&shortName="+$('#changeCategorySlug'+id).val()+"&showName="+encodeURIComponent($('#changeCategoryName'+id).val())+"&childOf="+$('#categoryParent'+id+' option:selected').val(),
			});
  			$('#row'+id).html('<td><span id="showName'+id+'">'+$('#changeCategoryName'+id).val()+'</span><br><div class="rowSubText"><a href="#" id="modify'+id+'" onclick="modifyButton('+id+', \''+$('#changeCategoryName'+id).val()+'\', \''+$('#changeCategorySlug'+id).val()+'\', \''+$('#categoryParent'+id+' option:selected').text()+'\', false)">'.$translation["modify"].'</a> | <a class="text-danger" href="?delete='+id+'">'.$translation["delete"].'</a></div></td><td><span id="shortName'+id+'">'+$('#changeCategorySlug'+id).val()+'</span></td><td><span id="parentName'+id+'">'+$('#categoryParent'+id+' option:selected').text()+'</span></td>');
  			
  		}
  	}

	
</script>