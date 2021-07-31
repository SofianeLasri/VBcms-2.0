<?php

class moduleDatabaseConnect {
    // UPDATE 01/07/2021 : N'est plus utile après reflexion
    
    // Le but de cette classe est de créer un driver sql sécurisé, ne permettant pas de causer des dégats à la base de donnée du cms
    // Le module devra s'identifier en initialisant sa connexion, et le driver se chargera de vérifier qu'il a bien l'autorisation d'utiliser la bdd
    
    private $bdd; // On a besoin du vrai driver sql

    private $moduleName;
    private $permissions = array();
    private $tables = array();

    // Variables propres à la requête
    private $query; // Query sera remis à 0 lors d'un select
    private $selectedColumns = array();
    private $selectedTables = array();
    private $condition;

    // Variables pour la préparation
    private $prepareType;
    private $preparePositions = array();
    private $isPreparedQuery;

    // Variables pour l'éxecution de la requête
    private $queryResult;

    public function __construct($moduleName) {
        global $bdd;
        $this->bdd = $bdd;
        $this->moduleName = $moduleName;

        // Maintenant on va vérifier que l'extension dispose bien des permissions demandées
        $response = $bdd->prepare("SELECT * FROM `vbcms-extensionsPermissions` WHERE extensionName = ?");
        $response->execute([$moduleName]);
        $moduleStoredPermissions = $response->fetch(PDO::FETCH_ASSOC);
        
        $moduleStoredPermissions["databasePerms"] = json_decode($moduleStoredPermissions["databasePerms"], true);
        if(!empty($moduleStoredPermissions["databasePerms"])){
            
            // On va renseigner les permissions de l'extension, ainsi que ses tables
            foreach ($moduleStoredPermissions["databasePerms"]["permissions"] as $moduleStoredPermission){
                array_push($this->permissions, $moduleStoredPermission);
            }
            foreach ($moduleStoredPermissions["databasePerms"]["tables"] as $moduleStoredPermissionTable){
                array_push($this->tables, $moduleStoredPermissionTable);
            }
        } else {
            // Ici, l'extension n'a aucune permission d'accordée
            throw new Exception('ERREUR: Vous ne disposez d\'aucune autorisation sur la base de donnée.');
        }
    }

    // À partir d'ici, ce ne seront que des fonctions de type requête

    // SELECT/UPDATE/DELETE doivent être éxecuté en premier
    function select($selectedColumns){
        // On vide $query
        $this->query = null; $queryResult=null;
        $this->isPreparedQuery = false;
        
        if(!is_array($selectedColumns)) {
            $temp = $selectedColumns;
            $selectedColumns = null;
            $selectedColumns[0] = $temp;
            $temp = null;
        }
        $this->selectedColumns = $selectedColumns;
        $this->query = "SELECT ";
        for($i = 0; $i < count($selectedColumns); $i++){
            if (!empty($selectedColumns[$i+1])) {
                $this->query = $this->query.$selectedColumns[$i].",";
            } else {
                $this->query = $this->query.$selectedColumns[$i];
            }
        }
    }

    function update($selectedColumn){
        $this->query = null; $queryResult=null;
        $this->isPreparedQuery = false;

        unset($selectedColumns);
        $selectedColumns[0] = $selectedColumn;
        $this->query = "UPDATE ".$selectedColumn;
    }

    function delete(){
        $this->query = "DELETE";
        $this->isPreparedQuery = false;
    }

    function from($selectedTables){
        if(!is_array($selectedTables)) {
            $temp = $selectedTables;
            $selectedTables = null;
            $selectedTables[0] = $temp;
            $temp = null;
        }

        $this->selectedTables = $selectedTables;
        // Ici on voit bien l'intérêt de lancer SELECT en premier car on ne fait que rajouter les instructions
        $this->query = $this->query." FROM ";
        for($i = 0; $i < count($selectedTables); $i++){
            if(!in_array($selectedTables[$i], $this->tables))
                throw new Exception('ERREUR: Vous n\'avez pas la permission d\'agir sur la table'.$selectedTables[$i].'.');
            if (!empty($selectedTables[$i+1])) {
                $this->query = $this->query.$selectedTables[$i].",";
            } else {
                $this->query = $this->query.$selectedTables[$i];
            }
        }
    }

