<?php

class moduleDatabaseConnect {
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

        unset($selectedColumns);
        $selectedColumns[0] = $selectedColumn;
        $this->query = "UPDATE ".$selectedColumn;
    }

    function delete(){
        $this->query = "DELETE";
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

    // Partie éxecution et résolution de résultat

    function execute(){
        $this->queryResult = $this->bdd->query($this->query);
        //echo $this->query;
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