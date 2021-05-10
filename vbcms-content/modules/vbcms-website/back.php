<?php
switch ($language) {
    case "FR":
        include $vbcmsRootPath.'/vbcms-content/modules/vbcms-website/includes/translations/FR.php';
        break;
    case "EN":
        include $vbcmsRootPath.'/vbcms-content/modules/vbcms-website/includes/translations/EN.php';
        break;
    default:
    	include $vbcmsRootPath.'/vbcms-content/modules/vbcms-website/includes/translations/FR.php';
        break;
}
?>