<?php
// Page d'initialisation
adminNavbarAddCategory("vbcms-loadingscreen-system", "loadingscreen");
adminNavbarAddItem("vbcms-loadingscreen-system", "fa-plus-circle", "loadingscreen_create", "/vbcms-admin/loadingscreen/create");
adminNavbarAddItem("vbcms-loadingscreen-system", "fa-list", "loadingscreens_list", "/vbcms-admin/loadingscreen/list");
adminNavbarAddItem("vbcms-loadingscreen-system", "fa-brush", "loadingscreens_themes", "/vbcms-admin/loadingscreen/themes");


//Création des tables
$bdd->query("CREATE TABLE IF NOT EXISTS `vbcms-loadingscreeens` (
`id` INT(11) NOT NULL AUTO_INCREMENT,
`themeId` INT(11) NOT NULL,
`name` INT(255) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE = InnoDB;");

$bdd->query("CREATE TABLE IF NOT EXISTS `vbcms-tempLoadingscreeens` (
`id` INT(11) NOT NULL AUTO_INCREMENT,
`themeId` INT(11) NOT NULL,
`name` INT(255) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE = InnoDB;");

$bdd->query("CREATE TABLE IF NOT EXISTS `vbcms-loadingscreensParameters` (
`loadingScreenId` INT(11) NOT NULL,
`name` VARCHAR(128) NOT NULL,
`value` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
PRIMARY KEY (`loadingScreenId`, `name`)
) ENGINE = InnoDB;");
?>