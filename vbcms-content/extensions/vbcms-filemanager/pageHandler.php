<?php
if($type =="admin"){
    switch($parameters[1]){
        case 'browse':
            if(verifyUserPermission($_SESSION['user_id'], $this->name, 'access-browse')){
                $pageToInclude = $extensionFullPath."/admin/browse.php";
                $this->extensionCreatePage($type, 0, $pageToInclude, translate("gallery_filemanager"), "", "");
            }
            
            break;
        
        case 'openFilemanager':
            if(isset($parameters[2]) && !empty($parameters[2])){
                if(isJson(urldecode($parameters[2]))){
                    // Ici on les paramètres sont les mêmes que ceux du gestionnaire de fichiers
                    // Donc pas besoin de faire d'association, on va simplement les sortir
                    echo VBcmsGetSetting("websiteUrl")."vbcms-content/extensions/vbcms-filemanager/includes/responsivefilemanager/dialog.php?".http_build_query(json_decode(urldecode($parameters[2]), true));
                }
            }else{
                echo "ERREUR: Aucun paramètre de spécifié.";
            }
        break;
    }
}