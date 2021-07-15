<?php
// Le gestionnaire client est assez vide comparé à celui qui gère la partie admin
// En fait je dois encore faire quelques ajouts, comment par exemple si un module utilise l'alias de l'index en partie client
// Il faudra dans un premier temps, vérifier que ce module ne gère pas le premier chemin de l'url,
// Puis on cherchera sur la liste des modules activés si ce dernier ne le gère pas.

// On vérifie que le premier chemin ne soit pas vbcms-content afin de permettre aux extension de pouvoir communiquer
if ($urlPath[1]!="vbcms-content") {
    if($urlPath[1]=="backTasks"){
        include $GLOBALS['vbcmsRootPath']."/vbcms-core/clientBackTasks.php";
    } else {
        // J'indique que l'utilisateur ayant l'ip X a visité cette page à cet instant t
        $response = $bdd->prepare("INSERT INTO `vbcms-websiteStats` (id, date, page, ip) VALUES (?,?,?,?)");
        $response->execute([null, date("Y-m-d H:i:s"), $_SERVER['REQUEST_URI'], $ip]);
        
        $moduleParams = array();
        for ($i=2; $i<count($urlPath); $i++) { 
            array_push($moduleParams, $urlPath[$i]);
        }
        loadModule("client", $urlPath[1], $moduleParams);
    }
}