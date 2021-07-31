<?php
if(isset($_GET['id'])){
    $loadingScreenIdentifier = $_GET['id'];
} else {
    $redirectToList = true;
}

?>

<!-- Contenu -->
<div class="page-content d-flex flex-column" leftSidebar="240" rightSidebar="0" style="height:calc(100% - 60px); padding: 0!important;">
    <div style="padding: 30px 50px; background-color:#3e3e3e;">
        <div class="d-flex text-white">
           <div style="margin-right: 50px;">
                <h4><?=translate("loadingscreens_openEditor")?></h4>
                <button type="button" class="btn btn-brown"><?=translate("loadingscreens_openEditor")?></button>
                <h4><?=translate("modifyProperties")?></h4>
                <div class="form-group">
                    <label><?=translate("theme")?></label>
                    <select class="form-control form-control-sm" id="themeSelection">
                        <option>1</option>
                        <option>2</option>
                        <option>3</option>
                        <option>4</option>
                        <option>5</option>
                    </select>
                </div>

                <div class="form-group">
                    <label><?=translate("loadingscreens_previewResolution")?></label>
                    <select class="form-control form-control-sm" id="previewResolution">
                        <option value='{"width":2560,"height":1440}'>2560 x 1440</option>
                        <option value='{"width":1920,"height":1080}' selected>1920 x 1080</option>
                        <option value='{"width":1366,"height":768}'>1366 x 768</option>
                        <option value='{"width":1280,"height":720}'>1280 x 720</option>
                    </select>
                </div>
           </div>
           <div class="flex-grow-1 ">
               <h4>Prévisualisation</h4>
               <div id="loadingScreenPreview" class="rounded" style="background-image: url('https://api.apiflash.com/v1/urltoimage?access_key=65e037cb81b44087ba537b58dd19e4ff&format=jpeg&quality=80&response_type=image&url=<?php echo urlencode(VBcmsGetSetting("websiteUrl")."loadingscreen/".$loadingScreenIdentifier."?preview"); ?>&width=1920&height=1080');"></div>
           </div>
        </div>
    </div>
    <div style="padding: 30px 50px;">
        <h3><?=translate("loadingscreens_create")?></h3>
        <p><?=translate("loadingscreens_createPhrase")?></p>

        <div class="d-flex flex-column" id="page-content">
            <div class="d-flex flex-wrap">
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
                </div>
            </div>
        </div>
    </div>
	

	<script type="text/javascript">
        $( document ).ready(function() {
            <?php
            if(isset($redirectToList)){
                echo 'window.location.href = "'.VBcmsGetSetting("websiteUrl").'vbcms-admin/'.$urlPath[2].'/browse";';
            }
            ?>
            resizePreview();
        });
        $( window ).resize(function() {
            resizePreview();
        });

        function resizePreview(){
            $('#loadingScreenPreview').css("height",$('#loadingScreenPreview').width() / (16/9));
        }

        document.getElementById('previewResolution').addEventListener('change', function (evt) {
            let newRes = JSON.parse(this.value);
            let previousUrl = $("#loadingScreenPreview").css("background-image");
            
            let previewUrl = new URL(previousUrl.substring(5, previousUrl.length - 2));
            previewUrl.searchParams.set('width', newRes.width);
            previewUrl.searchParams.set('height', newRes.height);
            $("#loadingScreenPreview").css("background-image", 'url("'+previewUrl.toString()+'")');
        });
	</script>
</div>