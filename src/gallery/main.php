<?php
/////////////////////////////////////////////////
// PPAGES ~ centerkey.com/ppages               //
// GPL ~ Copyright (c) individual contributors //
/////////////////////////////////////////////////

function currentHour() {
   return intval(time() / (60 * 60));
   }

function generateVerification() {
   return sha1($_SERVER["HTTP_HOST"] . currentHour());
   }

function validVerification($verification) {
   return (
      $verification == sha1($_SERVER["HTTP_HOST"] . currentHour()) ||
      $verification == sha1($_SERVER["HTTP_HOST"] . currentHour() - 1));
   }

function currentPage($pages) {
   $request = $_GET["page"];
   foreach ($pages as $page)
      if ($request == $page->name && $page->show)
         return $page->name;
   return $request == "thanks" ? "thanks" : "gallery";
   }

function menuItemHtml($page, $name, $current) {
   $link = $page == "gallery" ? "." : "?page=" . $page;
   $class = $page == $current ? " class=current" : "";
   return "<li$class><a href='$link' class=plain>$name</a></li>\n";
   }

function displayMenuBar($pages, $current) {
   echo "<ul class=navigation-bar>";
   foreach ($pages as $page)
      if ($page->show)
         echo menuItemHtml($page->name, $page->title, $current);
   echo "</ul>\n";
   }

function displayGallery($italicTitle, $capsTitle) {
   $fieldId =          "id";
   $fieldCaption =     "caption";
   $fieldDescription = "description";
   $fieldBadge =       "badge";
   $captionClass = "b " . ($italicTitle ? "i " : "") . ($capsTitle ? "c" : "");
   $captionStyleStart =  htmlspecialchars("<span class='$captionClass'>", ENT_QUOTES);
   $captionStyleEnd =    htmlspecialchars("</span><br>");
   $galleryDb = readDb("data/gallery-db.json");
   echo "<div class=gallery>\n";
   foreach ($galleryDb as $imageDb) {
      $id =          $imageDb->{$fieldId};
      $caption =     $imageDb->{$fieldCaption};
      $description = $imageDb->{$fieldDescription};
      $badge =       $imageDb->{$fieldBadge};
      if (!empty($badge))
         $badge = "<div class=badge>$badge</div>";
      echo "<div class=image>$badge<a href='data/portfolio/$id-large.jpg' rel='lightbox{gallery}'
         title='$captionStyleStart$caption$captionStyleEnd$description'><img
         src='data/portfolio/$id-small.png' alt='Thumbnail'
         title='Click for full size, and right arrow to advance'></a>
         <p class='$captionClass'>$caption</p></div>\n";
      }
   echo "</div>\n";
   }

function contactPageHtml() {
   $verification = generateVerification();
   return "
      <h3>Contact the Artist</h3>
      <form class=feedback method=post>
         <input type=hidden name=verification value=$verification>
         <p><label>Message:</label>
            <textarea name=message rows=6 cols=50></textarea></p>
         <p><label>Name:</label>
            <input name=name size=35></p>
         <p><label>Email:</label>
            <input name=email size=40></p>
         <p><label>&nbsp;</label>
            <button>Send Message</button></p>
      </form>
      <p class=corner-msg>Gallery powered by <a href='http://centerkey.com/ppages/'>PPAGES</a></p>
      ";
   }

function contactThanksHtml() {
   $msg = isset($_GET["invalid"]) ? "Error!" : "Your message has been sent.";
   return "<h3>Thank You</h3><p>$msg</p>
      <p><a href='.'><button>&lt;&lt; Back to Gallery</button></a></p>";
   }

function readCustomPage($name) {
   $filename = "data/page-$name.html";
   if (!file_exists($filename)) {
      touch($filename);
      $defaultCustomPageHtml =
         "<section>\n   <h2>This page is under construction.</h2>\n   <hr>\n   <p>Edit: " .
         realpath($filename) . "</p>\n</section>\n";
      file_put_contents($filename, $defaultCustomPageHtml);
      }
   readfile($filename);
   }

function displayPage($name) {
   echo "<div class=page>\n";
   if ($name == "contact")
      echo contactPageHtml();
   else if ($name == "thanks")
      echo contactThanksHtml();
   else
      readCustomPage($name);
   echo "</div>\n";
   }

function displayCreativeCommons() {
   echo "
      <a class=external-site rel=license href='http://creativecommons.org/licenses/by-sa/4.0/'>
         <img src='https://i.creativecommons.org/l/by-sa/4.0/80x15.png' alt='Creative Commons License'
            title='This work is licensed under a Creative Commons Attribution-ShareAlike 4.0 International License.'>
      </a>\n";
   }

function displayFooter($statement, $license, $bookmarks) {
   echo "<footer>\n";
   if ($license)
      displayCreativeCommons();
   if ($bookmarks)
      echo "<div id=social-buttons class=plain></div>\n";
   echo "<div>$statement</div>\n</footer>\n";
   }

?>
