<?php
// Je rappel qu'on a $type et $params
$params = $moduleParams;
$uploadFolderPath = $_SERVER['DOCUMENT_ROOT'].'/vbcms-content/uploads';


if($type=="admin") {
	// Variables nécessaire à création d'une page
	$pageDepedencies = '';

	if ($params[1]=="modifyNavbar") {
		
		// Variables Modifier la navbar à création d'une page
		$pageTitle = "Modifier la navbar";
		$pageDepedencies = '<script src="'.$websiteUrl.'vbcms-admin/js/jquery-sortable-lists-navbarEdit.js"></script>';
		$pageToInclude = $_SERVER['DOCUMENT_ROOT']."/vbcms-content/modules/website/admin/modifyNavbar.php";
		createModulePage($pageTitle, $pageDepedencies, $pageToInclude);
	}
	
}

// Fonctions publiques


?>