<?php
// On va récupérer la liste des extensions activées pour la comparer à la liste d'extension installées
$activatedExtensions = $bdd->query("SELECT * FROM `vbcms-activatedExtensions`")->fetchAll(PDO::FETCH_ASSOC);

$activatedExtensionsNames = array();
foreach($activatedExtensions as $activatedExtension){
	array_push($activatedExtensionsNames, $activatedExtension['name']);
}
$requiredModulesNames = array();

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
				$extensionsList[$extensionInfos['type']][$extensionListIndex][$extensionInfoKey] = $extensionInfoValue;
			}
			// On vérifie que l'extension dispose d'une icône
			if(file_exists($extensionsFolder.$extensionFolder.'/extension-logo.jpg')){
				$extensionsList[$extensionInfos['type']][$extensionListIndex]['extensionLogo'] = $GLOBALS['websiteUrl'].'vbcms-content/extensions/'.$extensionFolder.'/extension-logo.jpg';
			}elseif(file_exists($extensionsFolder.$extensionFolder.'/extension-logo.png')){
				$extensionsList[$extensionInfos['type']][$extensionListIndex]['extensionLogo'] = $GLOBALS['websiteUrl'].'vbcms-content/extensions/'.$extensionFolder.'/extension-logo.png';
			}else{
				$extensionsList[$extensionInfos['type']][$extensionListIndex]['extensionLogo'] = null;
			}

			$extensionsList[$extensionInfos['type']][$extensionListIndex]['activated'] = false; // Flag pour juste après
			$extensionsList[$extensionInfos['type']][$extensionListIndex]['isWsSuscribed'] = false; // Pour le moment on a pas le workshop donc je met false

			$extensionListIndex++;
		}
	}
}

// Maintenant on va comparer les extensiosn installées avec les extensions activées
foreach ($activatedExtensions as $activatedExtension){
	// Dans un premier temps on va vérifier qu'il existe une extension de ce type (ouai au moins si y en a pas c'est réglé x) )
	if(isset($extensionsList[$activatedExtension['type']])){
		for($i = 0; $i < count($extensionsList[$activatedExtension['type']]); $i++){
			if($extensionsList[$activatedExtension['type']][$i]['name'] == $activatedExtension['name']){
				$extensionsList[$activatedExtension['type']][$i]['activated'] = true;
				foreach($extensionsList[$activatedExtension['type']][$i]['requiredModules'] as $requiredModule){
					if(!in_array($requiredModule, $requiredModulesNames))
						array_push($requiredModulesNames, $requiredModule);
				}
				break;
			}
		}
	}
}

// Enfin on va vérifier que toutes les dépendances sont satisfaites
$keys = array_keys($extensionsList);
foreach ($requiredModulesNames as $requiredModuleName){
	for($i = 0; $i < sizeof($keys); $i++){
		for($j = 0; $j < count($extensionsList[$keys[$i]]); $j++){
			if($extensionsList[$keys[$i]][$j]['name'] == $requiredModuleName && $extensionsList[$keys[$i]][$j]['activated'] == false){
				$extensionsList[$keys[$i]][$j]['isAnUnsatisfiedDependency'] = true;
			}
		}
	}
}

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title><?=$websiteName?> | <?=translate("ws_addonsLists")?></title>
	<?php include 'includes/depedencies.php';?>
