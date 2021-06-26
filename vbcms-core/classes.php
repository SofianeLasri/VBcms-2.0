<?php
class moduleDatabaseConnect {
    // Le but de cette classe est de créer un driver sql sécurisé, ne permettant pas de causer des dégats à la base de donnée du cms
    // Le module devra s'identifier en initialisant sa connexion, et le driver se chargera de vérifier qu'il a bien l'autorisation d'utiliser la bdd
    
    global $bdd; // On a besoin du vrai driver sql

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
        
        $this->moduleName = $moduleName;

        // Maintenant on va vérifier que l'extension dispose bien des permissions demandées
        $response = $bdd->prepare("SELECT * FROM `vbcms-extensionsPermissions` WHERE extensionName = ?");
        $response->execute([$moduleName]);
        $moduleStoredPermissions = $response->fetch(PDO::FETCH_ASSOC);
        if(!empty($moduleStoredPermissions)){
            // On va renseigner les permissions de l'extension, ainsi que ses tables
            foreach ($moduleStoredPermissions["permissions"] as $moduleStoredPermission){
                array_push($permissions, $moduleStoredPermission);
            }
            foreach ($moduleStoredPermissions["tables"] as $moduleStoredPermissionTable){
                array_push($tables, $moduleStoredPermissionTable);
            }
        } else {
            // Ici, l'extension n'a aucune permission d'accordée
        }
    }

    // À partir d'ici, ce ne seront que des fonctions de type requête

    // SELECT/UPDATE/DELETE doivent être éxecuté en premier
    function select(array $selectedColumns){
        // On vide $query
        $query = null; $queryResult=null;

        $this->selectedColumns = $selectedColumns;
        $query = "SELECT ";
        for(int $i = 0; $i < $selectedColumns.count(); $i++){
            if (!empty($selectedColumns[$i+1])) {
                $query += $selectedColumns[$i].",";
            } else {
                $query += $selectedColumns[$i];
            }
        }
    }

    function update($selectedColumn){
        $query = null; $queryResult=null;

        unset($selectedColumns);
        $selectedColumns[0] = $selectedColumn;
        $query = "UPDATE ".$selectedColumn;
    }

    function delete(){
        $query = "DELETE";
    }

    function from(array $selectedTables){
        $this->selectedTables = $selectedTables;
        // Ici on voit bien l'intérêt de lancer SELECT en premier car on ne fait que rajouter les instructions
        $query += " FROM ";
        for(int $i = 0; $i < $selectedTables.count(); $i++){
            if(!in_array($selectedTables[$i], $tables))
                throw new Exception('ERREUR: Vous n\'avez pas la permission d\'agir sur la table'.$selectedTables[$i].'.');
            if (!empty($selectedTables[$i+1])) {
                $query += $selectedTables[$i].",";
            } else {
                $query += $selectedTables[$i];
            }
        }
    }

    function where(array $columnsToCompare, array $operators, array $columnsToCompareTo){
        // On utilise des listes pour éviter des détournements tels que des INNER JOIN avec des tables qu'il ne faudrait pas
        $condition = null;
        // On fait des vérifications pour être sûr qu'il n'y ai pas un problème de nombre
        if($columnsToCompare.count()<$operators.count())
            throw new Exception('ERREUR: Le nombre d\'objets à comparer est inférieur au nombre d\'opérateurs.');
        if($columnsToCompareTo.count()<$operators.count())
            throw new Exception('ERREUR: Le nombre de résultats à comparer est inférieur au nombre d\'opérateurs.');
        
        for(int $i = 0; $i < $columnsToCompare.count(); $i++){
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
                $query += " ".$AndOr." "; // Et on applique le AND ou le OR
            }
        }
        $query += " WHERE ".$condition;
    }

    function set(array $columnsToSet, array $valuesToSet){
        if($columnsToSet.count()!=$valuesToSet.count())
            throw new Exception('ERREUR: Le nombre de colonnes à modifier n\'est pas égal au nombre de valeurs spécifiées.');
        $query += " SET ";

        for(int $i = 0; $i < $columnsToSet.count(); $i++){
            if (!empty($columnsToSet[$i+1])) {
                $query += $columnsToSet[$i]."=".$valuesToSet[$i].",";
            } else {
                $query += $columnsToSet[$i]."=".$valuesToSet[$i];
            }
        }
    }

    function innerJoin(array $tablesToJoin, array $keys){
        if($tablesToJoin.count()<2 || $keys.count()<1 || $tablesToJoin.count() > 2 || $keys.count() > 2){
            throw new Exception('ERREUR: Veuillez vérifier le nombre de tables/clés.');
        }

        foreach($tablesToJoin as $tableToJoin){
            if(!in_array($tableToJoin, $tables))
                throw new Exception('ERREUR: Vous n\'avez pas la permission d\'agir sur la table '.$tableToJoin.'.');
        }
        if(empty($keys[1])) $keys[1]=$keys[0];
        $query += " INNER JOIN ".$tablesToJoin[1]." ON ".$tablesToJoin[0].".".$keys[0]." = ".$tablesToJoin[1].".".$keys[1];
    }

    function leftJoin(array $tablesToJoin, array $keys){
        if($tablesToJoin.count()<2 || $keys.count()<1 || $tablesToJoin.count() > 2 || $keys.count() > 2){
            throw new Exception('ERREUR: Veuillez vérifier le nombre de tables/clés.');
        }

        foreach($tablesToJoin as $tableToJoin){
            if(!in_array($tableToJoin, $tables))
                throw new Exception('ERREUR: Vous n\'avez pas la permission d\'agir sur la table '.$tableToJoin.'.');
        }
        if(empty($keys[1])) $keys[1]=$keys[0];
        $query += " LEFT JOIN ".$tablesToJoin[1]." ON ".$tablesToJoin[0].".".$keys[0]." = ".$tablesToJoin[1].".".$keys[1];
    }

    function rightJoin(array $tablesToJoin, array $keys){
        if($tablesToJoin.count()<2 || $keys.count()<1 || $tablesToJoin.count() > 2 || $keys.count() > 2){
            throw new Exception('ERREUR: Veuillez vérifier le nombre de tables/clés.');
        }

        foreach($tablesToJoin as $tableToJoin){
            if(!in_array($tableToJoin, $tables))
                throw new Exception('ERREUR: Vous n\'avez pas la permission d\'agir sur la table '.$tableToJoin.'.');
        }
        if(empty($keys[1])) $keys[1]=$keys[0];
        $query += " RIGHT JOIN ".$tablesToJoin[1]." ON ".$tablesToJoin[0].".".$keys[0]." = ".$tablesToJoin[1].".".$keys[1];
    }

    function fullJoin(array $tablesToJoin, array $keys){
        if($tablesToJoin.count()<2 || $keys.count()<1 || $tablesToJoin.count() > 2 || $keys.count() > 2){
            throw new Exception('ERREUR: Veuillez vérifier le nombre de tables/clés.');
        }

        foreach($tablesToJoin as $tableToJoin){
            if(!in_array($tableToJoin, $tables))
                throw new Exception('ERREUR: Vous n\'avez pas la permission d\'agir sur la table '.$tableToJoin.'.');
        }
        if(empty($keys[1])) $keys[1]=$keys[0];
        $query += " FULL JOIN ".$tablesToJoin[1]." ON ".$tablesToJoin[0].".".$keys[0]." = ".$tablesToJoin[1].".".$keys[1];
    }

    function execute(){
        $queryResult = $bdd->query($query);
    }
    
    function fetch($pdoFetchMethod){
        return $queryResult->fetch($pdoFetchMethod);
    }

    function fetchColumn($pdoFetchMethod){
        return $queryResult->fetchColumn($pdoFetchMethod);
    }

    function fetchAll($pdoFetchMethod){
        return $queryResult->fetchAll($pdoFetchMethod);
    }
    
}
