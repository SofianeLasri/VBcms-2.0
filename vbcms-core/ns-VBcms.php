<?php
namespace VBcms{
    use \PDO;
    // Ce namespace est réservé aux modules
    // On ne pourra pas empêcher l'utilisation de fonction natives, mais on peu essayer de sécuriser le plus possible
    
    class module {
        // Cette classe se chargera de charger les modules
        private $name, $path, $adminAccess, $clientAccess, $vbcmsVerId;
        
        private $bdd, $mbdd;

        private $permissions = array();
    
        public function __construct($name, $path, $adminAccess, $clientAccess, $vbcmsVerId){
            $this->name = $name;
            $this->path = $path;
            $this->adminAccess = $adminAccess;
            $this->clientAccess = $clientAccess;
            $this->vbcmsVerId = $vbcmsVerId;
    
            // On va initialiser le driver sql
            global $bdd;
            $this->bdd = $bdd;
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
    
        function call(array $parameters, $type){
            //$mbdd=$this->mbdd;
            $bdd=$this->bdd;
            include $GLOBALS['vbcmsRootPath'].'/vbcms-content/extensions/'.$this->path."/moduleLoadPage.php"; // Le module appelé va se charger du reste
            
        }
    }
}