<?php
switch ($_SESSION["language"]) {
    case "FR":
        include $_SERVER['DOCUMENT_ROOT'].'/vbcms-content/modules/gallery/includes/translations/FR.php';
        break;
    case "EN":
        include $_SERVER['DOCUMENT_ROOT'].'/vbcms-content/modules/gallery/includes/translations/EN.php';
        break;
    default:
    	include $_SERVER['DOCUMENT_ROOT'].'/vbcms-content/modules/gallery/includes/translations/FR.php';
        break;
}
?>