    function where($columnsToCompare, $operators, $columnsToCompareTo){
        if(!is_array($columnsToCompare)) {
            $temp = $columnsToCompare;
            $columnsToCompare = null;
            $columnsToCompare[0] = $temp;
            $temp = null;
        }
        if(!is_array($operators)) {
            $temp = $operators;
            $operators = null;
            $operators[0] = $temp;
            $temp = null;
        }
        if(!is_array($columnsToCompareTo)) {
            $temp = $columnsToCompareTo;
            $columnsToCompareTo = null;
            $columnsToCompareTo[0] = $temp;
            $temp = null;
        }
        // On utilise des listes pour éviter des détournements tels que des INNER JOIN avec des tables qu'il ne faudrait pas
        $condition = null;
        // On fait des vérifications pour être sûr qu'il n'y ai pas un problème de nombre
        if(count($columnsToCompare)<count($operators))
            throw new Exception('ERREUR: Le nombre d\'objets à comparer est inférieur au nombre d\'opérateurs.');
        if(count($columnsToCompareTo)<count($operators))
            throw new Exception('ERREUR: Le nombre de résultats à comparer est inférieur au nombre d\'opérateurs.');
        
        for($i = 0; $i < count($columnsToCompare); $i++){
            $AndOr = "AND"; // Est utile si on a plusieurs where, permet de faire un AND par défaut

            // Ici on vérifie que l'extension n'a pas précisé un AND ou un OR avec les symboles && et ||
            if(startsWith($columnsToCompare[$i], "&&")){
                $AndOr = "AND";
                substr($columnsToCompare[$i], 2); // On supprime le flag
            }elseif(startsWith($columnsToCompare[$i], "||")){
                $AndOr = "OR";
                substr($columnsToCompare[$i], 2); // On supprime le flag
            }

            // Maitenant on peut insérer la colonne/objet a comparer
            $condition += $columnsToCompare[$i];
            if(!empty($operators[$i])){
                // Ici on va check si l'opérateur = IS NULL ou IS NOT NULL car dans ce cas, on a pas besoin d'un objet à comparer
                if(in_array($operators[$i], ["IS NULL", "IS NOT NULL"])){
                    $condition += " ".$operators[$i];
                } else {
                    $condition += $operators[$i];
                    $condition += $columnsToCompareTo[$i];
                }                
            }

            // On vérifie quand même qu'il y a bien un prochain where
            if (!empty($columnsToCompare[$i+1])) {
                $this->query = $this->query." ".$AndOr." "; // Et on applique le AND ou le OR
            }
        }
        $this->query = $this->query." WHERE ".$condition;
    }

    function set($columnsToSet, $valuesToSet){
        if(!is_array($columnsToSet)) {
            $temp = $columnsToSet;
            $columnsToSet = null;
            $columnsToSet[0] = $temp;
            $temp = null;
        }
        if(!is_array($valuesToSet)) {
            $temp = $valuesToSet;
            $valuesToSet = null;
            $valuesToSet[0] = $temp;
            $temp = null;
        }

        if(count($columnsToSet)!=count($valuesToSet))
            throw new Exception('ERREUR: Le nombre de colonnes à modifier n\'est pas égal au nombre de valeurs spécifiées.');
        $this->query = $this->query." SET ";

        for($i = 0; $i < count($columnsToSet); $i++){
            if (!empty($columnsToSet[$i+1])) {
                $this->query = $this->query.$columnsToSet[$i]."=".$valuesToSet[$i].",";
            } else {
                $this->query = $this->query.$columnsToSet[$i]."=".$valuesToSet[$i];
            }
        }
    }

    function innerJoin(array $tablesToJoin, array $keys){
        if(count($tablesToJoin)<2 || count($keys)<1 || count($tablesToJoin) > 2 || count($keys) > 2){
            throw new Exception('ERREUR: Veuillez vérifier le nombre de tables/clés.');
        }

        foreach($tablesToJoin as $tableToJoin){
            if(!in_array($tableToJoin, $tables))
                throw new Exception('ERREUR: Vous n\'avez pas la permission d\'agir sur la table '.$tableToJoin.'.');
        }
        if(empty($keys[1])) $keys[1]=$keys[0];
        $this->query = $this->query." INNER JOIN ".$tablesToJoin[1]." ON ".$tablesToJoin[0].".".$keys[0]." = ".$tablesToJoin[1].".".$keys[1];
    }

    function leftJoin(array $tablesToJoin, array $keys){
        if(count($tablesToJoin)<2 || count($keys)<1 || count($tablesToJoin) > 2 || count($keys) > 2){
            throw new Exception('ERREUR: Veuillez vérifier le nombre de tables/clés.');
        }

        foreach($tablesToJoin as $tableToJoin){
            if(!in_array($tableToJoin, $tables))
                throw new Exception('ERREUR: Vous n\'avez pas la permission d\'agir sur la table '.$tableToJoin.'.');
        }
        if(empty($keys[1])) $keys[1]=$keys[0];
        $this->query = $this->query." LEFT JOIN ".$tablesToJoin[1]." ON ".$tablesToJoin[0].".".$keys[0]." = ".$tablesToJoin[1].".".$keys[1];
    }

