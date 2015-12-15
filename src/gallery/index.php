<!doctype html>
<!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
<!--  PPAGES - PHP Portfolio Art Gallery Exhibit Showcase  -->
<!--  centerkey.com/ppages - Open Source (GPL)             -->
<!--  Copyright (c) individual contributors                -->
<!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
<html>
<head>
<meta charset=utf-8>
<meta name=apple-mobile-web-app-title content="Gallery">
<link rel=icon             href="http://centerkey.com/ppages/graphics/bookmark.png">
<link rel=apple-touch-icon href="http://centerkey.com/ppages/graphics/mobile-home-screen.png">
<link rel=stylesheet       href="https://cdn.jsdelivr.net/fontawesome/4/css/font-awesome.min.css">
<link rel=stylesheet       href="https://cdn.jsdelivr.net/slimbox/2/css/slimbox2.css">
<link rel=stylesheet       href="https://cdn.jsdelivr.net/dna.js/0/dna.css">
<link rel=stylesheet       href="css/reset.css" >
<link rel=stylesheet       href="css/style.css" >
<link rel=stylesheet       href="data/style.css">

<?php
   $dataFolder = "data/";
   include "php/common.php";

   include "console/php/database.php";
   include "console/php/console-settings.php";
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
<script src="https://cdn.jsdelivr.net/slimbox/2/js/slimbox2.min.js"></script>
<script src="https://cdn.jsdelivr.net/dna.js/0/dna.min.js"></script>
<script src="js/library.js"></script>
<script src="js/app.js"></script>
</body>
</html>
