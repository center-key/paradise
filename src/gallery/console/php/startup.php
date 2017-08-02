<?php
///////////////////////////////////////////////////
// Paradise ~ centerkey.com/paradise             //
// GPLv3 ~ Copyright (c) individual contributors //
///////////////////////////////////////////////////

// Startup
// Initializes the data folder

$defaultSettingsDb = array(
   "title" =>          "My Gallery",
   "title-font" =>     "Reenie Beanie",
   "title-size" =>     "400%",
   "subtitle" =>       "Photography &bull; Art Studio",
   "footer" =>         "Copyright &copy; " . gmdate("Y"),
   "caption-caps" =>   false,
   "caption-italic" => true,
   "cc-license" =>     false,
   "bookmarks" =>      true,
   "contact-email" =>  "",
   "pages" => array(
      array("name" => "gallery", "title" => "Gallery", "show" =>  true),
      array("name" => "artist",  "title" => "Artist",  "show" =>  false),
      array("name" => "contact", "title" => "Contact", "show" =>  false)
      )
   );
$defaultAccountsDb = array(
   "users" =>   json_decode("{}"),  //email -> created (epoch), hash, enabled (boolean)
   "invites" => json_decode("{}")   //inviteCode -> from, to, expires (epoch), accepted (epoch)
   );

function workaroundToUpgradeToNewSecureFolder($dataFolder, $secureFolder) {
   $fileSearch = glob("{$dataFolder}/key-*.txt");
   if (count($fileSearch) !== 0) {
      preg_match("/key-(.*)[.]txt/", $fileSearch[0], $matches);
      $installKey = $matches[1];
      rename("{$dataFolder}/accounts-db-{$installKey}.json", "{$secureFolder}/accounts-db.json");
      unlink("{$dataFolder}/key-{$installKey}.txt");
      }
   }

function setupSecureFolder($dataFolder) {
   $pattern = "{$dataFolder}/secure-*";
   $fileSearch = glob($pattern);
   if (count($fileSearch) === 0) {
      if (!mkdir("{$dataFolder}/secure-" . mt_rand() . mt_rand() . mt_rand()))
         exit("Unable to create secure data folder, check permissions in: {$dataFolder}");
      $fileSearch = glob($pattern);
      }
   workaroundToUpgradeToNewSecureFolder($dataFolder, $fileSearch[0]);
   return $fileSearch[0];
   }

function setupDb($dbFilename, $defaultDb) {
   initializeFile($dbFilename, json_encode($defaultDb));
   }

function setupCustomCss($dataFolder) {
   $defaultCss = array(
      "/*  Paradise PHP Photo Gallery                                */",
      "/*  Edit this CSS file to customize the look of the gallery.  */",
      "/*  Put custom images in: gallery/~data~/graphics             */",
      "",
      "body { color: whitesmoke; background-color: dimgray; }",
      "body >footer { background-color: gray; border-color: black; }",
      ".gallery-images .image img { border-color: black; }"
      );
   $filename = "{$dataFolder}/custom-style.css";
   initializeFile($filename, implode(PHP_EOL, $defaultCss));
   }

function setupCustomPage($dataFolder, $pageName) {
   $filename = "{$dataFolder}/page-{$pageName}.html";
   if (!file_exists($filename)) {
      $defaultHtml = "<h3>This page is under construction.</h3>\n<hr>\nEdit: ";
      touch($filename);
      file_put_contents($filename, $defaultHtml . realpath($filename) . PHP_EOL);
      }
   }

initializeFolder($dataFolder, true);
$uploadsFolder =   initializeFolder("{$dataFolder}/uploads", false);
$portfolioFolder = initializeFolder("{$dataFolder}/portfolio", true);
$secureFolder =    setupSecureFolder($dataFolder);
$accountsDbFile =  "{$secureFolder}/accounts-db.json";
$settingsDbFile =  "{$dataFolder}/settings-db.json";
$galleryDbFile =   "{$dataFolder}/gallery-db.json";
setupDb($settingsDbFile, $defaultSettingsDb);
setupDb($accountsDbFile, $defaultAccountsDb);
setupCustomCss($dataFolder);
setupCustomPage($dataFolder, $defaultSettingsDb["pages"][1]["name"]);
generateGalleryDb();
?>
