<div class="d-flex">
    <div class="flex-grow-1 d-flex flex-column">
        <div class="mt-2">
            <button class="btn btn-sm btn-brown" data-toggle="modal" data-target="#inviteUserModal"><i class="fas fa-envelope"></i> <?=translate('inviteUser')?></button>
            <button class="btn btn-outline-brown btn-sm" data-toggle="modal" data-target="#createUserModal"><i class="fas fa-user-plus"></i> <?=translate('localAccountCreation')?></button>
            <!--<a href="#" class="btn btn-outline-brown btn-sm"><i class="fas fa-user-plus"></i> <?=translate('localAccountCreation')?></a>-->
        </div>
        <?php
            $userGroups=$bdd->query("SELECT * FROM `vbcms-userGroups` ORDER BY `groupId` ASC")->fetchAll(PDO::FETCH_ASSOC);
            foreach($userGroups as $userGroup){
                echo ('<div class="d-flex flex-column p-4">
                    <div class="text-brown border-bottom">');

                $usersCount = $bdd->prepare("SELECT COUNT(*) FROM `vbcms-users` WHERE groupId = ?");
                $usersCount->execute([$userGroup['groupId']]);
                $usersCount=$usersCount->fetchColumn();

                echo translate($userGroup['groupName'])." (".$usersCount;

                if($usersCount>1) echo " ".strtolower(translate("users")).")";
                else echo " ".strtolower(translate("user")).")";

                echo ('</div>
                <div class="d-flex flex-wrap userList">');

                $users = $bdd->prepare("SELECT * FROM `vbcms-users` WHERE groupId = ?");
                $users->execute([$userGroup['groupId']]);
                $users=$users->fetchAll(PDO::FETCH_ASSOC);

                foreach($users as $user){
                    $userProfilPic = $bdd->prepare("SELECT value FROM `vbcms-usersSettings` WHERE userId = ? AND name = 'profilPic'");
                    $userProfilPic->execute([$user['id']]);
                    $userProfilPic=$userProfilPic->fetchColumn();
                    if(empty($userProfilPic)){
                        $userProfilPic = VBcmsGetSetting("websiteUrl")."vbcms-admin/images/misc/programmer.png";
                    }
                    
                    $joinedDate = $bdd->prepare("SELECT value FROM `vbcms-usersSettings` WHERE userId = ? AND name = 'joinedDate'");
                    $joinedDate->execute([$user['id']]);
                    $joinedDate = $joinedDate->fetchColumn();
                    if(!empty($joinedDate)) {
                        $joinedDate = new DateTime($joinedDate);
                        $joinedDate = $joinedDate->format('l jS F Y');
                    } else $joinedDate = translate('unknownF');

                    $groupsOptions = null;
                    foreach($userGroups as $userGroup){
                        if($userGroup['groupId'] == $user['groupId']) $groupsOptions = $groupsOptions."<option value='".$userGroup['groupId']."' selected>".translate($userGroup['groupName'])."</option>";
                        else $groupsOptions = $groupsOptions."<option value='".$userGroup['groupId']."'>".translate($userGroup['groupName'])."</option>";
                    }
                    
                    echo ('<div class="userCard d-flex flex-column">
                        <div class="d-flex">
                            <div class="userProfilPic" style="background-image:url(\''.$userProfilPic.'\')"></div>
                            <div class="ml-2">
                                <h6 class="mb-n1">'.$user['username'].'</h6>
                                <small class="text-muted"><strong>Auth:</strong>'.$user['auth'].' <strong>'.translate('joinedOn').'</strong>: '. $joinedDate.'</small><br>
                                <small>');
                                    if($user['id']!=$_SESSION['user_id']){
                                        echo ('<a href="#" onclick="toogle(\'edit-'.$user['username'].'\')" class="text-brown">'.translate("modifyUser").'</a>');
                                    }
                                    if($user['auth']=='vbcms'){
                                        echo('<a href="#" onclick="editLocalAccount(\''.$user['id'].'\')" class="text-brown">'.translate("modifyLocalAccount").'</a>');
                                    }
                                echo('</small></div>
                        </div>');
                        echo ('<div id="edit-'.$user['username'].'" style="display: none;"><div class="d-flex flex-column mt-2"">
                                    <div class="form-inline">
                                        <label>Changer de groupe</label>
                                        <select class="form-control form-control-sm flex-grow-1 ml-2" id="groupUser'.$user['id'].'" onchange="changeUserGroup('.$user['id'].')">
                                            '.$groupsOptions.'
                                        </select>
                                    </div>
                                    <div class="d-flex mt-2">
                                        <button class="btn btn-sm btn-brown">Modifier ses permissions</button>');
                                        if($user['id']!=$_SESSION['user_id']){
                                            echo('<button class="btn btn-sm btn-danger ml-2">Expulser</button>');
                                        }
                                    echo('</div>
                                </div>');
                            echo('</div>');
                    echo ('</div>');
                }
                echo "</div></div>";
            }
        ?>
        <!--
        <div class="d-flex flex-column p-4">
            <div class="text-brown border-bottom">
                Un groupe trop génial (1 utilisateur)
            </div>
            <div class="d-flex flex-wrap userList">
                <div class="userCard d-flex">
                    <div class="userProfilPic" style="background-image:url('https://cdn.akamai.steamstatic.com/steamcommunity/public/images/avatars/ee/ee6f9c9ffd6bb2fd2114a378f3f03d997f79e4b9_full.jpg')"></div>
                    <div class="ml-2">
                        <h6 class="mb-n1">sofianelasri</h6>
                        <small class="text-muted">A rejoint le: </small><br>
                        <a href="#" class="text-brown"><?=translate("modifyUser")?></a>
                    </div>
                </div>
            </div>
        </div>
        -->
    </div>
    <div class="admin-tips" style="position: relative !important; ">
        <div class="tip">
            <h5>Gérer les utilisateurs</h5>
            <p>VBcms peut être utilisé par plusieurs personnes en même temps. Ici tu peux gérer leur compte, mais également inviter d'autres personnes.<br><strong>Fais bien attention à qui aura accès au panneau d'administration.</strong></p>
        </div>
    </div>
</div>

<!-- Modal pour création de compte local -->
<div class="modal fade" id="inviteUserModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-brown text-white">
                <h5 class="modal-title"><?=translate('inviteUser')?></h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p><?=translate('toInviteAnUserYouMustSpecifyHisEmailOrUsername')?></p>
                <div class="form-group">
                    <label><?=translate('usernameEmail')?></label>
                    <input type="text" class="form-control" id="searchNetUser">
                    <div class="border rounded-bottom">
                        <div class="d-flex flex-column" id="netUserList">
                            <!--<div class="userCard d-flex align-items-center" style="cursor: pointer;">
                                <div class="userProfilPic loadingBack"></div>
                                <div class="ml-2">
                                    <h6 class="loadingBack rounded" style="width: 10em; height: 1em;"></h6>
                                </div>
                            </div>-->
                        </div>
                        <div class="w-100 bg-brown rounded-bottom p-1">
                            <span class="brand-name text-white">VBcms.net</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-brown" data-dismiss="modal"><?=translate("close")?></button>
                <button id="sendInvite" onclick="sendInvite()" type="button" class="btn btn-brown" disabled><?=translate("sendInvite")?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="localAccountCreationModal" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-brown text-white">
                <h5 id="extensionActivationModalTitle" class="modal-title"><?=translate('modifyLocalAccount')?></h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="localAccountCreationForm" class="needs-validation" novalidate>
                    <div class="form-group">
                        <label><?=translate('username')?></label>
                        <input type="text" class="form-control" name="localUserUsername" id="localUserUsername" placeholder="" value="<?=$_SESSION['user_username']?>" required>
                        <small class="form-text text-muted"><?=translate("localAccountCreation_loginCanBeDifferent")?></small>
                        <div class="invalid-feedback"><?=translate("localAccountCreation_pleaseEnterLogin")?></div>
                    </div>
                    <div class="form-group">
                        <label><?=translate('password')?></label>
                        <input type="password" class="form-control" name="localUserPassword1" id="localUserPassword1" placeholder="" required>
                        <div class="invalid-feedback" id="localUserPassword1Alert">
                            <?=translate('localAccountCreation_youCreateAnAccountWithoutPassword')?> <img height="16" src="<?=VBcmsGetSetting("websiteUrl")?>vbcms-admin/images/emojis/thinkingHard.png">
                        </div>
                    </div>
                    <div class="form-group">
                        <label><?=translate('repeatPassword')?></label>
                        <input type="password" class="form-control" name="localUserPassword2" id="localUserPassword2" placeholder="" required>
                        <div class="invalid-feedback" id="localUserPassword2Alert"><?=translate("localAccountCreation_pleaseRewriteYourPassword")?></div>
                    </div>

                    <div>
                        <h5><?=translate("whyCreateALocalAccount")?></h5>
                        <p>Autant le dire tout de suite, les serveurs de VBcms ne sont pas réputés pour être très fiables... Il sera assez fréquent de les voir inaccessibles, surtout à ce stade du développement.<br><br><strong>Le compte local te permettera d'accéder au panneau d'administration, même en cas de panne générale.</strong> Tu ne pourras pas télécharger d'extensions ni mettre VBcms à jour, mais au moins tu pourras continuer à gérer ton site. :D</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-brown" data-dismiss="modal"><?=translate("cancel")?></button>
                    <button id="registerBtn" type="button" class="btn btn-brown" disabled><?=translate("create")?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
(function() {
'use strict';
window.addEventListener('load', function() {
    // Fetch all the forms we want to apply custom Bootstrap validation styles to
    var forms = document.getElementsByClassName('needs-validation');
    // Loop over them and prevent submission
    var validation = Array.prototype.filter.call(forms, function(form) {
    form.addEventListener('submit', function(event) {
        if (form.checkValidity() === false) {
        event.preventDefault();
        event.stopPropagation();
        }
        form.classList.add('was-validated');
    }, false);
    });
}, false);
})();

function editLocalAccount(id) {
    $.get("<?=VBcmsGetSetting("websiteUrl")?>vbcms-admin/backTasks/?getNetIdLocalAccount="+id, function(data) {
        var json = JSON.parse(data);
        if(!jQuery.isEmptyObject(json)){
            $("#localUserUsername").val(json.username);
        } else{
            $("#localUserUsername").val("");
        }
    });
    $("#registerBtn").attr("onclick", "sendLocalAccountInfos('"+id+"')");
    $('#localAccountCreationModal').modal('show');
}

$("#localUserPassword1").change(function() {
    checkPassword();
});
$("#localUserPassword2").change(function() {
    checkPassword();
});

function sendLocalAccountInfos(id){
    $.post( "<?=VBcmsGetSetting("websiteUrl")?>vbcms-admin/backTasks?setLocalAccount="+id, $( "#localAccountCreationForm" ).serialize() )
    .done(function( data ) {
        if(data!=""){
            SnackBar({
                message: data,
                status: "danger",
                timeout: false
            });
        } else {
            SnackBar({
                message: '<?=translate("success-saving")?>',
                status: "success"
            });
            $('#localAccountCreationModal').modal('hide');
        }
    });
}

function changeUserGroup(id){
    var array = {
        id: id,
        groupId: $("#groupUser"+id).val()
    };
    $.get("<?=VBcmsGetSetting("websiteUrl")?>vbcms-admin/backTasks/?changeUserGroup="+JSON.stringify(array), function(data) {
        if(data!=""){
            SnackBar({
                message: data,
                status: "danger",
                timeout: false
            });
        } else {
            SnackBar({
                message: '<?=translate("success-saving")?>',
                status: "success"
            });
        }
    });

    
}

function checkPassword(){
    if ($("#localUserPassword1").val()!=$("#localUserPassword2").val()) {
        $("#localUserPassword1Alert").html("<?=translate("localAccountCreation_passwordsDontMatches")?>");
        $("#localUserPassword1Alert").css("display","block");
        $("#localUserPassword2Alert").html("<?=translate("localAccountCreation_passwordsDontMatches")?>");
        $("#localUserPassword2Alert").css("display","block");
        $("#registerBtn").attr("disabled", "");
    } else {
        var passw = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,32}$/;
        if($("#localUserPassword1").val().match(passw)) { 
            $("#localUserPassword1Alert").html('<?=translate('localAccountCreation_youCreateAnAccountWithoutPassword')?> <img height="16" src="<?=VBcmsGetSetting("websiteUrl")?>vbcms-admin/images/emojis/thinkingHard.png">');
            $("#localUserPassword1Alert").css("display","");
            $("#localUserPassword2Alert").html('<?=translate("localAccountCreation_pleaseRewriteYourPassword")?>');
            $("#localUserPassword2Alert").css("display","");
            $("#registerBtn").removeAttr("disabled");
        } else { 
            $("#localUserPassword1Alert").html("<?=translate("localAccountCreation_yourPasswordIsTooWeak")?>");
            $("#localUserPassword1Alert").css("display","block");
            $("#localUserPassword2Alert").html("<?=translate("localAccountCreation_yourPasswordIsTooWeak")?>");
            $("#localUserPassword2Alert").css("display","block");
            $("#registerBtn").attr("disabled", "");
        }
        
    }
}

function toogle(idToToogle){
    if($("#"+idToToogle).css("display") == "none"){
        $("#"+idToToogle).css("display", "block");
    }else{
        $("#"+idToToogle).css("display", "none");
    }
}

document.getElementById('searchNetUser').addEventListener("change", function (evt) {
    checkUser();
}, false);

function checkUser() {
    $("#netUserList").html('<div class="userCard d-flex align-items-center">\
                                <div class="userProfilPic loadingBack"></div>\
                                <div class="ml-2">\
                                    <h6 class="loadingBack rounded" style="width: 10em; height: 1em;"></h6>\
                                </div>\
                            </div>');
    $.get("https://api.vbcms.net/profiles/v1/search/"+encodeURIComponent(JSON.stringify($("#searchNetUser").val())), function(data, statusText, xhr) {
        if(xhr.status==200){
            if (!isJson(data)) {
                if(typeof $("#sendInvite").attr("disabled") == 'undefined'){
                    $("#sendInvite").addAttr("disabled");
                }
                $("#netUserList").html("<div class='p-2'><i class='fas fa-exclamation-circle warningBlink'></i><span class='text-danger ml-1'><b>VBcms.net renvoie:</b></span><br><code><pre>"+data+"</pre></code></div>");
            } else {
                $("#netUserList").html();
                var json = JSON.parse(data);
                $.each( json, function( index, user ) {
                    $("#netUserList").append('<div class="userCard d-flex align-items-center" onclick="fillInviteUser(\'\');">\
                                    <div class="userProfilPic" style="background-image:url(\''+user.profilePic+'\')"></div>\
                                    <div class="ml-2">\
                                        <h6>'+user.username+'</h6>\
                                    </div>\
                                </div>');
                });
            }
        }else{
            $("#netUserList").html("<div class='p-2'><i class='fas fa-exclamation-circle warningBlink'></i><span class='text-danger ml-1'><b>La requête vers VBcms.net renvoie le code </b></span><code>"+xhr.status+"</code></div>");
        }
        
    });
}

function fillInviteUser(username){
    $('#searchNetUser').val('+user.username+');
    if(typeof $("#sendInvite").attr("disabled") !== 'undefined'){
        $("#sendInvite").removeAttr("disabled");
    }
}

function sendInvite(){
    var array = {
        username: $("#searchNetUser").val(),
        key: "<?=VBcmsGetSetting('encryptionKey')?>"
    };
    $.get("https://api.vbcms.net/profiles/v1/invite/"+encodeURIComponent(JSON.stringify(array)), function(data, statusText, xhr) {
        if(xhr.status==200){
            if (!isJson(data)) {
                $("#netUserList").html("<div class='p-2'><i class='fas fa-exclamation-circle warningBlink'></i><span class='text-danger ml-1'><b>VBcms.net renvoie:</b></span><br><code><pre>"+data+"</pre></code></div>");
            } else {
                $('#inviteUserModal').modal('hide');
                SnackBar({
                    message: "<?=translate('inviteSent')?>",
                    status: "success"
                });
            }
        }else{
            $("#netUserList").html("<div class='p-2'><i class='fas fa-exclamation-circle warningBlink'></i><span class='text-danger ml-1'><b>La requête vers VBcms.net renvoie le code </b></span><code>"+xhr.status+"</code></div>");
        }
        
    });
}
</script>