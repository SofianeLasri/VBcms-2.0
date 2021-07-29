<?php
if($type =="admin"){
    switch($parameters[1]){
        case 'browse':
            if(verifyUserPermission($_SESSION['user_id'], $this->name, 'access-browse')){
                $pageToInclude = $extensionFullPath."/admin/browse.php";
                extensionCreatePage($type, 0, $pageToInclude, translate("gallery_filemanager"), "", "");
            }
            
            break;
        
        case 'create':
            
        break;
    }
}