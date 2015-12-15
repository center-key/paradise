<!doctype html>
<!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
<!--  PPAGES - PHP Portfolio Art Gallery Exhibit Showcase  -->
<!--  centerkey.com/ppages - Open Source (GPL)             -->
<!--  Copyright (c) individual contributors                -->
<!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
<?php
   $dataFolder = "data/";
   include "php/common.php";
   include "main.php";
   function showHideClass($show) { return $show ? "show-me" : "hide-me"; }
   $settings = readDb($settingsDbFile);
   $gallery =  readDb($galleryDbFile);
?>
<html>
<head>
<meta charset=utf-8>
<meta name=apple-mobile-web-app-title content="Gallery">
<title><?= $settings->{"title"} ?> &bull; <?= $settings->{"subtitle"} ?></title>
<link rel=icon             href="http://centerkey.com/ppages/graphics/bookmark.png">
<link rel=apple-touch-icon href="http://centerkey.com/ppages/graphics/mobile-home-screen.png">
<link rel=stylesheet       href="https://cdn.jsdelivr.net/fontawesome/4/css/font-awesome.min.css">
<link rel=stylesheet       href="https://cdn.jsdelivr.net/slimbox/2/css/slimbox2.css">
<link rel=stylesheet       href="https://cdn.jsdelivr.net/dna.js/0/dna.css">
<link rel=stylesheet       href="css/reset.css" >
<link rel=stylesheet       href="css/style.css" >
<link rel=stylesheet       href="data/style.css">
<style>
   @import url(http://fonts.googleapis.com/css?family=<?= urlencode($settings->{"title-font"}) ?>);
   h1 {
      font-family: "<?= $settings->{"title-font"} ?>", sans-serif;
      font-size:   <?= $settings->{"title-size"} ?>;
      }
</style>
</head>
<body>

<header>
   <h1><?= $settings->{"title"} ?></h1>
   <h2><?= $settings->{"subtitle"} ?></h2>
</header>

<main>
   <?php
      $pages = $settings->{"pages"};
      $current = currentPage($pages);
      displayMenuBar($pages, $current);
      if ($current == "gallery")
         displayGallery($settings->{"caption-italic"}, $settings->{"caption-caps"});
      else
         displayPage($current);
   ?>
</main>

<footer>
   <div class=<?= showHideClass($settings->{"cc-license"}) ?>>
      <a class=external-site rel=license href="http://creativecommons.org/licenses/by-sa/4.0/">
         <img src="https://i.creativecommons.org/l/by-sa/4.0/80x15.png" alt="Creative Commons License"
            title="This work is licensed under a Creative Commons Attribution-ShareAlike 4.0 International License.">
      </a>
   </div>
   <div id=social-buttons class=<?= showHideClass($settings->{"bookmarks"}) ?>></div>
   <div><?= $settings->{"footer-html"} ?></div>
</footer>

<script src="https://cdn.jsdelivr.net/jquery/2/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/slimbox/2/js/slimbox2.min.js"></script>
<script src="https://cdn.jsdelivr.net/dna.js/0/dna.min.js"></script>
<script src="js/library.js"></script>
<script src="js/app.js"></script>
</body>
</html>
