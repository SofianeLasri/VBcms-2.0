<?php
if($type =="admin"){
    $pageDepedencies = '<link href="'.$GLOBALS['websiteUrl'].'vbcms-content/extensions/'.$this->path.'/assets/css/admin.css" rel="stylesheet">';
    switch($parameters[1]){
        case 'browse':
            if(verifyUserPermission($_SESSION['user_id'], $this->name, 'access-browse')){
                $pageToInclude = $extensionFullPath."/admin/browse.php";
                extensionCreatePage($type, 0, $pageToInclude, translate("loadingscreens_list"), "", $pageDepedencies);
            }
            
            break;
        
        case 'create':
            if(verifyUserPermission($_SESSION['user_id'], $this->name, 'access-browse')){
                $pageToInclude = $extensionFullPath."/admin/create.php";
                extensionCreatePage($type, 0, $pageToInclude, translate("loadingscreens_create"), "", $pageDepedencies);
            }
        break;
    }
}