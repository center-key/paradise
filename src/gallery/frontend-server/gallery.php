<?php
///////////////////////////////////////////////////////////////
// Paradise ~ centerkey.com/paradise                         //
// GPLv3 ~ Copyright (c) individual contributors to Paradise //
///////////////////////////////////////////////////////////////

function getGalleryUrl() {
   // Example: https://example.com/travel/gallery
   $tls =      isset($_SERVER["HTTPS"]) && strtolower($_SERVER["HTTPS"]) !== "off";
   $protocol = $tls ? "https://" : "http://";
   $ignore =   array("/index.php", "/image/fallback/one.php", "/frontend-server/send-message.php");
   return $protocol . $_SERVER["SERVER_NAME"] . str_replace($ignore, "", $_SERVER["SCRIPT_NAME"]);
   }

function getRootUrl() {
   // Example: https://example.com/travel
   $url = dirname(getGalleryUrl());
   return str_ends_with($url, ":") ? getGalleryUrl() : $url;
   }

function getData($dbFilename) {
   if (!is_file($dbFilename))
      exit("Setup incomplete");  //displayed to all users until initial admin account is created
   return json_decode(file_get_contents($dbFilename));
   }

function showHideClass($show) {
   return $show ? "show-me" : "hide-me";
   }

function linkText($url) {
   // Returns the clean displayable version of the link.
   // Example: "https://example.com/gallery" --> "example.com/gallery"
   $parts = explode("//", $url);
   return end($parts);
   }

function toCamelCase($kebabCase) {
   $camelCase =    str_replace(' ', '', ucwords(str_replace('-', ' ', $kebabCase)));
   $camelCase[0] = strtolower($camelCase[0]);
   return $camelCase;
   }

function styleClasses($settings) {
   $options = array(
      "dark-mode",
      "image-border",
      "show-description",
      "caption-caps",
      "caption-italic",
      );
   $enabled = function($property) use ($settings) { return $settings->{toCamelCase($property)}; };
   return implode(" ", array_filter($options, $enabled));
   }

function getImagesHtml($gallery, $settings) {
   // Returns a string of "<figure>...</figure>" tags representing the content of the gallery.
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
         <figure itemprop=associatedMedia itemscope itemtype=https://schema.org/ImageObject>
            <a href=~data~/portfolio/{$image->id}-large.jpg data-title='{$imageTitleHtml}' itemprop=contentUrl>
               <img src=~data~/portfolio/{$image->id}-small.png alt=thumbnail itemprop=thumbnail>
            </a>
            <aside>
               {$badgeHtml}
               {$stampHtml}
            </aside>
            <figcaption>
               <span itemprop=caption>{$image->caption}</span>
               <a href=image/{$image->id}/{$image->code} class=plain itemprop=url><i data-icon=link></i></a>
               <p itemprop=description>{$image->description}</p>
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
   $id =           preg_match("/\/image\/([0-9]+)/", $uri, $matches) ? $matches[1] : "missing";
   $dbFilename =   __DIR__ . "/../~data~/portfolio/{$id}-db.json";
   $missingImage = '{ "caption": "That image does not appear to exist", "description": "" }';
   $imageDb =      json_decode(is_file($dbFilename) ? file_get_contents($dbFilename) : $missingImage);
   $galleryUrl =   getGalleryUrl();
   return (object)array(
      "caption" =>     $imageDb->caption,
      "description" => $imageDb->description,
      "urlSmall" =>    "{$galleryUrl}/~data~/portfolio/{$id}-small.png",
      "urlLarge" =>    "{$galleryUrl}/~data~/portfolio/{$id}-large.jpg",
      );
   }

function setValues($settings, $gallery) {
   $titleFontParam = urlencode($settings->titleFont);
   $artistPageFile = __DIR__ . "/../~data~/page-{$settings->pages[1]->name}.html";
   $galleryUrl =     getGalleryUrl();
   $id =             empty($gallery) ? "NA" : $gallery[0]->id;
   return (object)array(
      "cardImageUrl" =>   "{$galleryUrl}/~data~/portfolio/{$id}-large.jpg",
      "thumbnailUrl" =>   "{$galleryUrl}/~data~/portfolio/{$id}-small.png",
      "titleFontUrl" =>   "https://fonts.googleapis.com/css?family={$titleFontParam}",
      "styleClasses" =>   styleClasses($settings),
      "artistPageHtml" => $settings->pages[1]->show ? file_get_contents($artistPageFile) : "",
      );
   }

function migrateSettings($settings) {  //see: console/admin-server/startup.php:$defaultSettingsDb
   foreach($settings as $key => $value) {
      $newKey = toCamelCase($key);
      if ($newKey !== $key && !isset($settings->$newKey) && isset($settings->$key)) {
         $settings->$newKey = $value;
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

$settings = getData(__DIR__ . "/../~data~/settings-db.json");
$gallery =  getData(__DIR__ . "/../~data~/gallery-db.json");
$pages =    $settings->pages;
$values =   setValues(migrateSettings($settings), $gallery);
?>
