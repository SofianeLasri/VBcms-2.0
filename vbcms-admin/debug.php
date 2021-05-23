<?php
$url = "$http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
if(isset($_GET["orderBy"])) $orderBy = $_GET["orderBy"];
else  $orderBy = "id";

if(isset($_GET["order"])) $order = $_GET["order"];
else  $order = "ASC";

if($order=="ASC") $orderInverse = "DESC";
else $orderInverse = "ASC";

if(isset($_GET["page"])) $page = $_GET["page"];
else{
	$query = parse_url($url, PHP_URL_QUERY);
	if ($query) {
	    $url .= '&page=1';
	} else {
	    $url .= '?page=1';
	}
	header("Location: $url");
}

if(isset($_GET["limit"])){
	$limit = "LIMIT ". $_GET["limit"];
	$offset = "OFFSET ".$_GET["limit"] * ($page-1);
}else{
	$limit = "LIMIT ". 25;
	$offset = "OFFSET ". 25 * ($page-1);
}

if (isset($_POST['viewAll'])) {
	$offset = "";
	$limit = "";

	$query = parse_url($url, PHP_URL_QUERY);
	if ($query) {
	    $url .= '&viewAll';
	} else {
	    $url .= '?viewAll';
	}
	header("Location: $url");
} elseif(isset($_POST['showItemsNumber'])){
	$limit = "LIMIT ".$_POST['showItemsNumber'];
	$offset = "OFFSET ".$_POST['showItemsNumber'] * ($page-1);

	$query = parse_url($url, PHP_URL_QUERY);
	if ($query) {
	    $url .= '&limit='.$_POST['showItemsNumber'];
	} else {
	    $url .= '?limit='.$_POST['showItemsNumber'];
	}
	header("Location: $url");
}

$events = $bdd->query("SELECT * FROM `vbcms-events` ORDER BY $orderBy $order $limit $offset")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title><?=$websiteName?> | Debug Mode</title>
	<?php include 'includes/depedencies.php';?>
</head>
<body>
	<?php 
	include ('includes/navbar.php');
	?>

	<!-- Contenu -->
	<div class="page-content" leftSidebar="240" rightSidebar="0">
		<h3>Debug Mode</h3>
		<p>Si un problème intervient, il sera peut-être répertorié ici. <strong>Les évènements de plus de 30 jours sont supprimés.</strong></p>
		
		<div class="mt-5">
			<h5>Évènements</h5>
			<form class="form-inline mb-2" method="POST">
				<div class="form-check">
					<input class="form-check-input" type="checkbox" value="" name="viewAll">
					<label class="form-check-label">Tout voir</label>
				</div>
				<div class="form-group mx-sm-3">
					<label>Nombre d'éléments à afficher</label>
					<select class="form-control form-control-sm" name="showItemsNumber">
						<option>25</option>
						<option>50</option>
						<option>100</option>
						<option>250</option>
						<option>500</option>
			    	</select>
			  	</div>
				<button type="submit" class="btn btn-brown btn-sm">Appliquer</button>
			</form>
			<table class="table">
				<thead class="thead-brown">
					<tr>
						<th scope="col"><a id="id" href="?orderBy=id&order=<?=$orderInverse?>" class="text-white">ID</a></th>
						<th scope="col"><a id="date" href="?orderBy=date&order=<?=$orderInverse?>" class="text-white">Date</a></th>
						<th scope="col"><a id="module" href="?orderBy=module&order=<?=$orderInverse?>" class="text-white">Module</a></th>
						<th scope="col"><a id="content" href="?orderBy=content&order=<?=$orderInverse?>" class="text-white">Content</a></th>
						<th scope="col"><a id="url" href="?orderBy=url&order=<?=$orderInverse?>" class="text-white">Url</a></th>
						<th scope="col"><a id="ip" href="?orderBy=ip&order=<?=$orderInverse?>" class="text-white">IP</a></th>
					</tr>
				</thead>
				<tbody>
					<!--
					<tr>
						<th scope="row">1</th>
						<td>11/12/2001</td>
						<td>vbcms-website</td>
						<td>Load page /index.php</td>
						<td>https://vbcms.net/</td>
						<td>Un ip</td>
					</tr>
					-->
					<?php
					foreach ($events as $event) {
						echo '<tr>
								<th scope="row">'.$event['id'].'</th>
								<td>'.$event['date'].'</td>
								<td>'.$event['module'].'</td>
								<td>'.$event['content'].'</td>
								<td>'.$event['url'].'</td>
								<td>'.$event['ip'].'</td>
							</tr>';
					}
					?>
				</tbody>
			</table>

			<span>
			<?php
			$query = $_GET;
			

			if($page != 1){
				$query['page'] = $page-1;
				echo'<a href="?'.http_build_query($query).'" class="btn btn-outline-brown">Précédent</a>';
			} 
			$count = $bdd->query("SELECT COUNT(*) FROM `vbcms-events`")->fetchColumn();
			if (($count - $_GET["limit"] * ($page)) > 0){
				$query['page'] = $page+1;
				echo '<a class="btn btn-outline-brown mx-2" href="?'.http_build_query($query).'">Suivant</a>';
			} 
			?></span>
		</div>
	</div>
</body>
</html>