<?php
/////////////////////////////////////////////////
// PPAGES ~ www.centerkey.com/ppages           //
// GPL ~ Copyright (c) individual contributors //
/////////////////////////////////////////////////

$imageNum = 0;

function getFreeImageId() {
   global $imageDbFilter, $imageNum;
   static $numLen = 3;  //ex: "037"
   if (!$imageNum)  //"gallery/037.json" --> 38
      $imageNum = 1 + trim(end(glob($imageDbFilter)), "a..z/.");
   return str_pad($imageNum, $numLen, "0", STR_PAD_LEFT);  //38 --> "038"
   }

function setNextImageId() {
   global $imageNum;
   $imageNum++;
   }

function generateGalleryDb() {
   global $galleryDbFile, $portfolioFolder, $imageDbFilter,
      $imageFieldId, $imageFieldOrigFileName, $imageFieldUploadDate,
      $imageFieldDisplay, $imageFieldCaption, $imageFieldDescription, $imageFieldBadge;
   $galleryDb = array();
   $dbFiles = glob($imageDbFilter);
   foreach ($dbFiles as $dbFile) {
      $imageDb = readDb($dbFile);
      if ($imageDb->{$imageFieldDisplay} == "on") $imageDb->{$imageFieldDisplay} = "true"; //temporary backwards compatibility
      if ($imageDb->{$imageFieldDisplay} == "true")
         $galleryDb[] = $imageDb;
      }
   saveDb($galleryDbFile, $galleryDb);
   }

function processUpdateImageDb() {
   global $portfolioFolder, $imageDbFilter,
      $imageFieldId, $imageFieldOrigFileName, $imageFieldUploadDate,
      $imageFieldDisplay, $imageFieldCaption, $imageFieldDescription, $imageFieldBadge;
   $imageId = $_POST[$imageFieldId];
   echo "<div>Updating record: #$imageId</div>";
   $dbFile = $portfolioFolder . dbFileName($imageId);
   $imageDb = readDb($dbFile);
   $imageDb->{$imageFieldDisplay} =     $_POST[$imageFieldDisplay];
   $imageDb->{$imageFieldCaption} =     $_POST[$imageFieldCaption];
   $imageDb->{$imageFieldDescription} = $_POST[$imageFieldDescription];
   $imageDb->{$imageFieldBadge} =       $_POST[$imageFieldBadge];
   saveDb($dbFile, $imageDb);
   generateGalleryDb();
   }

function processDeleteImageDb() {
   echo "<div class=advisory>Sorry, the delete image feature is not ready yet.</div>";
   }

function displayPortfolioHtml($id, $status, $title, $desc, $badge, $date, $thumbFile, $file, $origFile, $origFileName) {
   global $imageFieldId, $imageFieldDisplay, $imageFieldCaption,
      $imageFieldDescription, $imageFieldBadge,
      $actionField, $actionUpdateImage, $actionDeleteImage;
   $checked = $status ? " checked" : "";
   echo "
      <div class=image-box>
         <div class='push-right align-right'><a class=external-site
            href='$origFile'><img class=picture-frame src='$thumbFile' alt='$file'
            title='Click to view original uploaded file ($origFileName)'></a>
            <p class=small>Uploaded: <b>$date</b></p>
         </div>
         <label><span>Display:</span>
            <input class=portfolio-$imageFieldDisplay name=$id type=checkbox$checked>
            <span class=small>(show in gallery)</span>
         </label>
         <label><span>Caption:</span>
            <input class=portfolio-$imageFieldCaption name=$id type=text value='$title'>
         </label>
         <label><span>Description:</span>
            <textarea class=portfolio-$imageFieldDescription name=$id>$desc</textarea>
         </label>
         <label><span>Badge:</span>
            <input class=portfolio-$imageFieldBadge name=$id type=text value='$badge'></label>
         <p class=sans-label>
            <button class=portfolio-move-up name=$id>&uarr; Move Up</button>
            <button class=portfolio-move-down name=$id>&darr; Move Down</button>
            <button class=portfolio-delete name=$id>&times; Delete</button>
         </p>
      </div>\n";
   }

function displayPortfolio() {
   global $portfolioFolder, $origFileCode, $thumbFileCode, $thumbFileExt,
      $imageDbFilter, $imageFieldId, $imageFieldOrigFileName,
      $imageFieldUploadDate, $imageFieldDisplay, $imageFieldCaption,
      $imageFieldDescription, $imageFieldBadge;
   $dbFiles = glob($imageDbFilter);
   foreach ($dbFiles as $dbFile) {
      $imageDb = ReadDb($dbFile);
      $imageId = $imageDb->{$imageFieldId};
      $base = $portfolioFolder . $imageDb->{$imageFieldId};
      $thumbFile = $base . $thumbFileCode . $thumbFileExt;
      $origFileName = $imageDb->{$imageFieldOrigFileName};
      $origFile = $base . $origFileCode . getFileExtension($origFileName);
      if ($imageDb->{$imageFieldDisplay} == "on") $imageDb->{$imageFieldDisplay} = "true"; //temporary backwards compatibility
      displayPortfolioHtml($imageId, $imageDb->{$imageFieldDisplay} == "true",
         $imageDb->{$imageFieldCaption}, $imageDb->{$imageFieldDescription},
         $imageDb->{$imageFieldBadge}, $imageDb->{$imageFieldUploadDate},
         $thumbFile, $file, $origFile, $origFileName);
      }
   if (count($dbFiles) == 0)
      echo "<p>There are no photos in your portfolio.</p>";
   }

?>
