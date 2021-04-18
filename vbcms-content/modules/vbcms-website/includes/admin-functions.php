<?php
function getSizeName($numOctets){
    // Array contenant les differents unités 
    $unite = array('octet','ko','mo','go');
    
    if ($numOctets < 1000){
        return $numOctets.$unite[0];
    } else {
        if ($numOctets < 1000000){
            $ko = round($numOctets/1024,2);
            return $ko.$unite[1];
        } else {
            if ($numOctets < 1000000000){
                $mo = round($numOctets/(1024*1024),2);
                return $mo.$unite[2];
            } else{ //Normalement on est pas censé attendre le Gigaoctet sur VBcms -> /!\ int 32bit = 2Go
                $go = round($numOctets/(1024*1024*1024),2);
                return $go.$unite[3];    
            }
        }
    }
}

function getFolderContent($dir, $type) {
    global $uploadFolderPath, $bdd;

    if (substr($dir, -1)=="/") {
        $dir=substr($dir, 0, -1);
    }

    $results = array();

    $response = $bdd->prepare("SELECT * FROM `vbcms-folders` WHERE fullpath = ?");
    $response->execute([$dir]);
    if (empty($response->fetch())) {
        $response = $bdd->prepare("INSERT INTO `vbcms-folders` (id, name, fullpath) VALUES (?,?,?)");
        $response->execute([null, basename($dir), $dir]);
    }

    if (strpos($dir, $uploadFolderPath)===false){ //Si le chemin complet n'est pas déjà indiqué
        $fullDir = $uploadFolderPath.$dir;
    } else {
        $fullDir = $dir;
    }

    if (substr($dir, -1)=="/") {// Permet de check si le dossier a un / à la fin
                                // A MODIFIER CAR CA SERT A RIEN
        $directorySeparator="";
    }else{
        $directorySeparator="/";
    }

    $files = scandir($fullDir);
    if ($type == 0){ // Liste tout récursivement
        foreach ($files as $key => $value) {
            $path = $fullDir.$directorySeparator.$value;
            if (!is_dir($path)) {
                $results[] = $path;
            } else if ($value != "." && $value != "..") {
                $results[] = $path;
                $results=array_merge($results, getFolderContent($path, 0));
            }
        }
    } elseif ($type == 1) { // liste les fichiers et dossiers
        foreach ($files as $key => $value) {
            $path = $fullDir.$directorySeparator.$value;
            if (!is_dir($path)) {
                $securePath = str_replace($uploadFolderPath, "", $path);
                $response = $bdd->prepare("SELECT id FROM `vbcms-folders` WHERE fullpath = ?"); // Je récupère l'id du dossier parent
                $response->execute([$dir]);
                $parentId = $response->fetch();

                //Cherche si le fichier est déjà indexé
                $response = $bdd->prepare("SELECT id FROM `vbcms-files` WHERE parentFolder = ? AND name = ?"); // Je récupère l'id du dossier parent
                $response->execute([$parentId[0], basename($securePath)]);

                if (empty($response->fetch())){
                    $response = $bdd->prepare("INSERT INTO `vbcms-files` (id, name, parentFolder, size, title, description, articles) VALUES (?,?,?,?,?,?,?)");
                    $response->execute([null, basename($securePath), $parentId[0], filesize($path), "", "", "[]"]);
                }

                $results[] = $path;
            } else if ($value != "." && $value != "..") {
                $results[] = $path;
            }
        }
    } elseif ($type == 2) { //Liste uniquement les dossiers
        foreach ($files as $key => $value) {
            $path = $fullDir.$directorySeparator.$value;
            if (is_dir($path) && $value != "." && $value != "..") {
                $results[] = $path;
            }
        }
    } elseif ($type == 3) { //Liste uniquement les fichiers
        foreach ($files as $key => $value) {
            $path = $fullDir.$directorySeparator.$value;
            if (!is_dir($path)) {
                $securePath = str_replace($uploadFolderPath, "", $path);
                $response = $bdd->prepare("SELECT id FROM `vbcms-folders` WHERE fullpath = ?"); // Je récupère l'id du dossier parent
                $response->execute([$dir]);
                $parentId = $response->fetch();

                //Cherche si le fichier est déjà indexé
                $response = $bdd->prepare("SELECT id FROM `vbcms-files` WHERE parentFolder = ? AND name = ?"); // Je récupère l'id du dossier parent
                $response->execute([$parentId[0], basename($securePath)]);

                if (empty($response->fetch())){
                    $response = $bdd->prepare("INSERT INTO `vbcms-files` (id, name, parentFolder, size, title, description, articles) VALUES (?,?,?,?,?,?,?)");
                    $response->execute([null, basename($securePath), $parentId[0], filesize($path), "", "", "[]"]);
                }

                $results[] = $path;
            }
        }
    }

    for ($i=0;$i<count($results);$i++) { 
        $results[$i]=str_replace($uploadFolderPath, "", $results[$i]);
    }
    
    return $results;
}

