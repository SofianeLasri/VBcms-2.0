<?php
$vbcmsVer = $bdd->query("SELECT value FROM `vbcms-settings` WHERE name='vbcmsVersion'")->fetchColumn();
?>
<header>
	<div class="navbar managerHeader d-flex">
		<div class="brand d-flex">
			<div class="desktop-toggler mx-2">
				<a href="#" class="menu-toggler" data-action="toggle" data-side="left"><i class="fas fa-bars"></i></a>
			</div>
			<a href="<?=VBcmsGetSetting("websiteUrl")?>vbcms-admin" class="brand-name"><?=VBcmsGetSetting("websiteName")?></a>
		</div>

		<div class="menu d-flex ml-auto justify-content-end">
			<div class="menu-item dropdown">
				<a href="#" class="menu-link" role="button" data-toggle="dropdown">
					<div class="menu-icon">
						<i class="fas fa-bell"></i>
					</div>
					<div id="notificationsNumber" class="menu-label">n</div>
				</a>
				<div id="notificationsDropdown" class="dropdown-menu notificationsDropdown" aria-labelledby="userProfileDD">					
					<!--<a class="dropdown-item" target="_blank" href="https://vbcms.net/manager/myaccount">
						<small><strong>vbcms-updater</strong> - 2021-05-10 08:55:29</small><br>
						Une mise à jour est disponible!
					</a>-->
				</div>
			</div>
			<div class="menu-item d-flex align-items-center dropdown">
				<a href="#" class="menu-link dropdown-toggle" role="button" id="userProfileDD" data-toggle="dropdown">
					<div class="menu-img">
						<img src="<?=$_SESSION['user_profilePic']?>">
					</div>
					<div class="menu-text align-content-center mx-2">
						<?=$_SESSION['user_username']?>
					</div>
				</a>
				<div class="dropdown-menu userDropdown" aria-labelledby="userProfileDD">
					<div class="dropdown-topItem">
						<span class="brand-name">VBcms</span><small class="ml-1"><?=$vbcmsVer?></small>
					</div>
					
					<a class="dropdown-item" target="_blank" href="https://vbcms.net/manager/myaccount"><?=translate("myProfil")?></a>
				    <a class="dropdown-item" target="_blank" href="https://vbcms.net/manager/myliscence"><?=translate("manageliscence")?></a>
				    <a class="dropdown-item" href="?logout"><?=translate("disconnect")?></a>
				</div>
			</div>
		</div>
	</div>
</header>


<!-- barre lattérale de naviguation -->
<div class="sidebar sidebarminify">
	<div class="scrollLinks">
		<div class="menu" >
			<div class="menu-header"><?=translate("naviguation")?></div>
			<div class="menu-item">
				<a href="/vbcms-admin" class="menu-link">
					<span class="menu-icon"><i class="fas fa-home"></i></span>
					<span class="menu-text"><?=translate("dashboard")?></span>
				</a>
			</div>
			<div class="menu-item">
				<a href="/vbcms-admin/settings" class="menu-link">
					<span class="menu-icon"><i class="fas fa-wrench"></i></span>
					<span class="menu-text"><?=translate("settings")?></span>
				</a>
			</div>
			<?php if(verifyUserPermission($_SESSION['user_id'], 'vbcms', 'updatePanel')) { ?>
			<div class="menu-item">
				<a href="/vbcms-admin/updater" class="menu-link">
					<span class="menu-icon"><i class="fas fa-cloud-download-alt"></i></span>
					<span class="menu-text"><?=translate("update")?></span>
				</a>
			</div>
			<?php } ?>

			<?php 
			if(VBcmsGetSetting("debugMode") == "1" && verifyUserPermission($_SESSION['user_id'], 'vbcms', 'accessDebug')){
				echo '<div class="menu-item">
				<a href="/vbcms-admin/debug" class="menu-link">
					<span class="menu-icon"><i class="fas fa-bug"></i></span>
					<span class="menu-text">Debug</span>
				</a>
			</div>';
			}
			?>
			
			<?php if(verifyUserPermission($_SESSION['user_id'], 'vbcms', 'manageExtensions')) { ?>
			<div class="menu-divider"></div>
			<div class="menu-header"><?=translate("workshop")?></div>
			<div class="menu-item">
				<a href="/vbcms-admin/workshop/manage" class="menu-link">
					<span class="menu-icon"><i class="fas fa-wrench"></i></span>
					<span class="menu-text"><?=translate("ws_manage")?></span>
				</a>
			</div>
			<?php } ?>

			<!-- Insérer les liens ici -->

			<?php
			$navbarItems = $bdd->query("SELECT * FROM `vbcms-adminNavbar`")->fetchAll(PDO::FETCH_ASSOC);
			$parent = null;
			foreach ($navbarItems as $navbarItem) {
				if ($navbarItem["parentId"]==0) {
					$parent['id'] = $navbarItem['id'];
					$parent['name'] = $navbarItem['value1'];
					$response = $bdd->prepare("SELECT adminAccess FROM `vbcms-activatedExtensions` WHERE name=?");
					$response->execute([$parent['name']]);
					$parent['alias'] = $response->fetchColumn();
					$parent['access'] = verifyUserPermission($_SESSION['user_id'], $parent['name'], 'access'); // true ou false

					if($parent['access']){
						echo '<div class="menu-divider"></div>';
						echo '<div class="menu-header">'.translate($navbarItem["value2"]).'</div>';
					}
					
				} elseif($navbarItem["parentId"]==$parent['id'] && $parent['access']) {
					if(verifyUserPermission($_SESSION['user_id'], $parent['name'], 'access-'.$navbarItem["value3"])){
						echo '<div class="menu-item">
						<a href="/vbcms-admin/'.$parent['alias'].$navbarItem["value3"].'" class="menu-link">
							<span class="menu-icon"><i class="fas '.$navbarItem["value1"].'"></i></span>
							<span class="menu-text">'.translate($navbarItem["value2"]).'</span>
						</a>
					</div>';
					}
					
				}
			}
			?>
			

		</div>
	</div>
</div>
<!-- FIN barre lattérale de naviguation -->

<script type="text/javascript">
	var pathname = window.location.pathname;
	if (pathname.slice(-1)=="/") {
		pathname = pathname.substring(0, pathname.length - 1);
	}
	$('a[href="'+pathname+'"]').addClass( "active" );

	$(".menu-toggler" ).click(function() {
		if ($(".sidebarminify").css("left") == "0px") {
			$(".sidebarminify").css("left", "-240px");
			resizePageContent(0,"left");
		}else{
			$(".sidebarminify").css("left", "0px");
			resizePageContent(240,"left");
		}
	});
	async function loadNotifications(){
		await $.get("<?=VBcmsGetSetting("websiteUrl")?>vbcms-admin/backTasks/?getNotifications", function(data){
			var notifications = JSON.parse(data);
			if (notifications.length!=0) {
				$("#notificationsNumber").html(notifications.length);
				jQuery.each(JSON.parse(data), function(index){
					$("#notificationsDropdown").append('<a class="dropdown-item" href="'+notifications[index]["link"]+'">\
						<small><strong>'+JSON.parse(notifications[index]["origin"])[0]+'</strong> - '+notifications[index]["date"]+'</small><br>\
						'+notifications[index]["content"]+'\
					</a>');
				});
			} else {
				$("#notificationsNumber").remove();
				$("#notificationsDropdown").html("<div class='text-center text-muted'>Vous n'avez aucune notification <i class=\"far fa-smile\"></i></div>");
			}
			
		});
	}
	loadNotifications();
</script>