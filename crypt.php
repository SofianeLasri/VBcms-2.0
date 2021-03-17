<?php
if (isset($_POST["password"]) AND !empty($_POST["password"])) {
	$cryptedPass = crypt($_POST["password"]);
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Crypter un mot de passe</title>
</head>
<body>
	<form action="crypt.php" method="POST">
		<label>Entrez un mot de passe</label>
		<input type="text" name="password">
		<input type="submit" name="submit">
	</form>
	<?php
	if (isset($cryptedPass)) {
		echo "Votre mot de passe crypté est:<strong>".$cryptedPass."</strong>";
	}
	?>
</body>
</html>