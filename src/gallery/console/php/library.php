<?php
/////////////////////////////////////////////////
// PPAGES ~ centerkey.com/ppages               //
// GPL ~ Copyright (c) individual contributors //
/////////////////////////////////////////////////

// Library
// Constants and general utilities

$version =        "v0.0.5";
$dataFolder =     "../data";
$settingsDbFile = "{$dataFolder}/settings-db.json";
$galleryDbFile =  "{$dataFolder}/gallery-db.json";

function readDb($dbFilename) {
   logEvent("read-db", $dbFilename);
   $dbStr = file_get_contents($dbFilename);
   if ($dbStr === false)
      exit("Error reading database: {$dbFilename}");
   return json_decode($dbStr);
   }

function saveDb($dbFilename, $db) {
   logEvent("save-db", $dbFilename);
   if (!file_put_contents($dbFilename, json_encode($db)))
      exit("Error saving database: {$dbFilename}");
   }

function formatMsg($msg) {
   return is_null($msg) ? "[null]" : (empty($msg) ? "[empty]" :
      ($msg === true ? "[true]" : ($msg === false ? "[false]" :
      (is_object($msg) ? get_class($msg) . ":" . count($msg) : $msg))));
   }

function logEvent() {  //any number of parameters to log
   global $installKey, $dataFolder;
   $delimiter = " | ";
   $logFilename =     "{$dataFolder}/log-{$installKey}.txt";
   $archiveFilename = "{$dataFolder}/log-archive-{$installKey}.txt";
   $milliseconds = substr(explode(" ", microtime())[0], 1, 4);
   $event = array(date("Y-m-d H:i:s"), $milliseconds, $delimiter, formatMsg($_SESSION["username"]));
   foreach (func_get_args() as $msg) {
      $event[] = $delimiter;
      $event[] = formatMsg($msg);
      }
   $event[] = PHP_EOL;
   file_put_contents($logFilename, $event, FILE_APPEND);
   if (filesize($logFilename) > 100000)  //approximate file size limit: 100 KB
      rename($logFilename, $archiveFilename);
   }

function httpJsonResponse($data) {
   header("Cache-Control: no-cache");
   header("Content-Type:  application/json");
   echo json_encode($data);
   }

?>
