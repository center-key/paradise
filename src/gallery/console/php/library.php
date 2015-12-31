<?php
///////////////////////////////////////////////////
// PPAGES ~ centerkey.com/ppages                 //
// GPLv3 ~ Copyright (c) individual contributors //
///////////////////////////////////////////////////

// Library
// Constants and general utilities

$version =    "v0.1.1";
$dataFolder = str_replace("console/php", "data", __DIR__);

date_default_timezone_set("UTC");

function getGalleryUrl() {
   return ($_SERVER["HTTPS"] === "on" ? "https://" : "http://") . $_SERVER["HTTP_HOST"] .
      str_replace("/console/rest/index.php", "", $_SERVER["SCRIPT_NAME"]);
   }

function getProperty($map, $key) {
   return is_array($map) && isset($map[$key]) ? $map[$key] :
      (is_object($map) && isset($map->{$key}) ? $map->{$key} : null);
   }

function emptyObj($object) {
   return count(get_object_vars($object)) === 0;
   }

function appClientData() {
   $data = array(
      "version" =>         $version,
      "user-list-empty" => emptyObj(readAccountsDb()->users)
      );
   return json_encode($data);
   }

function readDb($dbFilename) {
   $dbStr = file_get_contents($dbFilename);
   if ($dbStr === false)
      exit("Error reading database: {$dbFilename}");
   return json_decode($dbStr);
   }

function saveDb($dbFilename, $db) {
   if (!file_put_contents($dbFilename, json_encode($db)))
      exit("Error saving database: {$dbFilename}");
   return $db;
   }

function readGalleryDb() {
   global $galleryDbFile;
   return readDb($galleryDbFile);
   }

function saveGalleryDb($db) {
   global $galleryDbFile;
   return saveDb($galleryDbFile, $db);
   }

function readPortfolioDb() {
   global $portfolioFolder;
   $portfolioDb = array_map("readDb", glob("{$portfolioFolder}/*-db.json"));
   usort($portfolioDb, function($a, $b) { return $a->sort < $b->sort ? -1 : 1; });
   return $portfolioDb;
   }

function readPortfolioImageDb($id) {
   global $portfolioFolder;
   $dbFilename = "{$portfolioFolder}/{$id}-db.json";
   logEvent("readPortfolioImageDb", $dbFilename);
   return is_file($dbFilename) ? readDb($dbFilename, $db) : false;
   }

function savePortfolioImageDb($db) {
   global $portfolioFolder;
   logEvent("{$portfolioFolder}/{$db->id}-db.json", $db);
   return saveDb("{$portfolioFolder}/{$db->id}-db.json", $db);
   }

function readSettingsDb() {
   global $settingsDbFile;
   return readDb($settingsDbFile);
   }

function saveSettingsDb($db) {
   global $settingsDbFile;
   return saveDb($settingsDbFile, $db);
   }

function readAccountsDb() {
   global $accountsDbFile;
   return readDb($accountsDbFile);
   }

function saveAccountsDb($db) {
   global $accountsDbFile;
   logEvent("save-accounts-db", count($db->users), count($db->invites));
   return saveDb($accountsDbFile, $db);
   }

function displayTrue($imageDb) { return $imageDb->display; }
function convert($imageDb) {
   return array(
      "id" =>          $imageDb->id,
      "caption" =>     $imageDb->caption,
      "description" => $imageDb->description,
      "badge" =>       $imageDb->badge
      );
   }
function generateGalleryDb() {
   return saveGalleryDb(array_map("convert", array_values(
      array_filter(readPortfolioDb(), "displayTrue"))));
   }

function validEmailFormat($email) {
   $basicEmailPattern = "/^.+@.+[.].+$/";
   return preg_match($basicEmailPattern, $email) === 1;
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
   $milliseconds = substr(microtime(), 2, 3);
   $event = array(date("Y-m-d H:i:s."), $milliseconds, $delimiter, formatMsg($_SESSION["user"]));
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

function sendEmail($subjectLine, $sendTo, $messageLines) {
   $sendFrom = $_SESSION["user"];
   $success = mail($sendTo, $subjectLine, implode(PHP_EOL, $messageLines), "From: $sendFrom");
   logEvent("send-email", $success, $sendTo, $subjectLine);
   $confirmationSubject = "PPAGES email confirmation notice";
   $confirmationLines = array(
      "This is an automated message from the PPAGES system.",
      "",
      "An email message was just sent on your behalf as follows:",
      "\tSubject: {$subjectLine}",
      "\tTo: {$sendTo}",
      "",
      "This is an informational message only -- no action is required on your part.",
      "",
      "- PPAGES"
      );
   if ($success)
      mail($sendFrom, $confirmationSubject, implode(PHP_EOL, $confirmationLines), "From: $sendFrom");
   return $success;
   }

?>
