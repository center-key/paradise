<?php require "admin-server/security.php"; ?>
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
   <meta name=description                content="List of fonts availabile for the Paradise PHP Photo Gallery title text">
   <meta name=apple-mobile-web-app-title content="Fonts">
   <title>Fonts Sampler &bull; Paradise</title>
   <link rel=icon             href=https://centerkey.com/paradise/graphics/bookmark.png>
   <link rel=apple-touch-icon href=https://centerkey.com/paradise/graphics/mobile-home-screen.png>
   <link rel=preconnect       href=https://fonts.googleapis.com>
   <link rel=preconnect       href=https://fonts.gstatic.com crossorigin>
   <link rel=stylesheet       href=https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@@@pkg.cdnVersion.fontAwesome/css/all.min.css>
   <link rel=stylesheet       href=https://cdn.jsdelivr.net/npm/dna.js@@@pkg.cdnVersion.dnajs/dist/dna.css>
   <link rel=stylesheet       href=https://cdn.jsdelivr.net/npm/web-ignition@@@pkg.cdnVersion.webIgnition/dist/reset.min.css>
   <link rel=stylesheet       href=../paradise.min.css>
   <link rel=stylesheet       href=../~data~/custom-style.css>
   <style>
      table.font-sampler tbody tr td { vertical-align: middle; text-align: left; padding: 10px; }
      table.font-sampler tbody tr td:last-child { width: 100%; }
   </style>
   <script defer src=https://ajax.googleapis.com/ajax/libs/webfont/1.6/webfont.js></script>
   <script defer src=https://cdn.jsdelivr.net/npm/jquery@@@pkg.cdnVersion.jQuery/dist/jquery.min.js></script>
   <script defer src=https://cdn.jsdelivr.net/npm/dna.js@@@pkg.cdnVersion.dnajs/dist/dna.min.js></script>
   <script>globalThis.clientData = <?=appClientData()?>;</script>
   <script data-on-load=startup>
      const startup = () => {
         WebFont.load({ google: { families: clientData.fonts } });
         $('#font-row h1').css({ fontSize: clientData.titleSize }).text(clientData.title);
         dna.clone('font-row', clientData.fonts);
         };
   </script>
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

</body>
</html>
