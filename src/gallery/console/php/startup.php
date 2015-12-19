<?php
/////////////////////////////////////////////////
// PPAGES ~ centerkey.com/ppages               //
// GPL ~ Copyright (c) individual contributors //
/////////////////////////////////////////////////

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

function setupDataFolder($dataFolder, $name) {
   $folder =      "{$dataFolder}/{$name}";
   $defaultPage = "{$dataFolder}/{$name}/index.html";
   if (!is_dir($folder) && !mkdir($folder, 0777))
      exit("Unable to create data folder: {$folder}");
   if (!is_file($defaultPage) && !file_put_contents($defaultPage, "Nothing to see here."))
      exit("Unable to write to data folder: {$defaultPage}");
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

date_default_timezone_set("UTC");
foreach(["", "graphics", "portfolio", "uploads"] as $name)
   setupDataFolder($dataFolder, $name);
$installKey = setupInstallKey($dataFolder);
setupDb($settingsDbFile, $defaultSettingsDb);
setupDb($galleryDbFile, []);
?>
