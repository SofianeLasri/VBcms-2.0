<?php
function enable($name, $path, $adminAccess, $clientAccess){
    global $bdd;
    adminNavbarAddCategory($name, "loadingscreens");
    adminNavbarAddItem($name, "fas fa-plus-circle", "create", "/create");
    adminNavbarAddItem($name, "fas fa-list", "list", "/browse");
    adminNavbarAddItem($name, "fas fa-brush", "themes", "/themes");

    // On va créer les tables
    if(!tableExist("vbcmsLoadingScreens_list")) $bdd->query("CREATE TABLE `vbcmsLoadingScreens_list` ( `identifier` VARCHAR(128) NOT NULL , `visibility` INT NOT NULL , `sequenceId` INT NULL DEFAULT NULL , `showName` VARCHAR(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL , PRIMARY KEY (`identifier`)) ENGINE = InnoDB;");
    if(!tableExist("vbcmsLoadingScreens_sequences")) $bdd->query("CREATE TABLE `vbcmsLoadingScreens_sequences` ( `id` INT NOT NULL AUTO_INCREMENT , `name` VARCHAR(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;");
    if(!tableExist("vbcmsLoadingScreens_sequencesData")) $bdd->query("CREATE TABLE `vbcmsLoadingScreens_sequencesData` ( `sequenceId` INT NOT NULL , `dataId` INT NOT NULL , `parentId` INT NULL DEFAULT NULL , `type` VARCHAR(128) NOT NULL , `data` JSON NOT NULL , PRIMARY KEY (`sequenceId`, `dataId`)) ENGINE = InnoDB;");
    if(!tableExist("vbcmsLoadingScreens_tempSequencesData")) $bdd->query("CREATE TABLE `vbcmsLoadingScreens_tempSequencesData` ( `sequenceId` INT NOT NULL , `dataId` INT NOT NULL , `parentId` INT NULL DEFAULT NULL , `type` VARCHAR(128) NOT NULL , `data` JSON NOT NULL , `date` DATETIME NOT NULL , PRIMARY KEY (`sequenceId`, `dataId`)) ENGINE = InnoDB;");
    if(!tableExist("vbcmsLoadingScreens_clientsData")) $bdd->query("CREATE TABLE `vbcmsLoadingScreens_clientsData` ( `identifier` VARCHAR(64) NOT NULL , `stringId` VARCHAR(32) NOT NULL , `data` JSON NOT NULL , PRIMARY KEY (`identifier`)) ENGINE = InnoDB;");
    if(!tableExist("vbcmsLoadingScreens_previewTokens")) $bdd->query("CREATE TABLE `vbcmsLoadingScreens_previewTokens` ( `stringId` VARCHAR(32) NOT NULL , `lsId` INT NOT NULL , `expire` DATETIME NOT NULL , PRIMARY KEY (`stringId`)) ENGINE = InnoDB;");
}

function deleteData(){
    global $bdd;
    $bdd->query('DROP TABLE vbcmsLoadingScreens_list');
    $bdd->query('DROP TABLE vbcmsLoadingScreens_sequences');
    $bdd->query('DROP TABLE vbcmsLoadingScreens_sequencesData');
    $bdd->query('DROP TABLE vbcmsLoadingScreens_tempSequencesData');
    $bdd->query('DROP TABLE vbcmsLoadingScreens_clientsData');
    $bdd->query('DROP TABLE vbcmsLoadingScreens_previewTokens');
}

function getSettingsHTML($params){
    echo('<h5>C\'est bien la page du créateur d\'écrans de chargement</h5>');
}