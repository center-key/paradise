<?php
/////////////////////////////////////////////////
// PPAGES ~ centerkey.com/ppages               //
// GPL ~ Copyright (c) individual contributors //
/////////////////////////////////////////////////

// File Uploader

//////////////////////////////////////
// Updating Valum's AJAX File-Uploader
// -----------------------------------
// 1) Download install zip from:
//      http://valums-file-uploader.github.io/file-uploader/
// 2) Unzip file
// 3) Copy the following files:
//       client/fileuploader.css
//       client/fileuploader.js
//       client/loading.gif
//       server/php.php
//    into the PPAGES project folder:
//       ppages/src/gallery/console/file-uploader/
//////////////////////////////////////

require("php/library.php");
require("file-uploader/php.php");

$allowedExtensions = ["jpg", "jpeg", "png"];
$sizeLimit =         1 * 1024 * 1024;  //1MB
$uploadFolder =      "../data/uploads/";

$uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
$result = $uploader->handleUpload($uploadFolder);
logEvent("file-upload", $result["success"], $uploadFolder, $result["newFilename"]);
header("Cache-Control: no-cache");
header("Content-Type:  application/json");
echo json_encode($result);
?>
