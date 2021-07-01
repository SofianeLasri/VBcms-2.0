<?php
// On va récupérer la liste des extensions activées pour la comparer à la liste d'extension installées
$activatedExtensions = $bdd->query("SELECT * FROM `vbcms-activatedExtensions`")->fetchAll(PDO::FETCH_ASSOC);

// On va scanner le dossier des extensions pour les afficher dans la page
$extensionsFolder = $GLOBALS['vbcmsRootPath'].'/vbcms-content/extensions/';
$extensionsFolderContent = scandir($extensionsFolder);
$extensionsList = array();
$extensionListIndex = 0;
foreach ($extensionsFolderContent as $extensionFolder){
	if(!in_array($extensionFolder,[".", ".."]) && is_dir($extensionsFolder.$extensionFolder)){ // Ici on check qu'il s'agisse bien d'un dossier
		if(file_exists($extensionsFolder.$extensionFolder.'/extensionInfos.json')){
			$extensionInfos = json_decode(file_get_contents($extensionsFolder.$extensionFolder.'/extensionInfos.json'),true);
			foreach($extensionInfos as $extensionInfoKey => $extensionInfoValue){
				$extensionsList[$extensionInfos['type'].'s'][$extensionListIndex][$extensionInfoKey] = $extensionInfoValue;
			}
			// On vérifie que l'extension dispose d'une icône
			if(file_exists($extensionsFolder.$extensionFolder.'/extension-logo.jpg')){
				$extensionsList[$extensionInfos['type'].'s'][$extensionListIndex]['extensionLogo'] = $GLOBALS['websiteUrl'].'vbcms-content/extensions/'.$extensionFolder.'/extension-logo.jpg';
			}elseif(file_exists($extensionsFolder.$extensionFolder.'/extension-logo.png')){
				$extensionsList[$extensionInfos['type'].'s'][$extensionListIndex]['extensionLogo'] = $GLOBALS['websiteUrl'].'vbcms-content/extensions/'.$extensionFolder.'/extension-logo.png';
			}else{
				$extensionsList[$extensionInfos['type'].'s'][$extensionListIndex]['extensionLogo'] = null;
			}

			$extensionsList[$extensionInfos['type'].'s'][$extensionListIndex]['activated'] = false; // Flag pour juste après
			$extensionsList[$extensionInfos['type'].'s'][$extensionListIndex]['isWsSuscribed'] = false; // Pour le moment on a pas le workshop donc je met false
		}
	}
}

