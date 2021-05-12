<?php
// Ce fichier permet d'inclure la gallerie dans différentes pages
// Inclu header car le fichier peut être appelé séparément du panel

// Dans ce cas on doit recréer le root path
$vbcmsRootPath = getcwd();
if(strpos($vbcmsRootPath, "/vbcms-content") !== false){
    $vbcmsRootPath = substr($vbcmsRootPath, 0, strpos($vbcmsRootPath, "/vbcms-content"));
}
require_once $GLOBALS['vbcmsRootPath']."/vbcms-admin/includes/header.php";
require $GLOBALS['vbcmsRootPath']."/vbcms-content/modules/vbcms-filemanager/admin/gallery-htmlOnly.php";
require $GLOBALS['vbcmsRootPath']."/vbcms-content/modules/vbcms-filemanager/admin/gallery-scriptOnly.php";