<?php
switch ($language) {
    case "FR":
        include $_SERVER['DOCUMENT_ROOT'].'/vbcms-content/modules/blog/includes/translations/FR.php';
        break;
    case "EN":
        include $_SERVER['DOCUMENT_ROOT'].'/vbcms-content/modules/blog/includes/translations/EN.php';
        break;
    default:
    	include $_SERVER['DOCUMENT_ROOT'].'/vbcms-content/modules/blog/includes/translations/FR.php';
        break;
}