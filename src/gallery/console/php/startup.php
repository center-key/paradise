<?php
///////////////////////////////////////////////////
// PPAGES ~ centerkey.com/ppages                 //
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
   "caption-caps" =>   true,
   "caption-italic" => false,
   "cc-license" =>     false,
   "bookmarks" =>      true,
   "contact-email" =>  "",
   "pages" => array(
      array("name" => "gallery", "title" => "Gallery", "show" =>  true),
      array("name" => "artist",  "title" => "Artist",  "show" =>  false),
      array("name" => "contact", "title" => "Contact", "show" =>  true)
      )
   );
$defaultGalleryDb = array();
$defaultAccountsDb = array(
   "users" =>   array(),  //email -> created (epoch), hash, enabled (boolean)
   "invites" => array()   //inviteCode -> from, to, expires (epoch), accepted (epoch)
   );

function setupDataFolder($dataFolder, $name) {
   $folder =      "{$dataFolder}/{$name}";
   $defaultView = "{$dataFolder}/index.html";
   if (!is_dir($folder) && !mkdir($folder))
      exit("Unable to create data folder: {$folder}");
   if (!is_file($defaultView) && !file_put_contents($defaultView, "Nothing to see."))
      exit("Unable to write into data folder: {$defaultView}");
   }

function setupInstallKey($folder) {
   $fileSearch = glob("{$folder}/key-*.txt");
   if (count($fileSearch) === 0) {
      $fileSearch[] = "{$folder}/key-" . mt_rand() . mt_rand() . mt_rand() . ".txt";
      touch($fileSearch[0]);
      }
   preg_match("/key-(.*)[.]txt/", $fileSearch[0], $matches);
   return $matches[1];
   }

function setupDb($dbFilename, $defaultDb) {
   if (!is_file($dbFilename) && !file_put_contents($dbFilename, json_encode($defaultDb)))
      exit("Error creating database: {$dbFilename}");
   }

function setupCustomCss($dataFolder) {
   $defaultCss = array(
      "/*  PPAGES - PHP Portfolio Art Gallery Exhibit to Showcase    */",
      "/*  Edit this CSS file to customize the look of the gallery.  */",
      "/*  Put custom images in: gallery/data/graphics               */",
      "",
      "body { color: whitesmoke; background-color: dimgray; }",
      "body >footer { background-color: gray; border-color: black; }",
      ".gallery-images .image img { border-color: black; }"
      );
   $filename = "{$dataFolder}/custom-style.css";
   if (!is_file($filename) && !file_put_contents($filename, implode(PHP_EOL, $defaultCss)))
      exit("Error creating CSS file: {$filename}");
   }

function setupCustomPage($dataFolder, $pageName) {
   $filename = "{$dataFolder}/page-{$pageName}.html";
   if (!file_exists($filename)) {
      $defaultHtml = "<h3>This page is under construction.</h3>\n<hr>\nEdit: ";
      touch($filename);
      file_put_contents($filename, $defaultHtml . realpath($filename) . PHP_EOL);
      }
   }

date_default_timezone_set("UTC");
foreach(["", "graphics", "portfolio", "uploads"] as $name)
   setupDataFolder($dataFolder, $name);
$installKey = setupInstallKey($dataFolder);
$settingsDbFile = "{$dataFolder}/settings-db.json";
$galleryDbFile =  "{$dataFolder}/gallery-db.json";
$accountsDbFile = "{$dataFolder}/accounts-db-{$installKey}.json";
setupDb($settingsDbFile, $defaultSettingsDb);
setupDb($galleryDbFile,  $defaultGalleryDb);
setupDb($accountsDbFile, $defaultAccountsDb);
setupCustomCss($dataFolder);
setupCustomPage($dataFolder, $defaultSettingsDb["pages"][1]["name"]);
?>
