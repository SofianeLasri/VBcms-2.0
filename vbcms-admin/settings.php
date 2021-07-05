<?php
$curentUpdateCanal = $bdd->query("SELECT value FROM `vbcms-settings` WHERE name='updateCanal'")->fetchColumn();
$steamApiKey = $bdd->query("SELECT value FROM `vbcms-settings` WHERE name='steamApiKey'")->fetchColumn();

if (isset($_POST["submit"])) {
	$response = $bdd->prepare("UPDATE `vbcms-settings` SET value=? WHERE name='websiteName'");
	$response->execute([$_POST["websiteName"]]);

	$response = $bdd->prepare("UPDATE `vbcms-settings` SET value=? WHERE name='websiteDescription'");
	$response->execute([$_POST["websiteDescription"]]);

	$response = $bdd->prepare("UPDATE `vbcms-settings` SET value=? WHERE name='websiteMetaColor'");
	$response->execute([$_POST["websiteMetaColor"]]);

	$response = $bdd->prepare("UPDATE `vbcms-settings` SET value=? WHERE name='websiteLogo'");
	$response->execute([$_POST["websiteLogo"]]);

	$response = $bdd->prepare("UPDATE `vbcms-settings` SET value=? WHERE name='steamApiKey'");
	$response->execute([$_POST["steamApiKey"]]);

	$response = $bdd->prepare("UPDATE `vbcms-settings` SET value=? WHERE name='updateCanal'");
	$response->execute([$_POST["updateCanal"]]);

	if (isset($_POST["debugMode"])) {
		$response = $bdd->prepare("UPDATE `vbcms-settings` SET value=? WHERE name='debugMode'");
		$response->execute(["1"]);
	} else {
		$response = $bdd->prepare("UPDATE `vbcms-settings` SET value=? WHERE name='debugMode'");
		$response->execute(["0"]);
	}


	header("Refresh:0");
}


?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title><?=$websiteName?> | <?=$translation["settings"]?></title>
	<?php include 'includes/depedencies.php';?>
	<link rel="stylesheet" href="<?=$websiteUrl?>vbcms-admin/vendors/pick-a-color/css/pick-a-color-1.2.3.min.css">
</head>
<body>
	<?php 
	include ('includes/navbar.php');
	?>

	<!-- Contenu -->
	<div class="page-content d-flex flex-column" leftSidebar="240" rightSidebar="0" style="min-height: calc(100% - 60px);">
		<h3><?=$translation["settings"]?></h3>
        <p>C'est ici que sont regroupés les paramètres de VBcms, mais également des différentes extensions.</p>
		
        <div class="settingsContainer flex-grow-1 d-flex flex-column">
            <div class="tabs">
                <ul>
                    <li class="active">
                        <a href="#">Paramètres généraux</a>
                    </li>
                    <li>
                        <a href="#">VBcms Website System</a>
                    </li>
                </ul>
            </div>
            <div id="settingsContent" class="content centerVerHori flex-grow-1">
                
            </div>
        </div>
    </div>
    
    <script src="<?=$websiteUrl?>vbcms-admin/vendors/pick-a-color/js/tinycolor-0.9.15.min.js"></script>
	<script src="<?=$websiteUrl?>vbcms-admin/vendors/pick-a-color/js/pick-a-color-1.2.3.min.js"></script>
    <script type="text/javascript">
        $( document ).ready(function() {
            let url = new URL(window.location.href);
		    let search_params = url.searchParams;

            if(search_params.get('p')==null){
                let array = {};
				array.moduleName="VBcms";
                array.parameters="";

                search_params.append('p', JSON.stringify(array));
                let newUrl = url.toString();
                window.history.replaceState({}, '', newUrl);
            }
            setSettingsContent();
        });

        function setSettingsContent(){
            $("#settingsContent").addClass("centerVerHori");
            $("#settingsContent").html('<svg class="spinner" width="65px" height="65px" viewBox="0 0 66 66" xmlns="http://www.w3.org/2000/svg">\
                    <circle class="path" fill="none" stroke-width="6" stroke-linecap="round" cx="33" cy="33" r="30"></circle>\
                </svg>');
            let url = new URL(window.location.href);
		    let search_params = url.searchParams;

            console.log("Debug - call:<?=$websiteUrl?>vbcms-admin/backTasks/?getSettingsHTML="+encodeURIComponent(search_params.get('p')));
            $.get("<?=$websiteUrl?>vbcms-admin/backTasks/?getSettingsHTML="+encodeURIComponent(search_params.get('p')), function(data) {
                $("#settingsContent").removeClass("centerVerHori");
                $("#settingsContent").html(data);

                // Ici on active les différents éléments JS à chaque intégration de page
                $('[data-toggle="tooltip"]').tooltip()
                $(".pick-a-color").pickAColor();
            });
        }
    </script>
</body>
</html>