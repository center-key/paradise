<?php
///////////////////////////////////////////////////////////////
// Paradise ~ centerkey.com/paradise                         //
// GPLv3 ~ Copyright (c) individual contributors to Paradise //
///////////////////////////////////////////////////////////////

// Library
// Constants and general utilities

$version =    "[PARADISE-VERSION]";
$dataFolder = str_replace("console/server", "~data~", __DIR__);

$googleFonts = array(  //see https://fonts.google.com
   "Allan", "Allerta Stencil", "Amatic SC", "Anonymous Pro", "Arimo", "Arvo",
   "Bowlby One SC", "Bubblegum Sans",
   "Cherry Cream Soda", "Chewy", "Chango", "Coda", "Corben",
   "Devonshire",
   "Emilys Candy", "Ewert",
   "Galindo", "Geo", "Geostar", "Graduate", "Gruppo",
   "Fascinate Inline", "Faster One", "Flavors",
   "Homemade Apple",
   "Irish Grover",
   "Josefin Sans", "Jura", "Just Another Hand",
   "Kenia", "Kristi",
   "League Script", "Life Savers", "Lobster", "Londrina Outline", "Londrina Solid", "Love Ya Like A Sister",
   "Monoton", "Mouse Memoirs",
   "Neucha",
   "Old Standard TT",
   "Open Sans", "Orbitron",
   "Pacifico", "Philosopher", "Princess Sofia",
   "Reenie Beanie", "Rock Salt",
   "Sail", "Six Caps", "Slackey", "Sniglet", "Special Elite", "Syncopate",
   "Tangerine",
   "UnifrakturMaguntia",
   "Vibur"
   );

date_default_timezone_set("UTC");

function getGalleryUrl() {
   $tls = isset($_SERVER["HTTPS"]) && strtolower($_SERVER["HTTPS"]) !== "off";
   $protocol = $tls ? "https://" : "http://";
   $ignore = array("/console/rest/index.php");
   return $protocol . $_SERVER["SERVER_NAME"] . str_replace($ignore, "", $_SERVER["SCRIPT_NAME"]);
   }

function timeMillis() {
   return intval(microtime(TRUE) * 1000);
   }

function fileSysFriendly($string) {
   setlocale(LC_ALL, "en_US");
   $asciiLowercase = strtolower(iconv("UTF-8", "ASCII//TRANSLIT", trim($string)));
   return preg_replace("/\s.*|&.t;|[^a-z0-9_-]/", "", $asciiLowercase);
   }

function getProperty($map, $key) {
   return is_array($map) && isset($map[$key]) ? $map[$key] :
      (is_object($map) && isset($map->{$key}) ? $map->{$key} : null);
   }

function emptyObj($object) {
   return count(get_object_vars($object)) === 0;
   }

function initializeFile($filename, $fileContents) {
   if (!is_file($filename) && !file_put_contents($filename, $fileContents))
      logAndExit("Error initializing file, check permissions for: {$filename}");
   return $filename;
   }

function initializeFolder($folder, $blockDirIndex) {
   if (!is_dir($folder) && !mkdir($folder))
      logAndExit("Error initializing folder, check permissions for: {$folder}");
   if ($blockDirIndex)
      initializeFile("{$folder}/index.html", "Nothing to see.");
   return $folder;
   }

$dbCache = array();
function readDb($dbFilename) {
   global $dbCache;
   if (!isset($dbCache[$dbFilename])) {
      $dbStr = file_get_contents($dbFilename);
      if ($dbStr === false)
         logAndExit("Error reading database: {$dbFilename}");
      $dbCache[$dbFilename] = json_decode($dbStr);
      }
   return $dbCache[$dbFilename];
   }
function saveDb($dbFilename, $db) {
   if (readOnlyMode())
      return $db;
   $bytes = file_put_contents($dbFilename, json_encode($db));
   logEvent("save-db", basename($dbFilename), $bytes);
   if ($bytes === false)
      logAndExit("Error saving database: {$dbFilename}");
   $dbCache[$dbFilename] = $db;
   return $dbCache[$dbFilename];
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
   return preg_replace("/\s+/", "-", trim(preg_replace("/[^a-z]/", " ", strtolower($caption))));
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
      "badge" =>       isset($imageDb->badge) ? $imageDb->badge : "",
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
   $event = array(date("Y-m-d H:i:s."), $milliseconds, $delimiter, formatMsg(getCurrentUser()));
   foreach (func_get_args() as $msg) {
      $event[] = $delimiter;
      $event[] = formatMsg($msg);
      }
   $event[] = PHP_EOL;
   file_put_contents($logFilename, $event, FILE_APPEND);
   if (filesize($logFilename) > 500000)  //approximate file size limit: 500 KB
      rename($logFilename, $archiveFilename);
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

?>
