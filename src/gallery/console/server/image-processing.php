<?php
///////////////////////////////////////////////////////////////
// Paradise ~ centerkey.com/paradise                         //
// GPLv3 ~ Copyright (c) individual contributors to Paradise //
///////////////////////////////////////////////////////////////

// Image processing library

$thumbHeight =    300;
$fullWidthMax =  1800;
$fullHeightMax = 1200;

function getNextImageId() {
   global $portfolioFolder;
   $numLen =   3;  //example:  "017"
   $filter =   "{$portfolioFolder}/*-db.json";
   $dbFiles =  glob($filter);  //end() expects variable pass by reference
   $imageNum = intval(basename(end($dbFiles) ?: "0", "-db.json")) + 1;
   return str_pad($imageNum, $numLen, "0", STR_PAD_LEFT);  //example: 17 --> "017"
   }

function imageToFile($origImage, $origWidth, $origHeight, $newWidth, $newHeight, $newFile) {
   $newImage = imagecreatetruecolor($newWidth, $newHeight);
   imagecopyresampled($newImage, $origImage, 0, 0, 0, 0,
      $newWidth, $newHeight, $origWidth, $origHeight);
   $pathInfo = pathinfo($newFile);
   if ($pathInfo["extension"] === ".png")
      imagepng($newImage, $newFile);
   else
      imagejpeg($newImage, $newFile);
   imagedestroy($newImage);
   }

function createThumbnail($origImage, $origWidth, $origHeight, $file) {
   global $thumbHeight;
   $newWidth = $thumbHeight * $origWidth / $origHeight;
   imageToFile($origImage, $origWidth, $origHeight, $newWidth, $thumbHeight, $file);
   logEvent("create-thumbnail", $origImage, $file, $newWidth);
   }

function createFullImage($origImage, $origWidth, $origHeight, $file) {
   global $fullWidthMax, $fullHeightMax;
   $scale = min($fullWidthMax / $origWidth, $fullHeightMax / $origHeight, 1);
   $newWidth = $origWidth * $scale;
   $newHeight = $origHeight * $scale;
   imageToFile($origImage, $origWidth, $origHeight, $newWidth, $newHeight, $file);
   logEvent("create-full-image", $origImage, $file, $scale);
   }

function createImages($origFile, $id) {
   global $portfolioFolder;
   list($origWidth, $origHeight, $origType) = getimagesize($origFile);
   ini_set("memory_limit", "128M");
   if ($origType == IMAGETYPE_JPEG)
      $origImage = imagecreatefromjpeg($origFile);
   else
      $origImage = imagecreatefrompng($origFile);
   $aspectRatio = $origWidth / $origHeight;
   createThumbnail($origImage, $origWidth, $origHeight, "{$portfolioFolder}/{$id}-small.png");
   createFullImage($origImage, $origWidth, $origHeight, "{$portfolioFolder}/{$id}-large.jpg");
   imagedestroy($origImage);
   logEvent("create-images", $origFile, $id, $aspectRatio);
   }

function deleteImages($id) {
   global $portfolioFolder;
   foreach (glob("{$portfolioFolder}/{$id}-*") as $filename)
      unlink($filename);
   logEvent("delete-images", $id);
   }

function fileInfo($filename) {
   global $uploadsFolder;
   $path =     "{$uploadsFolder}/{$filename}";
   $pathInfo = pathinfo($filename);
   $type =     exif_imagetype($path);
   return (object)array(
      "name" =>      $filename,
      "path" =>      $path,
      "extension" => strtolower($pathInfo["extension"]),
      "type" =>      $type,
      "valid" =>     $type == IMAGETYPE_JPEG || $type == IMAGETYPE_PNG,
      );
   }

function processUpload($file) {
   global $uploadsFolder, $portfolioFolder;
   $id = getNextImageId();
   $origFile = "{$portfolioFolder}/{$id}-original.{$file->extension}";
   rename($file->path, $origFile);
   createImages($origFile, $id);
   $dbFilename = "{$portfolioFolder}/{$id}-db.json";
   $imageDb = (object)array(
      "id" =>          $id,
      "sort" =>        intval($id) * 10000,
      "original" =>    $file->name,
      "uploaded" =>    gmdate("Y-m-d"),
      "display" =>     false,
      "caption" =>     "",
      "description" => "",
      "badge" =>       "",
      "stamp" =>       false,
      );
   saveDb($dbFilename, $imageDb);
   logEvent("process-upload", $imageDb->id, $imageDb->sort, $imageDb->original);
   }

function processUploads() {
   global $uploadsFolder;
   $imagePattern = "/[.](jpg|jpeg|png)$/i";
   $uploadedFilenames = array_values(preg_grep($imagePattern, scandir($uploadsFolder)));
   $uploadedFiles = array_map('fileInfo', $uploadedFilenames);
   $validFiles =   array_filter($uploadedFiles, function($file) { return $file->valid; });
   $invalidFiles = array_filter($uploadedFiles, function($file) { return !$file->valid; });
   foreach ($uploadedFiles as $file)
      if ($file->valid)
         processUpload($file);
      else
         logEvent("invalid-image-file-deleted", $file->name, $file, unlink($file->path));
   $msg = "Images processed: " . count($validFiles) . ", Invalid: " . count($invalidFiles);
   logEvent("process-uploads", $msg);
   return (object)array(
      "count" =>   count($validFiles),
      "files" =>   array_column($validFiles, 'name'),
      "fails" =>   array_column($invalidFiles, 'name'),
      "message" => $msg,
      );
   }

?>
