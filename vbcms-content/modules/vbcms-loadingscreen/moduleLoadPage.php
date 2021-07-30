<?php
// Je rappel qu'on a $type et $params
$params = $moduleParams;
$uploadFolderPath = $GLOBALS['vbcmsRootPath'].'/vbcms-content/uploads';


if($type=="admin") {
	// Variables nécessaire à création d'une page
	$pageDepedencies = '<link href="'.$websiteUrl.'vbcms-content/modules/vbcms-loadingscreen/assets/css/admin.css" rel="stylesheet">';

	if ($params[1]=="create") {
		
		// Variables Modifier la navbar à création d'une page
		$pageTitle = $translation["loadingscreen_create"];
		$pageToInclude = $GLOBALS['vbcmsRootPath']."/vbcms-content/modules/vbcms-loadingscreen/admin/create.php";
		createModulePage($pageTitle, "", $pageDepedencies, $pageToInclude, 0);
	}elseif ($params[1]=="list") {
		
		// Variables Modifier la navbar à création d'une page
		$pageTitle = $translation["loadingscreens_list"];
		$pageToInclude = $GLOBALS['vbcmsRootPath']."/vbcms-content/modules/vbcms-loadingscreen/admin/list.php";
		createModulePage($pageTitle, "", $pageDepedencies, $pageToInclude, 0);
	}elseif ($params[1]=="themes") {
		
		// Variables Modifier la navbar à création d'une page
		$pageTitle = $translation["loadingscreens_themes"];
		$pageToInclude = $GLOBALS['vbcmsRootPath']."/vbcms-content/modules/vbcms-loadingscreen/admin/themes.php";
		createModulePage($pageTitle, "", $pageDepedencies, $pageToInclude, 0);
	}
	
}elseif($type=="client"){
	if (!empty($params[0])) {
		
		echo $params[0];
	}
}

// Fonctions publiques


?>