<?php
///////////////////////////////////////////////////////////////
// Paradise ~ centerkey.com/paradise                         //
// GPLv3 ~ Copyright (c) individual contributors to Paradise //
///////////////////////////////////////////////////////////////

function getGalleryUrl() {
   $tls = isset($_SERVER["HTTPS"]) && strtolower($_SERVER["HTTPS"]) !== "off";
   $protocol = $tls ? "https://" : "http://";
   $ignore = array("/index.php", "/image/one.php", "/server/send-message.php");
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
   $options = array(
      "dark-mode",
      "image-border",
      "show-description",
      "caption-caps",
      "caption-italic",
      );
   $enabled = function($property) use ($settings) { return $settings->{$property}; };
   return implode(" ", array_filter($options, $enabled));
   }

function getImagesHtml($gallery, $settings) {
   $stampIcon = $settings->stampIcon;
   $stampTooltip = empty($settings->stampTitle) ? "" :
      "title='" . str_replace("'", "&apos;", $settings->stampTitle) . "'";
   function toImageHtml($image, $stampIcon, $stampTooltip) {
      $badgeHtml = empty($image->badge) ? "" : "<span class=badge>{$image->badge}</span>";
      $stampHtml = isset($image->stamp) && $image->stamp && !empty($stampIcon) ?
         "<i class=stamp data-icon={$stampIcon} {$stampTooltip}></i>" : "";
      $imageTitleHtml = str_replace("'", "&apos;",
         "<span class=image-caption>{$image->caption}</span>" .
         "<span class=image-description>{$image->description}</span>");
      return "
         <figure>
            <a href=~data~/portfolio/{$image->id}-large.jpg data-title='{$imageTitleHtml}'>
               <img src=~data~/portfolio/{$image->id}-small.png alt=thumbnail>
            </a>
            <aside>
               {$badgeHtml}
               {$stampHtml}
            </aside>
            <figcaption>
               {$image->caption}
               <a href=image/{$image->id}/{$image->code} class=plain><i data-icon=link></i></a>
               <p>{$image->description}</p>
            </figcaption>
         </figure>";
      };
   $imagesHtml = implode(PHP_EOL, array_map(
      function ($image) use ($stampIcon, $stampTooltip) {
         return toImageHtml($image, $stampIcon, $stampTooltip);
         },
      $gallery));
   return empty($gallery) ? "<h2>Gallery is empty</h2>" : $imagesHtml;
   }

function getImageInfo($uri, $gallery) {
   $id = preg_match("/\/image\/([0-9]+)/", $uri, $matches) ? $matches[1] : "missing";
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

function migrateSettings($settings) {  //see: console/server/startup.php:$defaultSettingsDb
   if (!isset($settings->{"dark-mode"}))
      $settings->{"dark-mode"} = true;
   if (!isset($settings->{"image-border"}))
      $settings->{"image-border"} = true;
   if (!isset($settings->{"show-description"}))
      $settings->{"show-description"} = false;
   if (!isset($settings->stampIcon))
      $settings->stampIcon = "star";
   if (!isset($settings->stampTitle))
      $settings->stampTitle = "";
   return $settings;
   }

$settings = getData(__DIR__ . "/../~data~/settings-db.json");
$gallery =  getData(__DIR__ . "/../~data~/gallery-db.json");
$pages = $settings->pages;
$values = setValues(migrateSettings($settings), $gallery);
?>
