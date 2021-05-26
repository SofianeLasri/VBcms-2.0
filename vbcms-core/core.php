<?php
// Ce qu'on fait ici c'est récupérer le dossier racine de VBcms
// Sur la pre2.0 toute la partie logique se passait dans /vbcms-admin
// Ce raisonnement était donc logique car toujours valable.

// Il faudra en revanche, voir si cela fonctionnera toujours avec la nouvelle architecture.
if(!isset($vbcmsRootPath)){
	$vbcmsRootPath = getcwd();
	if(strpos($vbcmsRootPath, "/vbcms-core") !== false){
	    $vbcmsRootPath = substr($vbcmsRootPath, 0, strpos($vbcmsRootPath, "/vbcms-core"));
	}
}
// On utilisera $GLOBALS['vbcmsRootPath']

// Connexxion à la base de donnée
require_once 'dbConnect.php';
