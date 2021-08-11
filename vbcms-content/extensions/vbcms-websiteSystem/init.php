<?php
if(isset($initCall)&&!empty($initCall)){
    if($initCall[0]=="enable"){

    } elseif($initCall[0]=="disable"){

    } elseif($initCall[0]=="deleteData"){

    } elseif($initCall[0]=="getSettingsHTML"){
        // $initCall[1] contient les paramètres
        echo('<h5>C\'est bien la page du module de site internet</h5>');
    } 
}