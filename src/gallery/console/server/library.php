<?php
///////////////////////////////////////////////////////////////
// Paradise ~ centerkey.com/paradise                         //
// GPLv3 ~ Copyright (c) individual contributors to Paradise //
///////////////////////////////////////////////////////////////

// Library
// Constants and general utilities

$version =    "[PARADISE-VERSION]";
$dataFolder = str_replace("console/server", "~data~", __DIR__);

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
   global $version;
   $data = array(
      "version" =>       $version,
      "userListEmpty" => emptyObj(readAccountsDb()->users)
      );
   return json_encode($data);
   }

function initializeFile($filename, $fileContents) {
   if (!is_file($filename) && !file_put_contents($filename, $fileContents))
      exit("Error initializing file, check permissions for: {$filename}");
   return $filename;
   }

function initializeFolder($folder, $blockDirIndex) {
   if (!is_dir($folder) && !mkdir($folder))
      exit("Error initializing folder, check permissions for: {$folder}");
   if ($blockDirIndex)
      initializeFile("{$folder}/index.html", "Nothing to see.");
   return $folder;
   }

function readDb($dbFilename) {
   $dbStr = file_get_contents($dbFilename);
   if ($dbStr === false)
      exit("Error reading database: {$dbFilename}");
   return json_decode($dbStr);
   }

function saveDb($dbFilename, $db) {
   if (!$_SESSION["read-only-user"] && !file_put_contents($dbFilename, json_encode($db)))
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

function toUriCode($caption) {
   return $code = preg_replace("/\s+/", "-",
       trim(preg_replace("/[^a-z]/", " ", strtolower($caption))));
   }

function displayTrue($imageDb) {
   return $imageDb->display;
   }

function convert($imageDb) {
   return array(
      "id" =>          $imageDb->id,
      "code" =>        toUriCode($imageDb->caption),
      "caption" =>     $imageDb->caption,
      "description" => $imageDb->description,
      "badge" =>       $imageDb->badge
      );
   }
function generateGalleryDb() {
   return saveGalleryDb(array_map("convert", array_values(
      array_filter(readPortfolioDb(), "displayTrue"))));
   }

function extractSort($imageDb) {
   return $imageDb->sort;
   }

function calcNewPortfolioSort($currentSort, $up) {
   $sorts = array_map("extractSort", readPortfolioDb());
   array_unshift($sorts, 0);                //in case move to the top
   array_push($sorts, end($sorts) + 10000);  //in case move to the bottom
   $currentLoc = array_search($currentSort, $sorts);
   $nearLoc = min(max($currentLoc + ($up ? -1 : 1), 1), count($sorts) - 2);
   $farLoc =  min(max($currentLoc + ($up ? -2 : 2), 0), count($sorts) - 1);
   logEvent("move-image", $currentLoc - 1, $currentSort);
   return floor(($sorts[$nearLoc] + $sorts[$farLoc]) / 2);
   }

function validEmailFormat($email) {
   $basicEmailPattern = "/^.+@.+[.].+$/";
   return preg_match($basicEmailPattern, $email) === 1;
   }

function formatMsg($msg) {
   return is_null($msg) ? "[null]" : ($msg === true ? "[true]" : ($msg === false ? "[false]" :
      ($msg === "" ? "[empty]" : (is_object($msg) || is_array($msg) ? json_encode($msg) : $msg))));
   }

function logEvent() {  //any number of parameters to log
   global $secureFolder;
   $delimiter = " | ";
   $logFilename =     "{$secureFolder}/events.log";
   $archiveFilename = "{$secureFolder}/events-archive.log";
   $milliseconds = substr(microtime(), 2, 3);
   $event = array(date("Y-m-d H:i:s."), $milliseconds, $delimiter, formatMsg($_SESSION["user"]));
   foreach (func_get_args() as $msg) {
      $event[] = $delimiter;
      $event[] = formatMsg($msg);
      }
   $event[] = PHP_EOL;
   file_put_contents($logFilename, $event, FILE_APPEND);
   if (filesize($logFilename) > 500000)  //approximate file size limit: 500 KB
      rename($logFilename, $archiveFilename);
   }

function httpJsonResponse($data) {
   header("Cache-Control: no-cache");
   header("Content-Type:  application/json");
   echo json_encode($data);
   logEvent("http-json-response", $data);
   }

function isReadOnlyExampleEmailAddress($email) {
   return preg_match("/@example[.]com$/", $email) === 1;
   }

function finishSendEmail($sendTo, $subjectLine, $messageLines) {
   $sendFrom = $_SESSION["user"];
   $subjectLine = "Paradise PHP Photo Gallery - {$subjectLine}";
   $messageLines[] = "";
   $messageLines[] = "- Paradise";
   $messageLines[] = "";
   if (isReadOnlyExampleEmailAddress($sendTo))
      $sendTo = $sendFrom;
   return $_SESSION["read-only-user"] ||
      mail($sendTo, $subjectLine, implode(PHP_EOL, $messageLines), "From: $sendFrom");
   }

function sendEmail($sendTo, $subjectLine, $messageLines) {
   $success = finishSendEmail($sendTo, $subjectLine, $messageLines);
   logEvent("send-email", $success, $sendTo, $subjectLine);
   $confirmationSubject = "Email confirmation";
   $confirmationLines = array(
      "This is an automated message from the Paradise PHP Photo Gallery system.",
      "",
      "An email message was just sent on your behalf as follows:",
      "\tSubject: Paradise PHP Photo Gallery - {$subjectLine}",
      "\tTo: {$sendTo}",
      "",
      "This is an informational message only -- no action is required on your part.",
      );
   if ($success)
      finishSendEmail($_SESSION["user"], $confirmationSubject, $confirmationLines);
   return $success;
   }

?>
