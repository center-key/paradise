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
   return "<li$class><a href='$link'>$name</a>\n";
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
   return "<h3>Contact the Artist</h3>
      <script>document.writeln(
         '<fo' + 'rm method=post act' + 'ion=feed' + 'back.php>');</script>
      <input type=hidden name=verification value=$verification>
      <br>
      <p><label>Message:</label>
         <textarea name=message rows=6 cols=50></textarea></p>
      <p><label>Name:</label>
         <input name=name size=35></p>
      <p><label>Email:</label>
         <input name=email size=40></p>
      <p><label>&nbsp;</label>
         <button>Send Message</button></p>
      <script>document.writeln(
         '<input type=hidden name=real value=window.location.hostname><\\/form>');</script>
      <p class=corner-msg>Gallery powered by
         <a href='http://centerkey.com/ppages/'>PPAGES</a></p>";
   }

function contactThanksHtml() {
   $msg = isset($_GET["invalid"]) ? "Error!" : "Your message has been sent.";
   return "<h3>Thank You</h3><p>$msg</p>
      <p><a href='.'><button>&lt;&lt; Back to Gallery</button></a></p>";
   }

function displayPage($name) {
   echo "<div class=page>\n";
   if ($name == "contact")
      echo contactPageHtml();
   else if ($name == "thanks")
      echo contactThanksHtml();
   else
      readfile("data/page-$name.html");
   echo "</div>\n";
   }

function displayCreativeCommons() {
   echo "&nbsp;&nbsp;
      <a class=external-site rel=license
      href='http://creativecommons.org/licenses/by-sa/3.0/'><img
      alt='Creative Commons License'
      title='These works are licensed under a Creative Commons Attribution-ShareAlike 3.0 Unported License'
      src='http://i.creativecommons.org/l/by-sa/3.0/80x15.png'></a>\n";
   }

function displayFooter($statement, $license, $bookmarks) {
   echo "<div class=footer>\n";
   if ($license)
      displayCreativeCommons();
   if ($bookmarks)
      echo "<div id=social-buttons class=plain></div>\n";
   echo "<div>$statement</div>\n</div>\n";
   }

?>
