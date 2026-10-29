<?php
///////////////////////////////////////////////////////////////
// Paradise ~ centerkey.com/paradise                         //
// GPLv3 ~ Copyright (c) Individual contributors to Paradise //
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
   $imageID =  str_pad($imageNum, $numLen, "0", STR_PAD_LEFT);  //example: 17 --> "017"
   logEvent(__FILE__, __FUNCTION__, $imageID);
   return $imageID;
   }

function imageToFile($origImage, $origWidth, $origHeight, $newWidth, $newHeight, $newFile) {
   $newImage = imagecreatetruecolor($newWidth, $newHeight);
   imagecopyresampled($newImage, $origImage, 0, 0, 0, 0,
      $newWidth, $newHeight, $origWidth, $origHeight);
   $pathInfo = pathinfo($newFile);
   if ($pathInfo["extension"] === ".png")
      imagepng($newImage, $newFile);
   elseif ($pathInfo["extension"] === ".webp")
      imagewebp($newImage, $newFile);
   else
      imagejpeg($newImage, $newFile);
   $times = mb_chr(215, 'UTF-8');  //"×"
   logEvent(__FILE__, __FUNCTION__, basename($newFile), "{$newWidth}{$times}{$newHeight}");
   }

function createThumbnail($origImage, $origWidth, $origHeight, $file) {
   global $thumbHeight;
   $newWidth = intval($thumbHeight * $origWidth / $origHeight);
   imageToFile($origImage, $origWidth, $origHeight, $newWidth, $thumbHeight, $file);
   logEvent(__FILE__, __FUNCTION__, basename($file), "{$newWidth}px");
   }

function createFullImage($origImage, $origWidth, $origHeight, $file) {
   global $fullWidthMax, $fullHeightMax;
   $scale =     round(min($fullWidthMax / $origWidth, $fullHeightMax / $origHeight, 1), 4);
   $newWidth =  intval($origWidth * $scale);
   $newHeight = intval($origHeight * $scale);
   imageToFile($origImage, $origWidth, $origHeight, $newWidth, $newHeight, $file);
   logEvent(__FILE__, __FUNCTION__, basename($file), "scale: {$scale}");
   }

function createImages($origFile, $id) {
   global $portfolioFolder;
   list($origWidth, $origHeight, $origType) = getimagesize($origFile);
   ini_set("memory_limit", "128M");
   if ($origType == IMAGETYPE_PNG)
      $origImage = imagecreatefrompng($origFile);
   elseif ($origType == IMAGETYPE_WEBP)
      $origImage = imagecreatefromwebp($origFile);
   else
      $origImage = imagecreatefromjpeg($origFile);
   $aspectRatioMsg = "aspect: " . round($origWidth / $origHeight, 3);
   createThumbnail($origImage, $origWidth, $origHeight, "{$portfolioFolder}/{$id}-small.png");
   createFullImage($origImage, $origWidth, $origHeight, "{$portfolioFolder}/{$id}-large.jpg");
   $mimeType = image_type_to_mime_type($origType);
   logEvent(__FILE__, __FUNCTION__, $id, basename($origFile), $mimeType, $aspectRatioMsg);
   }

function deleteImages($id) {
   global $portfolioFolder;
   foreach (glob("{$portfolioFolder}/{$id}-*") as $filename)
      unlink($filename);
   logEvent(__FILE__, __FUNCTION__, $id);
   }

function fileInfo($filename) {
   global $uploadsFolder;
   $path =     "{$uploadsFolder}/{$filename}";
   $pathInfo = pathinfo($filename);
   $type =     exif_imagetype($path);
   $valid =    in_array($type, [IMAGETYPE_PNG, IMAGETYPE_WEBP, IMAGETYPE_JPEG]);
   return (object)array(
      "name" =>      $filename,
      "path" =>      $path,
      "extension" => strtolower($pathInfo["extension"]),
      "type" =>      $type,
      "valid" =>     $valid,
      );
   }

function processUpload($file) {
   global $uploadsFolder, $portfolioFolder;
   $id =       getNextImageId();
   $origFile = "{$portfolioFolder}/{$id}-original.{$file->extension}";
   rename($file->path, $origFile);
   createImages($origFile, $id);
   $dbFilename = "{$portfolioFolder}/{$id}-db.json";
   $imageDb = (object)array(
      "id" =>          $id,
      "code" =>        "one-image",
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
   logEvent(__FILE__, __FUNCTION__, $imageDb->id, "sort: {$imageDb->sort}", $imageDb->original);
   }

function processUploads() {
   global $uploadsFolder;
   $imagePattern =      "/[.](png|webp|jpg|jpeg)$/i";
   $uploadedFilenames = array_values(preg_grep($imagePattern, scandir($uploadsFolder)));
   $uploadedFiles =     array_map('fileInfo', $uploadedFilenames);
   $validFiles =        array_filter($uploadedFiles, function($file) { return $file->valid; });
   $invalidFiles =      array_filter($uploadedFiles, function($file) { return !$file->valid; });
   $filesMsg =          "files: " .   count($validFiles);
   $invalidMsg =        "invalid: " . count($invalidFiles);
   foreach ($uploadedFiles as $file)
      if ($file->valid)
         processUpload($file);
      else
         logErrorEvent(__FILE__, __FUNCTION__, "Invalid image file",
            $file->name, $file, unlink($file->path));
   logEvent(__FILE__, __FUNCTION__, $filesMsg, $invalidMsg);
   return (object)array(
      "count" =>   count($validFiles),
      "files" =>   array_column($validFiles,   'name'),
      "fails" =>   array_column($invalidFiles, 'name'),
      "message" => "{$filesMsg}, {$invalidMsg}",
      );
   }

?>
