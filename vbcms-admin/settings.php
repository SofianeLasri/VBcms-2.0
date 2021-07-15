<?php
$activatedExtensions = $bdd->query("SELECT * FROM `vbcms-activatedExtensions`")->fetchAll(PDO::FETCH_ASSOC);
$extensionsFolder = $GLOBALS['vbcmsRootPath'].'/vbcms-content/extensions/';

foreach ($activatedExtensions as $activatedExtension){
    $extJsonPath = $extensionsFolder.$activatedExtension['path'].'/extensionInfos.json';
    if(file_exists($extJsonPath)){

    } else {
        $error = "<b>ERREUR:</b> Le fichier <code>".$extJsonPath.'/extensionInfos.json</code> n\'existe pas!';
    }
}

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title><?=$websiteName?> | <?=translate("settings")?></title>
	<?php include 'includes/depedencies.php';?>
	<link rel="stylesheet" href="<?=$websiteUrl?>vbcms-admin/vendors/pick-a-color/css/pick-a-color-1.2.3.min.css">
</head>
<body>
	<?php 
	include ('includes/navbar.php');
	?>

	<!-- Contenu -->
	<div class="page-content d-flex flex-column" leftSidebar="240" rightSidebar="0" style="min-height: calc(100% - 60px);">
        <?php
            if(isset($error) && !empty($error)){
                echo '<div class="alert alert-danger" role="alert">'.$error.'</div>';
            }
        ?>
		<h3><?=translate("settings")?></h3>
        <p>C'est ici que sont regroupés les paramètres de VBcms, mais également des différentes extensions.</p>
		
        <div class="settingsContainer flex-grow-1 d-flex flex-column">
            <div class="tabs">
                <ul id="tabExtSettingsLinks">
                    <li id="ext-VBcms">
                        <a href="#" onclick="change('VBcms')">Paramètres de VBcms</a>
                    </li>
                    <?php
                        foreach ($activatedExtensions as $activatedExtension){
                            $extJsonPath = $extensionsFolder.$activatedExtension['path'].'/extensionInfos.json';
                            if(file_exists($extJsonPath) && verifyUserPermission($_SESSION['user_id'], $activatedExtension["name"], 'access-settings')){
                                $extInfos = json_decode(file_get_contents($extJsonPath), true);
                                echo "<li id=\"ext-".$activatedExtension["name"]."\"><a href=\"#\" onclick=\"change('".$activatedExtension["name"]."')\">".$extInfos['showname']."</a></li>";
                            }
                        }
                    ?>
                    <!-- Modèle
                    <li id="nomExt">
                        <a href="#" class="active">VBcms Website System</a>
                    </li>
                    -->
                </ul>
            </div>
            <div id="settingsContent" class="content centerVerHori flex-grow-1">
                
            </div>
        </div>
    </div>
    
    <script src="<?=$websiteUrl?>vbcms-admin/vendors/pick-a-color/js/tinycolor-0.9.15.min.js"></script>
	<script src="<?=$websiteUrl?>vbcms-admin/vendors/pick-a-color/js/pick-a-color-1.2.3.min.js"></script>
    <script type="text/javascript">
        // S'éxecute une fois la page chargée
        $( document ).ready(function() {
            // On va récupérer l'url et ses paramètres
            let url = new URL(window.location.href);
		    let search_params = url.searchParams;

            // On check si le paramètre p existe
            if(search_params.get('p')==null){
                // S'il n'existe pas, on va le créer
                let array = {};
				array.moduleName="VBcms";
                array.parameters="";

                search_params.append('p', JSON.stringify(array));
                let newUrl = url.toString();
                window.history.replaceState({}, '', newUrl);
            }
            // Et on lance la fonction qui affiche la page
            setSettingsContent();
        });

        function setSettingsContent(){
            // On ajoute l'animation de chargement
            $("#settingsContent").addClass("centerVerHori");
            $("#settingsContent").html('<svg class="spinner" width="65px" height="65px" viewBox="0 0 66 66" xmlns="http://www.w3.org/2000/svg">\
                    <circle class="path" fill="none" stroke-width="6" stroke-linecap="round" cx="33" cy="33" r="30"></circle>\
                </svg>');
            
            // On récupère l'url ainsi que ses paramètres
            // Pas besoin de vérifier que p existe, on l'a déjà fait juste en haut
            let url = new URL(window.location.href);
		    let search_params = url.searchParams;
            
            // On récupère les infos de la requête
            var extSettingsQuery = JSON.parse(search_params.get('p'));
            // Et on surligne le lien qui correspond à l'extension souhaitée
            $("#tabExtSettingsLinks").find('.active').removeClass('active');
            $("#ext-"+extSettingsQuery.moduleName).addClass("active");

            // Pour le debug
            console.log("Debug - call:<?=$websiteUrl?>vbcms-admin/backTasks/?getSettingsHTML="+encodeURIComponent(search_params.get('p')));
            // On récupère le contenu de la page
            $.get("<?=$websiteUrl?>vbcms-admin/backTasks/?getSettingsHTML="+encodeURIComponent(search_params.get('p')), function(data) {
                // On supprime l'animation de chargement
                $("#settingsContent").removeClass("centerVerHori");
                // Et on insère le contenu
                $("#settingsContent").html(data);

                // Enfin, on active les différents éléments JS à chaque intégration de page, même si ce n'est pas toujours utile
                $('[data-toggle="tooltip"]').tooltip()
                $(".pick-a-color").pickAColor();
            });
        }

        function change(extensionName){
            // Cette fonction permet de charger une autre page

            // On va récupérer l'url et ses paramètres
            let url = new URL(window.location.href);
		    let search_params = url.searchParams;
            // On recréé la requête
            let array = {};
            array.moduleName=extensionName;
            array.parameters="";

            // Et on modifie le paramètre p
            search_params.set('p', JSON.stringify(array));
            let newUrl = url.toString();
            window.history.replaceState({}, '', newUrl);

            // Enfin on lance la fonction qui affiche la page
            setSettingsContent();
        }
    </script>
</body>
</html>