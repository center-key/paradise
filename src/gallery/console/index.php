<?php require "php/security.php"; ?>
<!doctype html>
<!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
<!--  PPAGES - PHP Portfolio Art Gallery Exhibit to Showcase   -->
<!--  centerkey.com/ppages                                     -->
<!--  GPLv3 - Copyright (c) individual contributors            -->
<!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
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
<link rel=stylesheet       href="style.css">
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
         <p>Wow!</p>
      </section>
   </div>
   <div>
      <section>
         <h3>Transfer Photos to Gallery</h3>
         <p>Wow!</p>
      </section>
      <section>
         <h3>Gallery Settings</h3>
         <p>Wow!</p>
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
      You are logged into <b><?= $_SERVER["HTTP_HOST"] ?></b><br>
      PPAGES <?= $version ?>
   </div>
</footer>

<script src="https://cdn.jsdelivr.net/jquery/2/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/crypto-js/3/rollups/sha256.js"></script>
<script src="https://cdn.jsdelivr.net/dna.js/0/dna.min.js"></script>
<script src="js/auth.js"></script>
<script src="js/console.js"></script>
<script src="js/app.js"></script>
</body>
</html>
