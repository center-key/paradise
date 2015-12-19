<?php
/////////////////////////////////////////////////
// PPAGES ~ centerkey.com/ppages               //
// GPL ~ Copyright (c) individual contributors //
/////////////////////////////////////////////////

// Security
// Redriects browser to Sign In page if user is not authenticted.
//
// Put at first line of PHP file:
//    <?php $noAuth = true; $redirectAuth = "console"; require "php/security.php";
// $noAuth (optional): If true, redirect will not happen but $loggedIn will be set to true or false.
// $redirectAuth (optional): If set and user is authorized, redirects to named page.

$sessionTimout =  1200;  //1200 seconds --> 20 mintues
session_start();
require "php/library.php";
require "php/startup.php";

function redirectToPage($page) {
   logEvent("page-redirect", $page, $_SERVER["REQUEST_URI"]);
   header("Location: ./" . $page);
   exit();
   }

$loggedIn = isset($_SESSION["active"]) && time() < $_SESSION["active"] + $sessionTimout;
if ($loggedIn)
   $_SESSION["active"] = time();
if ($loggedIn && $redirectAuth)
   redirectToPage($redirectAuth);
elseif (!$loggedIn && !$noAuth)
   redirectToPage("sign-in");
?>
