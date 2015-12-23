<?php
///////////////////////////////////////////////////
// PPAGES ~ centerkey.com/ppages                 //
// GPLv3 ~ Copyright (c) individual contributors //
///////////////////////////////////////////////////

require "php/gallery.php";

// Initialize
$subject =   "PPAGES - Gallery Feedback";
$from =      "From: PPAGES <{$settings->email}>";
$thanksUri = "./#thanks";
$bar =       "-----------------------";

// Create message
$msg =  $_SERVER["HTTP_HOST"] . "\n$bar";
$msg .= "\nName: " .    htmlspecialchars($_POST["name"]);
$msg .= "\nE-mail: " .  htmlspecialchars($_POST["email"]);
$msg .= "\nMessage: " . htmlspecialchars($_POST["message"]);
$msg .= "\n$bar\n";

// Send message
mail($settings->email, $subject, $msg, $from);
header("Location: $thanksUri");
?>
