<div class="d-flex">
    <div class="flex-grow-1 d-flex flex-column">
        <div class="mt-2">
            <button class="btn btn-sm btn-brown" data-toggle="modal" data-target="#inviteUserModal"><i class="fas fa-envelope"></i> <?=translate('inviteUser')?></button>
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
                    $userProfilPic = file_get_contents("https://api.vbcms.net/profiles/v1/get/".$user['netId']);
                    if(isJson($userProfilPic)){
                        $userProfilPic = json_decode($userProfilPic, true);
                        $userProfilPic = $userProfilPic['profilePic'];
                    } else {
                        // Ici on a soit pas trouvé l'utilisateur, soit les serveurs sont down
                        // Du coup on va check dans localAccounts
                        $userProfilPic = $bdd->prepare("SELECT * FROM `vbcms-localAccounts` WHERE netIdAssoc = ?");
                        $userProfilPic->execute([$user['netId']]);
                        $userProfilPic=$userProfilPic->fetch(PDO::FETCH_ASSOC);
                        if(!empty($userProfilPic)){
                            $userProfilPic = $userProfilPic['profilePic'];
                        }else{
                            // Ici l'utilisateur n'existe pas dans la liste des comptes locaux
                            // Donc on va lui mettre une image placeholder
                            $userProfilPic = VBcmsGetSetting("websiteUrl")."vbcms-admin/images/misc/programmer.png";
                        }
                    }

                    $joinedDate = new DateTime($user['localJoinedDate']);

                    $groupsOptions = null;
                    foreach($userGroups as $userGroup){
                        if($userGroup['groupId'] == $user['groupId']) $groupsOptions = $groupsOptions."<option value='".$userGroup['groupId']."' selected>".translate($userGroup['groupName'])."</option>";
                        else $groupsOptions = $groupsOptions."<option value='".$userGroup['groupId']."'>".translate($userGroup['groupName'])."</option>";
                    }
                    
                    
                    if($user['username'] != $_SESSION['user_username']){
                        echo ('<div class="userCard d-flex flex-column">
                        <div class="d-flex">
                            <div class="userProfilPic" style="background-image:url(\''.$userProfilPic.'\')"></div>
                            <div class="ml-2">
                                <h6 class="mb-n1">'.$user['username'].'</h6>
                                <small class="text-muted">'.translate('joinedOn').': '. $joinedDate->format('l jS F').'</small><br>
                                <small><a href="#" class="text-brown">'.translate("modifyUser").'</a></small>
                            </div>
                        </div>');
                        echo ('<div class="d-flex flex-column mt-2" id="edit-'.$user['username'].'">
                            <div class="form-inline">
                                <label>Changer de groupe</label>
                                <select class="form-control form-control-sm flex-grow-1 ml-2" id="newGroup">
                                    '.$groupsOptions.'
                                </select>
                            </div>
                            <div class="d-flex mt-2">
                                <button class="btn btn-sm btn-brown">Modifier ses permissions</button>
                                <button class="btn btn-sm btn-danger ml-2">Expulser</button>
                            </div>
                        </div>');
                    }else{
                        echo ('<div class="userCard d-flex flex-column">
                        <div class="d-flex">
                            <div class="userProfilPic" style="background-image:url(\''.$userProfilPic.'\')"></div>
                            <div class="ml-2">
                                <h6 class="mb-n1">'.$user['username'].'</h6>
                                <small class="text-muted">'.translate('joinedOn').': '. $joinedDate->format('l jS F').'</small><br>
                                <small class="text-brown">Toi :)</small>
                            </div>
                        </div>');
                    }
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
            <p>VBcms peut être utilisé par plusieurs peronnes en même temps. Ici tu peux gérer leur compte, mais également inviter d'autres personnes.<br><strong>Fais bien attention à qui aura accès au panneau d'administration.</strong></p>
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

<script type="text/javascript">
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
        key: "<?=$GLOBALS['encryptionKey']?>"
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