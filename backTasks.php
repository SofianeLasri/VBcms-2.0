<?php
if (isset($_GET["loadClientNavbar"])) {
	echo loadClientNavbar($_GET["loadClientNavbar"]);
} elseif (isset($_GET["loadLastNavItem"])) {
	echo loadLastNavItem($_GET["loadLastNavItem"]);
} else {?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title><?=$websiteName?> | Tâches de fond</title>
</head>
<body>

</body>
</html>
<?php } ?>