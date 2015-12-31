<?php require "php/security.php"; ?>
<?php require "php/console.php"; ?>
<?php workaroundToUpgradePortfolio(); ?>
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
<link rel=stylesheet       href="file-uploader/fileuploader.css">
<link rel=stylesheet       href="../css/reset.css">
<link rel=stylesheet       href="css/color-blocks.css">
<link rel=stylesheet       href="css/style.css">
</head>
<body>

<header>
   <aside>
      <button data-href=".." class=external-site>Visit gallery</button>
      <button data-href="sign-out">Sign out</button>
   </aside>
   <h1>PPAGES &ndash; PHP Portfolio Art Gallery Exhibit to Showcase</h1>
   <h2>Administrator Console</h2>
</header>

<main>
   <div>

      <section>
         <h3>Status</h3>
         <div id=status-msg></div>
      </section>

      <section>
         <h3>Image Portfolio</h3>
         <div>
            <div id=portfolio-image class=dna-template data-item-id=~~id~~>
               <figure>
                  <img data-attr-src="../data/portfolio/~~id~~-large.jpg" class=external-site
                     data-href="../data/portfolio/~~id~~-large.jpg" alt="Thumbnail">
                  <figcaption>Uploaded: <b>~~uploaded~~</b></figcaption>
               </figure>
               <label data-class=~~display,display-on,display-off~~>
                  <span>Display:</span>
                  <input name=display type=checkbox data-prop-checked=~~display~~ data-change=app.ui.savePortfolio>
                  <span>(show in gallery)</span>
               </label>
               <label>
                  <span>Caption:</span>
                  <input name=caption value=~~caption~~ data-smart-update=app.ui.savePortfolio>
               </label>
               <label>
                  <span>Description:</span>
                  <textarea name=description data-smart-update=app.ui.savePortfolio>~~description~~</textarea>
               </label>
               <label>
                  <span>Badge:</span>
                  <input name=badge value=~~badge~~ data-smart-update=app.ui.savePortfolio>
               </label>
            </div>
         </div>
         <div data-placeholder=portfolio-image>No images in your portfolio yet.</div>
      </section>

   </div>
   <div>

      <section>
         <h3>Gallery Uploader</h3>
         <i id=processing-files class="fa fa-spinner fa-spin"></i>
         <div id=file-uploader></div>
      </section>

      <section id=gallery-settings class=dna-template>
         <h3>Gallery Settings</h3>
         <fieldset class=settings-website>
            <legend>Website</legend>
            <label>
               <span>Title:</span>
               <input name=title value=~~title~~ data-smart-update=app.ui.saveSettings placeholder="Enter website header">
            </label>
            <label>
               <span>Title font:</span>
               <select name=title-font data-option=~~title-font~~ data-change=app.ui.saveSettings>
                  <?= displayFontOptions(); ?>
               </select>
               <i data-href="https://www.google.com/fonts/specimen/~~title-font~~" class="fa fa-info-circle external-site"></i>
            </label>
            <label>
               <span>Title size:</span>
               <select name=title-size data-option=~~title-size~~ data-change=app.ui.saveSettings>
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
               <input name=subtitle value=~~subtitle~~ data-smart-update=app.ui.saveSettings placeholder="Enter website subheader">
            </label>
            <div>
               <span>Captions:</span>
               <span>
                  <label>
                     <input type=checkbox name=caption-italic data-prop-checked=~~caption-italic~~ data-change=app.ui.saveSettings>
                     <span><i>italic</i></span>
                  </label>
                  <label>
                     <input type=checkbox name=caption-caps data-prop-checked=~~caption-caps~~ data-change=app.ui.saveSettings>
                     <span class=small-caps>all caps</span>
                  </label>
               </span>
            </div>
            <label>
               <span>Creative Commons:</span>
               <input type=checkbox name=cc-license data-prop-checked=~~cc-license~~ data-change=app.ui.saveSettings>
               <span>display</span>
               <a href="http://creativecommons.org/licenses/by-sa/4.0/" class=external-site title="CC BY 4.0">
                  <i class="fa fa-info-circle"></i>
               </a>
            </label>
            <label>
               <span>Social share icons:</span>
               <input type=checkbox name=bookmarks data-prop-checked=~~bookmarks~~ data-change=app.ui.saveSettings>
               <span>display</span>
            </label>
            <label>
               <span>Footer:</span>
               <input type=text name=footer value=~~footer~~ data-smart-update=app.ui.saveSettings>
            </label>
            <label>
               <span>E-mail:</span>
               <input type=email name=contact-email value=~~contact-email~~ placeholder="Address for feedback" data-smart-update=app.ui.saveSettings>
               <a href="../#contact" class=external-site><i class="fa fa-info-circle"></i></a>
            </label>
         </fieldset>
         <fieldset class=settings-tabs>
            <legend>Tabs</legend>
            <div>
               <div data-array=~~pages~~ data-item-id=~~[count]~~ data-item-type=page>
                  <label>
                     <span>#<span>~~[count]~~</span>:</span>
                     <input name=title value=~~title~~ data-smart-update=app.ui.saveSettings placeholder="Title for menu tab">
                  </label>
                  <label>
                     <input name=show type=checkbox data-prop-checked=~~show~~ data-change=app.ui.saveSettings>
                     <span>display</span>
                  </label>
               </div>
            </div>
         </fieldset>
      </section>

      <section class=admin-accounts>
         <h3>Administrators</h3>
         <fieldset>
            <legend>Your profile</legend>
            <div>Email:</div>
            <div><b><?= $_SESSION["user"] ?></b></div>
         </fieldset>
         <fieldset>
            <legend>Accounts</legend>
            <div>
               <div id=user-account class=dna-template>~~[value]~~</div>
            </div>
         </fieldset>
         <fieldset>
            <legend>Current invitations</legend>
            <div>
               <div id=account-invite class=dna-template>
                  <small>~~date~~</small>: <b>~~to~~</b>
               </div>
            </div>
            <i data-placeholder=account-invite>No outstanding invitations.</i>
            <div class=send-invite>
               <button data-click=app.invites.prompt>New account</button>
               <div>
                  <label>
                     <span>Email:</span>
                     <input type=email data-key-up=app.invites.validate placeholder="New user's email address">
                  </label>
                  <button data-click=app.invites.send disabled>Send invitation</button>
               </div>
            </div>
         </fieldset>
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
      Logged into <b><?= $_SERVER["HTTP_HOST"] ?></b>
   </div>
</footer>

<script src="https://cdn.jsdelivr.net/jquery/2/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/crypto-js/3/rollups/sha256.js"></script>
<script src="https://cdn.jsdelivr.net/dna.js/0/dna.min.js"></script>
<script src="file-uploader/fileuploader.js"></script>
<script>var app = {}; app.clientData = <?= appClientData(true) ?>;</script>
<script src="js/library.js"></script>
<script src="js/console.js"></script>
</body>
</html>
