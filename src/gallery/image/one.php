<?php require "../php/gallery.php"; ?>
<?php list($id, $caption, $description) = getImageInfo($_SERVER["REQUEST_URI"], $gallery); ?>
<!doctype html>
<!-- - - - - - - - - - - - - - - - - - - - - - - - -->
<!-- Paradise ~ centerkey.com/paradise             -->
<!-- GPLv3 ~ Copyright (c) individual contributors -->
<!-- - - - - - - - - - - - - - - - - - - - - - - - -->
<html lang=en>
<head>
<meta charset=utf-8>
<meta name=viewport                   content="width=device-width, initial-scale=1">
<meta name=apple-mobile-web-app-title content="<?= $caption ?>">
<title><?= $caption ?> &bull; <?= $settings->{"title"} ?></title>
<link rel=icon             href=http://centerkey.com/paradise/graphics/bookmark.png>
<link rel=apple-touch-icon href=../../~data~/portfolio/<?= $id ?>-small.png>
<link rel=stylesheet       href=https://cdn.jsdelivr.net/fontawesome/4.7/css/font-awesome.min.css>
<link rel=stylesheet       href=https://cdn.jsdelivr.net/dna.js/1.0/dna.css>
<link rel=stylesheet       href=http://centerkey.com/css/reset.css>
<link rel=stylesheet       href=../../css/style.css>
<link rel=stylesheet       href=../../~data~/custom-style.css>
<style>
   @import url(http://fonts.googleapis.com/css?family=<?= urlencode($settings->{"title-font"}) ?>);
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
         <img src=../../~data~/portfolio/<?= $id ?>-large.jpg data-href=../..  alt="<?= $caption ?>">
      </figure>
      <p><?= $description ?></p>
   </div>
</main>

<footer>
   <div class=<?= showHideClass($settings->{"cc-license"}) ?>>
      <a class=external-site rel=license href=http://creativecommons.org/licenses/by-sa/4.0>
         <img src=https://i.creativecommons.org/l/by-sa/4.0/80x15.png alt=License>
      </a>
   </div>
   <div id=social-buttons class=<?= showHideClass($settings->{"bookmarks"}) ?>></div>
   <div><?= $settings->{"footer"} ?></div>
</footer>

<script src=https://cdn.jsdelivr.net/jquery/3.1/jquery.min.js></script>
<script src=https://cdn.jsdelivr.net/dna.js/1.0/dna.min.js></script>
<script src=../../js/app.js></script>
</body>
</html>