</head>
<body>
	<style type="text/css">
		.warningBlink{
		    animation:warningBlinkText 2.4s infinite;
		}
		@keyframes warningBlinkText{
		    0%{     color: #FFC107;    }
		    22.5%{    color: transparent; }
		    27.5%{    color: transparent;  }
		    50%{   color: #DC3545;    }
			72.5%{    color: transparent; }
		    77.5%{    color: transparent;  }
			100%{     color: #FFC107;    }
		}
	</style>
	<?php 
	include ('includes/navbar.php');
	?>

	<!-- Contenu -->
	<div class="page-content" leftSidebar="240" rightSidebar="0">
		<h3><?=translate("ws_manage")?></h3>
		<p><?=translate("ws_pageDesc")?></p>

		<div class="width-50em d-flex flex-column">
		<?php
		if($debugMode == "1"){ ?>
			<h5>Debug</h5>
			<div class="border rounded my-2">
			<pre><code><?php echo 'extensionsFolderContent:<br>'; print_r($extensionsFolderContent); echo '<br>extensionsList:<br>'; print_r($extensionsList); ?></code></pre>
			</div>
		<?php } ?>
			
			<?php
			foreach ($extensionsList as $extensionTypeName => $extensionTypeExtensions){
				echo "<h5>".translate("ws_".$extensionTypeName.'s')."</h5>";
				foreach ($extensionTypeExtensions as $extension){
					if(!empty($extension['extensionLogo'])) $backgroundLogo = 'style="background-image: url(\''.$extension['extensionLogo'].'\')"';
					else $backgroundLogo = null;
					if(isset($extension['isAnUnsatisfiedDependency'])&&$extension['isAnUnsatisfiedDependency'] == true) $depedencyWarning = '<i class="fas fa-exclamation-circle warningBlink ml-1" data-toggle="tooltip" data-placement="top" title="'.translate("ws_requireddependece").'"></i>';
					else $depedencyWarning = null;

					echo('<div class="workshop-suscribedCard my-2" id="'.$extension['name'].'" depedencies=\''.json_encode($extension['requiredModules']).'\' adminAccess="'.$extension['adminAccess'].'" clientAccess="'.$extension['clientAccess'].'" type="'.$extension['type'].'">
					<div class="addonLogo" '.$backgroundLogo.'></div>
					<div class="ml-4 addonDetails flex-grow-1">
						<h6 class="mb-0"><a class="text-dark text-decoration-none" href="#">'.$extension['showname'].'</a>'.$depedencyWarning.'</h6>
						<small class="text-muted">Par <a class="text-brown" href="https://workshop.vbcms.net/team/'.$extension['author'].'">Team Workshop à Récupérer</a></small>
						<p>'.$extension['description'].'</p>
					</div>
					<div class="addonControl">');
					if($extension['activated'])
						echo '<button class="btn btn-sm btn-brown float-right my-1" id="toogle-addon-'.$extension['name'].'" onclick="disableAddon(\''.$extension['name'].'\')">'.translate("ws_disable").'</button>';
					else
						echo '<button class="btn btn-sm btn-brown float-right my-1" id="toogle-addon-'.$extension['name'].'" onclick="enableAddon(\''.$extension['name'].'\')">'.translate("ws_enable").'</button>';
					
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
				<h5>Créer une extension</h5>
				<p><b>Créer une extension pour VBcms est un jeu d'enfant!</b><br><a href="#" class="text-brown">Check la documentation</a> pour en savoir plus, les créateurs sont régulièrement mis en avant sur le Workshop!</p>
				<img class="mt-n3" width="96" src="<?=$websiteUrl?>vbcms-admin/images/misc/create-addon.jpg">
			</div>
			<div class="tip">
				<h5>Quèsaco une dépendance?</h5>
				<p>Tu as très probablement déjà du voir ce message d'avertissement lors de l'activation de la désactivation d'une extension, sans trop savoir ce qu'est une dépendance.</p>
				<img class="mt-n1 mb-1" src="<?=$websiteUrl?>vbcms-admin/images/misc/alerte-dependance.jpg">
				<p><b>Une dépendance est une extension nécessaire au bon fonctionnement d'autres extensions</b>. La désactiver pourrait provoquer une erreur fatale, c'est pour cela que VBcms désactive tous ses liens par défaut.</p>
			</div>
		</div>
	</div>

	<div class="modal fade" id="extensionActivationModal">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header bg-brown text-white">
					<h5 id="extensionActivationModalTitle" class="modal-title"><?=translate('ws_activateModule')?></h5>
					<button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body" id="extensionActivationModalBody">
					<div id="moduleAccessDiv" moduleName="">
						<div class="form-group">
							<label><?=translate('ws_clientAccess')?></label>
							<input type="text" class="form-control" id="clientAccessInput" placeholder="">
							<small class="form-text text-muted"><?=translate("ws_clientAccessExplaination")?></small>
							<div class="invalid-feedback">Veuillez renter un alias unique</div>
						</div>
						<div class="form-group">
							<label><?=translate('ws_adminAccess')?></label>
							<input type="text" class="form-control" id="adminAccessInput" placeholder="">
							<small class="form-text text-muted"><?=translate("ws_adminAccessExplaination")?></small>
							<div class="invalid-feedback">Veuillez renter un alias unique</div>
						</div>
					</div>

					<div id="depedenciesInfosDiv">
						<h5><?=translate("note")?></h5>
						<p><?=translate('ws_enableRequiredDepedencies')?><br>
						<?=translate('ws_enableRequiredDepedenciesMarkerInfo')?></p>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-outline-brown" data-dismiss="modal"><?=translate("close")?></button>
					<button id="extensionActivationModalBtn" onclick="" type="button" class="btn btn-brown"><?=translate("ws_enable")?></button>
				</div>
			</div>
		</div>
	</div>
	
	<div class="modal fade" id="extensionDesactivationModal">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header bg-brown text-white">
					<h5 id="extensionDesacctivationModalTitle" class="modal-title"><?=translate("ws_disableExtension")?></h5>
					<button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<p><?=translate("ws_askExtToDeleteItsData")?></p>
				</div>
				<div class="modal-footer">
					<button id="extensionDesactivationModalBtnNo" onclick="" type="button" class="btn btn-outline-brown"><?=translate("no")?></button>
					<button id="extensionDesactivationModalBtnYes" onclick="" type="button" class="btn btn-brown"><?=translate("yes")?></button>
				</div>
			</div>
		</div>
	</div>

	<script type="text/javascript">
		$(function () {
			$('[data-toggle="tooltip"]').tooltip()
		})

		document.getElementById('adminAccessInput').addEventListener("change", function (evt) {
			validateAccesses();
		}, false);

		document.getElementById('clientAccessInput').addEventListener("change", function (evt) {
			validateAccesses();
		}, false);
		
		function validateAccesses(){
			var array = {
				adminAccess: $("#adminAccessInput").val(),
				clientAccess: $("#clientAccessInput").val()
			};
			$.get("<?=$websiteUrl?>vbcms-admin/backTasks/?checkModulesAliases="+encodeURIComponent(JSON.stringify(array)), function(data) {
				if (!isJson(data)) {
					SnackBar({
						message: "<?=translate('ws_cantVerifyModulesAlias')?>: "+data,
						status: "danger",
						timeout: false
					});
				} else {
					var json = JSON.parse(data);
					console.log(json.clientAccess);
					if(json.clientAccess==true){
						$("#clientAccessInput").parent().children(".invalid-feedback").css("display", 'block');
					} else {
						$("#clientAccessInput").parent().children(".invalid-feedback").css("display", 'none');
					}
					console.log(json.adminAccess);
					if(json.adminAccess==true){
						$("#adminAccessInput").parent().children(".invalid-feedback").css("display", 'block');
					} else {
						$("#adminAccessInput").parent().children(".invalid-feedback").css("display", 'none');
					}

					if(json.clientAccess==false && json.adminAccess==false){
						$("#extensionActivationModalBtn").attr("onclick", "enableAddon('"+$("#moduleAccessDiv").attr('moduleName')+"', '"+$("#adminAccessInput").val()+"', '"+$("#clientAccessInput").val()+"')");
						if(typeof $("#extensionActivationModalBtn").attr("disabled") !== 'undefined'){
							$("#extensionActivationModalBtn").removeAttr("disabled");
						}
					}else{
						console.log("test");
						$("#extensionActivationModalBtn").attr("disabled", "");
					}
				}
			});
		}

		function enableAddon(name, adminAccess, clientAccess){
			if(($("#"+name).attr("type")=="module") && ((typeof adminAccess === 'undefined') || (typeof clientAccess === 'undefined'))){
				$("#moduleAccessDiv").css('display', 'block');
				$("#moduleAccessDiv").attr('moduleName', name);
				$("#extensionActivationModalTitle").html('<?=translate("ws_enableModule")?>');

				$("#clientAccessInput").attr("placeholder", $("#"+name).attr("clientAccess"));
				$("#adminAccessInput").attr("placeholder", $("#"+name).attr("adminAccess"));
				$("#clientAccessInput").attr("value", $("#"+name).attr("clientAccess"));
				$("#adminAccessInput").attr("value", $("#"+name).attr("adminAccess"));

				$("#extensionActivationModalBtn").attr("onclick", "enableAddon('"+name+"', '"+$("#"+name).attr("adminAccess")+"', '"+$("#"+name).attr("clientAccess")+"')");
				$("#extensionActivationModalBtn").html("<?=translate('ws_enable')?>");
				$('#extensionActivationModal').modal('toggle');

				if($("#"+name).attr("depedencies") != "[]"){
					$("#depedenciesInfosDiv").css('display', 'block');
				} else {
					$("#depedenciesInfosDiv").css('display', 'none');
				}
			} else {
				var array = {};
				array.name=name;
				if($("#"+name).attr("type")=="module"){
					array.adminAccess=adminAccess;
					array.clientAccess=clientAccess;
				}
				$.get("<?=$websiteUrl?>vbcms-admin/backTasks/?enableExtension="+encodeURIComponent(JSON.stringify(array)), function(data) {
					if (data != "") {
						SnackBar({
							message: "<?=translate('ws_errorEnableAddon')?>: "+data,
							status: "danger",
							timeout: false
						});
					} else {
						document.location.reload();
					}
				})
			}
		}

		function disableAddon(name, deleteData){
			if(typeof deleteData === 'undefined'){
				$("#extensionDesactivationModalBtnYes").attr("onclick", "disableAddon('"+name+"', true)");
				$("#extensionDesactivationModalBtnNo").attr("onclick", "disableAddon('"+name+"', false)");
				$('#extensionDesactivationModal').modal('toggle');
			} else {
				var array = {};
				array.name=name;
				array.deleteData=deleteData;
				$.get("<?=$websiteUrl?>vbcms-admin/backTasks/?disableExtension="+encodeURIComponent(JSON.stringify(array)), function(data) {
					if (data != "") {
						SnackBar({
							message: "<?=translate('ws_errorDisableAddon')?>: "+data,
							status: "danger",
							timeout: false
						});
					} else {
						document.location.reload();
					}
				})
			}
		}

		function unsuscribeAddon(name){

		}
	</script>
</body>
</html>