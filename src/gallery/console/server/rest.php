<?php
///////////////////////////////////////////////////////////////
// Paradise ~ centerkey.com/paradise                         //
// GPLv3 ~ Copyright (c) individual contributors to Paradise //
///////////////////////////////////////////////////////////////

// REST
// JSON-only zone

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
   $data = array("timestamp" => date("c"), "read-only" => readOnlyMode());
   return array("test" => true, "data" => $data);
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
   $value = str_replace("<", "&lt;", str_replace(">", "&gt;", trim($value)));
   if ($type === "boolean")
      $value = $value === "true";
   elseif ($type === "integer")
      $value = intval($value);
   return $value;
   }

function updateItem($resource, $itemType) {
   if ($itemType === "page") {
      $item = $resource->pages[fieldValue(getIdParam(), "integer") - 1];
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
   if (!readOnlyMode() && $resource) {
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
      $filename = fileSysFriendly($settings->title) . "-" . date("Y-m-d-Hi") . ".zip";
      logEvent("backup-start", $filename);
      $url = "../~data~/" . basename($backupsFolder) . "/" . $filename;
      $zip = new ZipArchive;
      if (readOnlyMode()) {
         $url = ".";
         $count = 10;
         sleep(2);
         }
      elseif ($zip->open("{$backupsFolder}/{$filename}", ZipArchive::CREATE) === TRUE) {
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
   $routes = array(
      "create" => function() { return actionCreate(); },
      "list" =>   function() { return getBackupFiles(); },
      );
   return runRoute($routes, $action);
   }

function getIdParam() {
   return isset($_GET["id"]) ? strtolower(trim($_GET["id"])) : "";
   }

function getEmailParam() {
   return isset($_GET["email"]) ? strtolower(trim($_GET["email"])) : "";
   }

function resource($loggedIn) {
   $routes = array(
      "settings" =>  function($action) { return restRequestSettings($action); },
      "gallery" =>   function($action) { return restRequestGallery(); },
      "portfolio" => function($action) { return restRequestPortfolio($action, getIdParam()); },
      "account" =>   function($action) { return restRequestAccount($action, getEmailParam()); },
      "invite" =>    function($action) { return restRequestInvite($action, getEmailParam()); },
      "backup" =>    function($action) { return restRequestBackup($action); },
      );
   $httpMethod = $_SERVER['REQUEST_METHOD'];
   $name =       $_GET["resource"];
   $action =     isset($_GET["action"]) ? $_GET["action"] : "get";
   $standardAction = in_array($action, array("create", "get", "update", "delete", "list"));
   if ($httpMethod === "POST")
      $httpBody = json_decode(file_get_contents("php://input"));
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
   logEvent("http-request", $httpMethod, $name, $action, !getProperty($resource, "error"));
   return $resource;
   }

?>
