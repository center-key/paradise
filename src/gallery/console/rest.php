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
require "php/image-processing.php";

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

function test() {  //url: http://localhost/ppages-test/gallery/console/rest?type=command&action=test
   return array("test" => true);
   }

function runCommand($action) {
   if ($action == "test" && $_SERVER["HTTP_HOST"] === "localhost")
      $resource = test();
   elseif ($action === "process-uploads")
      $resource = processUploads();
   elseif ($action === "generate-gallery")
      $resource = generateGalleryDb();
   else
      $resource = restError(400);
   return $resource;
   }

function fieldValue($value, $type) {
   $value = iconv("UTF-8", "UTF-8//IGNORE", $value);
   $value = str_replace("<", "&lt;", str_replace(">", "&gt;", $value));
   if ($type === "boolean")
      $value = $value === "true";
   elseif ($type === "integer")
      $value = intval($value);
   return $value;
   }

function updateItem($resource, $itemType) {
   if ($itemType === "page") {
      $item = $resource->pages[fieldValue($_GET["id"], "integer") - 1];
      if (isset($_GET["title"]))
         $item->title = fieldValue($_GET["title"], "string");
      if (isset($_GET["show"]))
         $item->show = fieldValue($_GET["show"], "boolean");
      }
   }

function updateSettings() {
   $fields = array(
      "title" =>          "string",
      "title-font" =>     "string",
      "title-size" =>     "string",
      "subtitle" =>       "string",
      "footer" =>         "string",
      "caption-caps" =>   "boolean",
      "caption-italic" => "boolean",
      "cc-license" =>     "boolean",
      "bookmarks" =>      "boolean",
      "contact-email" =>  "string"
      );
   $resource = readSettingsDb();
   if (isset($_GET["item"]))
      updateItem($resource, $_GET["item"]);
   else
      foreach ($fields as $field => $type)
         if (isset($_GET[$field]))
            $resource->{$field} = fieldValue($_GET[$field], $type);
   return saveSettingsDb($resource);
   }

function updatePortfolio($id) {
   $fields = array(
      "sort" =>        "integer",
      "display" =>     "boolean",
      "caption" =>     "string",
      "description" => "string",
      "badge" =>       "string"
      );
   $resource = readPortfolioImageDb($id);
   if ($resource) {
      foreach ($fields as $field => $type)
         if (isset($_GET[$field]))
            $resource->{$field} = fieldValue($_GET[$field], $type);
      savePortfolioImageDb($resource);
      generateGalleryDb();
      }
   return $resource ?: restError(404);
   }

function resource($loggedIn) {
   $type =   $_GET["type"];
   $action = $_GET["action"];
   $id =     $_GET["id"];
   $updateMode = $action === "update";
   if ($type === "security")
      $resource = securityRequest($action, $_POST["email"], $_POST["password"], $_POST["confirm"], $_POST["invite"]);
   elseif (!$loggedIn)
      $resource = restError(401);
   elseif ($type === "command")
      $resource = runCommand($action);
   elseif (!in_array($action, array(null, "get", "update")))
      $resource = restError(400);
   elseif ($type === "settings")
      $resource = $updateMode ? updateSettings() : readSettingsDb();
   elseif ($type === "gallery")
      $resource = readGalleryDb();
   elseif ($type === "portfolio")
      $resource = $updateMode ? updatePortfolio($id) : readPortfolioDb();
   else
      $resource = restError(404);
   logEvent("get-resource", $type, $action, $id, !getProperty($resource, "error"));
   return $resource;
   }

httpJsonResponse(resource($loggedIn));
?>
