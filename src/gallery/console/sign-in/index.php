<?php $noAuth = true; $redirectAuth = ".."; require "../php/security.php"; ?>
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
<link rel=stylesheet       href=../css/style.css>
</head>
<body>

<header>
   <aside>
      <button data-href=../..>Visit Gallery</button>
   </aside>
   <h1>Paradise PHP Photo Gallery</h1>
   <h2>Administrator Console</h2>
</header>

<main>
   <div>
      <section class=component-security data-on-load=admin.login.setup>
         <h2>
            Authentication
            <i data-icon=info-circle class=external-site
               data-href=https://github.com/center-key/paradise/wiki/faq#4-is-my-password-send-over-the-internet-in-clear-text></i>
         </h2>
         <form>
            <h3 class=for-create>Create account</h3>
            <p class=for-create>
               No user accounts exist yet.&nbsp; Create your account to continue the setup.
            </p>
            <p class=error-message></p>
            <label class=invite-code>
               <span>Invite code:</span>
               <input placeholder="Enter invite code">
            </label>
            <label>
               <span>Email:</span>
               <input type=email data-enter-key=admin.login.submit
                  placeholder="Enter your email address" required>
            </label>
            <label>
               <span>Password:</span>
               <input type=password data-enter-key=admin.login.submit
                  placeholder="Enter your password" required>
            </label>
            <label class=for-create>
               <span>Confirm:</span>
               <input type=password data-enter-key=admin.login.submit
                  placeholder="Re-enter your password">
            </label>
            <nav>
               <button data-click=admin.login.submit>Sign in</button>
            </nav>
            <p class=external-site>
               <a href=https://github.com/center-key/paradise/wiki/faq#5-how-do-i-reset-my-password>
                  <small>Forget your password?</small>
               </a>
            </p>
         </form>
      </section>
      <?php include "../../~data~/login-message.html"; ?>
   </div>
</main>

<footer>
   <div class=external-site>
      <a href=http://centerkey.com/paradise>Paradise website</a><br>
      <a href=https://github.com/center-key/paradise/wiki/faq>Wiki - Help</a>
   </div>
   <div>
      Paradise v<?= $version ?><br>
      <a href=https://github.com/center-key/paradise/blob/master/src/gallery/license.txt>GPLv3</a>
   </div>
</footer>

<script src=https://cdn.jsdelivr.net/npm/jquery@3.3/dist/jquery.min.js></script>
<script src=https://cdn.jsdelivr.net/npm/crypto-js@3.1/crypto-js.js></script>
<script src=https://cdn.jsdelivr.net/npm/dna.js@1.4/dna.min.js></script>
<script src=../../js/library.js></script>
<script src=../js/login.js></script>
<script>window.clientData = <?= appClientData(false) ?>;</script>
</body>
</html>
