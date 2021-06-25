<?php
// On vérifie que le premier chemin ne soit pas vbcms-content afin de permettre aux extension de pouvoir communiquer
if ($folders[1]!="vbcms-content") {
    if($folders[1]=="backTasks"){
        include $GLOBALS['vbcmsRootPath']."/vbcms-core/clientBackTasks.php";
    } else {
        // J'indique que l'utilisateur ayant l'ip X a visité cette page à cet instant t
        $response = $bdd->prepare("INSERT INTO `vbcms-websiteStats` (id, date, page, ip) VALUES (?,?,?,?)");
        $response->execute([null, date("Y-m-d H:i:s"), $_SERVER['REQUEST_URI'], $ip]);
        

        $moduleParams = array();
        for ($i=2; $i<count($folders); $i++) { 
            array_push($moduleParams, $folders[$i]);
        }
        loadModule("client", $folders[1], $moduleParams);


        
    }
}