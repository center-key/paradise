<?php
/////////////////////////////////////////////////
// PPAGES ~ centerkey.com/ppages               //
// GPL ~ Copyright (c) individual contributors //
/////////////////////////////////////////////////

$settingsDbFile = $dataFolder . "settings-db.json";
$galleryDbFile =  $dataFolder . "gallery-db.json";

function setupInstallKey($folder) {
   $fileSearch = glob($folder . "key-*.txt");
   if (count($fileSearch) === 0) {
      $fileSearch[] = $folder . "key-" . mt_rand() . mt_rand() . mt_rand() . ".txt";
      touch($fileSearch[0]);
      }
   preg_match("/key-(.*)[.]txt/", $fileSearch[0], $matches);
   return $matches[1];
   }

function setupDb($dbFileName, $defaultJson) {
   if (!is_file($dbFileName))
      file_put_contents($dbFileName, json_encode($defaultJson));
   }

$installKey = setupInstallKey($dataFolder);
setupDb($settingsDbFile, "{}");
setupDb($galleryDbFile,  "[]");
?>
