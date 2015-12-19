<?php
/////////////////////////////////////////////////
// PPAGES ~ centerkey.com/ppages               //
// GPL ~ Copyright (c) individual contributors //
/////////////////////////////////////////////////

// REST
//
// Get resource:
//    gallery/console/rest?type=gallery

$noAuth = true;
require "php/security.php";

function restError($code) {
   $messages = array(
      401 => "Unauthorized access",
      404 => "Resource not found",
      500 => "Unknown error"
      );
   return array(
      "error"   => true,
      "code"    => $code,
      "message" => $messages[$code]
      );
   }

function getResource() {
   global $settingsDbFile, $galleryDbFile;
   $type =   $_GET["type"];
   $action = $_GET["action"];
   $id =     $_GET["id"];
   $dbs = array(
      "settings" => $settingsDbFile,
      "gallery"  => $galleryDbFile
      );
   if (!$loggedIn)
      $resource = restError(401);
   elseif (array_key_exists($type, $dbs))
      $resource = readDb($dbs[$type]);
   else
      $resource = restError(404);
   return $resource;
   }

httpJsonResponse(getResource());
?>
