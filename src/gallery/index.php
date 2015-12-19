<?php require "php/gallery.php"; ?>
<!doctype html>
<!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
<!--  PPAGES - PHP Portfolio Art Gallery Exhibit to Showcase   -->
<!--  centerkey.com/ppages                                     -->
<!--  GPLv3 - Copyright (c) individual contributors            -->
<!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
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
      font-size: <?= $settings->{"title-size"} ?>;
      }
</style>
</head>
<body class="<?= styleClasses($settings) ?>">

<header>
   <h1><?= $settings->{"title"} ?></h1>
   <h2><?= $settings->{"subtitle"} ?></h2>
</header>

<main>
   <nav id=gallery class=dna-menu>
      <span class=<?= showHideClass($pages[0]->show) ?>><?= $pages[0]->title ?></span>
      <span class=<?= showHideClass($pages[1]->show) ?>><?= $pages[1]->title ?></span>
      <span class=<?= showHideClass($pages[2]->show) ?>><?= $pages[2]->title ?></span>
      <span class=<?= showHideClass(false) ?>>Thanks</span>
   </nav>
   <div id=gallery-panels class=dna-panels>
      <section data-hash=images class=gallery-images>
         <?php displayImages($gallery); ?>
      </section>
      <section data-hash=artist>
         <?php if ($pages[1]->show) readfile("data/page-{$pages[1]->name}.html"); ?>
      </section>
      <section data-hash=contact>
         <h3>Contact the Artist</h3>
         <form class=feedback method=post>
            <label>
               <span>Message:</span>
               <textarea name=message rows=6 cols=50 placeholder="Enter your message" required></textarea>
            </label>
            <label>
               <span>Name:</span>
               <input name=name size=35 placeholder="Enter your name"></p>
            </label>
            <label>
               <span>Email:</span>
               <input name=email size=40 type=email placeholder="Enter your email address" required>
            </label>
            <button type=submit>Send Message</button>
            <aside>Gallery powered by <a href="http://centerkey.com/ppages/">PPAGES</a></aside>
         </form>
      </section>
      <section data-hash=thanks>
         <h3>Thanks!</h3>
         <p>Your message has been sent.</p>
      </section>
   </div>
</main>

<footer>
   <div class=<?= showHideClass($settings->{"cc-license"}) ?>>
      <a class=external-site rel=license href="http://creativecommons.org/licenses/by-sa/4.0/">
         <img src="https://i.creativecommons.org/l/by-sa/4.0/80x15.png" alt="Creative Commons License"
            title="This work is licensed under a Creative Commons Attribution-ShareAlike 4.0 International License.">
      </a>
   </div>
   <div id=social-buttons class=<?= showHideClass($settings->{"bookmarks"}) ?>></div>
   <div><?= $settings->{"footer"} ?></div>
</footer>

<script src="https://cdn.jsdelivr.net/jquery/2/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/slimbox/2/js/slimbox2.min.js"></script>
<script src="https://cdn.jsdelivr.net/dna.js/0/dna.min.js"></script>
<script src="js/app.js"></script>
</body>
</html>
