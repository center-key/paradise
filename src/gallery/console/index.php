<?php session_start(); ?>
<!doctype html>
<!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
<!--  PHP Portfolio Art Gallery Exhibit Showcase (PPAGES)                      -->
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
<link rel=icon       href="../graphics/bookmark.png" type="image/png">
<link rel=stylesheet href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css">
<link rel=stylesheet href="../css/reset.css">
<link rel=stylesheet href="style.css">
<link rel=stylesheet href="fileuploader.css">
<?php
   include "library.php";
   function successfullLogin() {
      return $_POST["action"] == "login" &&
         accountValidHash($_POST["username"], $_POST["hash"]);
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

<div class=header>
   <div>
      <button onclick="window.open('..');" class=space-below-half>Visit Gallery</button><br>
      <?php if ($loggedIn) { ?>
      <button onclick="window.location.href='?logout';">Logout</button><br>
      <?php } ?>
   </div>
   <h1>PHP Portfolio Art Gallery Exhibit Showcase (PPAGES)</h1>
   <h2>Gallery Management Console</h2>
</div>

<?php $loggedIn ? displayConsole() : displayLogin(); ?>

<?php if ($loggedIn) echo "
   <div class=footer>
      <div>
         You are logged into <b>" . $_SERVER['HTTP_HOST'] . "</b> as <b>" .
         $_SESSION["username"] . "</b><br>
         PPAGES $version
      </div>
      PHP Portfolio Art Gallery Exhibit Showcase (PPAGES)<br>
      A web application to manage and display a photo gallery
   </div>";
?>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
<script src="../js/library.js"></script>
<script src="js/sha1hash.js"></script>
<script src="js/user-auth.js"></script>
<script src="js/console.js"></script>
<script src="js/start.js"></script>
</body>
</html>
