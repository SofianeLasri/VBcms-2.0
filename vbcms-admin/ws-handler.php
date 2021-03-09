<?php
if ($folders[3]=="" OR $folders[3]=="browse") {
	echo "Vitrine du workshop";
} elseif ($folders[3]=="manage") {
	include $_SERVER['DOCUMENT_ROOT']."/vbcms-admin/manage-ws-addons.php";
} else {
	include $_SERVER['DOCUMENT_ROOT']."/vbcms-admin/404.php";
}

?>