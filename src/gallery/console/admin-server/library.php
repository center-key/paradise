<?php
///////////////////////////////////////////////////////////////
// Paradise ~ centerkey.com/paradise                         //
// GPLv3 ~ Copyright (c) Individual contributors to Paradise //
///////////////////////////////////////////////////////////////

// Library
// Constants and general utilities

require __DIR__ . "/../../common.php";
require "font-options.php";

$appVersion =    "{{package.version}}";
$dbCacheStore =  null;
$galleryFolder = realpath(__DIR__ . "/../..");
$dataFolder =    "{$galleryFolder}/~data~";
$siteMapFile =   "{$galleryFolder}/sitemap.xml";
date_default_timezone_set("UTC");

function getTime() {
   // Returns the number of milliseconds elapsed since the epoch.
   return intval(microtime(true) * 1000);
   }

function daysToMsec($days) {
   // Converts days to milliseconds.
   // Example:
   //    daysToMsec(1) --> 86400000
   return $days * 24 * 60 * 60 * 1000;
   }

function toMb($bytes) {
   // Converts size in bytes to text showing megabytes.
   // Example:
   //    toMb(3577000) --> "3.58 MB"
   $mb = number_format($bytes / 1000000, 2);
   return "{$mb} MB";
   }

function toBytes($item) {
   // Example:
   //    toBytes("123456789 1234567") --> "17 B"
   $bytes = is_numeric($item) ? $item : strlen($item);
   return "{$bytes} B";
   }

function fileSysFriendly($string, $max) {
   // Example:
   //    fileSysFriendly("Die Känguru-Chroniken", 12) --> "die-kanguru"
   setlocale(LC_ALL, "en_US");
   $lower = strtolower(iconv("UTF-8", "ASCII//TRANSLIT", $string));
   $clean = trim(preg_replace("/[^a-z0-9]/", " ", $lower));
   $short = substr(preg_replace("/[ ]+/", "-", $clean), 0, $max + 1);
   $dash  = strrpos($short, "-") ?: $max;
   return strlen($short) > $max ? substr($short, 0, $dash) : $short;
   }

function emptyObj($object) {
   return count(get_object_vars($object)) === 0;
   }

function initializeFile($filename, $fileContents) {
   if (!is_file($filename))
      logEvent(__FILE__, __FUNCTION__, $filename, toBytes($fileContents));
   if (!is_file($filename) && !file_put_contents($filename, $fileContents))
      logErrorExit(__FILE__, __FUNCTION__, "Initializing file failed, check permissions", $filename);
   if (!is_writable($filename))
      exit("Error reading file, check permissions for: {$filename}");
   return $filename;
   }

function initializeFolder($folder, $blockDirIndex) {
   if (!is_dir($folder))
      logEvent(__FILE__, __FUNCTION__, $folder);
   if (!is_dir($folder) && !mkdir($folder))
      logErrorExit(__FILE__, __FUNCTION__, "Initializing folder failed, check permissions", $folder);
   if ($blockDirIndex)
      initializeFile("{$folder}/index.html", "Nothing to see.");
   if (!is_writable($folder))
      exit("Error reading folder, check permissions for: {$folder}");
   return $folder;
   }

function getDbCache() {
   global $dbCacheStore;
   if ($dbCacheStore == null)
      $dbCacheStore = (object)[];
   return $dbCacheStore;
   }

function readDb($dbFilename) {
   $dbCache = getDbCache();
   if (!isset($dbCache->{$dbFilename})) {
      $dbStr = file_get_contents($dbFilename);
      if ($dbStr === false)
         logErrorExit(__FILE__, __FUNCTION__, "Reading database failed", $dbFilename);
      $dbCache->{$dbFilename} = json_decode($dbStr);
      }
   return $dbCache->{$dbFilename};
   }

function saveDb($dbFilename, $dbObj) {
   // Example:
   //    saveDb("favorite-numbers.json", [3, 7, 21]);
   if (readOnlyMode())
      return $dbObj;
   $dbCache =  getDbCache();
   $dbJson =   json_encode($dbObj);
   $origSize = is_file($dbFilename) ? filesize($dbFilename) : 0;
   $newSize =  file_put_contents($dbFilename, $dbJson);
   if ($newSize === false)
      logErrorExit(__FILE__, __FUNCTION__, "Saving database failed", $dbFilename, toBytes($dbJson));
   logEvent(__FILE__, __FUNCTION__, basename($dbFilename), toBytes($origSize), toBytes($newSize));
   $dbCache->{$dbFilename} = $dbObj;
   return $dbCache->{$dbFilename};
   }

