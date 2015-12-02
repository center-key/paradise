<?php
/////////////////////////////////////////////////
// PPAGES ~ centerkey.com/ppages               //
// GPL ~ Copyright (c) individual contributors //
/////////////////////////////////////////////////

// Transfer

function displayTransfer() {
   ?>
   <div id=file-uploader>
      <noscript><p>Enable JavaScript to use file uploader.</p></noscript>
      </div>
   <div id=process-files style="display: none;">Preparing to process files...
      <img src="loading.gif" alt="Loading Icon"></div>
   <script src="fileuploader.js"></script>
   <script>
      function createUploader() {
         var uploader = new qq.FileUploader({
            element: document.getElementById('file-uploader'),
            action: 'fileuploader.php',
            allowedExtensions: ['jpg', 'jpeg', 'png'],
            sizeLimit: 1048576,  //1MB
            onComplete: function(id, fileName, responseJSON) {
               if (id == 0) {  //last image uploaded
                  document.getElementById('process-files').style.display = 'block';
                  setTimeout('window.location.reload();', 3000);
                  }
               }
            });
         //document.getElementsByClassName("qq-upload-button")[0].innerHTML =
         //   document.getElementsByClassName("qq-upload-button")[0].innerHTML.replace(
         //      "Upload a file", "<button type=submit>Upload Photos</button>");
         }
      window.onload = createUploader;
      </script>
   <?php }

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

function createImages($origFile, $base) {
   global $thumbFileCode, $thumbFileExt, $fullFileCode, $fullFileExt;
   list($origWidth, $origHeight, $origType) = getimagesize($origFile);
   $origImage = $origType == 2 ?
      imagecreatefromjpeg($origFile) : imagecreatefrompng($origFile);
   $aspectRatio = $origWidth / $origHeight;
   createThumbnail($origImage, $origWidth, $origHeight,
      $base . $thumbFileCode . $thumbFileExt);
   createFullImage($origImage, $origWidth, $origHeight,
      $base . $fullFileCode . $fullFileExt);
   imagedestroy($origImage);
   logEvent("create-images", $origFile, $base, $aspectRatio);
   }

function processUploadsFolder() {
   global $uploadsFolder, $portfolioFolder;
   global $origFileCode, $thumbFileCode, $thumbFileExt, $fullFileCode, $fullFileExt;
   global $imageFieldId, $imageFieldOrigFileName, $imageFieldUploadDate, $imageFieldCaption;
   global $thumbHeight, $fullWidthMax, $fullHeightMax;
   $files = scandir($uploadsFolder);
   foreach ($files as $file)
      if (isImageFile($file)) {
         $uploadFile = $uploadsFolder . $file;
         $base = $portfolioFolder . getFreeImageId();
         $origFile = $base . $origFileCode . getFileExtension($file);
         $dbFile = dbFileName($base);
         echo "Uploading new image ($file)<br>\n";
         rename($uploadFile, $origFile);
         createImages($origFile, $base);
         $imageDb = createEmptyDb();
         $imageDb->{$imageFieldId} = getFreeImageId();
         $imageDb->{$imageFieldOrigFileName} = $file;
         $imageDb->{$imageFieldUploadDate} = gmdate("Y-m-d");
         saveDb($dbFile, $imageDb);
         setNextImageId();
         }
   logEvent("process-uploads-folder", $uploadsFolder);
   }

function processReprocessImages() {
   global $imageOrigFilter, $portfolioFolder,
      $origFileCode, $thumbFileCode, $thumbFileExt, $fullFileCode, $fullFileExt,
      $imageFieldId, $imageFieldOrigFileName, $imageFieldUploadDate,
      $imageFieldCaption,
      $thumbHeight, $fullWidthMax, $fullHeightMax;
   $files = glob($imageOrigFilter);
   echo "Reprocessing " . count($files) . " images...\n";
   foreach ($files as $file) {
      $base = substr($file, 0, strrpos($file, $origFileCode));
      echo "&nbsp; " . basename($base);
      createImages($file, $base);
      }
   logEvent("process-reprocess-images", $imageOrigFilter, count($files));
   }

?>
