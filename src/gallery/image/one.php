<?php require "../server/gallery.php"; ?>
<?php list($id, $caption, $description) = getImageInfo($_SERVER["REQUEST_URI"], $gallery); ?>
<!doctype html>
<!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
<!-- Paradise ~ centerkey.com/paradise                         -->
<!-- GPLv3 ~ Copyright (c) individual contributors to Paradise -->
<!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
<html lang=en>
<head>
<meta charset=utf-8>
<meta name=viewport                   content="width=device-width, initial-scale=1">
<meta name=apple-mobile-web-app-title content="<?= $caption ?>">
<meta property=og:title               content="<?= $settings->{"title"} ?> - <?= $caption ?>">
<meta property=og:description         content="<?= $settings->{"subtitle"} ?> - <?= $description ?>">
<meta property=og:type                content="website">
<meta property=og:image               content="https://centerkey.com/graphics/center-key-logo-card.png">
<meta property=og:image:alt           content="Logo">
<title><?= $caption ?> &bull; <?= $settings->{"title"} ?></title>
<link rel=icon             href=https://centerkey.com/paradise/graphics/bookmark.png>
<link rel=apple-touch-icon href=../../~data~/portfolio/<?= $id ?>-small.png>
<link rel=stylesheet       href=https://use.fontawesome.com/releases/v5.1.0/css/all.css>
<link rel=stylesheet       href=https://cdn.jsdelivr.net/npm/dna.js@1.4/dna.css>
<link rel=stylesheet       href=https://centerkey.com/css/reset.css>
<link rel=stylesheet       href=../../paradise.css>
<link rel=stylesheet       href=../../~data~/custom-style.css>
<style>
   @import url(https://fonts.googleapis.com/css?family=<?= urlencode($settings->{"title-font"}) ?>);
   h1 {
      font-family: "<?= $settings->{"title-font"} ?>", sans-serif;
      font-size: <?= $settings->{"title-size"} ?>;
      }
</style>
</head>
<body class="<?= styleClasses($settings) ?>">

<header>
   <h1 data-href=../..><?= $settings->{"title"} ?></h1>
   <h2><?= $settings->{"subtitle"} ?></h2>
</header>

<main>
   <div class=one-image>
      <figure>
         <figcaption><?= $caption ?></figcaption>
         <img src=../../~data~/portfolio/<?= $id ?>-large.jpg data-href=../..  alt=image>
      </figure>
      <p><?= $description ?></p>
   </div>
</main>

<footer>
   <div class=<?= showHideClass($settings->{"cc-license"}) ?>>
      <a class=external-site rel=license href=https://creativecommons.org/licenses/by-sa/4.0>
         <img src=https://i.creativecommons.org/l/by-sa/4.0/80x15.png alt=license>
      </a>
   </div>
   <div class=<?= showHideClass($settings->{"bookmarks"}) ?>><div id=social-buttons></div></div>
   <div><?= $settings->{"footer"} ?></div>
</footer>

<script src=https://cdn.jsdelivr.net/npm/jquery@3.3/dist/jquery.min.js></script>
<script src=https://cdn.jsdelivr.net/npm/dna.js@1.4/dna.min.js></script>
<script src=https://cdn.jsdelivr.net/npm/magnific-popup@1.1/dist/jquery.magnific-popup.min.js></script>
<script src=../../paradise.js></script>
</body>
</html>
