<?php
require_once dirname(__FILE__) . '/vbcms-core/vendors/PHPSQLParser/vendor/autoload.php';

$test["database"]["permissions"] = ["SELECT", "INSERT", "UPDATE"];
$test["database"]["tables"] = ["vbcmsWebSys_blogCategories", "vbcmsWebSys_blogCategories", "vbcmsWebSys_blogPosts"];


// [path, droitLecture, droitEcriture]
$objet[1] = ["/modules/moduledeouf", true, true];
$objet[2] = ["/uploads/moduledeouf", true, true];
$objet[2] = ["/uploads", true, false];
$test["fileManager"] = $objet;

echo json_encode($test);
echo "<br><br>";

$string = ('CREATE TABLE IF NOT EXISTS `vbcms-blogPosts` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `categoryId` int(11) DEFAULT NULL,
    `authorId` bigint(255) NOT NULL,
    `slug` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `title` text COLLATE utf8_unicode_ci NOT NULL,
    `subTitle` text COLLATE utf8_unicode_ci NOT NULL,
    `content` text COLLATE utf8_unicode_ci NOT NULL,
    `headerImage` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
    `writtenOn` datetime NOT NULL,
    `modifiedOn` datetime NOT NULL,
    `description` text COLLATE utf8_unicode_ci NOT NULL,
    `views` int(11) NOT NULL,
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT;');

echo PhpMyAdmin\SqlParser\Utils\Formatter::format($string, ['type' => 'html'])."<br><br>";
$parser = new PhpMyAdmin\SqlParser\Parser($string);

var_dump($parser->statements[0]);
echo "<br><br>";
//echo json_encode($parser);
echo "<br><br>";

echo $parser->statements[0]->name->table;



echo "<br><br>";

$string = ("INSERT INTO `moduledeouf` (`id`, `exemple`) VALUES (NULL, 'test')");

echo PhpMyAdmin\SqlParser\Utils\Formatter::format($string, ['type' => 'html'])."<br><br>";
$parser = new PhpMyAdmin\SqlParser\Parser($string);

var_dump($parser->statements[0]);
echo "<br><br>";
//echo json_encode($parser);
echo "<br><br>";

echo $parser->statements[0]->into->dest->table;
echo "<br><br>";

$query = "SELECT * FROM table WHERE id = ? AND type = ?";
$lastPos = 0;
$positions = array();

while (($lastPos = strpos($query, "?", $lastPos))!== false) {
  $positions[] = $lastPos;
  $lastPos = $lastPos + strlen("?");
}
foreach ($positions as $value) {
  echo $value ."<br />";
}