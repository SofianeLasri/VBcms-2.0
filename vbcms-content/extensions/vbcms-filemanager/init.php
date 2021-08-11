<?php
if(isset($initCall)&&!empty($initCall)){
    if($initCall[0]=="enable"){
        adminNavbarAddCategory($this->name, "gallery_medias");
        adminNavbarAddItem($this->name, "fas fa-photo-video", "gallery_filemanager", "/browse");
    } elseif($initCall[0]=="disable"){

    } elseif($initCall[0]=="getSettingsHTML"){
        // $initCall[1] contient les paramètres
        echo('<h5>C\'est bien la page du filemanager</h5>');
    } elseif($initCall[0]=="getPermissions"){
        include 'permissions.php';
    }
}