<?php require "server/security.php"; ?>
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
<title>Fonts Sampler &bull; Paradise</title>
<link rel=icon             href=https://centerkey.com/paradise/graphics/bookmark.png>
<link rel=apple-touch-icon href=https://centerkey.com/paradise/graphics/mobile-home-screen.png>
<link rel=stylesheet       href=https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.6/css/all.min.css>
<link rel=stylesheet       href=https://cdn.jsdelivr.net/npm/dna.js@1.5/dist/dna.css>
<link rel=stylesheet       href=https://cdn.jsdelivr.net/npm/web-ignition@1.0/dist/reset.min.css>
<link rel=stylesheet       href=../paradise.css>
<link rel=stylesheet       href=../~data~/custom-style.css>
<style>
   table.font-sampler tbody tr td { vertical-align: middle; text-align: left; padding: 10px; }
   table.font-sampler tbody tr td:last-child { width: 100%; }
</style>
</head>
<body>

<header>
   <h1>Paradise Font Sampler</h1>
</header>

<main>
   <table class=font-sampler>
      <tbody class=external-site>
         <tr id=font-row class=dna-template>
            <td><a href="https://fonts.google.com/specimen/~~[value]~~">~~[value]~~</a></td>
            <td><h1 style="font-family: '~~[value]~~'"></h1></td>
         </tr>
      </tbody>
   </table>
</main>

<footer>
   <span>Paradise PHP Photo Gallery</span>
   <a href=https://fonts.google.com class=external-site>fonts.google.com</a>
</footer>

<script src=https://ajax.googleapis.com/ajax/libs/webfont/1.6/webfont.js></script>
<script src=https://cdn.jsdelivr.net/npm/jquery@3.3/dist/jquery.min.js></script>
<script src=https://cdn.jsdelivr.net/npm/dna.js@1.5/dist/dna.min.js></script>
<script>window.clientData = <?=appClientData()?>;</script>
<script>
   WebFont.load({ google: { families: clientData.fonts } });
   $('#font-row h1').css({ fontSize: clientData.titleSize }).text(clientData.title);
   dna.clone('font-row', clientData.fonts);
</script>
</body>
</html>
