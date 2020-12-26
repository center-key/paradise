<?php
///////////////////////////////////////////////////////////////
// Paradise ~ centerkey.com/paradise                         //
// GPLv3 ~ Copyright (c) individual contributors to Paradise //
///////////////////////////////////////////////////////////////

// Administrator Console
// Business logic

function getOutstandingInvites() {
   if (readOnlyMode())
      return $resource = (object)array(array("to" => "lee@example.com", "date" => date("Y-m-d")));
   return array_values(array_filter(array_values((array)readAccountsDb()->invites), "outstanding"));
   }

function getBackupFiles() {
   global $backupsFolder;
   $maxNumBackups = 3;
   $files = glob($backupsFolder . "/*.zip");
   function getTimestamp($filename) { return substr($filename, -19, 15); }  //travel-2018-09-05-0758.zip
   function newest($filenameA, $filenameB) {
      return strcmp(getTimestamp($filenameB), getTimestamp($filenameA));
      }
   usort($files, "newest");
   if (count($files) > $maxNumBackups)
      unlink(array_pop($files));
   function toObjBackup($file) {
      $url = "../~data~/" . basename(dirname($file)) . "/" . basename($file);
      return (object)array("filename" => basename($file), "url" => $url);
      };
   if (readOnlyMode())
      $files = array();
   return array_map("toObjBackup", $files);
   }

function appClientData() {
   global $version, $googleFonts;
   $settings = readSettingsDb();
   $data = (object)array(
      "version" =>       $version,
      "php" =>           phpversion(),
      "user" =>          getCurrentUser(),
      "server" =>        $_SERVER["SERVER_NAME"],
      "userListEmpty" => emptyObj(readAccountsDb()->users),
      "title" =>         $settings->title,
      "titleSize" =>     $settings->titleSize,
      "fonts" =>         $googleFonts,
      "invites" =>       getCurrentUser() ? getOutstandingInvites() : array(),
      "backupFiles" =>   getCurrentUser() ? getBackupFiles() : array(),
      );
   return json_encode($data);
   }

?>
