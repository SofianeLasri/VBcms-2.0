<?php
// Fichier de test
$deTest['type'] = 1;
$deTest['fldr'] = "";
$deTest['sort_by'] = "name";
$deTest['ascending'] = 1;

echo urlencode(json_encode($deTest));