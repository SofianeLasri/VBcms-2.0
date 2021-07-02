<?php
function enable($name, $path){
    adminNavbarAddCategory($name, "gallery_medias");
    adminNavbarAddItem($name, "fas fa-photo-video", "gallery_filemanager", "/vbcms-admin/".$path."/browse");
}

function disable(){

}