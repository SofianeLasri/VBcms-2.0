<?php
$deTest = function($index){
  $translation['test1'] = "Magnifique premier test";
  $translation['test2'] = "Magnifique premier test";

  return $translation[$index];
}
?>
<p>La phrase traduite est la suivante: <?=$deTest('test1')?></p>