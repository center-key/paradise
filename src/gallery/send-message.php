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

// Create message body
$message = array(
   getGalleryUrl(),
   "-----------------------------------",
   "Name: " .    $_POST["name"],
   "Email: " .   $_POST["email"],
   "Message: " . $_POST["message"],
   "-----------------------------------");
$body = htmlspecialchars(implode("\n", $message), ENT_NOQUOTES);

// Send message
mail($settings->email, $subject, $body, $from);
header("Location: $thanksUri");
?>
