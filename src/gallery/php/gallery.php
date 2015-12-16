<?php
/////////////////////////////////////////////////
// PPAGES ~ centerkey.com/ppages               //
// GPL ~ Copyright (c) individual contributors //
/////////////////////////////////////////////////

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

function displayCustomPage($filename) {
   if (!file_exists($filename)) {
      $defaultHtml = "<h3>This page is under construction.</h3>\n<hr>\nEdit: ";
      touch($filename);
      file_put_contents($filename, $defaultHtml . realpath($filename) . PHP_EOL);
      }
   readfile($filename);
   }

?>
