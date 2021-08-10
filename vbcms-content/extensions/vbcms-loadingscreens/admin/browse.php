<!-- Contenu -->
<div class="page-content d-flex flex-column" leftSidebar="240" rightSidebar="0" style="height:calc(100% - 60px);">
	<div class="d-flex align-items-center">
		<h3><?=translate("loadingscreens")?></h3>
		<div class="ml-2">
			<button type="button" class="btn btn-brown btn-sm" data-toggle="modal" data-target="#createLoadingScreen"><i class="fas fa-plus-circle"></i> <?=translate("create")?></button>
		</div>
	</div>
	<p><?=translate("loadingscreens_listPhrase")?></p>

	<div class="d-flex flex-column" id="page-content">
		<div class="d-flex flex-wrap">
			<?php
			$loadingscreens = $bdd->query('SELECT * FROM `vbcmsLoadingScreens_list`')->fetchAll(PDO::FETCH_ASSOC);
			foreach ($loadingscreens as $loadingscreen){
				$backgroundImage = 'https://api.apiflash.com/v1/urltoimage?access_key=65e037cb81b44087ba537b58dd19e4ff&format=jpeg&quality=80&response_type=image&url='.urlencode(VBcmsGetSetting("websiteUrl").$this->clientAccess.'/'.$loadingscreen['identifier']).'/&width=1920&height=1080';
				echo('
				<div class="ld-card border rounded mx-1 my-1" style="background-image: url(\''.$backgroundImage.'\');">
				<div class="ld-card-content p-2">
					<span><strong>'.$loadingscreen['showName'].'</strong></span>
					<a href="'.VBcmsGetSetting("websiteUrl").'vbcms-admin/'.$this->adminAccess.'/edit?id='.$loadingscreen['identifier'].'" class="btn btn-sm btn-brown float-right">'.translate('modify').'</a>
				</div>
			</div>');
			}
			?>
			<!--
			<div class="ld-card border rounded mx-1 my-1" style="background-image: url('https://sofianelasri.mtxserv.com/vbcms-content/uploads/stayonline.jpg');">
				<div class="ld-card-content p-2">
					<span><strong>Un super loading screen</strong></span>
					<a href="#" class="btn btn-sm btn-brown float-right">Modifier</a>
				</div>
			</div>

			<div class="ld-card border rounded mx-1 my-1" style="background-image: url('https://sofianelasri.mtxserv.com/vbcms-content/uploads/doubleload.jpg');">
				<div class="ld-card-content p-2">
					<span><strong>Un super loading screen</strong></span>
					<a href="#" class="btn btn-sm btn-brown float-right">Modifier</a>
				</div>
			</div>

			<div class="ld-card border rounded mx-1 my-1" style="background-image: url('https://sofianelasri.mtxserv.com/vbcms-content/uploads/themeTopImage.jpg');">
				<div class="ld-card-content p-2">
					<span><strong>Un super loading screen</strong></span>
					<a href="#" class="btn btn-sm btn-brown float-right">Modifier</a>
				</div>
			</div>

			<div class="ld-card border rounded mx-1 my-1" style="background-image: url('https://sofianelasri.mtxserv.com/vbcms-content/uploads/scp2.jpg');">
				<div class="ld-card-content p-2">
					<span><strong>Un super loading screen</strong></span>
					<a href="#" class="btn btn-sm btn-brown float-right">Modifier</a>
				</div>
			</div> -->
		</div>
	</div>

	<div class="modal fade" id="createLoadingScreen">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header bg-brown text-white">
					<h5 id="extensionDesacctivationModalTitle" class="modal-title"><?=translate("loadingscreens_giveAName")?></h5>
					<button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form id="createLoadingScreenForm">
						<p><?=translate("ws_askExtToDeleteItsData")?></p>
						<div class="form-group">
							<label><?=translate("name")?></label>
							<input type="text" class="form-control" id="showName" name="showName">
							<div id="badName" class="invalid-feedback"></div>
						</div>
						<div class="form-group">
							<label><?=translate("ws_clientAccess")?></label>
							<input type="text" class="form-control" id="identifier" name="identifier">
							<small class="form-text text-muted"><?=translate("loadingscreens_createLegendIdentifier")?></small>
							<div id="badIdentifier" class="invalid-feedback"></div>
						</div>
					</form>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-outline-brown" data-dismiss="modal"><?=translate("cancel")?></button>
					<button id="createLoadingScreenCreateBtn" onclick="createLoadingScreen()" type="button" class="btn btn-brown" disabled><?=translate("create")?></button>
				</div>
			</div>
		</div>
	</div>

	<script type="text/javascript">
		document.getElementById('showName').addEventListener('change', function (evt) {
			let slug = this.value
			slug = slug.toLowerCase().replace(/ /g,'-').replace(/[^\w-]+/g,'');
			$("#identifier").val(slug);

			let array = {};
			array.type="showName";
			array.name=this.value;
			$.get("<?=VBcmsGetSetting("websiteUrl")?>vbcms-admin/<?=$urlPath[2]?>/backTasks?checkIdentifierOrName="+encodeURIComponent(JSON.stringify(array)), function(data) {
				let json = JSON.parse(data);
				if(typeof json.error === 'undefined'){
					if(json.used == false){
						$("#badName").css("display", "none");
						if(typeof $("#createLoadingScreenCreateBtn").attr("disabled") !== 'undefined'  && $("#badIdentifier").css("display") == "none" && $("#identifier").val() != ""){
							$("#createLoadingScreenCreateBtn").removeAttr("disabled");
						}
						if(!isAlphanumeric($("#identifier").val())){
							if(typeof $("#createLoadingScreenCreateBtn").attr("disabled") === 'undefined'){
								$("#createLoadingScreenCreateBtn").attr("disabled", "");
							}
						}
					} else {
						$("#badName").html("<?=translate('alreadyUsed')?>");
						$("#badName").css("display", "block");
						if(typeof $("#createLoadingScreenCreateBtn").attr("disabled") === 'undefined'){
							$("#createLoadingScreenCreateBtn").attr("disabled", "");
						}
					}
				} else {
					$("#badName").html(json.error);
					$("#badName").css("display", "block");
					if(typeof $("#createLoadingScreenCreateBtn").attr("disabled") === 'undefined'){
						$("#createLoadingScreenCreateBtn").attr("disabled", "");
					}
				}
			})
        });

		document.getElementById('identifier').addEventListener('change', function (evt) {
			let array = {};
			array.type="identifier";
			array.name=this.value;
			$.get("<?=VBcmsGetSetting("websiteUrl")?>vbcms-admin/<?=$urlPath[2]?>/backTasks?checkIdentifierOrName="+encodeURIComponent(JSON.stringify(array)), function(data) {
				let json = JSON.parse(data);
				if(typeof json.error === 'undefined'){
					if(json.used == false){
						$("#badIdentifier").css("display", "none");
						if(typeof $("#createLoadingScreenCreateBtn").attr("disabled") !== 'undefined' && $("#badName").css("display") == "none" && $("#showName").val() != ""){
							$("#createLoadingScreenCreateBtn").removeAttr("disabled");
						}
						if(!isAlphanumeric($("#identifier").val())){
							if(typeof $("#createLoadingScreenCreateBtn").attr("disabled") === 'undefined'){
								$("#createLoadingScreenCreateBtn").attr("disabled", "");
							}
						}
					} else {
						$("#badIdentifier").html("<?=translate('alreadyUsed')?>");
						$("#badIdentifier").css("display", "block");
						if(typeof $("#createLoadingScreenCreateBtn").attr("disabled") === 'undefined'){
							$("#createLoadingScreenCreateBtn").attr("disabled", "");
						}
					}
				} else {
					$("#badIdentifier").html(json.error);
					$("#badIdentifier").css("display", "block");
					if(typeof $("#createLoadingScreenCreateBtn").attr("disabled") === 'undefined'){
						$("#createLoadingScreenCreateBtn").attr("disabled", "");
					}
				}
			})
        });

		function createLoadingScreen(){
			$.post( "<?=VBcmsGetSetting("websiteUrl")?>vbcms-admin/<?=$urlPath[2]?>/backTasks?createLoadingScreen", $( "#createLoadingScreenForm" ).serialize() )
            .done(function( data ) {
				if(data == ""){
					window.location.href = "<?=VBcmsGetSetting("websiteUrl")?>vbcms-admin/<?=$urlPath[2]?>/edit?id="+$("#identifier").val();
				} else {
					SnackBar({
                        message: data,
                        status: "danger",
                        timeout: false
                    });
				}
            });
		}

		function isAlphanumeric(text){
			var letterNumber = /^[0-9a-zA-Z-\-]+$/;
			if(text.match(letterNumber)){
				return true;
			}else{ 
				return false; 
			}
		}
	</script>
</div>