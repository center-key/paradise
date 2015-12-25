<?php
///////////////////////////////////////////////////
// PPAGES ~ centerkey.com/ppages                 //
// GPLv3 ~ Copyright (c) individual contributors //
///////////////////////////////////////////////////

// Console

$googleFonts = array(  //see https://www.google.com/fonts
   "Allan", "Allerta", "Allerta Stencil", "Anonymous Pro", "Arimo",
   "Arvo", "Bentham", "Bowlby One SC", "Buda", "Cabin", "Cantarell", "Cardo",
   "Cherry Cream Soda", "Chewy", "Chango", "Coda", "Copse",
   "Corben", "Cousine", "Crimson Text", "Cuprum",
   "Droid Sans", "Droid Sans Mono", "Droid Serif", "Geo", "Gruppo",
   "Homemade Apple", "IM Fell",
   "Inconsolata", "Josefin Sans", "Josefin Slab", "Just Another Hand",
   "Kenia", "Kristi", "Lato", "Lekton", "Lobster",
   "Merriweather", "Molengo", "Neucha", "Neuton",
   "Nobile", "Old Standard TT", "Orbitron",
   "PT Sans", "Philosopher", "Puritan", "Raleway", "Reenie Beanie",
   "Rock Salt", "Slackey", "Sniglet", "Special Elite",
   "Syncopate", "Tangerine", "Tinos", "Ubuntu", "UnifrakturCook",
   "UnifrakturMaguntia", "Vibur", "Vollkorn", "Yanone Kaffeesatz"
   );

function displayFontOptions() {
   global $googleFonts;
   foreach ($googleFonts as $font)
      echo "<option value='{$font}'>{$font}</option>\n";
   }

?>
