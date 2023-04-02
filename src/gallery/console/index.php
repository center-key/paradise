<?php require "admin-server/security.php"; ?>
<!doctype html>
<!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
<!-- Paradise ~ centerkey.com/paradise                         -->
<!-- GPLv3 ~ Copyright (c) Individual contributors to Paradise -->
<!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
<html lang=en>
<head>
   <meta charset=utf-8>
   <meta name=viewport                   content="width=device-width, initial-scale=1">
   <meta name=robots                     content="noindex, nofollow">
   <meta name=description                content="Paradise PHP Photo Gallery Administrator Console">
   <meta name=apple-mobile-web-app-title content="Console">
   <title>Paradise &bull; Administrator Console</title>
   <link rel=icon             href=https://centerkey.com/paradise/graphics/bookmark.png>
   <link rel=apple-touch-icon href=https://centerkey.com/paradise/graphics/mobile-home-screen.png>
   <link rel=preconnect       href=https://fonts.googleapis.com>
   <link rel=preconnect       href=https://fonts.gstatic.com crossorigin>
   <link rel=stylesheet       href=https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@{{pkg.cdnVersion.fontAwesome}}/css/all.min.css>
   <link rel=stylesheet       href=https://cdn.jsdelivr.net/npm/dropzone@{{pkg.devDependencies.dropzone}}/dist/dropzone.css>
   <link rel=stylesheet       href=https://cdn.jsdelivr.net/npm/dna-engine@{{pkg.devDependencies.dna-engine|version}}/dist/dna-engine.css>
   <link rel=stylesheet       href=https://cdn.jsdelivr.net/npm/web-ignition@{{pkg.devDependencies.web-ignition|version}}/dist/reset.min.css>
   <link rel=stylesheet       href=https://cdn.jsdelivr.net/npm/web-ignition@{{pkg.devDependencies.web-ignition|version}}/dist/layouts/color-blocks.css>
   <link rel=stylesheet       href=paradise-console.min.css>
   <script defer src=https://cdn.jsdelivr.net/npm/fetch-json@{{pkg.devDependencies.fetch-json|version}}/dist/fetch-json.min.js></script>
   <script defer src=https://cdn.jsdelivr.net/npm/jquery@{{pkg.devDependencies.jquery|version}}/dist/jquery.min.js></script>
   <script defer src=https://cdn.jsdelivr.net/npm/dropzone@{{pkg.devDependencies.dropzone}}/dist/dropzone-min.js></script>
   <script defer src=https://cdn.jsdelivr.net/npm/dna-engine@{{pkg.devDependencies.dna-engine|version}}/dist/dna-engine.min.js></script>
   <script defer src=https://cdn.jsdelivr.net/npm/web-ignition@{{pkg.devDependencies.web-ignition|version}}/dist/lib-x.min.js></script>
   <script defer src=paradise-console.min.js></script>
   <script>globalThis.clientData = <?=appClientData()?>;</script>
</head>
<body data-on-load=admin.setup>

