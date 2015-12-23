<?php
///////////////////////////////////////////////////
// PPAGES ~ centerkey.com/ppages                 //
// GPLv3 ~ Copyright (c) individual contributors //
///////////////////////////////////////////////////

// Library
// Constants and general utilities

$version =    "v0.0.5";
$dataFolder = "../data";

function getProperty($map, $key) {
   return is_array($map) && isset($map[$key]) ? $map[$key] :
      (is_object($map) && isset($map->{$key}) ? $map->{$key} : null);
   }

function appClientData() {
   $data = array(
      "version" =>         $version,
      "user-list-empty" => empty(readAccountsDb()->users)
      );
   return json_encode($data);
   }

function readDb($dbFilename) {
   $dbStr = file_get_contents($dbFilename);
   if ($dbStr === false)
      exit("Error reading database: {$dbFilename}");
   return json_decode($dbStr);
   }

function readAccountsDb() {
   global $accountsDbFile;
   return readDb($accountsDbFile);
   }

function saveDb($dbFilename, $db) {
   if (!file_put_contents($dbFilename, json_encode($db)))
      exit("Error saving database: {$dbFilename}");
   }

function saveSettingsDb($db) {
   global $settingsDbFile;
   saveDb($settingsDbFile, $db);
   }

function saveAccountsDb($db) {
   global $accountsDbFile;
   logEvent("save-accounts-db", count($db->users), count($db->invites));
   saveDb($accountsDbFile, $db);
   }

function formatMsg($msg) {
   return is_null($msg) ? "[null]" : ($msg === true ? "[true]" : ($msg === false ? "[false]" :
      (empty($msg) ? "[empty]" : (is_object($msg) || is_array($msg) ? json_encode($msg) : $msg))));
   }

function logEvent() {  //any number of parameters to log
   global $installKey, $dataFolder;
   $delimiter = " | ";
   $logFilename =     "{$dataFolder}/log-{$installKey}.txt";
   $archiveFilename = "{$dataFolder}/log-archive-{$installKey}.txt";
   $milliseconds = substr(explode(" ", microtime())[0], 1, 4);
   $event = array(date("Y-m-d H:i:s"), $milliseconds, $delimiter, formatMsg($_SESSION["user"]));
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
   logEvent("http-json-response", $data);
   }

?>
