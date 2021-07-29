<?php
function enable($name, $path, $adminAccess, $clientAccess){
    adminNavbarAddCategory($name, "loadingscreens");
    adminNavbarAddItem($name, "fas fa-list", "loadingscreens_browse", "/browse");
}

function disable(){

}

function getSettingsHTML($params){
    echo('<h5>C\'est bien la page du créateur d\'écrans de chargement</h5>');
}