function deleteContent($path, $alreadyFullPath){
    global $uploadFolderPath, $bdd;
    if ($alreadyFullPath) {
        $fullPath = $path;
    } else {
        $fullPath = $uploadFolderPath.$path;
    }
    
    $response = $bdd->prepare("SELECT id FROM `vbcms-folders` WHERE fullpath = ?"); // Je récupère l'id du dossier
    $response->execute([$path]);
    $folderId = $response->fetch();

    if (is_dir($fullPath)) {
        //echo "Suppression du dossier";
        $scan = getFolderContent($path, 0);
        foreach ($scan as $element) {
            if (strpos($element, $uploadFolderPath)===false) { //S'il ne s'agit pas du chemin d'accès complet
                $element = $uploadFolderPath.$element; //Je le créé
            }
            
            if (is_dir($element)) {
                deleteContent($element, true);
            } else {
                if (unlink($element)) { //Supprime le fichier et vérifie que c'est bon
                    $response = $bdd->prepare("DELETE FROM `vbcms-files` WHERE parentFolder = ?");
                    $response->execute([$folderId[0]]);
                } else {
                    echo "Impossible de supprimer le fichier";
                }
            }
        }

        if (rmdir($fullPath)) { //Supprime le dossier et vérifie que c'est bon
            $response = $bdd->prepare("DELETE FROM `vbcms-folders` WHERE id = ?");
            $response->execute([$folderId[0]]);
        } else {
            echo "Impossible de supprimer le dossier";
        }
    } else {
        //echo "Suppression du fichier";
        if (unlink($fullPath)) { //Supprime le fichier et vérifie que c'est bon
            $response = $bdd->prepare("DELETE FROM `vbcms-files` WHERE parentFolder = ?");
            $response->execute([$folderId[0]]);
        } else {
            echo "Impossible de supprimer le fichier";
        }
    }
}

function getFileDetails($filePath){ //string ou id
    global $uploadFolderPath, $bdd;
    $details = array();
    if (!is_numeric($filePath)) {
        array_push($details, basename($filePath)); //Ajoute le nom du fichier
        array_push($details, getSizeName(filesize($uploadFolderPath.$filePath))); //Ajoute la taille du fichier en questions

        if (filemtime($uploadFolderPath.$filePath)!=false) {
            array_push($details, date("F d Y H:i:s.", filemtime($uploadFolderPath.$filePath))); //Ajout de la date de modification
        }else{
            array_push($details, "Date inconnue");
        }
        $response = $bdd->prepare("SELECT id FROM `vbcms-folders` WHERE fullpath = ?"); // Je récupère l'id du dossier parent
        $response->execute([dirname($filePath)]);
        $parentId = $response->fetch();

        $response = $bdd->prepare("SELECT * FROM `vbcms-files` WHERE parentFolder = ? AND name = ?"); // Je récupère l'id du dossier parent
        $response->execute([$parentId[0], basename($filePath)]);
        $response = $response->fetch(PDO::FETCH_ASSOC);
        array_push($details, $response["title"]);
        array_push($details, $response["description"]);
        array_push($details, $response["id"]);
        array_push($details, $filePath);
    }else{
        $response = $bdd->prepare("SELECT * FROM `vbcms-files` WHERE id = ?"); // Je récupère l'id du dossier parent
        $response->execute([$filePath]);
        $response = $response->fetch(PDO::FETCH_ASSOC);
        $parentId = $response["parentFolder"];
        $filename = $response["name"];
        array_push($details, $response["name"]); //Ajoute le nom du fichier
        array_push($details, getSizeName($response["size"])); //Ajoute la taille du fichier en questions

        $response2 = $bdd->prepare("SELECT * FROM `vbcms-folders` WHERE id = ?"); // Je récupère le fullpath
        $response2->execute([$parentId]);
        $response2 = $response2->fetch(PDO::FETCH_ASSOC);

        if (filemtime($uploadFolderPath.$response2["fullpath"]."/".$filename)!=false) {
            array_push($details, date("F d Y H:i:s.", filemtime($uploadFolderPath.$response2["fullpath"]."/".$filename))); //Ajout de la date de modification
        }else{
            array_push($details, "Date inconnue");
        }
        array_push($details, $response["title"]);
        array_push($details, $response["description"]);
        array_push($details, $response["id"]);

        $response = $bdd->prepare("SELECT * FROM `vbcms-folders` WHERE id = ?"); // Je récupère le fullpath
        $response->execute([$parentId]);
        $response = $response->fetch(PDO::FETCH_ASSOC);
        array_push($details, $response2["fullpath"]."/".$filename);
    }
    
    return $details;
}

