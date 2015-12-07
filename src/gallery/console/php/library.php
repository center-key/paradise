<?php
/////////////////////////////////////////////////
// PPAGES ~ centerkey.com/ppages               //
// GPL ~ Copyright (c) individual contributors //
/////////////////////////////////////////////////

$version="v0.0.4";
date_default_timezone_set("UTC");
include "php/database.php";

$authTimestamp = "active";
$sessionTimout = 1200;  //20 mintues

$dataFolder = "../data/";
$customCssFile =   $dataFolder . "style.css";  //for site customizations
$graphicsFolder =  $dataFolder . "graphics/";  //for site customizations
$maskDataFile =    $dataFolder . "index.html";  //block folder browsing
$uploadsFolder =   $dataFolder . "uploads/";
$portfolioFolder = $dataFolder . "portfolio/";
$galleryDbFile =   $dataFolder . dbFileName("gallery");

$origFileCode =  "-original";
$thumbFileCode = "-small";
$thumbFileExt =  ".png";
$fullFileCode =  "-large";
$fullFileExt =   ".jpg";

$thumbHeight =      150;
$fullWidthMax =    1100;
$fullHeightMax =    800;
$imageDbFilter =   $portfolioFolder . dbFileName("*");
$imageOrigFilter = $portfolioFolder . "*" . $origFileCode . "*";

$imageFieldId =           "id";  //three digits, ex: "007"
$imageFieldOrigFileName = "original-file-name";
$imageFieldUploadDate =   "upload-date";  //ex: "2012-03-10"
$imageFieldDisplay =      "display";  //boolean
$imageFieldCaption =      "caption";
$imageFieldDescription =  "description";
$imageFieldBadge =        "badge";

$actionField =           "action";
$actionUpdateImage =     "update-image";
$actionDeleteImage =     "delete-image";
$actionUpdateSettings =  "update-settings";
$actionUpdateMenuBar =   "update-menu-bar";
$actionReprocessImages = "reprocess-images";
$actionChangePassword =  "change-password";
$actionCreateAccount =   "create-account";
//$actionsMenuBar = ["up"=>"&uarr;", "down"=>"&darr;", "show"=>"Show", "hide"=>"Hide", "edit"=>"Edit", "del"=>"&times;"];
$actionsMenuBar = ["show"=>"Show", "hide"=>"Hide"];

$settingsDbFile = $dataFolder . dbFileName("settings");

class ErrorStatus {
   public static $authFail = array("code" => 100, "msg" => "Authenticataion Failed!");
   public static $general =  array("code" => 101, "msg" => "Unknown Error");
   };

include "php/console.php";
include "php/console-login.php";
include "php/console-transfer.php";
include "php/console-accounts.php";
include "php/console-settings.php";
include "php/console-portfolio.php";
include "php/console-process.php";

function getFileExtension($fileName) {
   return strtolower(strrchr($fileName, "."));
   }

function isImageFile($fileName) {
   return stripos("..jpg.jpeg.png.", getFileExtension($fileName) . ".") > 0;
   }

function imageToFile($origImage, $origWidth, $origHeight, $newWidth, $newHeight, $newFile) {
   $newImage = imagecreatetruecolor($newWidth, $newHeight);
   imagecopyresampled($newImage, $origImage, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);
   getFileExtension($newfile) == ".png" ?
      imagepng($newImage, $newFile) : imagejpeg($newImage, $newFile);
   imagedestroy($newImage);
   }

function formatMsg($msg) {
   return $msg === null ? "[null]" : ($msg === true ? "[true]" : ($msg === false ? "[false]" :
      (is_object($msg) ? get_class($msg) . ":" . count($msg) : $msg)));
   }

function logEvent() {  //any number of parameters to log
   global $dataFolder;
   $delimiter = " | ";
   $logFilename =     $dataFolder . "log.txt";
   $archiveFilename = $dataFolder . "log-archive.txt";
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

?>
