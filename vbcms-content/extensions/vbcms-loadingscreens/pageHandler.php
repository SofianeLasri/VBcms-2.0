<?php
if($type =="admin"){
    $pageDepedencies = '<link href="'.$GLOBALS['websiteUrl'].'vbcms-content/extensions/'.$this->path.'/assets/css/admin.css" rel="stylesheet">';
    switch($parameters[1]){
        case 'browse':
            if(verifyUserPermission($_SESSION['user_id'], $this->name, 'access-browse')){
                $pageToInclude = $extensionFullPath."/admin/browse.php";
                $this->extensionCreatePage($type, 0, $pageToInclude, translate("loadingscreens_list"), "", $pageDepedencies);
            }
            
            break;
        
        case 'edit':
            if(verifyUserPermission($_SESSION['user_id'], $this->name, 'canEdit')){
                $pageToInclude = $extensionFullPath."/admin/edit.php";
                $this->extensionCreatePage($type, 0, $pageToInclude, translate("loadingscreens_create"), "", $pageDepedencies);
            }
        break;

        case 'backTasks':

            if(isset($_GET['checkIdentifierOrName'])){
                if(!empty($_GET['checkIdentifierOrName'])){
                    if(isJson(urldecode($_GET['checkIdentifierOrName']))){
                        $json = json_decode(urldecode($_GET['checkIdentifierOrName']), true);
                        if(isset($json['type']) && $json['type']=='identifier'){
                            $response = $bdd->prepare('SELECT * FROM `vbcmsLoadingScreens_list` WHERE identifier = ?');
                            $response->execute([$json['name']]);
                            $response=$response->fetch();
                        }elseif(isset($json['type']) && $json['type']=='showName'){
                            $response = $bdd->prepare('SELECT * FROM `vbcmsLoadingScreens_list` WHERE showName = ?');
                            $response->execute([$json['name']]);
                            $response=$response->fetch();
                        }else{
                            $return['error'] = translate('unknownType');
                        }
                        if(isset($response) && empty($response)){
                            $return['used'] = false;
                        } elseif(isset($response) && !empty($response)){
                            $return['used'] = true;
                        }
                        echo json_encode($return);
                    } else {
                        $return['error'] = translate('thisIsNotJSON');
                        echo json_encode($return);
                    }
                } else {
                    $return['error'] = translate('noNameGiven');
                    echo json_encode($return);
                }

            } elseif(isset($_GET["createLoadingScreen"])){
                if(isset($_POST)&&!empty($_POST)){
                    $query = $bdd->prepare('INSERT INTO `vbcmsLoadingScreens_list` (`identifier`, `visibility`, `sequenceId`, `showName`) VALUES (?, 1, NULL, ?)');
                    $query->execute([$_POST['identifier'], $_POST['showName']]);
                } else {
                    echo translate('noPostData');
                }
            } elseif(isset($_GET)&&!empty($_GET)){
                $return['error'] = "Commande \"".array_key_first($_GET)."(".$_GET[array_key_first($_GET)].")\" non reconnue.";
                echo json_encode($return);
            } else {
                $return['error'] = translate('noCommandSpecified');
                echo json_encode($return);
            }
        break;
    }
}