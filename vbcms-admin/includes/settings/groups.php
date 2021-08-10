<div class="d-flex">
    <div class="flex-grow-1 d-flex flex-column">
        <div class="mt-2">
            <button class="btn btn-sm btn-brown" data-toggle="modal" data-target="#createGroupModal"><i class="fas fa-users"></i> <?=translate('createGroup')?></button>
            <!--<a href="#" class="btn btn-outline-brown btn-sm"><i class="fas fa-user-plus"></i> <?=translate('localAccountCreation')?></a>-->
        </div>

        <div class="d-flex p-4">
            <div style="min-width:360px;">
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
                                
                                echo('<tr class="userCard" style="height:2em;">
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
            <div class="flex-fill">
                <h5>Permissions</h5>
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
</script>