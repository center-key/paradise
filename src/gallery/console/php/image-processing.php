<?php
///////////////////////////////////////////////////
// Paradise ~ centerkey.com/paradise             //
// GPLv3 ~ Copyright (c) individual contributors //
///////////////////////////////////////////////////

// Image processing library

$thumbHeight =   150;
$fullWidthMax = 1100;
$fullHeightMax = 800;

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
   $files = glob("{$uploadsFolder}/*.{jpg,jpeg,png}", GLOB_BRACE);
   foreach ($files as $filename) {
      $id = getNextImageId();
      $pathInfo = pathinfo($filename);
      $extension = strtolower($pathInfo["extension"]);
      $origFile = "{$portfolioFolder}/{$id}-original.{$extension}";
      rename($filename, $origFile);
      createImages($origFile, $id);
      $dbFilename = "{$portfolioFolder}/{$id}-db.json";
      $imageDb = array(
         "id" =>          $id,
         "sort" =>        intval($id) * 10000,
         "original" =>    basename($filename),
         "uploaded" =>    gmdate("Y-m-d"),
         "display" =>     false,
         "caption" =>     "",
         "description" => "",
         "badge" =>       ""
         );
      saveDb($dbFilename, $imageDb);
      }
   $msg = "Images processed: " . count($files);
   return array("count" => count($files), "files" => array_map("basename", $files), "message" => $msg);
   }

?>
