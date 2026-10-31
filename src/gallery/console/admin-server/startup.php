<?php
///////////////////////////////////////////////////////////////
// Paradise ~ centerkey.com/paradise                         //
// GPLv3 ~ Copyright (c) Individual contributors to Paradise //
///////////////////////////////////////////////////////////////

// Startup
// Initializes the data folder

$defaultSettingsDb = (object)[  //see: frontend-server/gallery.php:migrateSettings($settings)
   "title" =>              "My Gallery",
   "titleFont" =>          "Reenie Beanie",
   "titleSize" =>          "400%",
   "subtitle" =>           "Photography &bull; Art Studio",
   "darkMode" =>           true,
   "imageBorder" =>        true,
   "showDescription" =>    false,
   "captionCaps" =>        false,
   "captionItalic" =>      true,
   "stampIcon" =>          "star",
   "stampTitle" =>         "",
   "ccLicense" =>          false,
   "bookmarks" =>          true,
   "linkUrl" =>            getRootUrl(),
   "footer" =>             "&copy; " . gmdate("Y") . " The Artist",
   "googleVerification" => "",
   "contactEmail" =>       "",
   "pages" => [
      (object)["name" => "gallery", "title" => "Gallery", "show" =>  true],
      (object)["name" => "artist",  "title" => "Artist",  "show" =>  false],
      (object)["name" => "contact", "title" => "Contact", "show" =>  false],
      ],
   ];
$defaultAccountsDb = (object)[
   "users" =>   (object)[],  //keys: email, fields: email, created, hash, enabled, login, valid
   "invites" => (object)[],  //keys: inviteCode, fields: from, to, expires, accepted
   ];

function setupHiddenFolder($parentFolder, $name) {
   $root =       "{$parentFolder}/{$name}-";
   $pattern =    $root . "*";
   $fileSearch = glob($pattern);
   $message =    "Unable to create folder, check permissions";
   if (count($fileSearch) === 0) {
      if (!mkdir($root . mt_rand() . mt_rand() . mt_rand()))
         logErrorExit(__FILE__, __FUNCTION__, $message, $parentFolder, $name);
      $fileSearch = glob($pattern);
      }
   return $fileSearch[0];
   }

function setupDb($dbFilename, $defaultDb) {
   initializeFile($dbFilename, json_encode($defaultDb));
   }

function resetCustomCssForMigration($filename) {
   $oldDefaultCss = [
      "/* Paradise Photo Gallery                                   */",
      "/* Edit this CSS file to customize the look of the gallery. */",
      "/* Put custom images in: gallery/~data~/graphics            */",
      "",
      "body { color: whitesmoke; background-color: dimgray; }",
      "body >footer { background-color: gray; border-color: black; }",
      ".gallery-images .image img { border-color: black; }",
      ];
   $old = implode(PHP_EOL, $oldDefaultCss);
   if (file_get_contents($filename) == $old)
      logEvent(__FILE__, __FUNCTION__, $filename, strlen($old), unlink($filename));
   }

function setupCustomCss() {
   global $dataFolder;
   $defaultCss = [
      "/* Paradise Photo Gallery                                   */",
      "/* Edit this CSS file to customize the look of the gallery. */",
      "/* Put custom images in: gallery/~data~/graphics            */",
      "",
      ];
   $filename = "{$dataFolder}/custom-style.css";
   resetCustomCssForMigration($filename);
   initializeFile($filename, implode(PHP_EOL, $defaultCss));
   }

function setupCustomPage($pageName) {
   global $dataFolder;
   $filename = "{$dataFolder}/page-{$pageName}.html";
   if (!file_exists($filename)) {
      $instructions = "Edit: " . realpath($filename);
      $defaultHtml =  ["<h2>This page is under construction.</h2>", "<hr>", $instructions];
      $status = file_put_contents($filename, implode(PHP_EOL, $defaultHtml) . PHP_EOL);
      logEvent(__FILE__, __FUNCTION__, $filename, $status);
      }
   }

function setupRobotTxt() {
   // Creates a default Robots Exclusion Protocol file to prevent 404 File not found errors.
   global $galleryFolder;
   $filename = "{$galleryFolder}/robots.txt";
   if (!file_exists($filename)) {
      $status = file_put_contents($filename, "# Allow all bots" . PHP_EOL);
      logEvent(__FILE__, __FUNCTION__, $filename, $status);
      }
   }

function migrateFiles() {
   // Performs one-time upgrades to work with the latest release.
   global $galleryFolder;
   $deprecatedList = [
      ".htaccess",
      "sitemap.php",
      "server",
      "console/server",
      ];
   function renameDeprecatedFile($filename) {
      if (file_exists($filename))
         logEvent(__FILE__, __FUNCTION__, $filename, rename($filename, $filename . "-DEPRECATED"));
      }
   foreach ($deprecatedList as $deprecated)
      renameDeprecatedFile("{$galleryFolder}/{$deprecated}");
   }

initializeFolder($dataFolder, true);
$uploadsFolder =   initializeFolder("{$dataFolder}/uploads", false);
$portfolioFolder = initializeFolder("{$dataFolder}/portfolio", true);
$secureFolder =    setupHiddenFolder($dataFolder, "secure");
$backupsFolder =   setupHiddenFolder($dataFolder, "backups");
$accountsDbFile =  "{$secureFolder}/accounts-db.json";
$settingsDbFile =  "{$dataFolder}/settings-db.json";
$galleryDbFile =   "{$dataFolder}/gallery-db.json";
migrateFiles();
setupDb($settingsDbFile, $defaultSettingsDb);
setupDb($accountsDbFile, $defaultAccountsDb);
setupCustomCss();
setupCustomPage($defaultSettingsDb->pages[1]->name);
setupRobotTxt();
generateGalleryDb();
?>