function getFolderDetails($folderPath){
    global $uploadFolderPath, $bdd;
    $details = array();
    if (!is_numeric($folderPath)) {
        array_push($details, basename($folderPath)); //Ajoute le nom du fichier
        array_push($details, getSizeName(filesize($uploadFolderPath.$folderPath))); //Ajoute la taille du fichier en questions

        if (filemtime($uploadFolderPath.$folderPath)!=false) {
            array_push($details, date("F d Y H:i:s.", filemtime($uploadFolderPath.$folderPath))); //Ajout de la date de modification
        }else{
            array_push($details, "Date inconnue");
        }
        $response = $bdd->prepare("SELECT id FROM `vbcms-folders` WHERE fullpath = ?"); // Je récupère l'id du dossier parent
        $response->execute([$folderPath]);
        $response = $response->fetch(PDO::FETCH_ASSOC);
        array_push($details, $response["id"]);
        array_push($details, $folderPath);
    }else{
        $response = $bdd->prepare("SELECT * FROM `vbcms-folders` WHERE id = ?"); // Je récupère l'id du dossier parent
        $response->execute([$folderPath]);
        $response = $response->fetch(PDO::FETCH_ASSOC);
        array_push($details, $response["name"]); //Ajoute le nom du fichier
        array_push($details, getSizeName(filesize($uploadFolderPath.$response["fullpath"]))); //Ajoute la taille du fichier en questions

        if (filemtime($uploadFolderPath.$response["fullpath"])!=false) {
            array_push($details, date("F d Y H:i:s.", filemtime($uploadFolderPath.$response["fullpath"]))); //Ajout de la date de modification
        }else{
            array_push($details, "Date inconnue");
        }
        array_push($details, $response["id"]);
        array_push($details, $response["fullpath"]);
    }
    
    return $details;
}

function updateFileDetails($path, $title, $description){
    global $bdd;
    $response = $bdd->prepare("SELECT id FROM `vbcms-folders` WHERE fullpath = ?"); // Je récupère l'id du dossier parent
    $response->execute([dirname($path)]);
    $parentId = $response->fetch();

    $filename = basename($path);
    $response = $bdd->prepare("SELECT id FROM `vbcms-files` WHERE parentFolder = ? AND name = ?"); // Je récupère l'id du dossier parent
    $response->execute([$parentId[0], basename($path)]);
    $fileId = $response->fetch();

    $response = $bdd->prepare("UPDATE `vbcms-files` SET title= ?, description=? WHERE id = ?");
    $response->execute([$title, $description, $fileId[0]]);
}

function renameFile($path, $name){
    global $uploadFolderPath, $bdd;
    $response = $bdd->prepare("SELECT id FROM `vbcms-folders` WHERE fullpath = ?"); // Je récupère l'id du dossier parent
    $response->execute([dirname($path)]);
    $parentId = $response->fetch();

    $filename = basename($path);
    $response = $bdd->prepare("SELECT id FROM `vbcms-files` WHERE parentFolder = ? AND name = ?"); // Je récupère l'id du dossier parent
    $response->execute([$parentId[0], basename($path)]);
    $fileId = $response->fetch();

    rename($uploadFolderPath.$path, $uploadFolderPath.dirname($path)."/".$name);

    $response = $bdd->prepare("UPDATE `vbcms-files` SET name= ? WHERE id = ?");
    $response->execute([$name, $fileId[0]]);
}

