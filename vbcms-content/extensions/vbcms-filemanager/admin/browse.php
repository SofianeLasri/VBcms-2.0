<!-- Contenu -->
<div class="page-content d-flex flex-column" leftSidebar="240" rightSidebar="0" style="height:calc(100% - 60px);">
	<h3><?=translate("gallery_filemanager")?></h3>
	<p>La taille maximale d'envoie est de <code><?=(int)(ini_get('upload_max_filesize'))?> MB</code>.</p>
	<iframe class="flex-grow-1" src="<?=openFilemanager('admin')?>"></iframe>

	<script type="text/javascript">

	</script>
</div>