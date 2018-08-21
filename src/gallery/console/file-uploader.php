<?php
///////////////////////////////////////////////////////////////
// Paradise ~ centerkey.com/paradise                         //
// GPLv3 ~ Copyright (c) individual contributors to Paradise //
///////////////////////////////////////////////////////////////

// File Uploader

//////////////////////////////////////
// Updating Valum's AJAX File-Uploader
// -----------------------------------
// 1) Download install zip from:
//      https://valums-file-uploader.github.io/file-uploader/
// 2) Unzip file
// 3) Copy the following files:
//       client/fileuploader.css
//       client/fileuploader.js
//       client/loading.gif
//       server/php.php
//    into the Paradise project folder:
//       paradise/src/gallery/console/file-uploader/
//////////////////////////////////////

require "php/security.php";
require "file-uploader/php.php";
$uploadFolder = "../~data~/uploads/";

function upload($uploadFolder) {
   $allowedExtensions = ["jpg", "jpeg", "png"];
   $sizeLimit =         2 * 1024 * 1024;  //2 MB
   $uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
   return $uploader->handleUpload($uploadFolder);
   }

function readOnlyResponse() { return ["success" => true, "type" => "simulated"]; }
$result = $_SESSION["read-only-user"] ? readOnlyResponse() : upload($uploadFolder);
logEvent("file-upload", $result["success"], $uploadFolder, $result);
httpJsonResponse($result);
?>
