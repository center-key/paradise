<?php
/////////////////////////////////////////////////
// PPAGES ~ centerkey.com/ppages               //
// GPL ~ Copyright (c) individual contributors //
/////////////////////////////////////////////////

$version = "v0.0.5";
$settingsDbFile = $dataFolder . "settings-db.json";
$galleryDbFile =  $dataFolder . "gallery-db.json";

function setupInstallKey($folder) {
   $fileSearch = glob($folder . "key-*.txt");
   if (count($fileSearch) === 0) {
      logEvent("generate-install-key", $folder);
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

function formatMsg($msg) {
   return $msg === null ? "[null]" : ($msg === true ? "[true]" : ($msg === false ? "[false]" :
      (is_object($msg) ? get_class($msg) . ":" . count($msg) : $msg)));
   }

function logEvent() {  //any number of parameters to log
   global $installKey, $dataFolder;
   $delimiter = " | ";
   $logFilename =     $dataFolder . "log-" . $installKey . ".txt";
   $archiveFilename = $dataFolder . "log-archive-" . $installKey . ".txt";
   $event = [date("Y-m-d H:i:s"), substr(explode(" ", microtime())[0], 1, 4)];
   foreach (func_get_args() as $msg) {
      $event[] = $delimiter;
      $event[] = formatMsg($msg);
      }
   $event[] = PHP_EOL;
   file_put_contents($logFilename, $event, FILE_APPEND);
   if (filesize($logFilename) > 100000)  //approximate file size limit: 100 KB
      rename($logFilename, $archiveFilename);
   }

date_default_timezone_set("UTC");
$installKey = setupInstallKey($dataFolder);
setupDb($settingsDbFile, "{}");
setupDb($galleryDbFile,  "[]");
?>
