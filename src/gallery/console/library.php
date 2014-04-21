<?php
/////////////////////////////////////////////////
// PPAGES ~ www.centerkey.com/ppages           //
// GPL ~ Copyright (c) individual contributors //
/////////////////////////////////////////////////

$version="v0.0.4";
include "database.php";

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
$actionsMenuBar = array("up"=>"&uarr;", "down"=>"&darr;",
   "show"=>"Show", "hide"=>"Hide", "edit"=>"Edit", "del"=>"&times;");

$settingsDbFile = $dataFolder . dbFileName("settings");

class ErrorStatus {
   public static $authFail = array("code" => 100, "msg" => "Authenticataion Failed!");
   public static $general =  array("code" => 101, "msg" => "Unknown Error");
   };

include "console.php";
include "console-login.php";
include "console-transfer.php";
include "console-accounts.php";
include "console-settings.php";
include "console-portfolio.php";
include "console-process.php";

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

?>
