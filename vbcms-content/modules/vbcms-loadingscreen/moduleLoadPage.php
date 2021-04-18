<?php
// Je rappel qu'on a $type et $params
$params = $moduleParams;
$uploadFolderPath = $_SERVER['DOCUMENT_ROOT'].'/vbcms-content/uploads';


if($type=="admin") {
	// Variables nécessaire à création d'une page
	$pageDepedencies = '';

	if ($params[1]=="create") {
		
		// Variables Modifier la navbar à création d'une page
		$pageTitle = $translation["loadingscreen_create"];
		$pageDepedencies = '';
		$pageToInclude = $_SERVER['DOCUMENT_ROOT']."/vbcms-content/modules/vbcms-loadingscreen/admin/create.php";
		createModulePage($pageTitle, "", $pageDepedencies, $pageToInclude, 0);
	}
	
}elseif($type=="client"){

}

// Fonctions publiques


?>