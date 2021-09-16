<?php
class module {
    // Cette classe se chargera de charger les modules
    public $name, $path, $adminAccess, $clientAccess, $vbcmsVerId, $workshopId;

    public $extensionFullPath;
    
    private $bdd;

    public $permissions = array();

    public function __construct($name){
        $this->name = $name;

        // On va initialiser le driver sql
        global $bdd;
        $this->bdd = $bdd;
        $moduleInfos = $bdd->prepare("SELECT * FROM `vbcms-activatedExtensions` WHERE name=?");
        $moduleInfos->execute([$name]);
        $moduleInfos = $moduleInfos->fetch(PDO::FETCH_ASSOC);
        if(!empty($moduleInfos)){
            $this->path = $moduleInfos['path'];
            $this->adminAccess = $moduleInfos['adminAccess'];
            $this->clientAccess = $moduleInfos['clientAccess'];
            $this->vbcmsVerId = $moduleInfos['vbcmsVerId'];
            $this->workshopId = $moduleInfos['workshopId'];

            $this->extensionFullPath = $GLOBALS['vbcmsRootPath'].'/vbcms-content/extensions/'.$this->path;
        }        
    }
    function initModule($name, $path, $adminAccess, $clientAccess, $vbcmsVerId, $workshopId){
        $this->name = $name;
        $this->path = $path;
        $this->adminAccess = $adminAccess;
        $this->clientAccess = $clientAccess;
        $this->vbcmsVerId = $vbcmsVerId;
        if(empty($workshopId))$this->workshopId = NULL;
        else $this->workshopId = $workshopId;

        $bdd=$this->bdd;
        $initCall[0] = "enable";
        include $GLOBALS['vbcmsRootPath'].'/vbcms-content/extensions/'.$this->path."/init.php"; // Le module appelé va se charger du reste
        $query = $bdd->prepare("INSERT INTO `vbcms-activatedExtensions` (`name`, `type`, `path`, `adminAccess`, `clientAccess`, `vbcmsVerId`, `workshopId`) VALUES (?,?,?,?,?,?,?)");
        $query->execute([$name, "module", $path, $adminAccess, $clientAccess, $vbcmsVerId, $this->workshopId]);
    }

    function disableModule($deleteData){
        $bdd=$this->bdd;
        $query = $bdd->prepare("DELETE FROM `vbcms-activatedExtensions` WHERE name=?");
        $query->execute([$this->name]);

        $query = $bdd->prepare("SELECT * FROM `vbcms-adminNavbar` WHERE value1=?");
        $query->execute([$this->name]);
        $moduleNavParentsIds = $query->fetchAll(PDO::FETCH_ASSOC);
        foreach ($moduleNavParentsIds as $parentId){
            $query = $bdd->prepare("DELETE FROM `vbcms-adminNavbar` WHERE id=? OR parentId=?");
            $query->execute([$parentId['id'],$parentId['id']]);
        }

        if($deleteData){ // L'utilisateur a demandé la suppression des données, on va alors demander à l'extension de le faire
            $initCall[0] = "deleteData";
            include $GLOBALS['vbcmsRootPath'].'/vbcms-content/extensions/'.$this->path."/init.php";
        }
    }

    function call(array $parameters, $type){
        $moduleName=$this->name; $adminAccess=$this->adminAccess; $clientAccess=$this->clientAccess;
        $bdd=$this->bdd;
        $extensionFullPath = $this->extensionFullPath;
        global $translation;

        // Ici on ne peut pas récupérer $http et $urlPath, on va réécrire le code ici
        if(isset($_SERVER['HTTPS'])) $http = "https"; else $http = "http";
        include $extensionFullPath."/pageHandler.php"; // Le module appelé va se charger du reste
        
    }

    function getSettingsPage($parameters){
        $bdd=$this->bdd;
        $initCall[0] = "getSettingsHTML";
        $initCall[1] = $parameters;
        include $GLOBALS['vbcmsRootPath'].'/vbcms-content/extensions/'.$this->path."/init.php";
        
    }

    function getTranslationFile($langCode){
        if(file_exists($this->extensionFullPath."/includes/translations/".strtoupper($langCode).".php")){
            return $this->extensionFullPath."/includes/translations/".strtoupper($langCode).".php";
        }elseif(file_exists($this->extensionFullPath."/includes/translations/EN.php")){
            return $this->extensionFullPath."/includes/translations/EN.php";
        }
    }

    // Cette fonction est appelée par les modules afin de créer des pages selon 3 modes (pas vraiment besoin de l'appeler pour le 3ème)
    function extensionCreatePage($panelMode, $creationMode, $pageToInclude, $title, $description, $depedencies){
        // Le mode 0 correspond à l'inclusion d'une page qui retourne du code HTML
        // Le mode 1 correspond à l'inclusion d'une page qui ne fait que passer des paramètres
        // Le mode 2 correspond à l'inclusion d'une page qui n'utilise pas la maquette du thème, qui renvoie sa propre page
        global $bdd;
        
        // Ici on ne peut pas récupérer $http et $urlPath, on va réécrire le code ici
        if(isset($_SERVER['HTTPS'])) $http = "https"; else $http = "http";

        $url = parse_url("$http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");	
        $urlPath = explode("/", $url["path"]);

        
        if($creationMode == 0){
            if($panelMode == "admin"){
                $vbcmsRequest = true;
                require $GLOBALS['vbcmsRootPath']."/vbcms-admin/includes/emptyPage.php";
            }
        } elseif($creationMode == 1){

        } elseif($creationMode == 2){
            require $pageToInclude;
        }
    }

    // Cette fonction permet de réucpérer la liste des permissions de l'extension
    function getPermissions(){
        $initCall[0] = "getPermissions";
        include $GLOBALS['vbcmsRootPath'].'/vbcms-content/extensions/'.$this->path."/init.php";
        if(!isset($permissions)) $permissions = array();
        return $permissions;
    }
}
