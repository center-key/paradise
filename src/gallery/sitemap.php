<?php require "server/gallery.php"; ?>
<?php header('Content-type: application/xml; charset=utf-8'); ?>
<urlset
   xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
   xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
   xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
<?php
   $base = str_replace("/sitemap.php", "", getGalleryUrl());
   echo implode(PHP_EOL, array_map(
      function ($image) use ($base) {
         return "   <url><loc>{$base}/image/{$image->id}/{$image->code}</loc></url>";
         },
      $gallery));
?>
</urlset>
