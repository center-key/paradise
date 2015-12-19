<?php
/////////////////////////////////////////////////
// PPAGES ~ centerkey.com/ppages               //
// GPL ~ Copyright (c) individual contributors //
/////////////////////////////////////////////////

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
   echo "
      <div class=image>
         {$badge}
         <a rel=lightbox-gallery href='data/portfolio/{$image->id}-large.jpg'
            title='<span class=image-caption>{$image->caption}</span><br>{$image->description}'>
            <img src='data/portfolio/{$image->id}-small.png' alt='Thumbnail'
               title='Click for full size, and right arrow to advance'>
         </a>
         <p class=image-caption>{$image->caption}</p>
      </div>";
   }

function displayImages($gallery) {
   if (empty($gallery))
      echo "<h3>Gallery is empty</h3>";
   else
      foreach ($gallery as $image)
         displayImage($image);
   }

$settings = getData("data/settings-db.json");
$gallery =  getData("data/gallery-db.json");
$pages = $settings->pages;
?>
