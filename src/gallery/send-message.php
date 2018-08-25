<?php
///////////////////////////////////////////////////////////////
// Paradise ~ centerkey.com/paradise                         //
// GPLv3 ~ Copyright (c) individual contributors to Paradise //
///////////////////////////////////////////////////////////////

require "server/gallery.php";

// Initialize
$subject =   "Paradise PHP Photo Gallery - Message";
$from =      "From: Paradise <{$settings->email}>";
$thanksUri = "./#thanks";
$bar =       "-----------------------";

// Create message
$msg =  $_SERVER["HTTP_HOST"] . "\n$bar";
$msg .= "\nName: " .    htmlspecialchars($_POST["name"]);
$msg .= "\nEmail: " .   htmlspecialchars($_POST["email"]);
$msg .= "\nMessage: " . htmlspecialchars($_POST["message"]);
$msg .= "\n$bar\n";

// Send message
mail($settings->email, $subject, $msg, $from);
header("Location: $thanksUri");
?>
