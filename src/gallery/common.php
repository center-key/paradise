<?php
///////////////////////////////////////////////////////////////
// Paradise ~ centerkey.com/paradise                         //
// GPLv3 ~ Copyright (c) Individual contributors to Paradise //
///////////////////////////////////////////////////////////////

// Common
// Utilities and functions shared between the front-end and the console.

$minimumPhpVersion = '7.0.0';
$obsoletePhp =       version_compare(PHP_VERSION, $minimumPhpVersion, "<");

function polyfills() {
   if (!function_exists("array_column")) {     //polyfill: array_column(), PHP 5.5.0
      function array_column($array, $key) {
         return array_map(function($item) use ($key) { return $item->{$key}; }, $array);
         }
      }
   if (!function_exists("str_contains")) {     //polyfill str_contains(), PHP 8.0.0
      function str_contains($haystack, $needle) {
         return strpos($haystack, $needle) !== false;
         }
      }
   if (!function_exists("str_ends_with")) {    //polyfill: str_ends_with(), PHP 8.0.0:
      function str_ends_with($haystack, $needle) {
         return substr($haystack, -strlen($needle)) === $needle;
         }
      }
   if (!function_exists('str_starts_with')) {  //polyfill: str_starts_with(), PHP 8.0.0
      function str_starts_with($haystack, $needle) {
         return strlen($needle) === 0 || strpos($haystack, $needle) === 0;
         }
      }
   }
polyfills();

function getGalleryUrl() {
   // Example: https://example.com/travel/gallery
   $tls =      isset($_SERVER["HTTPS"]) && strtolower($_SERVER["HTTPS"]) !== "off";
   $protocol = $tls ? "https://" : "http://";
   $ignore =   array("/index.php", "/image/fallback/one.php", "/frontend-server/send-message.php");
   return $protocol . $_SERVER["SERVER_NAME"] . str_replace($ignore, "", $_SERVER["SCRIPT_NAME"]);
   }

function getRootUrl() {
   // Example: https://example.com/travel
   $galleryUrl = getGalleryUrl();
   $url =        dirname($galleryUrl);
   return str_ends_with($url, ":") ? $galleryUrl : $url;
   }

function toCamelCase($kebabCase) {
   // Example: "dark-mode" --> "darkMode"
   $camelCase =    str_replace(' ', '', ucwords(str_replace('-', ' ', $kebabCase)));
   $camelCase[0] = strtolower($camelCase[0]);
   return $camelCase;
   }

function toUriCode($caption) {
   // Turns a caption, like "Mona Lisa (1503)", into a URL safe string, like "mona-lisa-1503".
   $code = preg_replace("/\s+/", "-", trim(preg_replace("/[^a-z0-9]/", " ", strtolower($caption))));
   return empty($code) ? "one-image" : $code;
   }

function migrateSettings($settings) {  //see: console/admin-server/startup.php:$defaultSettingsDb
   foreach($settings as $key => $value) {
      $newKey = toCamelCase($key);
      if ($newKey !== $key && !isset($settings->$newKey) && isset($settings->$key)) {
         $settings->$newKey = $value;
         unset($settings->$key);
         }
      }
   if (!isset($settings->darkMode))
      $settings->darkMode = true;
   if (!isset($settings->imageBorder))
      $settings->imageBorder = true;
   if (!isset($settings->showDescription))
      $settings->showDescription = false;
   if (!isset($settings->stampIcon))
      $settings->stampIcon = "star";
   if (!isset($settings->stampTitle))
      $settings->stampTitle = "";
   if (!isset($settings->linkUrl))
      $settings->linkUrl = getRootUrl();
   return $settings;
   }

?>
