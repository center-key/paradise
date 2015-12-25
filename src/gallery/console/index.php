<?php require "php/security.php"; ?>
<?php require "php/console.php"; ?>
<!doctype html>
<!-- - - - - - - - - - - - - - - - - - - - - - - - -->
<!-- PPAGES ~ centerkey.com/ppages                 -->
<!-- GPLv3 - Copyright (c) individual contributors -->
<!-- - - - - - - - - - - - - - - - - - - - - - - - -->
<html>
<head>
<meta charset=utf-8>
<meta name=apple-mobile-web-app-title content="Console">
<title>PPAGES &bull; Administrator Console</title>
<link rel=icon             href="http://centerkey.com/ppages/graphics/bookmark.png">
<link rel=apple-touch-icon href="http://centerkey.com/ppages/graphics/mobile-home-screen.png">
<link rel=stylesheet       href="https://cdn.jsdelivr.net/fontawesome/4/css/font-awesome.min.css">
<link rel=stylesheet       href="https://cdn.jsdelivr.net/dna.js/0/dna.css">
<link rel=stylesheet       href="../css/reset.css">
<link rel=stylesheet       href="css/color-blocks.css">
<link rel=stylesheet       href="css/style.css">
</head>
<body>

<header>
   <aside>
      <p><button data-href="..">Visit Gallery</button></p>
      <p><button data-href="sign-out">Sign out</button></p>
   </aside>
   <h1>PPAGES &ndash; PHP Portfolio Art Gallery Exhibit to Showcase</h1>
   <h2>Administrator Console</h2>
</header>

<main>
   <div>

      <section>
         <h3>Status</h3>
         <p>Wow!</p>
      </section>
      <section>
         <h3>Image Portfolio</h3>
         <div>
            <div id=portfolio-image class=dna-template>
            </div>
         </div>
      </section>

   </div>
   <div>

      <section>
         <h3>Transfer Photos to Gallery</h3>
         <p>Wow!</p>
      </section>

      <section id=gallery-settings class=dna-template>
         <h3>Gallery Settings</h3>
         <fieldset class=settings-website>
            <legend>Website</legend>
            <label>
               <span>Title:</span>
               <input name=title value=~~title~~ placeholder="Enter website header">
            </label>
            <label>
               <span>Title font:</span>
               <select data-option=~~title-font~~>
                  <?= displayFontOptions(); ?>
               </select>
               <i data-href="https://www.google.com/fonts/specimen/~~title-font~~" class="fa fa-info-circle external-site"></i>
            </label>
            <label>
               <span>Title size:</span>
               <select data-option=~~title-size~~>
                  <option value=100%>100%</option>
                  <option value=200%>200%</option>
                  <option value=300%>300%</option>
                  <option value=400%>400%</option>
                  <option value=500%>500%</option>
                  <option value=600%>600%</option>
                  <option value=700%>700%</option>
                  <option value=800%>800%</option>
                  <option value=900%>900%</option>
               </select>
            </label>
            <label>
               <span>Subtitle:</span>
               <input name=Subtitle value=~~subtitle~~ placeholder="Enter website subheader">
            </label>
            <div>
               <span>Captions:</span>
               <span>
                  <label>
                     <input type=checkbox name=caption-italic data-prop-checked=~~caption-italic~~>
                     <span><i>italic</i></span>
                  </label>
                  <label>
                     <input type=checkbox name=caption-caps data-prop-checked=~~caption-caps~~>
                     <span class=small-caps>all caps</span>
                  </label>
               </span>
            </div>
            <label>
               <span>Creative Commons:</span>
               <input type=checkbox name=cc-license data-prop-checked=~~cc-license~~>
               <span>display</span>
               <a href="http://creativecommons.org/licenses/by-sa/4.0/" class=external-site title="CC BY 4.0">
                  <i class="fa fa-info-circle"></i>
               </a>
            </label>
            <label>
               <span>Social share icons:</span>
               <input type=checkbox name=bookmarks data-prop-checked=~~bookmarks~~>
               <span>display</span>
            </label>
            <label>
               <span>Footer:</span>
               <input type=text name=Footer value=~~footer~~>
            </label>
            <label>
               <span>E-mail:</span>
               <input type=email name=email value=~~email~~ placeholder="Address for feedback">
               <a href="../#contact" class=external-site><i class="fa fa-info-circle"></i></a>
            </label>
         </fieldset>
         <fieldset class=settings-tabs>
            <legend>Tabs</legend>
            <div>
               <div data-array=~~pages~~>
                  <label>
                     <span>#<span>~~[count]~~</span>:</span>
                     <input value=~~title~~ placeholder="Title for menu tab">
                  </label>
                  <label>
                     <input type=checkbox name=~~name~~ data-prop-checked=~~show~~>
                     <span>display</span>
                  </label>
               </div>
            </div>
         </fieldset>
      </section>

      <section>
         <h3>User Accounts</h3>
         <p>Wow!</p>
      </section>

   </div>
</main>

<footer>
   <div class=plain>
      Questions and bugs:<br>
      <a href="https://github.com/center-key/ppages/issues">github.com/center-key/ppages/issues</a>
   </div>
   <div>
      PPAGES <?= $version ?><br>
      <b><?= $_SESSION["user"] ?></b> logged into <b><?= $_SERVER["HTTP_HOST"] ?></b>
   </div>
</footer>

<script src="https://cdn.jsdelivr.net/jquery/2/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/crypto-js/3/rollups/sha256.js"></script>
<script src="https://cdn.jsdelivr.net/dna.js/0/dna.min.js"></script>
<script>var app = {}; app.clientData = <?= appClientData(true) ?>;</script>
<script src="js/library.js"></script>
<script src="js/console.js"></script>
</body>
</html>
