<?php
if ($paths[3]=="" OR $paths[3]=="browse") {
	echo "Vitrine du workshop";
} elseif ($paths[3]=="manage") {
	include $GLOBALS['vbcmsRootPath']."/vbcms-admin/manage-ws-addons.php";
} elseif ($paths[3]=="create") {
	include $GLOBALS['vbcmsRootPath']."/vbcms-admin/create-ws-addons.php";
} else {
	include $GLOBALS['vbcmsRootPath']."/vbcms-admin/404.php";
}