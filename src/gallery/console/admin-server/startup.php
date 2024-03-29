<?php
///////////////////////////////////////////////////////////////
// Paradise ~ centerkey.com/paradise                         //
// GPLv3 ~ Copyright (c) Individual contributors to Paradise //
///////////////////////////////////////////////////////////////

// Startup
// Initializes the data folder

$defaultSettingsDb = (object)array(  //see: frontend-server/gallery.php:migrateSettings($settings)
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
   "pages" => array(
      (object)array("name" => "gallery", "title" => "Gallery", "show" =>  true),
      (object)array("name" => "artist",  "title" => "Artist",  "show" =>  false),
      (object)array("name" => "contact", "title" => "Contact", "show" =>  false),
      ),
   );
$defaultAccountsDb = (object)array(
   "users" =>   (object)array(),  //key: email, fields: email, created, hash, enabled, login, valid
   "invites" => (object)array(),  //key: inviteCode, fields: from, to, expires, accepted
   );

function setupHiddenFolder($parentFolder, $name) {
   $root =       "{$parentFolder}/{$name}-";
   $pattern =    $root . "*";
   $fileSearch = glob($pattern);
   if (count($fileSearch) === 0) {
      if (!mkdir($root . mt_rand() . mt_rand() . mt_rand()))
         logAndExit("Unable to create {$name} folder, check permissions in: {$parentFolder}");
      $fileSearch = glob($pattern);
      }
   return $fileSearch[0];
   }

function setupDb($dbFilename, $defaultDb) {
   initializeFile($dbFilename, json_encode($defaultDb));
   }

function resetCustomCssForMigration($filename) {
   $oldDefaultCss = array(
      "/* Paradise Photo Gallery                                   */",
      "/* Edit this CSS file to customize the look of the gallery. */",
      "/* Put custom images in: gallery/~data~/graphics            */",
      "",
      "body { color: whitesmoke; background-color: dimgray; }",
      "body >footer { background-color: gray; border-color: black; }",
      ".gallery-images .image img { border-color: black; }",
      );
   $old = implode(PHP_EOL, $oldDefaultCss);
   if (file_get_contents($filename) == $old)
      logEvent("reset-custom-css-for-migration", $filename, strlen($old), unlink($filename));
   }

function setupCustomCss($dataFolder) {
   $defaultCss = array(
      "/* Paradise Photo Gallery                                   */",
      "/* Edit this CSS file to customize the look of the gallery. */",
      "/* Put custom images in: gallery/~data~/graphics            */",
      "",
      );
   $filename = "{$dataFolder}/custom-style.css";
   resetCustomCssForMigration($filename);
   initializeFile($filename, implode(PHP_EOL, $defaultCss));
   }

function setupCustomPage($dataFolder, $pageName) {
   $filename = "{$dataFolder}/page-{$pageName}.html";
   if (!file_exists($filename)) {
      $defaultHtml = "<h2>This page is under construction.</h2>\n<hr>\nEdit: ";
      touch($filename);
      file_put_contents($filename, $defaultHtml . realpath($filename) . PHP_EOL);
      }
   }

function migrateFiles() {
   // Performs one-time upgrades to work with the latest release.
   global $galleryFolder;
   $deprecatedList = array(
      ".htaccess",
      "sitemap.php",
      "server",
      "console/server",
      );
   function renameDeprecatedFile($filename) {
      if (file_exists($filename))
         logEvent("rename-deprecated-file", $filename, rename($filename, $filename . "-DEPRECATED"));
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
setupCustomCss($dataFolder);
setupCustomPage($dataFolder, $defaultSettingsDb->pages[1]->name);
generateGalleryDb();
?>
