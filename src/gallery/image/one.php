<?php require "../frontend-server/gallery.php"; ?>
<?php $imageInfo = getImageInfo($_SERVER["REQUEST_URI"], $gallery); ?>
<!doctype html>
<!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
<!-- Paradise ~ centerkey.com/paradise                         -->
<!-- GPLv3 ~ Copyright (c) individual contributors to Paradise -->
<!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
<html lang=en>
<head>
<meta charset=utf-8>
<meta name=viewport                   content="width=device-width, initial-scale=1">
<meta name=robots                     content="index, follow">
<meta name=description                content="<?=$imageInfo->description?> - <?=$settings->title?> - <?=$settings->subtitle?>">
<meta name=apple-mobile-web-app-title content="<?=$imageInfo->caption?>">
<meta name=twitter:card               content="summary_large_image">
<meta name=twitter:title              content="<?=$imageInfo->caption?>">
<meta name=twitter:description        content="<?=$imageInfo->description?> - <?=$settings->title?> - <?=$settings->subtitle?>">
<meta property=og:title               content="<?=$imageInfo->caption?>">
<meta property=og:description         content="<?=$imageInfo->description?> - <?=$settings->title?> - <?=$settings->subtitle?>">
<meta property=og:type                content="website">
<meta property=og:image               content="<?=$imageInfo->urlLarge?>">
<meta property=og:image:alt           content="<?=$imageInfo->caption?>">
<title><?=$imageInfo->caption?> &bull; <?=$settings->title?></title>
<link rel=icon             href=https://centerkey.com/paradise/graphics/bookmark.png>
<link rel=apple-touch-icon href=<?=$imageInfo->urlSmall?>>
<link rel=preconnect       href=https://fonts.googleapis.com>
<link rel=preconnect       href=https://fonts.gstatic.com crossorigin>
<link rel=stylesheet       href="<?=$values->titleFontUrl?>">
<link rel=stylesheet       href=https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@@@pkg.cdnVersion.fontAwesome/css/all.min.css>
<link rel=stylesheet       href=https://cdn.jsdelivr.net/npm/magnific-popup@@@pkg.cdnVersion.magnificPopup/dist/magnific-popup.css>
<link rel=stylesheet       href=https://cdn.jsdelivr.net/npm/dna.js@@@pkg.cdnVersion.dnajs/dist/dna.css>
<link rel=stylesheet       href=https://cdn.jsdelivr.net/npm/web-ignition@@@pkg.cdnVersion.webIgnition/dist/reset.min.css>
<link rel=stylesheet       href=../../paradise.min.css>
<link rel=stylesheet       href=../../~data~/custom-style.css>
<style>
   body >header h1 {
      font-family: "<?=$settings->titleFont?>", system-ui, sans-serif;
      font-size: <?=$settings->titleSize?>;
      }
</style>
</head>
<body class="<?=$values->styleClasses?>" itemscope itemtype=https://schema.org/ImageObject>

<header>
   <aside><a href=../..><i data-icon=images></i></a></aside>
   <h1 data-href=../..><span itemprop=name><?=$settings->title?></span></h1>
   <h2><?=$settings->subtitle?></h2>
</header>

<main>
   <div class=one-image>
      <figure>
         <figcaption itemprop=caption><?=$imageInfo->caption?></figcaption>
         <img src=<?=$imageInfo->urlLarge?> data-href=../.. alt=image itemprop=contentUrl>
      </figure>
      <p itemprop=description><?=$imageInfo->description?></p>
   </div>
</main>

<footer>
   <div class=<?=showHideClass($settings->ccLicense)?>>
      <a rel=license href=https://creativecommons.org/licenses/by-sa/4.0 class=external-site>
         <i data-brand=creative-commons></i>
      </a>
   </div>
   <div class=<?=showHideClass($settings->bookmarks)?>><div id=social-buttons></div></div>
   <div><?=$settings->footer?></div>
</footer>

<script src=https://cdn.jsdelivr.net/npm/fetch-json@@@pkg.cdnVersion.fetchJson/dist/fetch-json.min.js></script>
<script src=https://cdn.jsdelivr.net/npm/jquery@@@pkg.cdnVersion.jQuery/dist/jquery.min.js></script>
<script src=https://cdn.jsdelivr.net/npm/dna.js@@@pkg.cdnVersion.dnajs/dist/dna.min.js></script>
<script src=https://cdn.jsdelivr.net/npm/web-ignition@@@pkg.cdnVersion.webIgnition/dist/lib-x.min.js></script>
<script src=https://cdn.jsdelivr.net/npm/magnific-popup@@@pkg.cdnVersion.magnificPopup/dist/jquery.magnific-popup.min.js></script>
<script src=../../paradise.min.js></script>
</body>
</html>
