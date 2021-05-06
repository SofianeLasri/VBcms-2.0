<?php
if(isset($_POST["addon-slug"]) && !empty($_POST["addon-slug"]) && $_SESSION['loginType']!='local'){
	$encryptionKey = $bdd->query("SELECT value FROM `vbcms-settings` WHERE name = 'encryptionKey'")->fetchColumn();
	$wsUniqueId = file_get_contents("https://api.vbcms.net/ws/v1/getUniqueId/?token=".$encryptionKey."&addonName=".json_encode($_POST["addon-slug"]));

	if (is_numeric($wsUniqueId)) {
		$addonJsonInfos["workshopId"] = $wsUniqueId;
		$addonJsonInfos["requiredModules"] = "[".$_POST["addon-depedencies"]."]";
		$addonJsonInfos["name"] = $_POST["addon-slug"];
		$addonJsonInfos["showname"] = $_POST["addon-name"];
		$addonJsonInfos["version"] = $_POST["addon-version"];
		$addonJsonInfos["compatible"] = $_POST["addon-vbcmsVersion"];
		$addonJsonInfos["author"] = $_SESSION['user_id'];
		$addonJsonInfos["compatible"] = $_POST["addon-vbcmsVersion"];
		$addonJsonInfos["description"] = $_POST["addon-description"];
		if ($_POST["addon-type"] == 0){
			$jsonFilename = "module.json";
			$addonJsonInfos["clientAccess"] = $_POST["addon-clientAccess"];
			$addonJsonInfos["adminAccess"] = $_POST["addon-adminAccess"];
		} elseif ($_POST["addon-type"] == 1){
			$jsonFilename = "theme.json";
			$addonJsonInfos["designedFor"] = $_POST["addon-designedFor"];
		} elseif ($_POST["addon-type"] == 2){
			$jsonFilename = "plugin.json";
			// Pour l'instant rien pour les plugins
		} else {
			$error = "Type d'addon non reconnu.";
		}
		$addonJsonInfos = json_encode($addonJsonInfos);
		$order   = array("{\"", "\",", "\"}");
		$replace = array('{<br/>	"', "\",<br/>	", "\"<br/>}");
		$addonJsonInfos = str_replace($order, $replace, $addonJsonInfos);

	} else {
		$error = $wsUniqueId;
	}
	
}
?>
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
		<h3><?=$translation["ws_createAddon"]?></h3>
		<p>C'est ici que tu peux créer un addon, ou du moins préparer le terrin.<br></p>
		<ul>
			<li><strong>Un module</strong> est une application intégrée à VBcms qui aura son propre lien d'accès.</li>
			<li><strong>Un thème</strong> permet de changer l'apparence de l'interface des modules définis.</li>
			<li><strong>Un plugin</strong> est une micro application qui peut être utilisée dans un module.</li>
		</ul>
		<p>Tu dois choisir un type afin que VBcms créé ton addon avec les bonnes caractéristiques.</p>

		<div class="width-50em d-flex flex-column">
			<?php
			if ($_SESSION['loginType']=='local') {
				echo '<div class="alert alert-danger" role="alert">'.$translation["onlineAccountRequiredPhrase"].'</div>';
			}

			if(isset($error)){
				echo '<div class="alert alert-danger" role="alert"><strong>VBcms.net retourne une erreur:</strong><br>'.$error.'</div>';
			}
			?>

			<?php
			if (isset($addonJsonInfos)){?>
			<div class="mb-3">
				<h4>Code JSON a rentrer dans <code><?=$jsonFilename?></code></h4>
				<div class="border rounded">
				<pre><code><?=$addonJsonInfos?></code></pre>
				</div>
			</div>
			<?php } ?>

			<h5>Détails de l'addon</h5>
			<form id="addon-form" method="post" class="needs-validation" novalidate>
				<div class="form-group">
					<label>Type d'addon</label>
					<select name="addon-type" id="addon-type" class="form-control col-md-6" onchange="showAddonSpec()">
						<option value="0" selected>Module</option>
						<option value="1">Thème</option>
						<option value="2">Plugin</option>
					</select>
				</div>

				<div class="form-row">
					<div class="form-group col-md-6">
						<label>Nom de l'addon</label>
						<input type="text" id="addon-name" name="addon-name" value="<?= $_POST['addon-name'] ?? '' ?>" placeholder="Un super Addon" class="form-control" required>
						<div class="invalid-feedback">
					        Allez donne un p'tit nom sympa :p
					    </div>
					</div>
					<div class="form-group col-md-6">
						<label>Nom réduit</label>
						<input type="text" id="addon-slug" name="addon-slug" value="<?= $_POST['addon-slug'] ?? '' ?>" placeholder="Un-super-Addon" class="form-control" required>
						<div class="invalid-feedback">
					        Et en alphanumérique stp <3
					    </div>
					</div>
				</div>

				<div class="form-row">
					<div class="form-group col-md-6">
						<label>Version de l'addon</label>
						<input type="text" name="addon-version" value="<?= $_POST['addon-version'] ?? '' ?>" placeholder="10.59.27.03.2021" class="form-control" required>
					</div>
					<div class="form-group col-md-6">
						<label>Version de VBcms compatible</label>
						<input type="text" name="addon-vbcmsVersion" value="<?= $_POST['addon-vbcmsVersion'] ?? '2.0' ?>" placeholder="ne met pas 1.x stp" class="form-control" required>
					</div>
				</div>

				<div class="form-row">
					<div class="form-group col-md-6">
						<label>Dépendances de l'addon (A MODIFIER)</label>
						<input type="text" name="addon-depedencies" value="<?= $_POST['addon-depedencies'] ?? '' ?>" placeholder="ID des addons séparés par des ',' ex: 1,2,4" class="form-control">
					</div>
					<div class="form-group col-md-6">
						<label>Auteur de l'addon</label>
						<input type="text" name="addon-author" value="<?=$_SESSION['user_id']?>" placeholder="ID de l'auteur" class="form-control" readonly required>
					</div>
				</div>

				<div class="form-group">
					<label>Courte description de l'addon</label>
					<textarea name="addon-description" value="<?= $_POST['addon-description'] ?? '' ?>" class="form-control"></textarea>
				</div>

				<div id="moduleSpec" style="display:none;">
					<div class="form-row">
						<div class="form-group col-md-6">
							<label>Chemin d'accès client</label>
							<input type="text" name="addon-clientAccess" value="<?= $_POST['addon-clientAccess'] ?? '' ?>" placeholder="clientAccess" class="form-control">
						</div>
						<div class="form-group col-md-6">
							<label>Chemin d'accès admin</label>
							<input type="text" name="addon-adminAccess" value="<?= $_POST['addon-adminAccess'] ?? '' ?>" placeholder="adminAccess" class="form-control" required>
							<div class="invalid-feedback">
						        Il faut au moins un accès depuis le panel admin :p
						    </div>
						</div>
					</div>
				</div>

				<div id="themeSpec" style="display:none;">
					<div class="form-row">
						<div class="form-group col-md-6">
							<label>Conçu pour les modules</label>
							<input type="text" name="addon-designedFor" value="<?= $_POST['addon-designedFor'] ?? '' ?>" placeholder="ID du/des modules compatible(s)" class="form-control">
						</div>
					</div>
				</div>

				<div id="pluginSpec" style="display:none;">
					
				</div>

				<?php
				if ($_SESSION['loginType']=='local'){
					echo '<button type="button" onclick="$(\'#onlineAccountModal\').modal(\'toggle\');" class="btn btn-brown disabled">Générer le JSON</button>';
				} else {
					echo '<button type="submit" id="submit" class="btn btn-brown">Générer le JSON</button>';
				}
				?>
			</form>
			
		</div>
		<div class="admin-tips">
			<div class="tip">
				<h5><?=$translation["ws_createAddon"]?></h5>
				<p><?=$translation["tip_createAddon"]?></p>
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

	<div class="modal fade" id="onlineAccountModal">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 id="depedenciesModalTitle" class="modal-title"><?=$translation['onlineAccountRequired']?></h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<p id="depedenciesModalDesc"><?=$translation["onlineAccountRequiredPhrase"]?></p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal"><?=$translation["close"]?></button>
					<a href="https://vbcms.net/login/?from=<?php echo urlencode("$http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);?>" class="btn btn-brown">Se connecter</a>
				</div>
			</div>
		</div>
	</div>

	<script type="text/javascript">
		$(document).ready(function() {
			showAddonSpec()
		});

		$("#addon-name").on("change paste keyup", function() {
			//console.log($(this).val()); 
			var slug = $(this).val()
			slug = slug.toLowerCase().replace(/ /g,'-').replace(/[^\w-]+/g,'');
			$('#addon-slug').val(slug);
		});

		function showAddonSpec(){
			if ($( "#addon-type option:selected" ).val()==0) {
				$("#moduleSpec").css("display", "block");
				$("#themeSpec").css("display", "none");
				$("#pluginSpec").css("display", "none");
			} else if ($( "#addon-type option:selected" ).val()==1) {
				$("#moduleSpec").css("display", "none");
				$("#themeSpec").css("display", "block");
				$("#pluginSpec").css("display", "none");
			} else if ($( "#addon-type option:selected" ).val()==2) {
				$("#moduleSpec").css("display", "none");
				$("#themeSpec").css("display", "none");
				$("#pluginSpec").css("display", "block");
			}
		}

		function submitCreation(){

		}

		$("#submit").click(function(){
			var forms = document.getElementsByClassName('needs-validation');

			var validation = Array.prototype.filter.call(forms, function(form) {
				if(form.reportValidity() === false) {
					event.preventDefault();
			        event.stopPropagation();
				}
				form.classList.add('was-validated');
			});
		});
	</script>
</body>
</html>