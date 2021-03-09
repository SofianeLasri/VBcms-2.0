<?php
if ($folders[3]=="" OR $folders[3]=="changeTheme") {
	include $_SERVER['DOCUMENT_ROOT']."/vbcms-admin/website-changeTheme.php";
} elseif ($folders[3]=="modifyNavbar") {
	include $_SERVER['DOCUMENT_ROOT']."/vbcms-admin/website-modifyNavbar.php";
} else {
	include $_SERVER['DOCUMENT_ROOT']."/vbcms-admin/404.php";
}

?>