<?php
namespace VBcms{
    // Ce namespace est réservé aux modules
    // On ne pourra pas empêcher l'utilisation de fonction natives, mais on peu essayer de sécuriser le plus possible
    //include 'ns-VBcms/functions.php';
    //include 'ns-VBcms/classes.php';


    function print_r($object){
        echo "NS-VBcms ".\print_r($object, true);
    }
    
    class module {
        // Cette classe se chargera de charger les modules
        private $name, $path, $adminAccess, $clientAccess, $vbcmsVerId;
    
        private $permissions = array();
    
        public function __construct($name, $path, $adminAccess, $clientAccess, $vbcmsVerId){
            $this->name = $name;
            $this->path = $path;
            $this->adminAccess = $adminAccess;
            $this->clientAccess = $clientAccess;
            $this->vbcmsVerId = $vbcmsVerId;
    
            // On va initialiser le driver sql
            $mbdd = new \moduleDatabaseConnect($name);
        
            // Maintenant on va vérifier que l'extension dispose bien des permissions demandées
            global $bdd;
            $response = $bdd->prepare("SELECT * FROM `vbcms-extensionsPermissions` WHERE extensionName = ?");
            $response->execute([$name]);
            $permissions = $response->fetch(\PDO::FETCH_ASSOC);
            $this->permissions = json_decode($permissions['otherPerms'],true);
            if(empty($this->permissions)){
                // Ici, l'extension n'a aucune permission d'accordée
                throw new \Exception('ERREUR: Vous ne disposez d\'aucune autorisation.');
            }
        }
    
        function call(array $parameters, $type){
            include $GLOBALS['vbcmsRootPath'].'/vbcms-content/modules/'.$this->path."/moduleLoadPage.php"; // Le module appelé va se charger du reste
            
        }
    }
}