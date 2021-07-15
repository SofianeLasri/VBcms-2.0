<?php
if ($urlPath[3]=="" OR $urlPath[3]=="browse") {
	echo "Vitrine du workshop";
} elseif ($urlPath[3]=="manage") {
	include $GLOBALS['vbcmsRootPath']."/vbcms-admin/manage-ws-addons.php";
} elseif ($urlPath[3]=="create") {
	include $GLOBALS['vbcmsRootPath']."/vbcms-admin/create-ws-addons.php";
} else {
	include $GLOBALS['vbcmsRootPath']."/vbcms-admin/404.php";
}