<?php require "server/security.php"; ?>
<?php require "server/console.php"; ?>
<!doctype html>
<!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
<!-- Paradise ~ centerkey.com/paradise                         -->
<!-- GPLv3 ~ Copyright (c) individual contributors to Paradise -->
<!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
<html lang=en>
<head>
<meta charset=utf-8>
<meta name=viewport                   content="width=device-width, initial-scale=1">
<meta name=apple-mobile-web-app-title content="Console">
<title>Paradise &bull; Administrator Console</title>
<link rel=icon             href=https://centerkey.com/paradise/graphics/bookmark.png>
<link rel=apple-touch-icon href=https://centerkey.com/paradise/graphics/mobile-home-screen.png>
<link rel=stylesheet       href=https://use.fontawesome.com/releases/v5.1.0/css/all.css>
<link rel=stylesheet       href=https://cdn.jsdelivr.net/npm/dna.js@1.4/dna.css>
<link rel=stylesheet       href=https://centerkey.com/css/reset.css>
<link rel=stylesheet       href=https://centerkey.com/css/layouts/color-blocks.css>
<link rel=stylesheet       href=admin.css>
</head>
<body data-on-load=admin.setup>

<header>
   <aside>
      <button data-href=.. class=external-site>Gallery</button>
      <button data-href=sign-out>Sign out</button>
   </aside>
   <h1>Paradise PHP Photo Gallery</h1>
   <h2>Administrator Console</h2>
</header>

