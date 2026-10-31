<?php require "admin-server/security.php"; ?>
<?php
///////////////////////////////////////////////////////////////
// Paradise ~ centerkey.com/paradise                         //
// GPLv3 ~ Copyright (c) Individual contributors to Paradise //
///////////////////////////////////////////////////////////////

// Image Upload Endpoint

$uploadFolder = "../~data~/uploads/";

function upload($uploadFolder) {
   foreach ($_FILES["file"]["tmp_name"] as $index => $tempFile) {
      $filename = $_FILES["file"]["name"][$index];
      move_uploaded_file($tempFile, $uploadFolder . $filename);
      logEvent(__FILE__, __FUNCTION__, $index, $filename);
      }
   return (object)[
      "success" => true,
      "images" =>  $index + 1,
      ];
   }

function readOnlyResponse() {
   return (object)[
      "success" => true,
      "type" =>    "simulated",
      ];
   }

$result = readOnlyMode() ? readOnlyResponse() : upload($uploadFolder);
logEvent(__FILE__, __FUNCTION__, $result->success, $uploadFolder, $result);
httpJsonResponse($result);
?>
