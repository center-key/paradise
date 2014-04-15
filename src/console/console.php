<?php
// PPAGES ~ www.centerkey.com/ppages ~ Copyright (c) individual contributors
// Rights granted under GNU General Public License ~ ppages/src/gallery/license.txt

$blockNum = 0;

function displayBlock($header, $displayFunction) {
   global $blockNum;
   $blockNum++;
   echo "<div class=block$blockNum><h3>$header</h3>\n";
   $displayFunction();
   echo "</div>  <!-- end block$blockNum -->\n";
   }

function displayConsole() {
   echo "<div class=col1>\n";
   displayBlock("Status", "displayProcessStatus");
   displayBlock("Portfolio", "displayPortfolio");
   echo "</div>  <!-- end col1 -->\n<div class=col2>\n";
   displayBlock("Transfer Photos to Gallery", "displayTransfer");
   displayBlock("Gallery Settings", "displaySettings");
   displayBlock("User Accounts", "displayAccounts");
   echo "</div>  <!-- end col2 -->";
   }

?>
