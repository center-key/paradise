<?php
///////////////////////////////////////////////////////////////
// Paradise ~ centerkey.com/paradise                         //
// GPLv3 ~ Copyright (c) individual contributors to Paradise //
///////////////////////////////////////////////////////////////

require "gallery.php";

// Initialize
$email =     $settings->contactEmail;
$subject =   "Paradise PHP Photo Gallery - Message";
$from =      "From: Paradise <{$email}>";
$thanksUri = "../#thanks";

// Create message body
$message = array(
   getGalleryUrl(),
   "-----------------------------",
   "Name: " .    $_POST["name"],
   "Email: " .   $_POST["email"],
   "Message: " . $_POST["message"],
   "-----------------------------");
$body = htmlspecialchars(implode(PHP_EOL, $message), ENT_NOQUOTES);

// Send message
mail($email, $subject, $body, $from);
header("Location: $thanksUri");
?>
