<?php require "../server/gallery.php"; ?>
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
<meta name=apple-mobile-web-app-title content="<?=$imageInfo->caption?>">
<meta name=twitter:card               content="summary_large_image">
<meta name=twitter:title              content="<?=$settings->title?> - <?=$imageInfo->caption?>">
<meta name=twitter:description        content="<?=$imageInfo->description?>">
<meta property=og:title               content="<?=$settings->title?> - <?=$imageInfo->caption?>">
<meta property=og:description         content="<?=$settings->subtitle?> - <?=$imageInfo->description?>">
<meta property=og:type                content="website">
<meta property=og:image               content="<?=$imageInfo->urlLarge?>">
<meta property=og:image:alt           content="<?=$imageInfo->caption?>">
<title><?=$imageInfo->caption?> &bull; <?=$settings->title?></title>
<link rel=icon             href=https://centerkey.com/paradise/graphics/bookmark.png>
<link rel=apple-touch-icon href=<?=$imageInfo->urlSmall?>>
<link rel=stylesheet       href="<?=$values->titleFontUrl?>">
<link rel=stylesheet       href=https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.12/css/all.min.css>
<link rel=stylesheet       href=https://cdn.jsdelivr.net/jquery.magnific-popup/1.0/magnific-popup.css>
<link rel=stylesheet       href=https://cdn.jsdelivr.net/npm/dna.js@1.6/dist/dna.css>
<link rel=stylesheet       href=https://cdn.jsdelivr.net/npm/web-ignition@1.2/dist/reset.min.css>
<link rel=stylesheet       href=../../paradise.min.css>
<link rel=stylesheet       href=../../~data~/custom-style.css>
<style>
   body >header h1 {
      font-family: "<?=$settings->{"title-font"}?>", sans-serif;
      font-size: <?=$settings->{"title-size"}?>;
      }
</style>
</head>
<body class="<?=$values->styleClasses?>">

<header>
   <aside><a href=../..><i data-icon=images></i></a></aside>
   <h1 data-href=../..><span><?=$settings->title?></span></h1>
   <h2><?=$settings->subtitle?></h2>
</header>

<main>
   <div class=one-image>
      <figure>
         <figcaption><?=$imageInfo->caption?></figcaption>
         <img src=<?=$imageInfo->urlLarge?> data-href=../.. alt=image>
      </figure>
      <p><?=$imageInfo->description?></p>
   </div>
</main>

<footer>
   <div class=<?=showHideClass($settings->{"cc-license"})?>>
      <a rel=license href=https://creativecommons.org/licenses/by-sa/4.0 class=external-site>
         <i data-brand=creative-commons></i>
      </a>
   </div>
   <div class=<?=showHideClass($settings->bookmarks)?>><div id=social-buttons></div></div>
   <div><?=$settings->footer?></div>
</footer>

<script src=https://cdn.jsdelivr.net/npm/fetch-json@2.2/dist/fetch-json.min.js></script>
<script src=https://cdn.jsdelivr.net/npm/jquery@3.4/dist/jquery.min.js></script>
<script src=https://cdn.jsdelivr.net/npm/dna.js@1.6/dist/dna.min.js></script>
<script src=https://cdn.jsdelivr.net/npm/web-ignition@1.2/dist/library.min.js></script>
<script src=https://cdn.jsdelivr.net/npm/magnific-popup@1.1/dist/jquery.magnific-popup.min.js></script>
<script src=../../paradise.min.js></script>
</body>
</html>
