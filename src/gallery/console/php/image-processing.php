<?php
///////////////////////////////////////////////////////////////
// Paradise ~ centerkey.com/paradise                         //
// GPLv3 ~ Copyright (c) individual contributors to Paradise //
///////////////////////////////////////////////////////////////

// Image processing library

$thumbHeight =    150;
$fullWidthMax =  1600;
$fullHeightMax = 1200;

function getNextImageId() {
   global $portfolioFolder;
   $numLen = 3;  //ex: "037"
   $filter = "{$portfolioFolder}/*-db.json";
   $imageNum = intval(basename(end(glob($filter)) ?: "0", "-db.json")) + 1;
   return str_pad($imageNum, $numLen, "0", STR_PAD_LEFT);  //38 --> "038"
   }

function imageToFile($origImage, $origWidth, $origHeight, $newWidth, $newHeight, $newFile) {
   $newImage = imagecreatetruecolor($newWidth, $newHeight);
   imagecopyresampled($newImage, $origImage, 0, 0, 0, 0,
      $newWidth, $newHeight, $origWidth, $origHeight);
   $pathInfo = pathinfo($newfile);
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
   $origImage = $origType == 2 ? imagecreatefromjpeg($origFile) : imagecreatefrompng($origFile);
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

function processUploads() {
   global $uploadsFolder, $portfolioFolder;
   $files = array_values(preg_grep("/[.](jpg|jpeg|png)$/i", scandir($uploadsFolder)));
   foreach ($files as $filename) {
      $id = getNextImageId();
      $pathInfo = pathinfo($filename);
      $extension = strtolower($pathInfo["extension"]);
      $origFile = "{$portfolioFolder}/{$id}-original.{$extension}";
      rename("{$uploadsFolder}/{$filename}", $origFile);
      createImages($origFile, $id);
      $dbFilename = "{$portfolioFolder}/{$id}-db.json";
      $imageDb = [
         "id" =>          $id,
         "sort" =>        intval($id) * 10000,
         "original" =>    $filename,
         "uploaded" =>    gmdate("Y-m-d"),
         "display" =>     false,
         "caption" =>     "",
         "description" => "",
         "badge" =>       ""
         ];
      saveDb($dbFilename, $imageDb);
      }
   $msg = "Images processed: " . count($files);
   return ["count" => count($files), "files" => $files, "message" => $msg];
   }

?>
