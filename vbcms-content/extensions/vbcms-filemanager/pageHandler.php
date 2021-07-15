<?php
if($type =="admin"){
    switch($parameters[1]){
        case 'browse':
            if(verifyUserPermission($_SESSION['user_id'], $this->name, 'access-browse')){
                $pageToInclude = $extensionFullPath."/admin/browse.php";
                extensionCreatePage($type, 0, $pageToInclude, translate("gallery_filemanager"), "", "");
            }
            
            break;
        
        case 'backTasks':
            if(isset($parameters[2]) && !empty($parameters[2])){
                if($parameters[2]=="include"){
                    // On ne peut pas inclure le gestionnaire de fichier ici car cela créé bcp de problèmes à cause du fait qu'il s'agisse d'une classe
                    echo $GLOBALS['websiteUrl']."vbcms-content/extensions/vbcms-filemanager/includes/responsivefilemanager/dialog.php";
                }
            }else{
                echo "ERREUR: Aucun paramètre de spécifié.";
            }
        break;
    }
}