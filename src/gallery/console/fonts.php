<?php require "admin-server/security.php"; ?>
<!doctype html>
<html lang=en>
<!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
<!-- Paradise ~ centerkey.com/paradise                         -->
<!-- GPLv3 ~ Copyright (c) Individual contributors to Paradise -->
<!-- Title Font Sample Page                                    -->
<!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
<?php $clientData = appClientData(); ?>
<head>
   <meta charset=utf-8>
   <meta name=viewport                   content="width=device-width, initial-scale=1">
   <meta name=robots                     content="index, follow">
   <meta name=description                content="List of fonts availabile for the Paradise Photo Gallery title text">
   <meta name=apple-mobile-web-app-title content="Fonts">
   <title>Fonts Sampler &bull; Paradise</title>
   <link rel=icon             href=https://centerkey.com/paradise/graphics/bookmark-icon.png>
   <link rel=apple-touch-icon href=https://centerkey.com/paradise/graphics/mobile-home-screen.png>
   <link rel=preconnect       href=https://fonts.googleapis.com>
   <link rel=preconnect       href=https://fonts.gstatic.com crossorigin>
   <link rel=stylesheet       href=https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@{{package.devDependencies.-fortawesome-fontawesome-free|version}}/css/all.min.css>
   <link rel=stylesheet       href=https://cdn.jsdelivr.net/npm/dna-engine@{{package.devDependencies.dna-engine|version}}/dist/dna-engine.css>
   <link rel=stylesheet       href=https://cdn.jsdelivr.net/npm/web-ignition@{{package.devDependencies.web-ignition|version}}/dist/reset.min.css>
   <link rel=stylesheet       href=../paradise.min.css>
   <link rel=stylesheet       href=../~data~/custom-style.css>
   <style>
      body >header { color: white; background-color: steelblue; padding-bottom: 20px; margin-bottom: 0px; }
      table.font-sampler { margin-bottom: 0px; }
      table.font-sampler tbody tr td { vertical-align: middle; text-align: left; padding: 10px; }
      table.font-sampler tbody tr td:last-child { width: 100%; }
      table.font-sampler tbody tr td h1 { margin-bottom: 0px; }
   </style>
   <script defer src=https://ajax.googleapis.com/ajax/libs/webfont/1.6/webfont.js></script>
   <script defer src=https://cdn.jsdelivr.net/npm/dna-engine@{{package.devDependencies.dna-engine|version}}/dist/dna-engine.min.js></script>
   <script>globalThis.clientData = <?=$clientData?>;</script>
   <script data-on-load=startup>
      const startup = () => {
         WebFont.load({ google: { families: clientData.fonts } });
         const data = clientData.fonts.map(font => ({
            font:  font,
            href:  'https://fonts.google.com/specimen/' + font,
            style: `font-family: "${font}"; font-size: ${clientData.titleSize};`,
            title: clientData.title,
            }));
         dna.clone('font-row', data);
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
            <td><a href=~~href~~>~~font~~</a></td>
            <td><h1 style=~~style~~>~~title~~</h1></td>
         </tr>
      </tbody>
   </table>
</main>

<footer>
   <span>Paradise Photo Gallery</span>
   <a href=https://fonts.google.com class=external-site>fonts.google.com</a>
</footer>

</body>
</html>
