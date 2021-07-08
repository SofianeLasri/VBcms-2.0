<?php
if($type =="admin"){
    if($parameters[1]=="browse"){
        $pageToInclude = $GLOBALS['vbcmsRootPath']."/vbcms-content/extensions/vbcms-filemanager/admin/browse.php";
        extensionCreatePage($type, 0, $pageToInclude, $translation["gallery_filemanager"], "", "");
    }
}