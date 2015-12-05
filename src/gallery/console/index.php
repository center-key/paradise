<?php session_start(); ?>
<!doctype html>
<!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
<!--  PPAGES - PHP Portfolio Art Gallery Exhibit Showcase                      -->
<!--  http://centerkey.com/ppages                                              -->
<!--                                                                           -->
<!--  GNU General Public License:                                              -->
<!--  This program is free software; you can redistribute it and/or modify it  -->
<!--  under the terms of the GNU General Public License as published by the    -->
<!--  Free Software Foundation; either version 2 of the License, or (at your   -->
<!--  option) any later version.                                               -->
<!--                                                                           -->
<!--  This program is distributed in the hope that it will be useful, but      -->
<!--  WITHOUT ANY WARRANTY; without even the implied warranty of               -->
<!--  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                     -->
<!--                                                                           -->
<!--  See the GNU General Public License at http://www.gnu.org for more        -->
<!--  details.                                                                 -->
<!--                                                                           -->
<!--  Copyright (c) individual contributors to the PPAGES project              -->
<!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
<html>
<head>
<meta charset=utf-8>
<title>PPAGES &bull; Gallery Management Console</title>
<link rel=icon             href="../graphics/bookmark.png">
<link rel=apple-touch-icon href="graphics/mobile-home-screen.png">
<link rel=stylesheet       href="https://cdn.jsdelivr.net/fontawesome/4/css/font-awesome.min.css">
<link rel=stylesheet       href="https://cdn.jsdelivr.net/jquery.ui/1/jquery-ui.min.css">
<link rel=stylesheet       href="https://cdn.jsdelivr.net/dna.js/0/dna.css">
<link rel=stylesheet       href="../css/reset.css">
<link rel=stylesheet       href="style.css">
<link rel=stylesheet       href="fileuploader.css">
<?php
   include "php/library.php";
   function successfullLogin() {
      return $_POST["action"] == "login" && accountValidHash($_POST["username"], $_POST["hash"]);
      }
   if (isset($_GET["logout"]))
      session_unset();
   if (isset($_SESSION[$authTimestamp]) && time() - $_SESSION[$authTimestamp] > $sessionTimout)
      session_unset();
   if (isset($_SESSION[$authTimestamp]) || successfullLogin())
      $_SESSION[$authTimestamp] = time();
   $loggedIn = isset($_SESSION[$authTimestamp]);
?>
</head>
<body>

<header>
   <aside>
      <button data-href=".." class=space-below-half>Visit Gallery</button><br>
      <?php if ($loggedIn) { ?>
      <button data-href="?logout">Sign out</button><br>
      <?php } ?>
   </aside>
   <h1>PPAGES &ndash; PHP Portfolio Art Gallery Exhibit Showcase</h1>
   <h2>Gallery Management Console</h2>
</header>

<?php $loggedIn ? displayConsole() : displayLogin(); ?>

<?php if ($loggedIn) echo "
   <footer>
      <div>
         PPAGES &ndash; PHP Portfolio Art Gallery Exhibit Showcase<br>
         A web application to manage and display a photo gallery
      </div>
      <div>
         You are logged into <b>" . $_SERVER['HTTP_HOST'] . "</b> as <b>" . $_SESSION["username"] .
         "</b><br>PPAGES $version
      </div>
   </footer>";
?>

<script src="https://cdn.jsdelivr.net/jquery/2/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.ui/1/jquery-ui.min.js"></script>
<script src="https://cdn.jsdelivr.net/dna.js/0/dna.min.js"></script>
<script src="../js/library.js"></script>
<script src="js/sha1hash.js"></script>
<script src="js/user-auth.js"></script>
<script src="js/console.js"></script>
<script src="js/start.js"></script>
</body>
</html>
