<?php
///////////////////////////////////////////////////////////////
// Paradise ~ centerkey.com/paradise                         //
// GPLv3 ~ Copyright (c) individual contributors to Paradise //
///////////////////////////////////////////////////////////////

function getGalleryUrl() {
   $protocol = $_SERVER["HTTPS"] === "on" ? "https://" : "http://";
   $ignore = array("/index.php", "/image/one.php", "/send-message.php");
   return $protocol . $_SERVER["SERVER_NAME"] . str_replace($ignore, "", $_SERVER["SCRIPT_NAME"]);
   }

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

function getImagesHtml($gallery) {
   function toImageHtml($image) {
      $badge = empty($image->badge) ? "" : "<div class=badge>{$image->badge}</div>";
      $imageTitle =
         "<span class=image-caption>{$image->caption}</span>" .
         "<span class=image-description>{$image->description}</span>";
      return "
         <figure>
            {$badge}
            <a href=~data~/portfolio/{$image->id}-large.jpg
               data-title='{$imageTitle}'>
               <img src=~data~/portfolio/{$image->id}-small.png alt=thumbnail>
            </a>
            <figcaption>
               {$image->caption}
               <a href=image/{$image->id}/{$image->code} class=plain><i data-icon=link></i></a>
            </figcaption>
         </figure>";
      };
   $imagesHtml = implode(PHP_EOL, array_map("toImageHtml", $gallery));
   return empty($gallery) ? "<h3>Gallery is empty</h3>" : $imagesHtml;
   }

function getImageInfo($uri, $gallery) {
   preg_match("/\/image\/([0-9]+)/", $uri, $matches);
   $id = $matches[1];
   $dbFilename = __DIR__ . "/../~data~/portfolio/{$id}-db.json";
   $missingImage = '{ "caption": "That image does not appear to exist", "description": "" }';
   $imageDb = json_decode(is_file($dbFilename) ? file_get_contents($dbFilename) : $missingImage);
   $galleryUrl = getGalleryUrl();
   return (object)array(
      "caption" =>     $imageDb->caption,
      "description" => $imageDb->description,
      "urlSmall" =>    "{$galleryUrl}/~data~/portfolio/{$id}-small.png",
      "urlLarge" =>    "{$galleryUrl}/~data~/portfolio/{$id}-large.jpg",
      );
   }

function setValues($settings, $gallery) {
   $titleFontParam = urlencode($settings->{"title-font"});
   $artistPageFile = __DIR__ . "/../~data~/page-{$settings->pages[1]->name}.html";
   $galleryUrl = getGalleryUrl();
   $id = empty($gallery) ? "NA" : $gallery[0]->id;
   return (object)array(
      "cardImageUrl" =>   "{$galleryUrl}/~data~/portfolio/{$id}-large.jpg",
      "thumbnailUrl" =>   "{$galleryUrl}/~data~/portfolio/{$id}-small.png",
      "titleFontUrl" =>   "https://fonts.googleapis.com/css?family={$titleFontParam}",
      "styleClasses" =>   styleClasses($settings),
      "artistPageHtml" => $settings->pages[1]->show ? file_get_contents($artistPageFile) : "",
      );
   }

$settings = getData(__DIR__ . "/../~data~/settings-db.json");
$gallery =  getData(__DIR__ . "/../~data~/gallery-db.json");
$pages = $settings->pages;
$values = setValues($settings, $gallery);
?>
