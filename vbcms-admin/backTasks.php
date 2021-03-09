<?php
if (isset($_GET["enableWSAddon"]) && !empty($_GET["enableWSAddon"])) {
	global $bdd;
	$params = json_decode(urldecode($_GET["enableWSAddon"]));
	$depedencies = json_decode($params[1]);
	enableAddon($params[0]);
	
	foreach ($depedencies as $depedency) {
		$response = $bdd->prepare("SELECT * FROM `vbcms-modules` WHERE activated=1 AND workshopId=?");
		$response->execute([$depedency]);
		if (empty($response->fetch())) {
			enableAddon($depedency);
		}
		$response = $bdd->prepare("INSERT INTO `vbcms-modulesDepencies` (moduleId, depedencyId, mandatory) VALUES (?,?,?)");
		$response->execute([$params[0], $depedency, 1]);
	}
} elseif (isset($_GET["disableWSAddon"]) && !empty($_GET["disableWSAddon"])) {
	global $bdd;
	$params = json_decode(urldecode($_GET["disableWSAddon"]));
	$depedencies = json_decode($params[1]);
	disableAddon($params[0]);
	$response = $bdd->prepare("DELETE FROM `vbcms-modulesDepencies` WHERE moduleId=?"); // Je supprime tous ses liens
	$response->execute([$params[0]]);
	
	foreach ($depedencies as $depedency) {
		$response = $bdd->prepare("DELETE FROM `vbcms-modulesDepencies` WHERE moduleId=?"); // Je supprime tous ses liens
		$response->execute([$depedency]);
		disableAddon($depedency);
	}
} elseif (isset($_GET["getModuledepedencies"]) && !empty($_GET["getModuledepedencies"])) {
	global $bdd;
	$response = $bdd->prepare("SELECT * FROM `vbcms-modulesDepencies` WHERE moduleId=?");
	$response->execute([$_GET["getModuledepedencies"]]);
	$response = $response->fetchAll(PDO::FETCH_ASSOC);
	$depedencies = array();

	foreach ($response as $depedency) {
		array_push($depedencies, $depedency["depedencyId"]);
	}
	echo json_encode($depedencies);
} elseif (isset($_GET["checkIfModuleIsUsedByOthers"]) && !empty($_GET["checkIfModuleIsUsedByOthers"])) { // Plus descriptif tu meurs
	global $bdd;
	$response = $bdd->prepare("SELECT * FROM `vbcms-modulesDepencies` WHERE depedencyId=?");
	$response->execute([$_GET["checkIfModuleIsUsedByOthers"]]);
	$response = $response->fetchAll(PDO::FETCH_ASSOC);
	$depedencies = array();

	foreach ($response as $depedency) {
		array_push($depedencies, $depedency["moduleId"]);
	}
	echo json_encode($depedencies);
} elseif (isset($_GET["loadClientNavbar"])) {
	echo loadClientNavbar($_GET["loadClientNavbar"]);
} elseif (isset($_POST["modifyNavItem"]) && !empty($_POST["modifyNavItem"])) {
	modifyNavItem($_POST["modifyNavItem"]);
} elseif (isset($_POST["recreateClientNav"]) && !empty($_POST["recreateClientNav"])) {
	recreateClientNav($_POST["recreateClientNav"]);
} elseif (isset($_GET["deleteNavItem"]) && !empty($_GET["deleteNavItem"])) {
	deleteNavItem($_GET["deleteNavItem"]);
}  elseif (isset($_GET["loadLastNavItem"])) {
	echo loadLastNavItem($_GET["loadLastNavItem"]);
} else {?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title><?=$websiteName?> | Tâches de fond</title>
	<?php include 'includes/depedencies.php';?>
</head>
<body>
	<?php 
	include ('includes/navbar.php');
	?>

	<!-- Contenu -->
	<div class="page-content" leftSidebar="240" rightSidebar="0">
		<div class="d-flex flex-column">
			<div class="align-self-center text-center">
				<img src="https://cdn.vbcms.net/images/vbcms-logo/raccoon-512x.png">
				<h1 class="mt-5">Tâches de fond</h1>
				<p>Cette page n'est pas accessible en tant que telle. Seuls les addons peuvent communiquer avec. :D</p>
			</div>
		</div>
	</div>
</body>
</html>
<?php } ?>