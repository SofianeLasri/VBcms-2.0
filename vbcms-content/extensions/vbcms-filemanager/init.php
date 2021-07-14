<?php
function enable($name, $path, $adminAccess, $clientAccess){
    adminNavbarAddCategory($name, "gallery_medias");
    adminNavbarAddItem($name, "fas fa-photo-video", "gallery_filemanager", "/browse");
}

function disable(){

}

function getSettingsHTML($params){
    echo('<h5>C\'est bien la page du filemanager</h5>');
}