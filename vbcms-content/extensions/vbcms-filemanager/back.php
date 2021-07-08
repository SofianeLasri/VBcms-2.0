<?php
switch ($language) {
    case "FR":
        include $GLOBALS['vbcmsRootPath'].'/vbcms-content/extensions/vbcms-filemanager/includes/translations/FR.php';
        break;
    case "EN":
        include $GLOBALS['vbcmsRootPath'].'/vbcms-content/extensions/vbcms-filemanager/includes/translations/EN.php';
        break;
    default:
    	include $GLOBALS['vbcmsRootPath'].'/vbcms-content/extensions/vbcms-filemanager/includes/translations/FR.php';
        break;
}