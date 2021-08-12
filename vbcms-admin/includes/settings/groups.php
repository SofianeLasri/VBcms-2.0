<div class="d-flex">
    <div class="flex-grow-1 d-flex flex-column">
        <div class="mt-2">
            <button class="btn btn-sm btn-brown" data-toggle="modal" data-target="#createGroupModal"><i class="fas fa-users"></i> <?=translate('createGroup')?></button>
            <!--<a href="#" class="btn btn-outline-brown btn-sm"><i class="fas fa-user-plus"></i> <?=translate('localAccountCreation')?></a>-->
        </div>

        <div class="d-flex p-4">
            <div class="p-2" style="min-width:360px;">
                <h5>Groupes</h5>
                <table style="width:100%;">
                    <tbody>
                        <?php
                            $userGroups=$bdd->query("SELECT * FROM `vbcms-userGroups` ORDER BY `groupId` ASC")->fetchAll(PDO::FETCH_ASSOC);
                            foreach($userGroups as $userGroup){
                                $systemGroups = ["superadmins", "admins", "users"];
                                if(in_array($userGroup['groupName'], $systemGroups)) $modify = false;
                                else $modify = true;
                                $usersCount = $bdd->prepare("SELECT COUNT(*) FROM `vbcms-users` WHERE groupId = ?");
                                $usersCount->execute([$userGroup['groupId']]);
                                $usersCount=$usersCount->fetchColumn();
                                
                                echo('<tr class="userCard" id="group-'.$userGroup['groupId'].'" onclick="selectGroup('.$userGroup['groupId'].')" style="height:2em;">
                                <th>
                                    <span>'.translate($userGroup['groupName']).'</span>
                                </th>
                                <td>
                                    <span class="text-muted">'.$usersCount.' <i class="fas fa-user"></i></span>
                                </td>
                                <td label="plusMenu">
                                    <div class="roundedLink" data-toggle="tooltip" data-placement="top" title="Plus" onclick="showPlusMenu('.$userGroup['groupId'].')"><i class="fas fa-ellipsis-h"></i></div>
                                </td>
                            </tr>');
                            }
                        ?>
                    </tbody>
                </table>
            </div>
            <div class="flex-fill p-2">
                <h5>Permissions</h5>
                <form id="permsForm">
                    <?php
                    // Fait en JS
                    /*
                        $activatedExtensions = $bdd->query("SELECT * FROM `vbcms-activatedExtensions`")->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($activatedExtensions as $activatedExtension){
                            $ext = new module($activatedExtension['name']);
                            $permissions = $ext->getPermissions();
                            foreach ($permissions as $permission){
                                $hasPerm = verifyGroupPermission(1, $activatedExtension['name'], $permission);
                                if($hasPerm) $hasPerm = "checked";
                                $inputName['extension'] = $activatedExtension['name'];
                                $inputName['permission'] = $permission;
                                echo('<div>
                                <h5 class="text-brown border-bottom">'.$activatedExtension['name'].'</h5>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="'.urlencode(json_encode($inputName)).'" '.$hasPerm.'>
                                    <label class="form-check-label">'.$permission.'</label>
                                </div>
                            </div>');
                            }
                        }
                    */
                    ?>
                    
                </form>
            </div>
        </div>
    </div>
    <div class="admin-tips" style="position: relative !important; ">
        <div class="tip">
            <h5>Gérer les groupes</h5>
            <p>Les groupes d'utilisateurs permettent de faciliter la gestion des permissions. 
            <br><br><strong>Par défaut, chaque nouvel utilisateur est affecté au groupe des utilisateurs.</strong> Ce groupe ne permet pas l'accès au panneau d'administration et ne possède par défaut aucune permission.
            <br><br><strong>Chaque utilisateur d'un groupe héritera les permissions de ce dernier</strong>, sauf s'il possède ses propres permissions (qui remplaceront celles du groupe).</p>
        </div>
    </div>
</div>

<div class="plusMenu border" id="plusMenu">
    <ul>
        <li><span><?=translate('rename')?></span></li>
        <li><span>Copier le nom (slug)</span></li>
        <li><span>Copier l'identifiant (ID)</span></li>
        <li class="danger"><span><?=translate('deleteGroup')?></span></li>
    </ul>
</div>

<script type="text/javascript">
$( document ).ready(function() {
    // On va récupérer l'url et ses paramètres
    let url = new URL(window.location.href);
    let search_params = url.searchParams;

    // On récupère les infos de la requête
    if(search_params.get('selectedGroup')==null){
        selectGroup(1);
    } else {
        selectGroup(search_params.get('selectedGroup'));
    }
    
});

$(function() {
    $('.userCard').hover(function() {
        $(this).find('.roundedLink').css('background-color', 'var(--mainBrown)');
        $(this).find('.roundedLink').css('color', 'white');
    }, function() {
        // on mouseout, reset the background colour
        $(this).find('.roundedLink').css('background-color', '#dadada');
        $(this).find('.roundedLink').css('color', '#6c757d');
    });
});

function showPlusMenu(groupId){
    $('#plusMenu').css("top", event.clientY);
    $('#plusMenu').css("left", event.clientX);
    $('#plusMenu').css("display", "block");
}

$(document).click(function(event) {
    if($('#plusMenu').css('display')!='none' && $(event.target).closest(".plusMenu").attr('id')!='plusMenu' && $(event.target).closest("td").attr("label")!='plusMenu'){
        $('#plusMenu').css("display", "none");
    }
});

function selectGroup(id){
    // On va récupérer l'url et ses paramètres
    let url = new URL(window.location.href);
	let search_params = url.searchParams;

    if(search_params.get('selectedGroup')!=null){
        $("#group-"+search_params.get('selectedGroup')).removeClass("active");
    }
    // Et on modifie le paramètre
    search_params.set('selectedGroup', id);
    let newUrl = url.toString();
    window.history.replaceState({}, '', newUrl);

    var array = {
        type: "group",
        id: id
    };

    $.get("<?=VBcmsGetSetting("websiteUrl")?>vbcms-admin/backTasks/?getPermissions="+encodeURIComponent(JSON.stringify(array)), function(data) {
        // On supprime l'animation de chargement
        $("#group-"+id).addClass("active");
        // Et on insère le contenu
        $("#permsForm").html("");
        if (!isJson(data)){
            $("#permsForm").append('<h5><?=translate('error')?>: <?=translate('thisIsNotJSON')?></h5><br>'+data);
        }else{
            var json = JSON.parse(data);
            $.each( json, function( extension, permissionList ) {
                $('#permsForm').append('<div>\
                                    <h5 class="text-brown border-bottom">'+extension+'</h5>');
                $.each( permissionList, function( index, permission ){
                    if(permission.access == true){
                        var hasPerm = "checked";
                    } else {
                        var hasPerm = null;
                    }

                    var inputName = {
                        extension: extension,
                        permission: permission.name
                    };
                    $("#permsForm").append('<div class="form-check">\
                                        <input class="form-check-input" type="checkbox" name="'+encodeURIComponent(JSON.stringify(inputName))+'" onclick="editPermissions('+id+')" '+hasPerm+'>\
                                        <label class="form-check-label">'+permission.name+'</label>\
                                    </div>');
                });
                $('#permsForm').append('</div>');
            });               
        }
    });
}

function editPermissions(id){
    var array = {
        type: "group",
        id: id
    };

    $.post( "<?=VBcmsGetSetting("websiteUrl")?>vbcms-admin/backTasks?editPermissions="+encodeURIComponent(JSON.stringify(array)), $( "#permsForm" ).serialize() )
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
        }
    });
}
</script>