<header>
   <aside>
      <button data-href=.. data-target=gallery>Gallery</button>
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
                     <input type=checkbox data-prop-checked=~~display~~
                        data-change=admin.ui.savePortfolio>
                     (show in gallery)
                  </label>
                  <label>
                     <span>Caption:</span>
                     <input value=~~caption~~ data-smart-update=admin.ui.savePortfolio>
                  </label>
                  <label>
                     <span>Description:</span>
                     <textarea data-smart-update=admin.ui.savePortfolio>~~description~~</textarea>
                  </label>
                  <p>
                     <label>
                        <span title="Text to display over image">Badge:</span>
                        <input value=~~badge~~ data-smart-update=admin.ui.savePortfolio>
                     </label>
                     <span>
                        <span>Stamp:</span>
                        <i data-attr-data-icon=~~stampInfo.icon~~ title=~~stampInfo.title~~></i>
                        <code>~~stampInfo.icon~~</code>
                        <label>
                           <input type=checkbox data-prop-checked=~~stamp~~
                              data-change=admin.ui.savePortfolio>
                           <span title="See Stamp icon in Gallery Settings">Show on image</span>
                        </label>
                     </span>
                  </p>
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
                  <img data-attr-src=../~data~/portfolio/~~id~~-small.png
                     data-popup-image=../~data~/portfolio/~~id~~-large.jpg alt=thumbnail>
                  <figcaption>Uploaded: <b>~~uploaded~~</b><br>Image #<b>~~id~~</b></figcaption>
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
               data-href=https://github.com/center-key/paradise/wiki/faq#2-what-kind-of-images-should-i-upload>
            </i>
         </h2>
         <div id=gallery-uploader></div>
      </section>

      <section id=gallery-settings class=dna-template>
         <h2>Gallery Settings</h2>
         <fieldset class=settings-website>
            <legend>Website</legend>
            <label>
               <span>Title:</span>
               <input value=~~title~~ data-smart-update=admin.ui.saveSettings
                  placeholder="Enter website header">
            </label>
            <label>
               <span>Title font:<i data-icon=info-circle data-href=fonts.php></i></span>
               <select data-option=~~titleFont~~ data-change=admin.ui.saveSettings>
                  <option data-array=fonts value=~~[value]~~>~~[value]~~</option>
               </select>
            </label>
            <label>
               <span>Title size:</span>
               <select data-option=~~titleSize~~ data-change=admin.ui.saveSettings>
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
               <input value=~~subtitle~~ data-smart-update=admin.ui.saveSettings
                  placeholder="Enter website subheader">
            </label>
            <div>
               Style:
               <div>
                  <label>
                     <input type=checkbox data-prop-checked=~~darkMode~~
                        data-change=admin.ui.saveSettings><span>Dark mode</span>
                  </label>
                  <label>
                     <input type=checkbox data-prop-checked=~~imageBorder~~
                        data-change=admin.ui.saveSettings><span>Show image border</span>
                  </label>
                  <label>
                     <input type=checkbox data-prop-checked=~~captionCaps~~
                        data-change=admin.ui.saveSettings><span>Caption in all caps</span>
                  </label>
                  <label>
                     <input type=checkbox data-prop-checked=~~captionItalic~~
                        data-change=admin.ui.saveSettings><span>Caption in italics</span>
                  </label>
                  <label>
                     <input type=checkbox data-prop-checked=~~showDescription~~
                        data-change=admin.ui.saveSettings><span>Show description</span>
                  </label>
               </div>
            </div>
            <label>
               <span>
                  Stamp icon (<a href=https://fontawesome.com/cheatsheet>list of icons</a>):
               </span>
               <input type=text value=~~stampIcon~~
                  data-smart-update=admin.ui.saveSettings placeholder="Enter icon name">
            </label>
            <label>
               <span>Stamp title:</span>
               <input type=text value=~~stampTitle~~
                  data-smart-update=admin.ui.saveSettings placeholder="Enter stamp tooltip help">
            </label>
            <div>
               Footer icons:
               <label>
                  <input type=checkbox data-prop-checked=~~ccLicense~~
                     data-change=admin.ui.saveSettings>Creative Commons
               <a href=https://creativecommons.org/licenses/by-sa/4.0><i data-icon=info-circle></i></a>
               </label>
               <label>
                  <input type=checkbox data-prop-checked=~~bookmarks~~
                     data-change=admin.ui.saveSettings>Social share
               </label>
            </div>
            <label>
               <span>Footer link URL:</span>
               <input type=text value=~~linkUrl~~ data-smart-update=admin.ui.saveSettings>
            </label>
            <label>
               <span>
                  Footer text (supports <a href=https://dev.w3.org/html5/html-author/charref
                  class=external-site>HTML entities</a>):
               </span>
               <input type=text value=~~footer~~ data-smart-update=admin.ui.saveSettings>
            </label>
            <label>
               <span>
                  Google Site Verification:
                  <i data-icon=info-circle data-href=https://support.google.com/webmasters/answer/9008080></i>
               </span>
               <input type=text value=~~googleVerification~~ spellcheck=false
                  data-smart-update=admin.ui.saveSettings placeholder="Enter 44 character code">
            </label>
         </fieldset>
         <fieldset class=settings-tabs>
            <legend>Tabs</legend>
            <div>
               <div data-array=~~pages~~ data-item-id=~~[count]~~ data-item-type=page>
                  <label>
                     <span>#<span>~~[count]~~</span>:</span>
                     <input value=~~title~~ data-smart-update=admin.ui.saveSettings
                        placeholder="Title for menu tab">
                  </label>
                  <label>
                     <input type=checkbox data-prop-checked=~~show~~
                        data-change=admin.ui.saveSettings>display
                  </label>
               </div>
            </div>
            <label>
               <span>Email for contact (Tab #3):<i data-icon=info-circle data-href=../#contact></i></span>
               <input type=email value=~~contactEmail~~
                  data-smart-update=admin.ui.saveSettings placeholder="Address for messages">
            </label>
         </fieldset>
      </section>

      <section class=admin-accounts>
         <h2>Gallery Administrators</h2>
         <fieldset>
            <legend>Accounts</legend>
            <div>
               <div id=user-account class=dna-template data-valid=~~valid~~ title=~~lastLogin~~>
                  ~~email~~
               </div>
            </div>
         </fieldset>
         <fieldset>
            <legend>Pending invitations</legend>
            <div>
               <div id=account-invite class=dna-template>
                  <small data-format-date=date>~~expires~~</small>: <b>~~to~~</b>
               </div>
            </div>
            <p data-placeholder=account-invite>No outstanding invitations.</p>
         </fieldset>
         <fieldset>
            <legend>Create account</legend>
            <div class=send-invite>
               <label>
                  <span>Email:</span>
                  <input type=email data-key-up=admin.invites.validate
                     placeholder="New user's email address">
               </label>
               <button data-click=admin.invites.send disabled>Send invitation</button>
            </div>
         </fieldset>
      </section>

      <section class=admin-accounts>
         <h2>Gallery Backup</h2>
         <fieldset>
            <legend>Create backup</legend>
            <div>
               <button data-click=admin.backups.create>Do backup now</button>
            </div>
         </fieldset>
         <fieldset>
            <legend>Backups for download</legend>
            <div class=external-site>
               <div id=backup-file class=dna-template>
                  <a href=~~url~~ title=~~size~~>~~filename~~</a>
               </div>
            </div>
            <div data-placeholder=backup-file>No backups have been created</div>
         </fieldset>
      </section>

   </div>
</main>

<footer id=page-footer class=dna-template>
   <div class=external-site>
      <a href=https://centerkey.com/paradise>Paradise website</a><br>
      <a href=https://github.com/center-key/paradise/wiki/faq>Wiki - Help</a>
   </div>
   <div>
      <b>~~user~~</b><br>
      Logged into <b>~~server~~</b>
   </div>
   <div>
      Paradise v<span>~~version~~</span><br>
      PHP v<span>~~php~~</span>
   </div>
</footer>

<notice-box>
   <header>
      <nav><i data-icon=times data-click=admin.ui.hideNotice></i></nav>
      <h2>Error</h2>
   </header>
   <div>
      <p></p>
      <b></b>
      <ul></ul>
   </div>
</notice-box>

</body>
</html>
