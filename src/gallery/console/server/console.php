<?php
///////////////////////////////////////////////////////////////
// Paradise ~ centerkey.com/paradise                         //
// GPLv3 ~ Copyright (c) individual contributors to Paradise //
///////////////////////////////////////////////////////////////

// Console

$googleFonts = array(  //see https://fonts.google.com
   "Allan", "Allerta Stencil", "Amatic SC", "Anonymous Pro", "Arimo", "Arvo",
   "Bowlby One SC", "Bubblegum Sans",
   "Cherry Cream Soda", "Chewy", "Chango", "Coda", "Corben",
   "Devonshire",
   "Emilys Candy",
   "Galindo", "Geo", "Graduate", "Gruppo",
   "Homemade Apple",
   "Irish Grover",
   "Josefin Sans", "Jura", "Just Another Hand",
   "Kenia", "Kristi",
   "League Script", "Life Savers", "Lobster", "Londrina Outline", "Londrina Solid", "Love Ya Like A Sister",
   "Mouse Memoirs",
   "Neucha",
   "Old Standard TT",
   "Open Sans", "Orbitron",
   "Pacifico", "Philosopher",
   "Reenie Beanie", "Rock Salt",
   "Sail", "Six Caps", "Slackey", "Sniglet", "Special Elite", "Syncopate",
   "Tangerine",
   "UnifrakturMaguntia",
   "Vibur"
   );

function displayFontOptions() {
   global $googleFonts;
   $toHtml = function($font) {
      return "<option value='{$font}'>{$font}</option>";
      };
   return implode("\n", array_map($toHtml, $googleFonts));
   }

function importFonts() {
   global $googleFonts;
   $toHtml = function($font) {
      $fontParam = urlencode($font);
      return "@import url(https://fonts.googleapis.com/css?family={$fontParam});";
      };
   return implode("\n", array_map($toHtml, $googleFonts));
   }

function displayTitles() {
   global $googleFonts, $dataFolder;
   $settings =  readDb("{$dataFolder}/settings-db.json");
   $title =     $settings->title;
   $titleSize = $settings->{"title-size"};
   $toHtml = function($font) use ($title, $titleSize) {
      $style = "\"font-size: {$titleSize}; font-family: '{$font}'\"";
      return "<tr><td>{$font}</td><td><h1 style={$style}>{$title}</h1></td></tr>";
      };
   return implode("\n", array_map($toHtml, $googleFonts));
   }

?>
