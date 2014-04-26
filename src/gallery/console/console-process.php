<?php
/////////////////////////////////////////////////
// PPAGES ~ www.centerkey.com/ppages           //
// GPL ~ Copyright (c) individual contributors //
/////////////////////////////////////////////////

// Process

function createDataFolders() {
   foreach(func_get_args() as $dataFolder)
      if (!is_dir($dataFolder))
         if (mkdir($dataFolder, 0777))  //owner: read/write, everyone else: read 0644
            echo "<div>Created Folder: $dataFolder</div>\n";
         else
            echo "<div>ERROR: Cannot write to the <b>data</b> folder ($dataFolder).</div>\n";
   }

function createCustomCss() {
   global $customCssFile;
   $customCss =
      "/* PHP Portfolio Art Gallery Exhibit Showcase (PPAGES)   */\n" .
      "/* Edit this CSS file to customize the look of the       */\n" .
      "/* gallery.  Put custom images in the 'graphics' folder. */\n" .
      "/* Find colors at: http://www.centerkey.com/colors       */\n\n" .
      "body           { color: whitesmoke; background-color: dimgray; }\n" .
      "div.image img  { border-color: black; }\n" .
      "div.footer     { background-color: #777; border-color: black; }";
   if (!is_file($customCssFile))
      file_put_contents($customCssFile, $customCss);
   }

function createMaskDataFile() {
   global $maskDataFile;
   if (!is_file($maskDataFile))
      file_put_contents($maskDataFile, "<!doctype html><html><head>" .
         "<meta http-equiv=refresh content='1; url=..'></head><body></html>\n");
   }

function startup() {
   global $uploadsFolder, $portfolioFolder, $graphicsFolder;
   createDataFolders($uploadsFolder, $portfolioFolder, $graphicsFolder);
   createCustomCss();
   createMaskDataFile();
   }

function displayProcessStatus() {
   global $actionField, $actionUpdateImage, $actionDeleteImage,
      $actionUpdateSettings, $actionUpdateMenuBar, $actionReprocessImages,
      $actionChangePassword, $actionCreateAccount;
   echo "<div id=status-msg>";
   startup();
   processUploadsFolder();
   foreach ($_POST as $field=>$value)
      $_POST[$field] = htmlspecialchars($value, ENT_QUOTES, "UTF-8");  //make safe
   switch ($_POST[$actionField]) {
      //case $actionUpdateImage:     processUpdateImageDb();    break;
      //case $actionDeleteImage:     processDeleteImageDb();    break;
      //case $actionUpdateSettings:  processUpdateSettings();   break;
      case $actionUpdateMenuBar:   processUpdateMenuBar();    break;
      case $actionReprocessImages: processReprocessImages();  break;
      case $actionChangePassword:  processChangePassword();   break;
      case $actionCreateAccount:   processCreateAccount();    break;
      }
   echo "</div>";
   }

?>
