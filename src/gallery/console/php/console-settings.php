<?php
/////////////////////////////////////////////////
// PPAGES ~ centerkey.com/ppages               //
// GPL ~ Copyright (c) individual contributors //
/////////////////////////////////////////////////

// Settings

$settingsFieldTitle =         "title";
$settingsFieldTitleFont =     "title-font";
$settingsFieldTitleSize =     "title-size";
$settingsFieldSubtitle =      "subtitle";
$settingsFieldFooter =        "footer";
$settingsFieldCaptionItalic = "caption-italic";  //boolean
$settingsFieldCaptionCaps =   "caption-caps";  //boolean
$settingsFieldCcLicense =     "cc-license";  //boolean
$settingsFieldBookmarks =     "bookmarks";  //boolean
$settingsFieldEmail =         "email";
$settingsFieldPages =         "pages";  //array

$settingsFields = array(
   $settingsFieldTitle,
   $settingsFieldTitleFont,
   $settingsFieldTitleSize,
   $settingsFieldSubtitle,
   $settingsFieldFooter,
   $settingsFieldCaptionItalic,
   $settingsFieldCaptionCaps,
   $settingsFieldCcLicense,
   $settingsFieldBookmarks,
   $settingsFieldEmail,
   $settingsFieldPages);
$settingsFieldsBoolean = array(
   $settingsFieldCaptionItalic,
   $settingsFieldCaptionCaps,
   $settingsFieldCcLicense,
   $settingsFieldBookmarks);
$settingsFieldsHtml = array(
   $settingsFieldTitle,
   $settingsFieldSubtitle,
   $settingsFieldFooter);

class SitePage {
   public $name;
   public $title;
   public $show;
   function __construct($name, $title, $show) {
      $this->name =  $name;
      $this->title = $title;
      $this->show =  $show;
      }
   }

function bbcodeToHtml($bbcode) {
   //Turn text with bbcode into displayable HTML (supports: b, i, url, and entities)
   $code = array('/\[b\](.*?)\[\/b\]/is', '/\[i\](.*?)\[\/i\]/is', '/\[url\=(.*?)\](.*?)\[\/url\]/is', '/\[\&amp\;(.*?)\;\]/is');
   $html = array('<b>$1</b>',             '<i>$1</i>',             '<a href="$1">$2</a>',              '&$1;');
   return preg_replace($code, $html, $bbcode);
   }

function getGoogleFonts() {  //see https://www.google.com/fonts
   return array("Allan", "Allerta", "Allerta Stencil", "Anonymous Pro", "Arimo",
      "Arvo", "Bentham", "Bowlby One SC", "Buda", "Cabin", "Cantarell", "Cardo",
      "Cherry Cream Soda", "Chewy", "Chango", "Coda", "Copse",
      "Corben", "Cousine", "Covered By Your Grace", "Crimson Text", "Cuprum",
      "Droid Sans", "Droid Sans Mono", "Droid Serif", "Geo", "Gruppo",
      "Homemade Apple", "IM Fell",
      "Inconsolata", "Josefin Sans", "Josefin Slab", "Just Another Hand",
      "Kenia", "Kristi", "Lato", "Lekton", "Lobster",
      "Merriweather", "Molengo", "Neucha", "Neuton",
      "Nobile", "Old Standard TT", "Orbitron",
      "PT Sans", "Philosopher", "Puritan", "Raleway", "Reenie Beanie",
      "Rock Salt", "Slackey", "Sniglet", "Special Elite",
      "Syncopate", "Tangerine", "Tinos", "Ubuntu", "UnifrakturCook",
      "UnifrakturMaguntia", "Vibur", "Vollkorn", "Yanone Kaffeesatz");
   }

function getDefaultSettings() {
   global $settingsFieldTitle, $settingsFieldTitleFont, $settingsFieldTitleSize,
      $settingsFieldSubtitle, $settingsFieldFooter,
      $settingsFieldCaptionItalic, $settingsFieldCaptionCaps,
      $settingsFieldCcLicense, $settingsFieldBookmarks, $settingsFieldPages;
   $pages = array();
   $pages[] = new SitePage("gallery", "Gallery", true);
   $pages[] = new SitePage("artist",  "Artist",  false);
   $pages[] = new SitePage("contact", "Contact", true);
   return array(
      $settingsFieldTitle =>         "My Gallery",
      $settingsFieldTitleFont =>     "Reenie Beanie",
      $settingsFieldTitleSize =>     "400%",
      $settingsFieldSubtitle =>      "Photography [&amp;bull;] Art Studio",
      $settingsFieldFooter =>        "Copyright [&amp;copy;] " . gmdate("Y"),
      $settingsFieldCaptionItalic => true,
      $settingsFieldCaptionCaps =>   false,
      $settingsFieldCcLicense =>     false,
      $settingsFieldBookmarks =>     true,
      $settingsFieldPages =>         $pages);
   }

