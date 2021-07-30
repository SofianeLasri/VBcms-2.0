<!-- Contenu -->
<div class="page-content" leftSidebar="240" rightSidebar="0">
	<h3><?=$translation["loadingscreens_themes"]?></h3>
	<p>Les thèmes de VBcms 1 ont été portés.</p>
	<div class="d-flex flex-column" id="page-content">
		<div class="d-flex flex-wrap">
			<?php
			$themes = $bdd->query("SELECT * FROM `vbcms-themes` WHERE designedFor = 6")->fetchAll(PDO::FETCH_ASSOC);
			foreach ($themes as $theme) {
				$themePath = $GLOBALS['vbcmsRootPath']."/vbcms-content/themes".$theme['path'];
				$themeInfos = json_decode(file_get_contents($themePath."/theme.json") ,true);
				echo '
				<div class="ld-card border rounded" style="background-image: url(\''.$GLOBALS['websiteUrl']."/vbcms-content/themes".$theme['path'].'/themeLargePic.jpg\');">
					<div class="ld-card-content p-2">
						<span><strong>'.$themeInfos['showname'].'</strong></span>
						<a href="/create?themeId='.$themeInfos['workshopId'].'" class="btn btn-sm btn-brown float-right">Choisir</a>
					</div>
				</div>';
			}
			?>
		</div>
		
	</div>
</div>