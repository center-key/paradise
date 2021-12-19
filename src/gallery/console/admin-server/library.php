<?php
///////////////////////////////////////////////////////////////
// Paradise ~ centerkey.com/paradise                         //
// GPLv3 ~ Copyright (c) individual contributors to Paradise //
///////////////////////////////////////////////////////////////

// Library
// Constants and general utilities

require "polyfills.php";
require "font-options.php";

$version =      "[PARADISE-VERSION]";
$dbCacheStore = null;
$galleryFolder = str_replace("/console/admin-server", "", __DIR__);
$dataFolder =    "{$galleryFolder}/~data~";
$siteMapFile =   "{$galleryFolder}/sitemap.xml";
date_default_timezone_set("UTC");

function getGalleryUrl() {
   $tls = isset($_SERVER["HTTPS"]) && strtolower($_SERVER["HTTPS"]) !== "off";
   $protocol = $tls ? "https://" : "http://";
   $ignore = array("/console/rest/index.php");
   return $protocol . $_SERVER["SERVER_NAME"] . str_replace($ignore, "", $_SERVER["SCRIPT_NAME"]);
   }

function getTime() {
   return intval(microtime(true) * 1000);
   }

function daysToMsec($days) {
   return $days * 24 * 60 * 60 * 1000;
}

function fileSysFriendly($string) {
   setlocale(LC_ALL, "en_US");
   $asciiLowercase = strtolower(iconv("UTF-8", "ASCII//TRANSLIT", trim($string)));
   return preg_replace("/\s.*|&.t;|[^a-z0-9_-]/", "", $asciiLowercase);
   }

function emptyObj($object) {
   return count(get_object_vars($object)) === 0;
   }

function initializeFile($filename, $fileContents) {
   if (!is_file($filename))
      logEvent("initialize-file", $filename, strlen($fileContents));
   if (!is_file($filename) && !file_put_contents($filename, $fileContents))
      logAndExit("Error initializing file, check permissions for: {$filename}");
   if (!is_writable($filename))
      exit("Error reading file, check permissions for: {$filename}");
   return $filename;
   }

function initializeFolder($folder, $blockDirIndex) {
   if (!is_dir($folder))
      logEvent("initialize-folder", $folder);
   if (!is_dir($folder) && !mkdir($folder))
      logAndExit("Error initializing folder, check permissions for: {$folder}");
   if ($blockDirIndex)
      initializeFile("{$folder}/index.html", "Nothing to see.");
   if (!is_writable($folder))
      exit("Error reading folder, check permissions for: {$folder}");
   return $folder;
   }

function getDbCache() {
   global $dbCacheStore;
   if ($dbCacheStore == null)
      $dbCacheStore = (object)array();
   return $dbCacheStore;
   }

function readDb($dbFilename) {
   $dbCache = getDbCache();
   if (!isset($dbCache->{$dbFilename})) {
      $dbStr = file_get_contents($dbFilename);
      if ($dbStr === false)
         logAndExit("Error reading database: {$dbFilename}");
      $dbCache->{$dbFilename} = json_decode($dbStr);
      }
   return $dbCache->{$dbFilename};
   }

function saveDb($dbFilename, $db) {
   $dbCache = getDbCache();
   if (readOnlyMode())
      return $db;
   $bytes = file_put_contents($dbFilename, json_encode($db));
   logEvent("save-db", basename($dbFilename), $bytes);
   if ($bytes === false)
      logAndExit("Error saving database: {$dbFilename}");
   $dbCache->{$dbFilename} = $db;
   return $dbCache->{$dbFilename};
   }

function readGalleryDb() {
   global $galleryDbFile;
   return readDb($galleryDbFile);
   }

function saveGalleryDb($db) {
   global $galleryDbFile;
   refreshSiteMap($db);
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
   logEvent("save-portfolio-image-db", "{$portfolioFolder}/{$db->id}-db.json", $db);
   return saveDb("{$portfolioFolder}/{$db->id}-db.json", $db);
   }

function toCamelCase($kebabCase) {
   $camelCase = str_replace(' ', '', ucwords(str_replace('-', ' ', $kebabCase)));
   $camelCase[0] = strtolower($camelCase[0]);
   return $camelCase;
   }