function readSettings($settingsDbFile) {
   global $settingsFieldsHtml;
   global $settingsFieldsBoolean;  //temporary backwards compatibility
   $settingsDb = readDb($settingsDbFile);
   foreach (getDefaultSettings() as $fieldName => $default)
      if ($settingsDb->{$fieldName} === null)
         $settingsDb->{$fieldName} = $default;
   foreach ($settingsFieldsHtml as $fieldName)
      if ($settingsDb->{$fieldName . "-html"} === null)
         $settingsDb->{$fieldName . "-html"} = bbcodeToHtml($settingsDb->{$fieldName});
   foreach ($settingsFieldsBoolean as $fieldName) $settingsDb->{$fieldName} = $settingsDb->{$fieldName} == true || $settingsDb->{$fieldName} == "on";  //temporary backwards compatibility
   return $settingsDb;
   }

function fontOptions($defaultFont) {
   global $settingsFieldTitleFont;
   $options = "<select name=$settingsFieldTitleFont>";
   foreach (getGoogleFonts() as $fontName)
      $options .= "<option" . ($fontName == $defaultFont ? " selected" : "") .
         ">$fontName</option>\n";
   $options .= "</select>";
   return $options;
   }

function sizeOptions($defaultSize) {
   global $settingsFieldTitleSize;
   $options = "<select name=$settingsFieldTitleSize>";
   for ($size = 1; $size < 10; $size++)
      $options .= "<option" . ($size == substr($defaultSize, 0, 1) ?
         " selected" : "") . ">$size" . "00%</option>\n";
   $options .= "</select>";
   return $options;
   }

function checkedHtml($fieldValue) {
   return $fieldValue ? " checked" : "";
   }

function displaySettingsWebsite($settingsDb) {
   global $actionUpdateSettings;
   global $settingsFieldTitle, $settingsFieldTitleFont, $settingsFieldTitleSize,
      $settingsFieldSubtitle, $settingsFieldFooter,
      $settingsFieldCaptionItalic, $settingsFieldCaptionCaps,
      $settingsFieldCcLicense, $settingsFieldBookmarks,
      $settingsFieldEmail;
   $emailHelp = "Information filled out by users in the contact form is sent to this e-mail address";
   $title =     $settingsDb->{$settingsFieldTitle};
   $fonts =     fontOptions($settingsDb->{$settingsFieldTitleFont});
   $sizes =     sizeOptions($settingsDb->{$settingsFieldTitleSize});
   $subtitle =  $settingsDb->{$settingsFieldSubtitle};
   $footer =    $settingsDb->{$settingsFieldFooter};
   $italic =    checkedHtml($settingsDb->{$settingsFieldCaptionItalic});
   $caps =      checkedHtml($settingsDb->{$settingsFieldCaptionCaps});
   $cc =        checkedHtml($settingsDb->{$settingsFieldCcLicense});
   $bookmarks = checkedHtml($settingsDb->{$settingsFieldBookmarks});
   $email =     $settingsDb->{$settingsFieldEmail};
   echo "<fieldset id=settings-website><legend>Website</legend>
      <label>Title:
         <input type=text name=$settingsFieldTitle value='$title'>
      </label>
      <label>
         Title Font:
         <a href='https://www.google.com/fonts' class=external-site title='Click to see fonts'>
            <i class='fa fa-info-circle'></i>
         </a>
         $fonts
      </label>
      <label>
         Title Size: $sizes
      </label>
      <label>
         Subtitle:
         <input type=text name=$settingsFieldSubtitle value='$subtitle'>
      </label>
      <label>
         Footer:
         <input type=text name=$settingsFieldFooter value='$footer'>
      </label>
      <div class=input-group-down>
         <label>Image Captions:</label>
         <div class=input-group>
            <label><input type=checkbox name=$settingsFieldCaptionItalic$italic>
               <i>italic</i>
            </label>
            <label><input type=checkbox name=$settingsFieldCaptionCaps$caps>
               <span class=small>ALL CAPS</span>
            </label>
         </div>
      </div>
      <div class=input-group-down>
         <label>Display:</label>
         <div class=input-group>
            <label>
               <input type=checkbox name=$settingsFieldCcLicense$cc>
               Creative Commons
               <a href='http://creativecommons.org/licenses/by-sa/4.0/' class=external-site title='CC BY 4.0'>
                  <i class='fa fa-info-circle'></i>
               </a>
            </label>
            <label>
               <input type=checkbox name=$settingsFieldBookmarks$bookmarks>
               Social Buttons
            </label>
         </div>
      </div>
      <label>
         E-mail:
         <a href='../?page=contact' class=external-site title='$emailHelp'>
            <i class='fa fa-info-circle'></i>
         </a>
         <input type=text name=$settingsFieldEmail value='$email'>
      </label>
      </fieldset>\n";
   }

