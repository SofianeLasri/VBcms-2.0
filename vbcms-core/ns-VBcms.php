<?php
namespace VBcms{
    use \PDO;
    // Ce namespace est réservé aux modules
    // On ne pourra pas empêcher l'utilisation de fonction natives, mais on peu essayer de sécuriser le plus possible
    
    class module {
        // Cette classe se chargera de charger les modules
        private $name, $path, $adminAccess, $clientAccess, $vbcmsVerId, $workshopId;
        
        private $bdd, $mbdd;

        private $permissions = array();
    
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
            }
            
            //$this->mbdd = new \moduleDatabaseConnect($name);
            
            // UPDATE 01/07/2021 : N'est plus utile après reflexion
            // Maintenant on va vérifier que l'extension dispose bien des permissions demandées
            /*
            $response = $bdd->prepare("SELECT * FROM `vbcms-extensionsPermissions` WHERE extensionName = ?");
            $response->execute([$name]);
            $permissions = $response->fetch(\PDO::FETCH_ASSOC);
            $this->permissions = json_decode($permissions['otherPerms'],true);
            if(empty($this->permissions)){
                // Ici, l'extension n'a aucune permission d'accordée
                throw new Exception('ERREUR: Vous ne disposez d\'aucune autorisation.');
            }
            */
            
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
            include $GLOBALS['vbcmsRootPath'].'/vbcms-content/extensions/'.$this->path."/init.php"; // Le module appelé va se charger du reste
            enable($name, $path);
            $query = $bdd->prepare("INSERT INTO `vbcms-activatedExtensions` (`name`, `type`, `path`, `adminAccess`, `clientAccess`, `vbcmsVerId`, `workshopId`) VALUES (?,?,?,?,?,?,?)");
            $query->execute([$name, "module", $path, $adminAccess, $clientAccess, $vbcmsVerId, $this->workshopId]);
        }

        function disableModule($deleteData){
            $bdd=$this->bdd;
            $query = $bdd->prepare("DELETE FROM `vbcms-activatedExtensions` WHERE name=?");
            $query->execute([$this->name]);
        }
    
        function call(array $parameters, $type){
            //$mbdd=$this->mbdd;
            $bdd=$this->bdd;
            include $GLOBALS['vbcmsRootPath'].'/vbcms-content/extensions/'.$this->path."/pageHandler.php"; // Le module appelé va se charger du reste
            
        }

        function getSettingsPage($parameters){
            $bdd=$this->bdd;
            include $GLOBALS['vbcmsRootPath'].'/vbcms-content/extensions/'.$this->path."/init.php";
            getSettingsHTML($parameters);
        }
    }
}