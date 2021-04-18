<?php
$result = $bdd->prepare("SELECT * FROM `vbcms-blogPosts` WHERE slug = ?");
$result->execute([basename($_SERVER['REQUEST_URI'])]);
$result = $result->fetch(PDO::FETCH_ASSOC);

$publishDate = date('F j, Y', strtotime($result["writtenOn"]));
$title = utf8_decode($result["title"]);
$content = utf8_decode($result["content"]);
$description = utf8_decode($result["description"]);

// Compteur de vues unique/jour
if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    $ip = $_SERVER['HTTP_CLIENT_IP'];
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
    $ip = $_SERVER['REMOTE_ADDR'];
}
$alreadyViewed = $bdd->prepare("SELECT * FROM `vbcms-websiteStats` WHERE date LIKE ? AND page = ? AND ip = ?");
$alreadyViewed->execute(['%'.date("Y-m-d").'%', $_SERVER['REQUEST_URI'], $ip]);

$pageTitle = "$websiteName | $title";
$websiteDescription = $description;

if ($alreadyViewed->rowCount()<=1) {
	$reponse = $bdd->prepare("UPDATE `vbcms-blogPosts` SET views = ? where slug = ?");
	$reponse->execute([$result["views"]+1, basename($_SERVER['REQUEST_URI'])]);
}
$pageContent =('<div class="articleContainer">
	<div class="articleHeader">
		<h1>'.$title.'</h1>
		<p class="subtitle">Publié le '.$publishDate.' - <i class="fas fa-eye"></i> '.$result["views"].' vues</p>
		<img src="'.$result["headerImage"].'">
	</div>
	
	<div class="articleContent">
		'.$content.'
	</div>
</div>');