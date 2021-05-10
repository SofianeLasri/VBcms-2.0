<?php
if ($folders[3]=="" OR $folders[3]=="browse") {
	echo "Vitrine du workshop";
} elseif ($folders[3]=="manage") {
	include $vbcmsRootPath."/vbcms-admin/manage-ws-addons.php";
} elseif ($folders[3]=="create") {
	include $vbcmsRootPath."/vbcms-admin/create-ws-addons.php";
} else {
	include $vbcmsRootPath."/vbcms-admin/404.php";
}

?>