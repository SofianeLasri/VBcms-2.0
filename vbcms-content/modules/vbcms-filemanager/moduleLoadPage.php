<?php
// Je rappel qu'on a $type et $params
$params = $moduleParams;
$uploadFolderPath = $vbcmsRootPath.'/vbcms-content/uploads';


if($type=="admin") {
	// Variables nécessaire à création d'une page
	$pageDepedencies = '';

	if ($params[1]=="browse") {
		
		// Variables nécessaire à création d'une page
		$pageTitle = "Fichiers";
		$pageToInclude = $vbcmsRootPath."/vbcms-content/modules/vbcms-filemanager/admin/gallery.php";
		createModulePage($pageTitle, "", $pageDepedencies, $pageToInclude, 0);

	} elseif($params[1]=="backTasks"){
		// Permet d'intéragir avec la base de donnée en mode admin

		// Fonction admin		
		include $vbcmsRootPath."/vbcms-content/modules/vbcms-filemanager/includes/admin-functions.php";

		if (isset($_GET["folderContent"])) {
			if (isset($_GET["folderOnly"])) {
				echo json_encode(getFolderContent($_GET["folderContent"], 2));
			} elseif (isset($_GET["folderAndFiles"])) {
				echo json_encode(getFolderContent($_GET["folderContent"], 1));
			} elseif (isset($_GET["filesOnly"])) {
				echo json_encode(getFolderContent($_GET["folderContent"], 3));
			} elseif (isset($_GET["listRecursive"])) {
				echo json_encode(getFolderContent($_GET["folderContent"], 0));
			}

			// Récupère les informations des fichiers spécifiés
		} elseif (isset($_GET["fileDetails"]) AND !empty($_GET["fileDetails"])) {
			echo json_encode(getFileDetails($_GET["fileDetails"]));

			// Récupère les informations des dossiers spécifiés
		} elseif (isset($_GET["folderDetails"]) AND !empty($_GET["folderDetails"])) {
			echo json_encode(getFolderDetails($_GET["folderDetails"]));

			// Met à jour les détails des fichiers dans la base de donnée
		} elseif (isset($_GET["updateFileDetails"]) AND !empty($_GET["updateFileDetails"])) {
			$updateFileDetails = urldecode($_GET["updateFileDetails"]);
			$updateFileDetails = json_decode($updateFileDetails);
			updateFileDetails($updateFileDetails[0], $updateFileDetails[1], $updateFileDetails[2]);

			// Renomme un fichier (pas un dossier -> la flemme on fera plus-tard)
		} elseif (isset($_GET["renameFile"]) AND !empty($_GET["renameFile"])) {
			$renameFile = urldecode($_GET["renameFile"]);
			$renameFile = json_decode($renameFile);
			renameFile($renameFile[0], $renameFile[1]);

			// Copie colle un fichier (ouai pareil, pas les dossiers car la flemme -> on fera plus-tard)
		} elseif (isset($_GET["copyMoveFile"]) AND !empty($_GET["copyMoveFile"])) {
			$copyMoveFile = urldecode($_GET["copyMoveFile"]);
			echo $copyMoveFile;
			$copyMoveFile = json_decode($copyMoveFile);
			copyMove($copyMoveFile[0], $copyMoveFile[1], $copyMoveFile[2]);

			// Supprime un fichier&/dossier
		} elseif (isset($_GET["deleteFileFolder"]) AND !empty($_GET["deleteFileFolder"])) {
			$deleteFileFolder = urldecode($_GET["deleteFileFolder"]);
			deleteFileFolder($deleteFileFolder);

			// Envoie un fichier (pas un dossier)
		} elseif (isset($_FILES['uploadFile']) AND isset($_POST["path"])) {
			try {
			    switch ($_FILES['uploadFile']['error']) {
			        case UPLOAD_ERR_OK:
			            break;
			        case UPLOAD_ERR_NO_FILE:
			            throw new RuntimeException('No file sent.');
			        case UPLOAD_ERR_INI_SIZE:
			        case UPLOAD_ERR_FORM_SIZE:
			            throw new RuntimeException('Exceeded filesize limit.');
			        default:
			            throw new RuntimeException('Unknown errors.');
			    }

			    if (!move_uploaded_file($_FILES['uploadFile']['tmp_name'], $uploadFolderPath.$_POST["path"]."/" . $_FILES['uploadFile']['name'])) {
			        throw new RuntimeException('Failed to move uploaded file.');
			    }
			} catch (Exception $e) {
				echo $e->getMessage();
			}
		    //move_uploaded_file($_FILES['uploadFile']['tmp_name'], $uploadFolderPath.$_POST["path"]."/" . $_FILES['uploadFile']['name']);
		    //exit;

		    // Créé un dossier
		} elseif (isset($_GET["createFolder"]) AND !empty($_GET["createFolder"])) {
			$deleteFileFolder = urldecode($_GET["createFolder"]);
			createFolder($deleteFileFolder);
		}
	}
	
}

// Fonctions publiques


?>