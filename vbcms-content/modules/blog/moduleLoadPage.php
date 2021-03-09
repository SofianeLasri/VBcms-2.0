<?php
// Je rappel qu'on a $type et $params
$params = $moduleParams;

// Fonctions publiques
require $_SERVER['DOCUMENT_ROOT']."/vbcms-content/modules/blog/includes/client-functions.php";

if ($type=="client") {
	if ($params[0]=="backTasks"){
		// Permet d'intéragir avec la base de donnée en mode client -> tout ce qui est get par exemple

		// Récupère la liste des posts
		if (isset($_GET["getPostsList"]) AND !empty($_GET["getPostsList"])) {
			$conditions = json_decode($_GET["getPostsList"]);
			echo json_encode(getPostsList($conditions[0], $conditions[1]));
		}
	}elseif ($params[0]!=""){
		$pageTitle = "Liste des articles";
		$pageToInclude = $_SERVER['DOCUMENT_ROOT']."/vbcms-content/modules/blog/client/viewArticle.php";
		$pageDepedencies = "";
		createModulePage($pageTitle, $pageDepedencies, $pageToInclude);
	}
} elseif($type=="admin") {
	// Variables nécessaire à création d'une page
	$pageDepedencies = '
	<!-- Summernote -->
	<link rel="stylesheet" href="'.$http.'://'.$_SERVER["HTTP_HOST"].'/vbcms-content/modules/blog/assets/vendors/summernote/dist/summernote-bs4.css">
	<script type="text/javascript" src="'.$http.'://'.$_SERVER["HTTP_HOST"].'/vbcms-content/modules/blog/assets/vendors/summernote/dist/summernote-bs4.js"></script>
	<link href="'.$http.'://'.$_SERVER["HTTP_HOST"].'/vbcms-content/modules/blog/assets/vendors/tam-emoji/css/emoji.css" rel="stylesheet">
	<script src="'.$http.'://'.$_SERVER["HTTP_HOST"].'/vbcms-content/modules/blog/assets/vendors/tam-emoji/js/config.js"></script>
  	<script src="'.$http.'://'.$_SERVER["HTTP_HOST"].'/vbcms-content/modules/blog/assets/vendors/tam-emoji/js/tam-emoji.min.js"></script>
  		';

	// Fonction admin		
	require $_SERVER['DOCUMENT_ROOT']."/vbcms-content/modules/blog/includes/admin-functions.php";

	if (isset($_GET["updateCategory"]) AND !empty($_GET["updateCategory"])) {
		$response = $bdd->prepare("UPDATE `vbcms-blogCategories` SET shortName= ?, showName=?, childOf=? WHERE id = ?");
		$response->execute([$_GET["shortName"], $_GET["showName"], $_GET["childOf"], $_GET["updateCategory"]]);

		// Récupère le contenu des dossiers selon les conditions spécifiées
	} elseif ($params[1]=="post-new") {
		// Variables nécessaire à création d'une page
		$pageTitle = "Éditeur";
		$pageToInclude = $_SERVER['DOCUMENT_ROOT']."/vbcms-content/modules/blog/admin/post-new.php";
		createModulePage($pageTitle, $pageDepedencies, $pageToInclude);

	} elseif($params[1]=="posts-list"){
		$pageTitle = "Liste des articles";
		$pageToInclude = $_SERVER['DOCUMENT_ROOT']."/vbcms-content/modules/blog/admin/posts-list.php";
		createModulePage($pageTitle, $pageDepedencies, $pageToInclude);

	} elseif($params[1]=="categories"){
		$pageTitle = "Catégories";
		$pageToInclude = $_SERVER['DOCUMENT_ROOT']."/vbcms-content/modules/blog/admin/categories.php";
		createModulePage($pageTitle, $pageDepedencies, $pageToInclude);
		
	} elseif($params[1]=="backTasks"){
		// Permet d'intéragir avec la base de donnée en mode admin
		if (isset($_POST["savePost"]) AND !empty($_POST["savePost"])) {
			savePost($_POST["savePost"]);

			// Sauvegarde le brouillon
		} elseif (isset($_POST["saveDraft"]) AND !empty($_POST["saveDraft"])) {
			saveDraft($_POST["saveDraft"]);

			// Mettre à jour l'article
		} elseif (isset($_POST["updatePost"]) AND !empty($_POST["updatePost"])) {
			updatePost($_POST["updatePost"]);

			// Supprime l'article ou le brouillon
		} elseif (isset($_GET["deletePostDraft"]) AND !empty($_GET["deletePostDraft"])) {
			$conditions = json_decode($_GET["deletePostDraft"]);
			deletePostDraft($conditions[0], $conditions[1]);
		}

	}
	
}

?>