// Maintenant on va comparer les extensiosn installées avec les extensions activées
foreach ($activatedExtensions as $activatedExtension){
	// Dans un premier temps on va vérifier qu'il existe une extension de ce type (ouai au moins si y en a pas c'est réglé x) )
	if(isset($extensionsList[$activatedExtension['type'].'s'])){
		for($i = 0; $i < count($extensionsList[$activatedExtension['type'].'s']); $i++){
			if($extensionsList[$activatedExtension['type'].'s'][$i]['name'] == $activatedExtension['name']){
				$extensionsList[$activatedExtension['type'].'s'][$i]['activated'] = true;
				break;
			}
		}
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title><?=$websiteName?> | <?=$translation["ws_addonsLists"]?></title>
	<?php include 'includes/depedencies.php';?>
</head>
<body>
	<?php 
	include ('includes/navbar.php');
	?>

	<!-- Contenu -->
	<div class="page-content" leftSidebar="240" rightSidebar="0">
		<h3><?=$translation["ws_manage"]?></h3>
		<p><?=$translation["ws_pageDesc"]?></p>

		<div class="width-50em d-flex flex-column">
			<h5>Debug</h5>
			<div class="border rounded my-2">
			<pre><code><?php echo 'extensionsFolderContent:<br>'; print_r($extensionsFolderContent); echo '<br>extensionsList:<br>'; print_r($extensionsList); ?></code></pre>
			</div>

			
			<?php
			foreach ($extensionsList as $extensionTypeName => $extensionTypeExtensions){
				if($extensionTypeName == "modules"){
					echo "<h5>".$translation["ws_modules"]."</h5>";
				}elseif($extensionTypeName == "themes"){
					echo "<h5>".$translation["ws_themes"]."</h5>";
				}elseif($extensionTypeName == "plugins"){
					echo "<h5>".$translation["ws_plugins"]."</h5>";
				}
				foreach ($extensionTypeExtensions as $extension){
					if(!empty($extension['extensionLogo'])) $backgroundLogo = 'style="background-image: url(\''.$extension['extensionLogo'].'\')"';
					else $backgroundLogo = null;
					echo('<div class="workshop-suscribedCard my-2" id="'.$extension['name'].'" depedencies="'.json_encode($extension['requiredModules']).'">
					<div class="addonLogo" '.$backgroundLogo.'></div>
					<div class="ml-4 addonDetails flex-grow-1">
						<h6 class="mb-0"><a class="text-dark text-decoration-none" href="#">'.$extension['showname'].'</a></h6>
						<small class="text-muted">Par <a class="text-brown" href="https://workshop.vbcms.net/team/'.$extension['author'].'">Team Workshop à Récupérer</a></small>
						<p>'.$extension['description'].'</p>
					</div>
					<div class="addonControl">');
					if($extension['activated'])
						echo '<button class="btn btn-sm btn-brown float-right my-1" id="toogle-addon-'.$extension['name'].'" onclick="disableAddon('.$extension['name'].')">'.$translation["ws_disable"].'</button>';
					else
						echo '<button class="btn btn-sm btn-brown float-right my-1" id="toogle-addon-'.$extension['name'].'" onclick="enableAddon('.$extension['name'].')">'.$translation["ws_enable"].'</button>';
					
					if($extension['isWsSuscribed'])
						echo '<button class="btn btn-sm btn-danger float-right my-1" onclick="unsuscribeAddon('.$extension['name'].')">Se désabonner</button>';
					
					echo('</div>
					</div>');
				}
				
			}
			?>
			<!--
			<h5>Modules</h5>
			<div class="workshop-suscribedCard my-2">
				<div class="addonLogo"></div>
				<div class="ml-4 addonDetails">
					<h6 class="mb-0"><a class="text-dark text-decoration-none" href="#">Nom de l'addon</a></h6>
					<small class="text-muted">Par <a class="text-brown" href="#">VBcms</a></small>
					<p>Une chouette description qui permettra à l'utilisateur de savoir à quoi sert votre addon. :D</p>
				</div>
				<div class="addonControl">
					<button class="btn btn-sm btn-brown float-right my-1" id="toogle-addon-16484" onclick="enableAddon(16484)">Activer</button>
					<button class="btn btn-sm btn-danger float-right my-1" onclick="unsuscribeAddon(16484)">Se désabonner</button>
				</div>
			</div>

			<h5 class="mt-4">Thèmes</h5>
			<div class="workshop-suscribedCard my-2">
				<div class="addonLogo"></div>
				<div class="ml-4 addonDetails">
					<h6 class="mb-0"><a class="text-dark text-decoration-none" href="#">Nom de l'addon</a></h6>
					<small class="text-muted">Par <a class="text-brown" href="#">VBcms</a></small>
					<p>Une chouette description qui permettra à l'utilisateur de savoir à quoi sert votre addon. :D</p>
				</div>
				<div class="addonControl">
					<button class="btn btn-sm btn-brown float-right my-1" id="toogle-addon-16484" onclick="enableAddon(16484)">Activer</button>
					<button class="btn btn-sm btn-danger float-right my-1" onclick="unsuscribeAddon(16484)">Se désabonner</button>
				</div>
			</div>
			-->
		</div>
		<div class="admin-tips">
			<div class="tip">
				<h5>Créer un addon</h5>
				<p><b>Créer un addon pour VBcms est un jeu d'enfant!</b><br><a href="#" class="text-brown">Check la documentation</a> pour en savoir plus, les créateurs sont régulièrement mis en avant sur le Workshop!</p>
				<img class="mt-n3" width="96" src="<?=$websiteUrl?>vbcms-admin/images/misc/create-addon.jpg">
			</div>
			<div class="tip">
				<h5>Quèsaco une dépendance?</h5>
				<p>Tu as très probablement déjà du voir ce message d'avertissement lors de l'activation de la désactivation d'un addon, sans trop savoir ce qu'est une dépendance.</p>
				<img class="mt-n1 mb-1" src="<?=$websiteUrl?>vbcms-admin/images/misc/alerte-dependance.jpg">
				<p><b>Une dépendance est un addon nécessaire au bon fonctionnement d'autres addons</b>. Le désactiver pourrait provoquer une erreur fatale, c'est pour cela que VBcms désactive tous ses liens par défaut.</p>
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

	<script type="text/javascript">

		function enableAddon(name, depedenciesId, confirm){
			if (depedenciesId!="[]"&&confirm==0) {
				$("#depedenciesModalTitle").html("<?=$translation['ws_requireddependecies']?>");
				$("#depedenciesModalDesc").html("<?=$translation['ws_enableRequireddependecies']?>");
				$("#depedenciesModalBtn").attr("onclick", "enableAddon("+name+", '"+depedenciesId+"', 1)");
				$("#depedenciesModalBtn").html("<?=$translation['ws_enable']?>");
				$('#depedenciesModal').modal('toggle');
			} else {
				var array = [];
				array.push(name);
				array.push(depedenciesId);
				console.log(encodeURIComponent(JSON.stringify(array)));
				$.get("<?=$websiteUrl?>vbcms-admin/backTasks/?enableWSAddon="+encodeURIComponent(JSON.stringify(array)), function(data) {
					if (data != "") {
						SnackBar({
							message: "<?=$translation['ws_errorEnableAddon']?>: "+data,
							status: "danger",
							timeout: false
						});
					} else {
						document.location.reload();
					}
				})
			}
		}

		function disableAddon(name, confirm){
			$.get("<?=$websiteUrl?>vbcms-admin/backTasks/?checkIfModuleIsUsedByOthers="+name, function(depedenciesId) {
				if (depedenciesId!="[]"&&confirm==0) {
					$("#depedenciesModalTitle").html("<?=$translation['ws_requireddependecies']?>");
					$("#depedenciesModalDesc").html("<?=$translation['ws_disableRequireddependecies']?>");
					$("#depedenciesModalBtn").attr("onclick", "disableAddon("+name+", 1)");
					$("#depedenciesModalBtn").html("<?=$translation['ws_disable']?>");
					$('#depedenciesModal').modal('toggle');
				} else {
					var array = [];
					array.push(name);
					array.push(depedenciesId);
					console.log(name);
					console.log(depedenciesId);
					console.log("<?=$websiteUrl?>vbcms-admin/backTasks/?disableWSAddon="+encodeURIComponent(JSON.stringify(array)));
					$.get("<?=$websiteUrl?>vbcms-admin/backTasks/?disableWSAddon="+encodeURIComponent(JSON.stringify(array)), function(data) {
						if (data != "") {
							SnackBar({
								message: "<?=$translation['ws_errorDisableAddon']?>: "+data,
								status: "danger",
								timeout: false
							});
						} else {
							document.location.reload();
						}
					});
				}
			})
		}

		function unsuscribeAddon(name){

		}
	</script>
</body>
</html>