function migrateSettings($settings) {
   foreach($settings as $key => $value) {
      $newKey = toCamelCase($key);
      if ($newKey !== $key && !isset($settings->$newKey) && isset($settings->$key)) {
         $settings->$newKey = $value;
         unset($settings->$key);
         }
      }
   return $settings;
   }

function readSettingsDb() {
   global $defaultSettingsDb, $settingsDbFile;
   return (object)array_merge((array)$defaultSettingsDb, (array)migrateSettings(readDb($settingsDbFile)));
   }

function saveSettingsDb($db) {
   global $settingsDbFile;
   logEvent("save-settings-db", array_values(get_object_vars($db)));
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
   logEvent("save-accounts-db", count((array)$db->users), count((array)$db->invites));
   return saveDb($accountsDbFile, $db);
   }

function toUriCode($caption) {
   return preg_replace("/\s+/", "-", trim(preg_replace("/[^a-z]/", " ", strtolower($caption))));
   }

function displayTrue($imageDb) {
   return $imageDb->display;
   }

function convert($imageDb) {
   return (object)array(
      "id" =>          $imageDb->id,
      "code" =>        toUriCode($imageDb->caption),
      "caption" =>     $imageDb->caption,
      "description" => $imageDb->description,
      "badge" =>       isset($imageDb->badge) ? $imageDb->badge : "",
      "stamp" =>       isset($imageDb->stamp) ? $imageDb->stamp : false,
      );
   }
function generateGalleryDb() {
   return saveGalleryDb(array_map("convert", array_values(
      array_filter(readPortfolioDb(), "displayTrue"))));
   }

function calcNewPortfolioSort($currentSort, $up) {
   $sorts = array_column(readPortfolioDb(), 'sort');
   array_unshift($sorts, 0);                 //allow space to move to the top
   array_push($sorts, end($sorts) + 10000);  //allow space to move to the bottom
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
   $maxLogFileSize =  500000;  //500 KB
   $delimiter =       " | ";
   $logFilename =     "{$secureFolder}/events.log";
   $archiveFilename = "{$secureFolder}/events-archive.log";
   $milliseconds = substr(microtime(), 2, 3);
   $user = getCurrentUser();
   $event = array(date("Y-m-d H:i:s."), $milliseconds, $delimiter, $user ? $user : '[anonymous]');
   if (filesize($logFilename) > $maxLogFileSize)
      rename($logFilename, $archiveFilename);
   foreach (func_get_args() as $msg) {
      $event[] = $delimiter;
      $event[] = formatMsg($msg);
      }
   $event[] = PHP_EOL;
   file_put_contents($logFilename, $event, FILE_APPEND);
   }

function logAndExit($message) {
   logEvent("server-error", $message);
   exit($message);
   }

function httpJsonResponse($data) {
   header("Cache-Control: no-cache");
   header("Content-Type:  application/json");
   echo json_encode($data);
   logEvent("http-response", $data);
   }

function isReadOnlyExampleEmailAddress($email) {
   return preg_match("/@example[.]com$/", $email) === 1;
   }

function finishSendEmail($sendTo, $subjectLine, $messageLines) {
   $subjectLine = "Paradise PHP Photo Gallery - {$subjectLine}";
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
      finishSendEmail(getCurrentUser(), $confirmationSubject, $confirmationLines);
   return $success;
   }

function refreshSiteMap($gallery) {
   // Update the sitemap.xml file based on the latest gallery data.
   global $siteMapFile;
   $xml = array(
      '<?xml version="1.0" encoding="UTF-8"?>',
      '<urlset',
      '   xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"',
      '   xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"',
      '   xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">',
      );
   $base = getGalleryUrl();
   foreach ($gallery as $image)
      $xml[] = "   <url><loc>{$base}/image/{$image->id}/{$image->code}</loc></url>";
   $xml[] = '</urlset>';
   if (!file_put_contents($siteMapFile, implode(PHP_EOL, $xml) . PHP_EOL))
      logEvent("site-map-refresh", "*** ERROR ***", "File not saved", $siteMapFile, count($gallery));
   return $gallery;
   }

?>
