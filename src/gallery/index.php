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
<link rel=icon       href="favicon.png">
<link rel=stylesheet href="https://cdn.jsdelivr.net/fontawesome/4/css/font-awesome.min.css">
<link rel=stylesheet href="css/reset.css" >
<link rel=stylesheet href="css/style.css" >
<link rel=stylesheet href="data/style.css">
<link rel=stylesheet href="slimbox2/slimbox2.css">
<?php
   include "console/database.php";
   include "console/console-settings.php";
   include "main.php";
   function settingToBoolean($str) { return $str == "on"; }
   $settingsDb = readSettings("data/settings-db.json");
   $title =       $settingsDb->{$settingsFieldTitle . "-html"};
   $titleFont =   $settingsDb->{$settingsFieldTitleFont};
   $titleSize =   $settingsDb->{$settingsFieldTitleSize};
   $subtitle =    $settingsDb->{$settingsFieldSubtitle . "-html"};
   $titleItalic = settingToBoolean($settingsDb->{$settingsFieldCaptionItalic});
   $titleCaps =   settingToBoolean($settingsDb->{$settingsFieldCaptionCaps});
   $footer =      $settingsDb->{$settingsFieldFooter . "-html"};
   $license =     settingToBoolean($settingsDb->{$settingsFieldCcLicense});
   $bookmarks =   settingToBoolean($settingsDb->{$settingsFieldBookmarks});
   $pages =       $settingsDb->{$settingsFieldPages};
   echo "<title>$title &bull; $subtitle</title>\n";
   echo "<style>
      @import url('http://fonts.googleapis.com/css?family=" . urlencode($titleFont) . "');
      h1 { font-family: '$titleFont', sans-serif; font-size: $titleSize; }
      </style>\n";
?>
</head>
<body>

<?php
   echo "<header>\n";
   echo "   <h1>$title</h1>\n";
   echo "   <h2>$subtitle</h2>\n";
   echo "</header>\n";
   echo "<main>\n";
   #echo "<pre>";  print_r($settingsDb);  echo "</pre>";
   $current = currentPage($pages);
   displayMenuBar($pages, $current);
   if ($current == "gallery")
      displayGallery($titleItalic, $titleCaps);
   else
      displayPage($current);
   echo "</main>\n";
   displayFooter($footer, $license, $bookmarks);
?>

<script src="https://cdn.jsdelivr.net/jquery/2/jquery.min.js"></script>
<script src="slimbox2/slimbox2.js"></script>
<script src="js/library.js"></script>
<script src="js/app.js"></script>
</body>
</html>
