<?php
ini_set('default_charset', 'utf-8');
function getPost($id, $type, $content){
    global $bdd;
    if ($type=="published") {
        $response = $bdd->prepare("SELECT * FROM `vbcms-blogPosts` WHERE id = ?");
        $response->execute([$id]);
        $post = $response->fetch(PDO::FETCH_ASSOC);

        $temp = array();
        $temp["type"] = "post";
        $temp["id"] = $post["id"];
        $temp["categoryId"] = $post["categoryId"];
        $temp["category"] = getCategoryInfos($post["categoryId"]);
        $temp["authorId"] = $post["authorId"];
        $temp["author"] = getUserNameById($post["authorId"]);
        $temp["slug"] = $post["slug"];
        $temp["title"] = utf8_decode($post["title"]);
        if($content) $temp["content"] = utf8_decode($post["content"]);
        $temp["description"] = utf8_decode($post["description"]);
        $temp["headerImage"] = $post["headerImage"];
        $temp["writtenOn"] = $post["writtenOn"];
        $temp["modifiedOn"] = $post["modifiedOn"];
    } elseif ($type=="draft") {
        $response = $bdd->prepare("SELECT * FROM `vbcms-blogDrafts` WHERE id = ?");
        $response->execute([$id]);
        $post = $response->fetch(PDO::FETCH_ASSOC);

        $temp = array();
        $temp["type"] = "draft";
        $temp["id"] = $post["id"];
        $temp["randId"] = $post["randId"];
        $temp["categoryId"] = $post["categoryId"];
        $temp["category"] = getCategoryInfos($post["categoryId"]);
        $temp["authorId"] = $post["authorId"]; 
        $temp["author"] = getUserNameById($post["authorId"]);
        $temp["slug"] = $post["slug"];
        $temp["title"] = utf8_decode($post["title"]);
        if($content) $temp["content"] = $post["content"];
        $temp["description"] = utf8_decode($post["description"]);
        $temp["headerImage"] = $post["headerImage"];
        $temp["writtenOn"] = $post["writtenOn"];
        $temp["modifiedOn"] = $post["modifiedOn"];
    }
    
    return $temp;
}

function getPostsList($condition, $order){
    global $bdd;
    if ($order=="DESC") {
        $order=false;
    } else {
        $order=true;
    }

    $results = array();
    
    if ($condition=="allPosts") {
        $response=$bdd->query("SELECT * FROM `vbcms-blogPosts` ORDER BY writtenOn DESC")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($response as $post) {
            array_push($results, getPost($post["id"], "published", false));
        }

        $response=$bdd->query("SELECT * FROM `vbcms-blogDrafts` ORDER BY writtenOn DESC")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($response as $post) {
           array_push($results, getPost($post["id"], "draft", false));
        }

    } elseif ($condition=="publishedOnly") {
        $response=$bdd->query("SELECT * FROM `vbcms-blogPosts` ORDER BY writtenOn DESC")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($response as $post) {
            array_push($results, getPost($post["id"], "published", false));
        }
    } elseif ($condition=="draftsOnly") {
        $response=$bdd->query("SELECT * FROM `vbcms-blogDrafts` ORDER BY writtenOn DESC")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($response as $post) {
           array_push($results, getPost($post["id"], "draft", false));
        }
    }
    return $results;
}

function getCategoryInfos($id){
    global $bdd, $translation;
    $results = array();
    $response = $bdd->prepare("SELECT * FROM `vbcms-blogCategories` WHERE id = ?");
    $response->execute([$id]);
    $response = $response->fetch(PDO::FETCH_ASSOC);
    if(!empty($response)){
        array_push($results, $response["shortName"]);
        array_push($results, $response["showName"]);
        if ($response["childOf"]!=0) {
            $response = $bdd->prepare("SELECT * FROM `vbcms-blogCategories` WHERE id = ?");
            $response->execute([$response["childOf"]]);
            $response = $response->fetch(PDO::FETCH_ASSOC);
            array_push($results, $response["showName"]);
        } else {
            array_push($results, "");
        }
    } else {
        array_push($results, "no-category");
        array_push($results, $translation["noCategory"]);
        array_push($results, "");
    }

    return $results;
}

?>