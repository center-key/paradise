<?php
///////////////////////////////////////////////////////////////
// Paradise ~ centerkey.com/paradise                         //
// GPLv3 ~ Copyright (c) individual contributors to Paradise //
///////////////////////////////////////////////////////////////

// REST Web Services
//
// Example read resource:
//    HTTP GET gallery/console/rest?resource=gallery
// Update value:
//    HTTP GET gallery/console/rest?resource=settings&action=update&caption-italic=true
//
// Resource   Action
// ---------  ------
// security   login, create
// command    process-uploads, generate-gallery
// settings   get, update
// gallery    get
// portfolio  get, update, delete, list
// account    list
// invite     list, create
// backup     list, create
//
// Note:
//    Query parameters are used instead of path parameters to avoid the need for
//    URL (.htaccess) configuration.

$noAuth = true;
require "../server/security.php";
require "../server/image-processing.php";

function restError($code) {
   $messages = array(
      400 => "Invalid parameters",
      401 => "Unauthorized access",
      402 => "Missing in action",
      404 => "Resource not found",
      500 => "Unknown error",
      501 => "Not implemented"
      );
   return array(
      "error"   => true,
      "code"    => $code,
      "message" => $messages[$code]
      );
   }

function runRoute($routes, $action) {
   return isset($routes[$action]) ? $routes[$action]() : restError(402);
   }

function test() {
   // URL: http://localhost/paradise-deploy/gallery/console/rest?resource=command&action=test
   return array("test" => true, "timestamp" => date("c"));
   }

function runCommand($action) {
   if ($action == "test")
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
      $move = $_GET["move"];
      if ($move)
         $resource->sort = calcNewPortfolioSort($resource->sort, $move === "up");
      savePortfolioImageDb($resource);
      generateGalleryDb();
      }
   return $resource ?: restError(404);
   }

function deletePortfolio($id) {
   $resource = readPortfolioImageDb($id);
   if (!$_SESSION["read-only-user"] && $resource) {
      deleteImages($id);
      generateGalleryDb();
      }
   return $resource ?: restError(404);
   }

function restRequestSettings($action) {
   return $action === "update" ? updateSettings() : readSettingsDb();
   }

function restRequestGallery() {
   return readGalleryDb();
   }

function restRequestPortfolio($action, $id) {
   $routes = array(
      "create" => function($id) { return restError(400); },
      "get" =>    function($id) { return restError(501); },
      "update" => function($id) { return updatePortfolio($id); },
      "delete" => function($id) { return deletePortfolio($id); },
      "list" =>   function($id) { return readPortfolioDb(); }
      );
   return $routes[$action]($id);
   }

function restRequestAccount($action, $email) {
   return array_keys(get_object_vars(readAccountsDb()->users));
   }

function restRequestBackup($action) {
   function actionCreate() {
      global $backupsFolder;
      $start = timeMillis();
      $settings = readSettingsDb();
      $accounts = readAccountsDb();
      function getInvitee($invite) { return $invite->to . ($invite->accepted ? " [accepted]" : ""); }
      $admins =   implode(PHP_EOL, array_keys(get_object_vars($accounts->users)));
      $invitees = implode(PHP_EOL, array_map("getInvitee", get_object_vars($accounts->invites)));
      $userList = date("c") . "\n\nAdministrators:\n" . $admins . "\n\nInvitations:\n" . $invitees;
      $titleWord = strtolower(explode(" ", trim($settings->title))[0]);
      $filename = $titleWord . "-" . date("Y-m-d-Hi") . ".zip";
      logEvent("backup-start", $filename);
      $url = "../~data~/" . basename($backupsFolder) . "/" . $filename;
      $zip = new ZipArchive;
      if ($zip->open("{$backupsFolder}/{$filename}", ZipArchive::CREATE) === TRUE) {
         $zip->addGlob("../../~data~/*.css");
         $zip->addGlob("../../~data~/*.json");
         $zip->addGlob("../../~data~/*.html");
         $zip->addGlob("../../~data~/portfolio/*.json");
         $zip->addGlob("../../~data~/portfolio/*-small.png");
         $zip->addGlob("../../~data~/portfolio/*-large.jpg");
         $zip->deleteName("../../~data~/index.html");
         $zip->addFromString("users.txt", $userList);
         $count = $zip->numFiles;
         $zip->close();
         }
      $milliseconds = timeMillis() - $start;
      logEvent("backup-end", $filename, "files: " . $count, "milliseconds: " . $milliseconds);
      return array("filename" => $filename, "url" => $url, "seconds" => $milliseconds / 1000);
      }
   function actionList() {
      global $backupsFolder;
      $maxNumBackups = 5;
      $files = array_reverse(glob($backupsFolder . "/*.zip"));
      if (count($files) > $maxNumBackups)
         unlink(array_pop($files));
      $toObj = function($file) {
         $url = "../~data~/" . basename(dirname($file)) . "/" . basename($file);
         return array("filename" => basename($file), "url" => $url);
         };
      return array_map($toObj, $files);
      }
   $routes = array(
      "create" => actionCreate,
      "list" =>   actionList,
      );
   return runRoute($routes, $action);
   }

function resource($loggedIn) {
   $routes = array(
      "settings" =>  function($action) { return restRequestSettings($action); },
      "gallery" =>   function($action) { return restRequestGallery(); },
      "portfolio" => function($action) { return restRequestPortfolio($action, $_GET["id"]); },
      "account" =>   function($action) { return restRequestAccount($action, $_GET["email"]); },
      "invite" =>    function($action) { return restRequestInvite($action, $_GET["email"]); },
      "backup" =>    function($action) { return restRequestBackup($action); },
      );
   $httpMethod = $_SERVER['REQUEST_METHOD'];
   $name =       $_GET["resource"];
   $action =     $_GET["action"] ?: "get";
   $_GET["email"] = strtolower(trim($_GET["email"]));
   $standardAction = in_array($action, array("create", "get", "update", "delete", "list"));
   if ($httpMethod === "POST")
      $httpBodyRaw = file_get_contents("php://input");
   if ($httpMethod === "POST")
      $httpBody = json_decode($httpBodyRaw);
   if ($name === "security")
      $resource = restRequestSecurity($action, $httpBody);
   elseif (!$loggedIn)
      $resource = restError(401);
   elseif ($name === "command")
      $resource = runCommand($action);
   elseif (isset($routes[$name]) && $standardAction)
      $resource = $routes[$name]($action);
   else
      $resource = restError(400);
   logEvent("rest-resource", $httpMethod, $httpMethod === "POST", $httpBodyRaw, $name, $action, $_GET["id"], !getProperty($resource, "error"));
   return $resource;
   }

httpJsonResponse(resource($loggedIn));
?>