function copyMove($path, $destination, $action){
    global $uploadFolderPath, $bdd;
    if ($action == "copy") {
        copy($uploadFolderPath.$path, $uploadFolderPath.$destination);
        $response = $bdd->prepare("SELECT id FROM `vbcms-folders` WHERE fullpath = ?"); // Je récupère l'id du dossier parent original
        $response->execute([dirname($path)]);
        $parentId = $response->fetch();
        $response = $bdd->prepare("SELECT id FROM `vbcms-folders` WHERE fullpath = ?"); // Je récupère l'id du dossier parent de destination
        $response->execute([dirname($destination)]);
        $parentDestId = $response->fetch();

        $response = $bdd->prepare("INSERT INTO `vbcms-files` (id, name, parentFolder, size, title, description, articles) VALUES (?,?,?,?,?,?,?)"); // Je créé la ligne du nouveau fichier
        $response->execute([null, basename($destination), $parentDestId[0], filesize($uploadFolderPath.$destination), "", "", "[]"]);
    } elseif($action == "move"){
        copy($uploadFolderPath.$path, $uploadFolderPath.$destination);
        unlink($uploadFolderPath.$path);
        $response = $bdd->prepare("SELECT id FROM `vbcms-folders` WHERE fullpath = ?"); // Je récupère l'id du dossier parent original
        $response->execute([dirname($path)]);
        $parentId = $response->fetch();
        $response = $bdd->prepare("SELECT id FROM `vbcms-folders` WHERE fullpath = ?"); // Je récupère l'id du dossier parent de destination
        $response->execute([dirname($destination)]);
        $parentDestId = $response->fetch();

        $response = $bdd->prepare("SELECT id FROM `vbcms-files` WHERE parentFolder = ? AND name = ?"); // Je récupère l'id du fichier à délacer
        $response->execute([$parentId[0], basename($path)]);
        $fileId = $response->fetch();

        $response = $bdd->prepare("UPDATE `vbcms-files` SET name= ?, parentFolder= ? WHERE parentFolder = ? AND name = ?"); // Je modifie les données en question
        $response->execute([basename($destination),$parentDestId[0] ,$parentId[0], basename($path)]);
    }
}

function deleteFileFolder($path){
    global $uploadFolderPath, $bdd;
    if (is_dir($uploadFolderPath.$path)) { // Si c'est un dossier
        $filesFolders=getFolderContent($path, 1); // Alors je scan le contenu du dossier
        foreach ($filesFolders as $object) { // Et je supprime tous les éléments 1 par 1
            deleteFileFolder($object);
        }
        $response = $bdd->prepare("DELETE FROM `vbcms-folders` WHERE fullpath = ?"); // Je supprime le dossier
        $response->execute([$path]);
        rmdir($uploadFolderPath.$path);
    } else {
        unlink($uploadFolderPath.$path);
        $response = $bdd->prepare("SELECT id FROM `vbcms-folders` WHERE fullpath = ?"); // Je récupère l'id du dossier parent
        $response->execute([dirname($path)]);
        $parentId = $response->fetch();

        $filename = basename($path);
        $response = $bdd->prepare("SELECT id FROM `vbcms-files` WHERE parentFolder = ? AND name = ?"); // Je récupère l'id du fichier
        $response->execute([$parentId[0], basename($path)]);
        $fileId = $response->fetch();

        $response = $bdd->prepare("DELETE FROM `vbcms-files` WHERE id = ?"); // Je supprime le fichier
        $response->execute([$fileId[0]]);
    }
}

function createFolder($path){
    global $uploadFolderPath, $bdd;
    mkdir($uploadFolderPath.$path, 0755);
    $response = $bdd->prepare("INSERT INTO `vbcms-folders` (id, name, fullpath) VALUES (?,?,?)");
    $response->execute([null, basename($path), $path]);
}
?>