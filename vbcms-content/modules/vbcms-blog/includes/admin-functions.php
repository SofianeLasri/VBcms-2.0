<?php
ini_set('default_charset', 'utf-8');
function saveDraft($postData){
    global $bdd;
    $postData = json_decode($postData);
    $writtenOn = $postData[6];
    $modifiedOn = $postData[7];
    date_parse_from_format("Y-m-d G:i:s", $modifiedOn);
    

    $response = $bdd->prepare("SELECT * FROM `vbcms-blogDrafts` WHERE randId = ?");
    $response->execute([$postData[0]]);
    
    if (empty($response->fetch())) {
        $response = $bdd->prepare("INSERT INTO `vbcms-blogDrafts` (id, randId, categoryId, authorId, slug, title, content, headerImage, writtenOn, modifiedOn, description, autosave) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");
        $response->execute([null, $postData[0], $postData[1], $_SESSION['user_id'], $postData[2], utf8_encode($postData[3]), utf8_encode($postData[4]), $postData[5], $writtenOn, $modifiedOn, utf8_encode($postData[8]), $postData[9]]);
    } else {
        $response = $bdd->prepare("UPDATE `vbcms-blogDrafts` SET categoryId=?, slug=?, title=?, content=?, headerImage=?, writtenOn=?, modifiedOn=?, description=?, autosave=? WHERE randId = ?");
        $response->execute([$postData[1], $postData[2], utf8_encode($postData[3]), utf8_encode($postData[4]), $postData[5], $writtenOn, $modifiedOn, utf8_encode($postData[8]), $postData[9], $postData[0]]);
    }
    
}

function savePost($postData){
    global $bdd;
    $postData = json_decode($postData);
    $writtenOn = $postData[5];
    $modifiedOn = $postData[6];
    date_parse_from_format("Y-m-d G:i:s", $modifiedOn);
    if ($postData[0] =='') {
        $postData[0] = 0;
    }

    $response = $bdd->prepare("INSERT INTO `vbcms-blogPosts` (id, categoryId, authorId, slug, title, content, headerImage, writtenOn, modifiedOn, description, views) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
    $response->execute([null, $postData[0], $_SESSION['user_id'], $postData[1], utf8_encode($postData[2]), utf8_encode($postData[3]), $postData[4], $writtenOn, $modifiedOn, utf8_encode($postData[7]),1]);
}

function updatePost($postData){
    global $bdd;
    $postData = json_decode($postData);
    $writtenOn = $postData[5];
    $modifiedOn = $postData[6];
    date_parse_from_format("Y-m-d G:i:s", $modifiedOn);

    $response = $bdd->prepare("SELECT * FROM `vbcms-blogPosts` WHERE id = ?");
    $response->execute([$postData[8]]);
    
    if (!empty($response->fetch())) {
        if ($postData[0] =='') {
            $postData[0] = 0;
        }
        $response = $bdd->prepare("UPDATE `vbcms-blogPosts` SET categoryId=?, authorId=?, slug=?, title=?, content=?, headerImage=?, writtenOn=?, modifiedOn=?, description=? WHERE id=?");
        $response->execute([$postData[0], $_SESSION['user_id'], $postData[1], utf8_encode($postData[2]), utf8_encode($postData[3]), $postData[4], $writtenOn, $modifiedOn, utf8_encode($postData[7]), $postData[8]]);
    } else {
        echo "L'article n°".$postData[8]." n'existe pas. :/";
    }
}

function deletePostDraft($type, $id){
    global $bdd;
    if ($type=="post") {
        $response = $bdd->prepare("DELETE FROM `vbcms-blogPosts` WHERE id = ?");
        $response->execute([$id]);
    } elseif ($type=="draft") {
        $response = $bdd->prepare("DELETE FROM `vbcms-blogDrafts` WHERE randId = ?");
        $response->execute([$id]);
    } else {
        echo "Le type ".$type." n'est pas reconnu :/";
    }
}

?>