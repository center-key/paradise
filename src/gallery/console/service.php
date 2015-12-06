<?php
/////////////////////////////////////////////////
// PPAGES ~ centerkey.com/ppages               //
// GPL ~ Copyright (c) individual contributors //
/////////////////////////////////////////////////

// Example Web Services:
//    service?action=portfolio&id=003 (get portfolio resource)
//    service?action=portfolio&id=003&field=display&value=true (set "display")
//    service?action=settings (get settings resource)
//    service?action=settings&field=title&value=My%20Gallery (set "title")

session_start();
include "php/library.php";

class StatusMessage {
   public $status = "ok";
   public $message;
   function __construct($msg) {
      $this->message = $msg;
      }
   }

class ErrorMessage {
   public $error;
   public $message;
   function __construct($code, $msg) {
      $this->error =   $code;
      $this->message = $msg;
      }
   }

function errorMessage($errorStatus) {
   return new ErrorMessage($errorStatus["code"], $errorStatus["msg"]);
   }

function readPortfolioDb($imageId) {
   global $portfolioFolder;
   $dbFile = $portfolioFolder . dbFileName($imageId);
   return readDb($dbFile);
   }

function updatePortfolioDb($imageId, $field, $value) {
   global $portfolioFolder;
   $dbFile = $portfolioFolder . dbFileName($imageId);
   $imageDb = readDb($dbFile);
   $imageDb->{$field} = $value;
   saveDb($dbFile, $imageDb);
   generateGalleryDb();
   return $imageDb;
   }

function readSettingsDb() {
   global $settingsDbFile;
   return readDb($settingsDbFile);
   }

function updateSettingsWebsite($field, $value) {
   global $settingsDbFile, $settingsFieldsBoolean, $settingsFieldsHtml;
   $settingsDb = readSettings($settingsDbFile);
   if ($field) {
      if (in_array($field, $settingsFieldsBoolean))
         $value = $value == "true";
      $settingsDb->{$field} = $value;
      if (in_array($field, $settingsFieldsHtml))
         $settingsDb->{$field . "-html"} = bbcodeToHtml($value);
      saveDb($settingsDbFile, $settingsDb);
      }
   return $settingsDb;
   }

function updateSettingsMenuBar($pageSelected, $pageAction, $pageTitle) {
   global $settingsDbFile, $settingsFieldPages;
   $settingsDb = readSettings($settingsDbFile);
   $pages = $settingsDb->{$settingsFieldPages};
   foreach ($pages as $page) {     //TODO: Delete this backwards compatibility workaround
      if (!isset($page->title))
         $page->title = $page->name;
      $page->name = strtolower($page->name);
      unset($page->page);
      }
   $msg = null;
   foreach ($pages as $slot => $page)
      if ($page->name == $pageSelected)
         switch ($pageAction) {
            case "save": $page->title = $pageTitle; break;
            case "up":   $msg = "Sorry, move up is not ready yet."; break;
            case "down": $msg = "Sorry, move down is not ready yet."; break;
            case "show": $page->show = true; break;
            case "hide": $page->show = false; break;
            case "edit": $msg = "Temporary fix is to edit \"data/page-$pageSelected.html\" file."; break;
            case "del":  $msg = "Sorry, delete is not ready yet."; break;
            }
   if ($pageAction && !$msg && !saveDb($settingsDbFile, $settingsDb))
      $msg = "Error saving $pageAction for $pageSelected";
   if ($msg)
      return new ErrorMessage(103, "Menu bar not updated [$msg]");
   else
      return $pages;
   }

function authentication() {
   global $authTimestamp, $sessionTimout;
   if (isset($_SESSION[$authTimestamp]) && time() - $_SESSION[$authTimestamp] > $sessionTimout)
      session_unset();
   $auth = isset($_SESSION[$authTimestamp]);
   if ($auth)
      $_SESSION[$authTimestamp] = time();
   return $auth;
   }

function webService() {
   $action = $_GET["action"];
   $id =     $_GET["id"];
   $field =  $_GET["field"];
   $task =   $_GET["task"];
   $page =   $_GET["page"];
   $value =  htmlspecialchars($_GET["value"]);
   switch ($action) {
      case "portfolio":
         if ($field)
            $responce = updatePortfolioDb($id, $field, $value);
         else
            $responce = readPortfolioDb($id);
         break;
      case "settings":
         $responce = updateSettingsWebsite($field, $value);
         break;
      case "menu-bar":
         $responce = updateSettingsMenuBar($page, $task, $value);
         break;
      case "logout":
         session_unset();
         $responce = new StatusMessage("Sign out complete");
         break;
      default:
         $responce = new ErrorMessage(101, "Invalid action");
      }
   return $responce;
   }

header("Cache-Control: no-cache");
header("Content-Type:  application/json");
if (authentication())
   echo json_encode(webService());
else
   echo json_encode(errorMessage(ErrorStatus::$authFail));
?>