    function rightJoin(array $tablesToJoin, array $keys){
        if(count($tablesToJoin)<2 || count($keys)<1 || count($tablesToJoin) > 2 || count($keys) > 2){
            throw new Exception('ERREUR: Veuillez vérifier le nombre de tables/clés.');
        }

        foreach($tablesToJoin as $tableToJoin){
            if(!in_array($tableToJoin, $tables))
                throw new Exception('ERREUR: Vous n\'avez pas la permission d\'agir sur la table '.$tableToJoin.'.');
        }
        if(empty($keys[1])) $keys[1]=$keys[0];
        $this->query = $this->query." RIGHT JOIN ".$tablesToJoin[1]." ON ".$tablesToJoin[0].".".$keys[0]." = ".$tablesToJoin[1].".".$keys[1];
    }

    function fullJoin(array $tablesToJoin, array $keys){
        if(count($tablesToJoin)<2 || count($keys)<1 || count($tablesToJoin) > 2 || count($keys) > 2){
            throw new Exception('ERREUR: Veuillez vérifier le nombre de tables/clés.');
        }

        foreach($tablesToJoin as $tableToJoin){
            if(!in_array($tableToJoin, $tables))
                throw new Exception('ERREUR: Vous n\'avez pas la permission d\'agir sur la table '.$tableToJoin.'.');
        }
        if(empty($keys[1])) $keys[1]=$keys[0];
        $this->query = $this->query." FULL JOIN ".$tablesToJoin[1]." ON ".$tablesToJoin[0].".".$keys[0]." = ".$tablesToJoin[1].".".$keys[1];
    }

    function createTable($sqlCommand){
        $this->query = null;
    }

    // Pour les requêtes avec du vrai code sql (car c'est quand même bcp mieux)
    function prepare($sql){
        unset($this->preparePositions);
        $this->isPreparedQuery = true;

        if(strpos($sql, "?") !== false){
            $this->prepareType = "markers";
            // Ici on prépapre avec des ?
            $lastPos = 0;
    
            while (($lastPos = strpos($query, "?", $lastPos))!== false) {
                $this->preparePositions[] = $lastPos;
                $lastPos = $lastPos + strlen("?");
                $this->query = $sql;
            }
        } elseif(strpos($sql, ":") !== false){
            // Ici on prépare avec les index
            $this->prepareType = "namedParameters";
            $this->query = $sql;
        }
    }

    // Partie éxecution et résolution de résultat

    function execute(){
        if(!$this->isPreparedQuery){
            $this->queryResult = $this->bdd->query($this->query);
            //echo $this->query;
        } else {
            if($this->prepareType = "makers"){

            }
        }
        
    }
    
    function fetch($pdoFetchMethod){
        return $this->queryResult->fetch($pdoFetchMethod);
    }

    function fetchColumn($pdoFetchMethod){
        return $this->queryResult->fetchColumn($pdoFetchMethod);
    }

    function fetchAll($pdoFetchMethod){
        return $this->queryResult->fetchAll($pdoFetchMethod);
    }
    
}

class module {
    // Cette classe se chargera de charger les modules
    private $name, $path, $adminAccess, $clientAccess, $vbcmsVerId, $workshopId;

    private $extensionFullPath;
    
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

            $this->extensionFullPath = $GLOBALS['vbcmsRootPath'].'/vbcms-content/extensions/'.$this->path;
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
        enable($name, $path, $adminAccess, $clientAccess);
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
            include $GLOBALS['vbcmsRootPath'].'/vbcms-content/extensions/'.$this->path."/init.php";
            deleteData();
        }
    }

    function call(array $parameters, $type){
        $moduleName=$this->name; $adminAccess=$this->adminAccess; $clientAccess=$this->clientAccess;
        $bdd=$this->bdd;
        $extensionFullPath = $this->extensionFullPath;
        global $translation;
        include $extensionFullPath."/pageHandler.php"; // Le module appelé va se charger du reste
        
    }

    function getSettingsPage($parameters){
        $bdd=$this->bdd;
        include $GLOBALS['vbcmsRootPath'].'/vbcms-content/extensions/'.$this->path."/init.php";
        getSettingsHTML($parameters);
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
}