function readGalleryDb() {
   global $galleryDbFile;
   return readDb($galleryDbFile);
   }

function saveGalleryDb($db) {
   global $galleryDbFile;
   refreshSiteMap($db);
   logEvent(__FILE__, __FUNCTION__, basename($galleryDbFile));
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
   return is_file($dbFilename) ? readDb($dbFilename) : false;
   }

function savePortfolioImageDb($db) {
   global $portfolioFolder;
   $dbFilename = "{$portfolioFolder}/{$db->id}-db.json";
   logEvent(__FILE__, __FUNCTION__, basename($dbFilename), $db);
   return saveDb($dbFilename, $db);
   }

function readSettingsDb() {
   global $defaultSettingsDb, $settingsDbFile;
   $currentSettings = migrateSettings(readDb($settingsDbFile));
   return (object)array_merge((array)$defaultSettingsDb, (array)$currentSettings);
   }

function saveSettingsDb($db) {
   global $settingsDbFile;
   logEvent(__FILE__, __FUNCTION__, basename($settingsDbFile));
   return saveDb($settingsDbFile, $db);
   }

function migrateAccounts($accounts) {
   foreach ($accounts->users as $email => $user) {
      if (!isset($user->email))
         $user->email = $email;
      if (!isset($user->login))
         $user->login = strtotime("1970-01-01");
      if (!isset($user->valid))
         $user->valid = 0;
      }
   return $accounts;
   }

function readAccountsDb() {
   global $accountsDbFile;
   return migrateAccounts(readDb($accountsDbFile));
   }

function saveAccountsDb($db) {
   global $accountsDbFile;
   logEvent(__FILE__, __FUNCTION__, count((array)$db->users), count((array)$db->invites));
   return saveDb($accountsDbFile, $db);
   }

function displayTrue($imageDb) {
   return $imageDb->display;
   }

function convert($imageDb) {
   return (object)[
      "id" =>          $imageDb->id,
      "code" =>        toUriCode($imageDb->caption),
      "caption" =>     $imageDb->caption,
      "description" => $imageDb->description,
      "badge" =>       isset($imageDb->badge) ? $imageDb->badge : "",
      "stamp" =>       isset($imageDb->stamp) ? $imageDb->stamp : false,
      ];
   }
function generateGalleryDb() {
   return saveGalleryDb(array_map("convert", array_values(
      array_filter(readPortfolioDb(), "displayTrue"))));
   }

function calcNewPortfolioSort($currentSort, $up) {
   $sorts = array_column(readPortfolioDb(), "sort");
   array_unshift($sorts, 0);                 //allow space to move to the top
   array_push($sorts, end($sorts) + 10000);  //allow space to move to the bottom
   $currentLoc = array_search($currentSort, $sorts);
   $nearLoc =    min(max($currentLoc + ($up ? -1 : 1), 1), count($sorts) - 2);
   $farLoc =     min(max($currentLoc + ($up ? -2 : 2), 0), count($sorts) - 1);
   $newSort =    floor(($sorts[$nearLoc] + $sorts[$farLoc]) / 2);
   logEvent(__FILE__, __FUNCTION__, $currentLoc - 1, $currentSort, $newSort);
   return $newSort;
   }

function validEmailFormat($email) {
   $basicEmailPattern = "/^.+@.+[.].+$/";
   return preg_match($basicEmailPattern, $email) === 1;
   }

function formatMsg($msg) {
   $maxLen = 150;
   $output =
      is_null($msg) ?        "[null]" :
      ($msg === true ?       "[true]" :
      ($msg === false ?      "[false]" :
      ($msg === "" ?         "[empty]" :
      (is_object($msg) ?     json_encode($msg) :
      (is_array($msg) ?      json_encode($msg) :
      strval($msg))))));
   return strlen($output) > $maxLen ? (substr($output, 0, $maxLen - 3) . "...") : $output;
   }

function rotateLogFile($logFilename, $archiveFilename) {
   $ok =    rename($logFilename, $archiveFilename);
   $from =  basename($logFilename);
   $to =    basename($archiveFilename);
   $bytes = toBytes(filesize($archiveFilename));
   if ($ok)
      logEvent(__FILE__, __FUNCTION__, "start", $from, "archived: {$bytes}", $to);
   }

