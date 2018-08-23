<?php
///////////////////////////////////////////////////////////////
// Paradise ~ centerkey.com/paradise                         //
// GPLv3 ~ Copyright (c) individual contributors to Paradise //
///////////////////////////////////////////////////////////////

function getData($dbFilename) {
   if (!is_file($dbFilename))
      exit("Setup incomplete");
   return json_decode(file_get_contents($dbFilename));
   }

function showHideClass($show) {
   return $show ? "show-me" : "hide-me";
   }

function styleClasses($settings) {
   function getClass($settings, $property) { return $settings->{$property} ? $property : ""; }
   return getClass($settings, "caption-italic") . " " . getClass($settings, "caption-caps");
   }

function displayImage($image) {
   $badge = empty($image->badge) ? "" : "<div class=badge>{$image->badge}</div>";
   $imageTitle =
      "<span class=image-caption>{$image->caption}</span>" .
      "<span class=image-description>{$image->description}</span>";
   echo "
      <div class=image>
         {$badge}
         <a href=~data~/portfolio/{$image->id}-large.jpg
            data-title='{$imageTitle}'>
            <img src=~data~/portfolio/{$image->id}-small.png alt=thumbnail>
         </a>
         <p class=image-caption>
            {$image->caption}
            <a href=image/{$image->id}/{$image->code} class=plain><i data-icon=link></i></a>
         </p>
      </div>";
   }

function displayImages($gallery) {
   if (empty($gallery))
      echo "<h3>Gallery is empty</h3>";
   else
      foreach ($gallery as $image)
         displayImage($image);
   }

function getImageInfo($uri, $gallery) {
   preg_match("/\/image\/([0-9]+)/", $uri, $matches);
   $id = $matches[1];
   $dbFilename = __DIR__ . "/../~data~/portfolio/{$id}-db.json";
   if (is_file($dbFilename))
      $imageDb = json_decode(file_get_contents($dbFilename));
   else
      $imageDb = json_decode('{ "caption": "That image does not appear to exist", "description": "" }');
   return [$id, $imageDb->caption, $imageDb->description];
   }

$settings = getData(__DIR__ . "/../~data~/settings-db.json");
$gallery =  getData(__DIR__ . "/../~data~/gallery-db.json");
$pages = $settings->pages;
?>
