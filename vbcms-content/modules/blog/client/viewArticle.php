<?php
$result = $bdd->prepare("SELECT * FROM `vbcms-blogPosts` WHERE slug = ?");
$result->execute([basename($_SERVER['REQUEST_URI'])]);
$result = $result->fetch(PDO::FETCH_ASSOC);

$publishDate = date('F j, Y', strtotime($result["writtenOn"]));
$title = utf8_decode($result["title"]);
$content = utf8_decode($result["content"])
?>
<div class="articleContainer d-flex flex-column justify-content-center align-items-center">
	<div class="articleHeader d-flex flex-column justify-content-center align-items-center">
		<h1><?=$title?></h1>
		<p class="subtitle">Publié le <?=$publishDate?></p>
		<img src="<?=$result["headerImage"]?>">
	</div>
	
	<div class="articleContent">
		<?=$content?>
	</div>
</div>