<main class=external-site>
   <div>

      <section>
         <h2>Status</h2>
         <div id=status-msg></div>
      </section>

      <section>
         <h2>Image Portfolio</h2>
         <div>
            <div id=portfolio-image class=dna-template data-item-id=~~id~~>
               <form>
                  <label data-class=~~display,display-on,display-off~~>
                     <span>Display:</span>
                     <input name=display type=checkbox data-prop-checked=~~display~~
                        data-change=admin.ui.savePortfolio>
                     (show in gallery)
                  </label>
                  <label>
                     <span>Caption:</span>
                     <input name=caption value=~~caption~~ data-smart-update=admin.ui.savePortfolio>
                  </label>
                  <label>
                     <span>Description:</span>
                     <textarea name=description
                        data-smart-update=admin.ui.savePortfolio>~~description~~</textarea>
                  </label>
                  <label>
                     <span>Badge:</span>
                     <input name=badge value=~~badge~~ data-smart-update=admin.ui.savePortfolio>
                  </label>
                  <div class=actions>
                     <i data-icon=arrow-up   data-click=admin.ui.move data-move=up></i>
                     <i data-icon=arrow-down data-click=admin.ui.move data-move=down></i>
                     <i data-icon=times class=icon-popup-anchor></i>
                     <div>
                        <p>Permanently delete this image?</p>
                        <button data-click=admin.ui.delete>Delete image</button>
                     </div>
                  </div>
               </form>
               <figure>
                  <img data-attr-src=../~data~/portfolio/~~id~~-large.jpg
                     data-href=../~data~/portfolio/~~id~~-large.jpg alt=thumbnail>
                  <figcaption>Uploaded: <b>~~uploaded~~</b></figcaption>
               </figure>
            </div>
         </div>
         <div data-placeholder=portfolio-image>No images in your portfolio yet.</div>
      </section>

   </div>
   <div>

      <section>
         <h2>
            Gallery Uploader
            <i data-icon=info-circle
               data-href=https://github.com/center-key/paradise/wiki/faq#1-what-kind-of-images-should-i-upload>
            </i>
         </h2>
         <i id=processing-files data-icon=spinner class=fa-spin></i>
         <div id=file-uploader></div>
      </section>

      <section id=gallery-settings class=dna-template>
         <h2>Gallery Settings</h2>
         <fieldset class=settings-website>
            <legend>Website</legend>
            <label>
               <span>Title:</span>
               <input name=title value=~~title~~ data-smart-update=admin.ui.saveSettings
                  placeholder="Enter website header">
            </label>
            <label>
               <span>Title font:<i data-icon=info-circle data-href=fonts.php></i></span>
               <select name=title-font data-option=~~title-font~~ data-change=admin.ui.saveSettings>
                  <?=displayFontOptions()?>
               </select>
            </label>
            <label>
               <span>Title size:</span>
               <select name=title-size data-option=~~title-size~~ data-change=admin.ui.saveSettings>
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
               <input name=subtitle value=~~subtitle~~ data-smart-update=admin.ui.saveSettings
                  placeholder="Enter website subheader">
            </label>
            <div>
               Captions:
               <div>
                  <label>
                     <input type=checkbox name=caption-caps data-prop-checked=~~caption-caps~~
                        data-change=admin.ui.saveSettings><span>all caps</span>
                  </label>
                  <label>
                     <input type=checkbox name=caption-italic data-prop-checked=~~caption-italic~~
                        data-change=admin.ui.saveSettings><i>italic</i>
                  </label>
               </div>
            </div>
            <div>
               Creative Commons:
               <a href=https://creativecommons.org/licenses/by-sa/4.0><i data-icon=info-circle></i></a>
               <label>
                  <input type=checkbox name=cc-license data-prop-checked=~~cc-license~~
                     data-change=admin.ui.saveSettings>display
               </label>
            </div>
            <div>
               <span>Social share icons:</span>
               <label>
                  <input type=checkbox name=bookmarks data-prop-checked=~~bookmarks~~
                     data-change=admin.ui.saveSettings>display
               </label>
            </div>
            <label>
               <span>Footer:</span>
               <input type=text name=footer value=~~footer~~ data-smart-update=admin.ui.saveSettings>
            </label>
            <label>
               <span>Email:<i data-icon=info-circle data-href=../#contact></i></span>
               <input type=email name=contact-email value=~~contact-email~~
                  data-smart-update=admin.ui.saveSettings placeholder="Address for messages">
            </label>
         </fieldset>
         <fieldset class=settings-tabs>
            <legend>Tabs</legend>
            <div>
               <div data-array=~~pages~~ data-item-id=~~[count]~~ data-item-type=page>
                  <label>
                     <span>#<span>~~[count]~~</span>:</span>
                     <input name=title value=~~title~~ data-smart-update=admin.ui.saveSettings
                        placeholder="Title for menu tab">
                  </label>
                  <label>
                     <input name=show type=checkbox data-prop-checked=~~show~~
                        data-change=admin.ui.saveSettings>display
                  </label>
               </div>
            </div>
         </fieldset>
      </section>

      <section class=admin-accounts>
         <h2>Administrators</h2>
         <fieldset>
            <legend>Accounts</legend>
            <div>
               <div id=user-account class=dna-template>~~[value]~~</div>
            </div>
         </fieldset>
         <fieldset>
            <legend>Pending invitations</legend>
            <div>
               <div id=account-invite class=dna-template>
                  <small>~~date~~</small>: <b>~~to~~</b>
               </div>
            </div>
            <p data-placeholder=account-invite>No outstanding invitations.</p>
         </fieldset>
         <fieldset>
            <legend>Create account</legend>
            <div class=send-invite>
               <button data-click=admin.invites.prompt>Send invitation</button>
               <div>
                  <label>
                     <span>Email:</span>
                     <input type=email data-key-up=admin.invites.validate
                        placeholder="New user's email address">
                  </label>
                  <button data-click=admin.invites.send disabled>Send invitation</button>
               </div>
            </div>
         </fieldset>
      </section>

   </div>
</main>

<footer class=external-site>
   <div>
      <a href=http://centerkey.com/paradise>Paradise website</a><br>
      <a href=https://github.com/center-key/paradise/wiki/faq>Wiki - Help</a>
   </div>
   <div>
      <b><?=$_SESSION["user"]?></b><br>
      Logged into <b><?=$_SERVER["SERVER_NAME"]?></b>
   </div>
   <div>
      Paradise v<?=$version?><br>
      <a href=https://github.com/center-key/paradise/blob/master/src/gallery/license.txt>GPLv3</a>
   </div>
</footer>

<script src=https://cdn.jsdelivr.net/npm/jquery@3.3/dist/jquery.min.js></script>
<script src=https://cdn.jsdelivr.net/npm/crypto-js@3.1/crypto-js.js></script>
<script src=https://cdn.jsdelivr.net/npm/dna.js@1.4/dna.min.js></script>
<script src=admin.js></script>
<script>window.clientData = <?=appClientData(true)?>;</script>
</body>
</html>
