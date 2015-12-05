<?php
/////////////////////////////////////////////////
// PPAGES ~ centerkey.com/ppages               //
// GPL ~ Copyright (c) individual contributors //
/////////////////////////////////////////////////

// Database

function dbFileName($dbName) {
   return $dbName . "-db.json";
   }

function createEmptyDb() {
   return json_decode("{}");
   }

function readDb($dbFile) {
   return is_file($dbFile) ?
      json_decode(file_get_contents($dbFile)) : createEmptyDb();
   }

function saveDb($dbFile, $db) {
   logEvent("save-db", $dbFile);
   return file_put_contents($dbFile, json_encode($db));
   }

?>
