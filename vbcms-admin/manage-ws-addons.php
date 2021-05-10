<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title><?=$websiteName?> | <?=$translation["ws_createAddon"]?></title>
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
			<h5>Modules</h5>
			<?php
			$modulesPath = $vbcmsRootPath.'/vbcms-content/modules/';
			$folder = array_diff(scandir($modulesPath), array('..', '.'));
			foreach ($folder as $isDir) {
				$modulePath = $isDir;
				$isDir = $modulesPath.$isDir;
				if (is_dir($isDir) == true) {
					$moduleJSON = array_diff(scandir($isDir), array('..', '.'));
					foreach ($moduleJSON as $isModule) {
						if ($isModule == "module.json") {
							$theme = ($isDir."/".$isModule);
							$themeJsonFileContents = file_get_contents($theme);
							$themeJsonFileContents = json_decode($themeJsonFileContents);

							$response = $bdd->prepare("SELECT * FROM `vbcms-modules` WHERE workshopId=?"); // Je récupère l'id du dossier parent
									$response->execute([$themeJsonFileContents->workshopId]);
									$response = $response->fetch(PDO::FETCH_ASSOC);
									if(file_exists($isDir."/module-logo.png")){
										$moduleLogo = $websiteUrl.'/vbcms-content/modules/'.basename($isDir).'/module-logo.png';
									} elseif(file_exists($isDir."/module-logo.jpg")){
										$moduleLogo = $websiteUrl.'/vbcms-content/modules/'.basename($isDir).'/module-logo.jpg';
									} else {
										$moduleLogo = "";
									}
									if (empty($response["activated"]) OR $response["activated"]==0) {
										echo '<div class="workshop-suscribedCard my-2" id="'.$themeJsonFileContents->workshopId.'" depedencies="'.$themeJsonFileContents->requiredModules.'">
									<div class="addonLogo" style="background: url(\''.$moduleLogo.'\'), linear-gradient(0deg, rgba(0, 0, 0, 0.75), rgba(0, 0, 0, 0.75));"></div>
									<div class="ml-4 addonDetails flex-grow-1">
										<h6 class="mb-0"><a class="text-dark text-decoration-none" href="#">'.$themeJsonFileContents->showname.'</a></h6>
										<small class="text-muted">Par <a class="text-brown" href="#">VBcms</a></small>
										<p>'.$themeJsonFileContents->description.'</p>
									</div>
									<div class="addonControl">
										<button class="btn btn-sm btn-brown float-right my-1" id="toogle-addon-'.$themeJsonFileContents->workshopId.'" onclick="enableAddon('.$themeJsonFileContents->workshopId.', \''.$themeJsonFileContents->requiredModules.'\', 0)">'.$translation["ws_enable"].'</button>
										<button class="btn btn-sm btn-danger float-right my-1" onclick="unsuscribeAddon('.$themeJsonFileContents->workshopId.')">'.$translation["ws_unsuscribe"].'</button>
									</div>
								</div>';
									} else {
										echo '<div class="workshop-suscribedCard my-2" id="'.$themeJsonFileContents->workshopId.'" depedencies="'.$themeJsonFileContents->requiredModules.'">
									<div class="addonLogo" style="background: url(\''.$moduleLogo.'\'), linear-gradient(0deg, rgba(0, 0, 0, 0.75), rgba(0, 0, 0, 0.75));"></div>
									<div class="ml-4 addonDetails flex-grow-1">
										<h6 class="mb-0"><a class="text-dark text-decoration-none" href="#">'.$themeJsonFileContents->showname.'</a></h6>
										<small class="text-muted">Par <a class="text-brown" href="#">VBcms</a></small>
										<p>'.$themeJsonFileContents->description.'</p>
									</div>
									<div class="addonControl">
										<button class="btn btn-sm btn-brown float-right my-1" id="toogle-addon-'.$themeJsonFileContents->workshopId.'" onclick="disableAddon('.$themeJsonFileContents->workshopId.', 0)">'.$translation["ws_disable"].'</button>
										<button class="btn btn-sm btn-danger float-right my-1" onclick="unsuscribeAddon('.$themeJsonFileContents->workshopId.')">'.$translation["ws_unsuscribe"].'</button>
									</div>
								</div>';
									}
									if(empty($response)){
										$response = $bdd->prepare("INSERT INTO `vbcms-modules` (name, path, clientAccess, adminAccess, activated, workshopId, version) VALUES (?,?,?,?,?,?,?)");
			 							$response->execute([$themeJsonFileContents->name, "/".$modulePath, $themeJsonFileContents->clientAccess, $themeJsonFileContents->adminAccess, 0, $themeJsonFileContents->workshopId, $themeJsonFileContents->version]);
									}				
						}
					}
				}
			}
			?>

			<!-- <div class="workshop-suscribedCard my-2">
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
			</div> -->

			<h5 class="mt-4">Thèmes</h5>
			<?php
			$modulesPath = $vbcmsRootPath.'/vbcms-content/themes/';
			$folder = array_diff(scandir($modulesPath), array('..', '.'));
			foreach ($folder as $isDir) {
				$modulePath = $isDir;
				$isDir = $modulesPath.$isDir;
				if (is_dir($isDir) == true) {
					$moduleJSON = array_diff(scandir($isDir), array('..', '.'));
					foreach ($moduleJSON as $isModule) {
						if ($isModule == "theme.json") {
							$theme = ($isDir."/".$isModule);
							$themeJsonFileContents = file_get_contents($theme);
							$themeJsonFileContents = json_decode($themeJsonFileContents);

							$response = $bdd->prepare("SELECT * FROM `vbcms-themes` WHERE workshopId=?"); // Je récupère l'id du dossier parent
									$response->execute([$themeJsonFileContents->workshopId]);
									$response = $response->fetch(PDO::FETCH_ASSOC);
									if(file_exists($isDir."/theme-logo.png")){
										$themeLogo = $websiteUrl.'/vbcms-content/themes/'.basename($isDir).'/theme-logo.png';
									} elseif(file_exists($isDir."/theme-logo.jpg")){
										$themeLogo = $websiteUrl.'/vbcms-content/themes/'.basename($isDir).'/theme-logo.jpg';
									} else {
										$themeLogo = "";
									}
									if (empty($response["activated"]) OR $response["activated"]==0) {
										echo '<div class="workshop-suscribedCard my-2" id="'.$themeJsonFileContents->workshopId.'" designedFor="'.$themeJsonFileContents->designedFor.'" depedencies="'.$themeJsonFileContents->requiredModules.'">
									<div class="addonLogo" style="background: url(\''.$themeLogo.'\'), linear-gradient(0deg, rgba(0, 0, 0, 0.75), rgba(0, 0, 0, 0.75));"></div>
									<div class="ml-4 addonDetails flex-grow-1">
										<h6 class="mb-0"><a class="text-dark text-decoration-none" href="#">'.$themeJsonFileContents->showname.'</a></h6>
										<small class="text-muted">Par <a class="text-brown" href="#">VBcms</a></small>
										<p>'.$themeJsonFileContents->description.'</p>
									</div>
									<div class="addonControl">
										<button class="btn btn-sm btn-brown float-right my-1" id="toogle-addon-'.$themeJsonFileContents->workshopId.'" onclick="enableAddon('.$themeJsonFileContents->workshopId.', \''.$themeJsonFileContents->requiredModules.'\', 0)">'.$translation["ws_enable"].'</button>
										<button class="btn btn-sm btn-danger float-right my-1" onclick="unsuscribeAddon('.$themeJsonFileContents->workshopId.')">'.$translation["ws_unsuscribe"].'</button>
									</div>
								</div>';
									} else {
										echo '<div class="workshop-suscribedCard my-2" id="'.$themeJsonFileContents->workshopId.'" designedFor="'.$themeJsonFileContents->designedFor.'" depedencies="'.$themeJsonFileContents->requiredModules.'">
									<div class="addonLogo" style="background: url(\''.$themeLogo.'\'), linear-gradient(0deg, rgba(0, 0, 0, 0.75), rgba(0, 0, 0, 0.75));"></div>
									<div class="ml-4 addonDetails flex-grow-1">
										<h6 class="mb-0"><a class="text-dark text-decoration-none" href="#">'.$themeJsonFileContents->showname.'</a></h6>
										<small class="text-muted">Par <a class="text-brown" href="#">VBcms</a></small>
										<p>'.$themeJsonFileContents->description.'</p>
									</div>
									<div class="addonControl">
										<button class="btn btn-sm btn-brown float-right my-1" id="toogle-addon-'.$themeJsonFileContents->workshopId.'" onclick="disableAddon('.$themeJsonFileContents->workshopId.', 0)">'.$translation["ws_disable"].'</button>
										<button class="btn btn-sm btn-danger float-right my-1" onclick="unsuscribeAddon('.$themeJsonFileContents->workshopId.')">'.$translation["ws_unsuscribe"].'</button>
									</div>
								</div>';
									}
									if(empty($response)){
										$response = $bdd->prepare("INSERT INTO `vbcms-themes` (workshopId, name, path, version, activated, designedFor) VALUES (?,?,?,?,?,?)");
			 							$response->execute([$themeJsonFileContents->workshopId, $themeJsonFileContents->name, "/".$modulePath,  $themeJsonFileContents->version, 0, $themeJsonFileContents->designedFor]);
									}				
						}
					}
				}
			}
			?>
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

		function enableAddon(workshopId, depedenciesId, confirm){
			if (depedenciesId!="[]"&&confirm==0) {
				$("#depedenciesModalTitle").html("<?=$translation['ws_requireddependecies']?>");
				$("#depedenciesModalDesc").html("<?=$translation['ws_enableRequireddependecies']?>");
				$("#depedenciesModalBtn").attr("onclick", "enableAddon("+workshopId+", '"+depedenciesId+"', 1)");
				$("#depedenciesModalBtn").html("<?=$translation['ws_enable']?>");
				$('#depedenciesModal').modal('toggle');
			} else {
				var array = [];
				array.push(workshopId);
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

		function disableAddon(workshopId, confirm){
			$.get("<?=$websiteUrl?>vbcms-admin/backTasks/?checkIfModuleIsUsedByOthers="+workshopId, function(depedenciesId) {
				if (depedenciesId!="[]"&&confirm==0) {
					$("#depedenciesModalTitle").html("<?=$translation['ws_requireddependecies']?>");
					$("#depedenciesModalDesc").html("<?=$translation['ws_disableRequireddependecies']?>");
					$("#depedenciesModalBtn").attr("onclick", "disableAddon("+workshopId+", 1)");
					$("#depedenciesModalBtn").html("<?=$translation['ws_disable']?>");
					$('#depedenciesModal').modal('toggle');
				} else {
					var array = [];
					array.push(workshopId);
					array.push(depedenciesId);
					console.log(workshopId);
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

		function unsuscribeAddon(workshopId){

		}
	</script>
</body>
</html>