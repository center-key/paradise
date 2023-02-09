<?php
///////////////////////////////////////////////////////////////
// Paradise ~ centerkey.com/paradise                         //
// GPLv3 ~ Copyright (c) Individual contributors to Paradise //
///////////////////////////////////////////////////////////////

// PHP 5.5.0: array_column()
if (!function_exists("array_column")) {
   function array_column($array, $key) {
      return array_map(function($item) use ($key) { return $item->{$key}; }, $array);
      }
   }

// PHP 8.0.0: str_contains()
if (!function_exists("str_contains")) {
   function str_contains($haystack, $needle) {
      return strpos($haystack, $needle) !== false;
      }
   }

// PHP 8.0.0: str_ends_with()
if (!function_exists("str_ends_with")) {
   function str_ends_with($haystack, $needle) {
      return substr($haystack, -strlen($needle)) === $needle;
      }
   }

?>
