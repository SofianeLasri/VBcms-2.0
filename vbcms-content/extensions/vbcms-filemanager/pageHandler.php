<?php
if($type =="admin"){
    switch($parameters[1]){
        case 'browse':
            $pageToInclude = $extensionFullPath."/admin/browse.php";
            extensionCreatePage($type, 0, $pageToInclude, $translation["gallery_filemanager"], "", "");
            break;
        
        case 'backTasks':
            if(isset($parameters[2]) && !empty($parameters[2])){
                if($parameters[2]=="include"){
                    // NE FONCTIONNE PAS
                    chdir($extensionFullPath."/includes/responsivefilemanager/");
                    require "dialog.php";
                }
            }else{
                echo "ERREUR: Aucun paramètre de spécifié.";
            }
        break;
    }
}