function logEvent($fnPath, $fnName, ...$messages) {
   // Example:
   //    logEvent(__FILE__, __FUNCTION__, "email sent", $emailAddress);
   global $secureFolder;
   $maxLogFileSize =  300000;  //300 KB
   $delimiter =       " | ";
   $logFilename =     "{$secureFolder}/events.log";
   $archiveFilename = "{$secureFolder}/events-archive.log";
   $user =            getCurrentUser();
   $userStr =         $user ? $user : "[anonymous]";
   $moduleName =      basename($fnPath, ".php");
   $fn =              toKebabCase($fnName);
   $maxReached =      is_file($logFilename) && filesize($logFilename) > $maxLogFileSize;
   if ($maxReached)
      rotateLogFile($logFilename, $archiveFilename);
   $allMessages = array_merge([$userStr, $moduleName, $fn], $messages);
   $timestamp =   date("Y-m-d H:i:s.") . substr(microtime(), 2, 3);  //example: "2026-10-28 09:30:52.826"
   $eventParts =  [$timestamp];
   foreach ($allMessages as $message)
      $eventParts[] = $delimiter . formatMsg($message);
   $eventParts[] = PHP_EOL;
   file_put_contents($logFilename, $eventParts, FILE_APPEND);
   }

function logErrorEvent($fnPath, $fnName, ...$messages) {
   // Example:
   //    logErrorEvent(__FILE__, __FUNCTION__, "email address not found", $emailAddress);
   logEvent($fnPath, $fnName, "*** ERROR ***", ...$messages);
   }

function logErrorExit($fnPath, $fnName, ...$messages) {
   logErrorEvent($fnPath, $fnName, ...$messages);
   logEvent(__FILE__, __FUNCTION__);
   exit("Something went wrong.");
   }

function httpJsonResponse($data) {
   header("cache-control: no-cache");
   header("content-type:  application/json");
   $body = json_encode($data);
   echo $body;
   logEvent(__FILE__, __FUNCTION__, $data, toBytes($body));
   }

function isReadOnlyExampleEmailAddress($email) {
   return preg_match("/@example[.]com$/", $email) === 1;
   }

function finishSendEmail($sendTo, $subjectLine, $messageLines) {
   $subjectLine =    "Paradise Photo Gallery - {$subjectLine}";
   $messageLines[] = "";
   $messageLines[] = "- Paradise";
   $messageLines[] = "";
   if (isReadOnlyExampleEmailAddress($sendTo))
      $sendTo = getCurrentUser();
   return readOnlyMode() ||
      mail($sendTo, $subjectLine, implode(PHP_EOL, $messageLines), "From: " . getCurrentUser());
   }

function sendEmail($sendTo, $subjectLine, $messageLines) {
   $success = finishSendEmail($sendTo, $subjectLine, $messageLines);
   logEvent(__FILE__, __FUNCTION__, $success, $sendTo, $subjectLine);
   $confirmationSubject = "Email confirmation";
   $confirmationLines = [
      "This is an automated message from the Paradise Photo Gallery system.",
      "",
      "An email message was just sent on your behalf as follows:",
      "\tSubject: Paradise Photo Gallery - {$subjectLine}",
      "\tTo: {$sendTo}",
      "",
      "This is an informational message only -- no action is required on your part.",
      ];
   if ($success)
      finishSendEmail(getCurrentUser(), $confirmationSubject, $confirmationLines);
   return $success;
   }

function refreshSiteMap($gallery) {
   // Update the sitemap.xml file based on the latest gallery data.
   global $siteMapFile;
   $xml = [
      '<?xml version="1.0" encoding="UTF-8" ?>',
      '<urlset',
      '   xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"',
      '   xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"',
      '   xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">',
      ];
   $base = getGalleryUrl();
   foreach ($gallery as $image)
      $xml[] = "   <url><loc>{$base}/image/{$image->id}/{$image->code}</loc></url>";
   $xml[] = "</urlset>";
   if (!file_put_contents($siteMapFile, implode(PHP_EOL, $xml) . PHP_EOL))
      logErrorEvent(__FILE__, __FUNCTION__, "File not saved", $siteMapFile, count($gallery));
   return $gallery;
   }

?>
