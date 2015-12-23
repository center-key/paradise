<?php
///////////////////////////////////////////////////
// PPAGES ~ centerkey.com/ppages                 //
// GPLv3 ~ Copyright (c) individual contributors //
///////////////////////////////////////////////////

// REST
//
// Get resource:
//    gallery/console/rest?type=gallery
// Update value:
//    gallery/console/rest?type=settings&action=update&caption-italic=true

$noAuth = true;
require "php/security.php";

function restError($code) {
   $messages = array(
      400 => "Invalid parameters",
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

function getResource($loggedIn) {
   global $settingsDbFile, $galleryDbFile;
   $type =   $_GET["type"];
   $action = $_GET["action"];
   $id =     $_GET["id"];
   $dbs = array(
      "settings" => $settingsDbFile,
      "gallery"  => $galleryDbFile
      );
   if ($type === "security")
      $resource = securityRequest($action, $_POST["email"], $_POST["password"], $_POST["confirm"], $_POST["invite"]);
   elseif (!$loggedIn)
      $resource = restError(401);
   elseif (array_key_exists($type, $dbs))
      $resource = readDb($dbs[$type]);
   else
      $resource = restError(404);
   logEvent("get-resource", $type, $action, $id, !getProperty($resource, "error"));
   return $resource;
   }

httpJsonResponse(getResource($loggedIn));
?>
