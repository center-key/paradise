<?php require "admin-server/security.php"; ?>
<?php
///////////////////////////////////////////////////////////////
// Paradise ~ centerkey.com/paradise                         //
// GPLv3 ~ Copyright (c) Individual contributors to Paradise //
///////////////////////////////////////////////////////////////

// Image Upload Endpoint

$uploadFolder = "../~data~/uploads/";

function upload($uploadFolder) {
   foreach ($_FILES["file"]["tmp_name"] as $i => $tempFile) {
      $filename = $_FILES["file"]["name"][$i];
      move_uploaded_file($tempFile, $uploadFolder . $filename);
      logEvent("file-upload-item", $i, $filename);
      }
   return (object)array(
      "success" => true,
      "images" =>  $i + 1,
      );
   }

function readOnlyResponse() {
   return (object)array(
      "success" => true,
      "type" =>    "simulated",
      );
   }

$result = readOnlyMode() ? readOnlyResponse() : upload($uploadFolder);
logEvent("file-upload", $result->success, $uploadFolder, $result);
httpJsonResponse($result);
?>