function displaySettingsMenuBar($pages) {
   global $actionUpdateMenuBar, $actionsMenuBar;
   echo "<fieldset id=settings-menu-bar><legend>Menu Bar Tabs</legend>\n";
   foreach ($pages as $loc => $page) {
      if (!isset($page->title)) {  //TODO: Delete this backwards compatibility workaround
         $page->title = $page->name;
         $page->name = strtolower($page->name);
         }
      $actions = "<div class=space-below-2x>";
      foreach ($actionsMenuBar as $action => $actionName)
         $actions .= "<button name={$page->name} class=$action>$actionName</button>\n";
      $actions .= "</div>";
      echo "<input name={$page->name} type=text value='{$page->title}'>$actions\n";
      }
   echo "</fieldset>\n";
   }

function displaySettingsReprocess() {
   global $actionReprocessImages;
   $confirmMsg = '"You are about to regenerate all the thumbnail images and slideshow images.  Continue?"';
   echo "<form method=post action='.'; onsubmit='return confirm($confirmMsg);'>\n";
   echo "<input type=hidden name=action value=$actionReprocessImages>\n";
   echo "<fieldset><legend>Image Processing</legend>
      <p><button>Reprocess Images</button></p>
      </fieldset>\n";
   echo "</form>\n";
   }

function displaySettings() {
   global $settingsDbFile, $settingsFieldPages;
   $settingsDb = readSettings($settingsDbFile);
   displaySettingsWebsite($settingsDb);
   displaySettingsMenuBar($settingsDb->{$settingsFieldPages});
   //displaySettingsReprocess();  //prereq: add dimentions to settings
   }

function processUpdateSettings() {
   global $settingsDbFile, $settingsFields, $settingsFieldsHtml, $settingsFieldPages;
   $settingsDb = createEmptyDb();
   foreach ($settingsFields as $fieldName)
       $settingsDb->{$fieldName} = "" . $_POST[$fieldName];
   foreach ($settingsFieldsHtml as $fieldName)
       $settingsDb->{$fieldName . "-html"} = bbcodeToHtml($settingsDb->{$fieldName});
   $settingsDb->{$settingsFieldPages} = readSettings($settingsDbFile)->{$settingsFieldPages};
   if (saveDb($settingsDbFile, $settingsDb))
      echo "<div>Updated gallery settings.</div>";
   else
      echo "<div class=advisory>Error saving gallery settings.</div>";
   }

function processUpdateMenuBar() {
   global $settingsDbFile, $settingsFieldPages;
   $pageSelected = $_POST["menu-bar-name"];
   $pageAction = $_POST["menu-bar-action"];
   $pageTitle = $_POST["menu-bar-title"];
   $settingsDb = readSettings($settingsDbFile);
   $pages = $settingsDb->{$settingsFieldPages};
   foreach ($pages as $page) {     //TODO: Delete this backwards compatibility workaround
      if (!isset($page->title))
         $page->title = $page->name;
      $page->name = strtolower($page->name);
      unset($page->page);
      }
   $msg = null;
   foreach ($pages as $slot => $page)
      if ($page->name == $pageSelected)
         switch ($pageAction) {
            case "save":
               strlen($pageTitle) > 0 ? $page->title = $pageTitle :
                  $msg = "Page title cannot be blank"; break;
            case "up": $msg = "Sorry, $pageAction is not ready yet."; break;
            case "down": $msg = "Sorry, $pageAction is not ready yet."; break;
            case "show": $page->show = true; break;
            case "hide": $page->show = false; break;
            case "edit": $msg = "Temporary fix is to edit 'data/page-$pageSelected.html' file."; break;
            case "del": $msg = "Sorry, $pageAction is not ready yet."; break;
            }
   if ($msg)
      echo "<div class=advisory>Error updating menu bar settings: $msg</div>";
   else if (saveDb($settingsDbFile, $settingsDb))
      echo "<div>Updated menu bar settings.</div>";
   else
      echo "<div class=advisory>Error saving menu bar settings to DB.</div>";
   }

?>
