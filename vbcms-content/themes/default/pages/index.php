<!DOCTYPE html>
<html lang="<?=$_SESSION['language']?>">
<head>
	<meta charset="utf-8">
	<title><?=$websiteName?></title>
	<?php include $GLOBALS['vbcmsRootPath'].'/vbcms-content/themes/default/includes/depedencies.php';?>
</head>
<body>
	<header>
		<div class="container">
			<div class="navbar navbar-expand-lg navbar-light">
				<a class="navbar-brand" href="<?=$_SERVER['HTTP_HOST']?>">
					<img class="mr-2" src="<?=$websiteLogo?>"><?=$websiteName?>
				</a>
				<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
					<span class="navbar-toggler-icon"></span>
				</button>
				<div class="collapse navbar-collapse justify-content-end" id="navbarNav">
					<ul class="navbar-nav" id="navbar-itemParent-0">
						<?php /*
						$navbar = json_decode(loadClientNavbar(0), true);
						foreach($navbar as $navItem){
							if($navItem["parentId"] == 0){
								if($navItem["value2"] == $http."://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']) || $navItem["value2"] == dirname($_SERVER['PHP_SELF'])){
									if(hasChildItem($navItem["id"])){
										echo '<li class="nav-item dropdown">';
										echo '<a class="nav-link dropdown-toggle" role="button" data-toggle="dropdown-'.$navItem["id"].'" href="'.$navItem["value2"].'" target="'.$navItem["value3"].'">'.$navItem["value1"].'</a>';

									} else echo '<li class="nav-item active"><a class="nav-link" href="'.$navItem["value2"].'" target="'.$navItem["value3"].'">'.$navItem["value1"].'</a></li>';
								} else {
									if(hasChildItem($navItem["id"])) echo '<li class="nav-item dropdown">';
									else echo '<li class="nav-item active">';
									echo '<a class="nav-link" href="'.$navItem["value2"].'" target="'.$navItem["value3"].'">'.$navItem["value1"].'</a></li>';
								}
							}
						}

						function hasChildItem($id){
							$navbar = json_decode(loadClientNavbar(0), true);
							foreach($navbar as $navItem){
								if($navItem["parentId"] == $id){
									return true;
								}
							}
							return false;
						} */
						?>
					</ul>
				</div>
			</div>
		</div>
	</header>
	<div class="index-presentationTop">
		<div class="backImage">
			<img src="<?=$websiteUrl?>vbcms-content/uploads/vitrine/raccon-vitrine.png">
		</div>
		<div class="content">
			<h1 class="txt-shadow">Merci d'avoir choisi VBcms!</h1>
			<p class="txt-shadow">Un tas de fonctionnalités t'attendent, découvres les vite et facilite toi la vie! :D</p>
		</div>
	</div>
	
	<?php include $GLOBALS['vbcmsRootPath'].'/vbcms-content/themes/default/includes/footer.php'; ?>
</body>
</html>