<?php require "server/gallery.php"; ?>
<!doctype html>
<!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
<!-- Paradise ~ centerkey.com/paradise                         -->
<!-- GPLv3 ~ Copyright (c) individual contributors to Paradise -->
<!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
<html lang=en>
<head>
<meta charset=utf-8>
<meta name=viewport                   content="width=device-width, initial-scale=1">
<meta name=apple-mobile-web-app-title content="Gallery">
<meta name=twitter:card               content="summary_large_image">
<meta name=twitter:title              content="<?=$settings->title?>">
<meta name=twitter:description        content="<?=$settings->subtitle?>">
<meta property=og:title               content="<?=$settings->title?>">
<meta property=og:description         content="<?=$settings->subtitle?>">
<meta property=og:type                content="website">
<meta property=og:image               content="<?=$values->cardImageUrl?>">
<meta property=og:image:alt           content="<?=$settings->title?>">
<title><?=$settings->title?> &bull; <?=$settings->subtitle?></title>
<link rel=icon             href=https://centerkey.com/paradise/graphics/bookmark.png>
<link rel=apple-touch-icon href=<?=$values->thumbnailUrl?>>
<link rel=stylesheet       href="<?=$values->titleFontUrl?>">
<link rel=stylesheet       href=https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.12/css/all.min.css>
<link rel=stylesheet       href=https://cdn.jsdelivr.net/jquery.magnific-popup/1.0/magnific-popup.css>
<link rel=stylesheet       href=https://cdn.jsdelivr.net/npm/dna.js@1.6/dist/dna.css>
<link rel=stylesheet       href=https://cdn.jsdelivr.net/npm/web-ignition@1.2/dist/reset.min.css>
<link rel=stylesheet       href=paradise.min.css>
<link rel=stylesheet       href=~data~/custom-style.css>
<style>
   h1 {
      font-family: "<?=$settings->{"title-font"}?>", sans-serif;
      font-size: <?=$settings->{"title-size"}?>;
      }
</style>
</head>
<body class="<?=$values->styleClasses?>">

<header>
   <h1 data-href=.><span><?=$settings->title?></span></h1>
   <h2><?=$settings->subtitle?></h2>
</header>

<main>
   <aside><i data-icon=tachometer-alt data-href=console></i></aside>
   <nav class=dna-menu data-nav=gallery>
      <span class=<?=showHideClass($pages[0]->show)?>><?=$pages[0]->title?></span>
      <span class=<?=showHideClass($pages[1]->show)?>><?=$pages[1]->title?></span>
      <span class=<?=showHideClass($pages[2]->show)?>><?=$pages[2]->title?></span>
      <span class=<?=showHideClass(false)?>>Thanks</span>
   </nav>
   <div class=dna-panels data-nav=gallery>
      <section data-hash=images class=gallery-images>
         <h2 class=hide-me>Gallery Images</h2>
         <?=getImagesHtml($gallery)?>
      </section>
      <section data-hash=artist>
         <h2 class=hide-me>The Artist</h2>
         <?=$values->artistPageHtml?>
      </section>
      <section data-hash=contact class=external-site>
         <h2>Contact the Artist</h2>
         <form class=send-message>
            <label>
               <span>Message:</span>
               <textarea name=message placeholder="Enter your message" required></textarea>
            </label>
            <label>
               <span>Name:</span>
               <input name=name placeholder="Enter your name">
            </label>
            <label>
               <span>Email:</span>
               <input name=email type=email placeholder="Enter your email address">
            </label>
            <p>
               <button type=submit>Send message</button>
            </p>
         </form>
         <nav>Gallery powered by <a href=https://centerkey.com/paradise>Paradise</a></nav>
      </section>
      <section data-hash=thanks>
         <h2>Thanks!</h2>
         <p>Your message has been sent.</p>
      </section>
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
<script src=paradise.min.js></script>
</body>
</html>
