<?php
/////////////////////////////////////////////////
// PPAGES ~ centerkey.com/ppages               //
// GPL ~ Copyright (c) individual contributors //
/////////////////////////////////////////////////

$dataFolder = "data/";
include "php/common.php";
$settings = readDb($settingsDbFile);

//Initialize
$email =      $settings->{"email"};
$sendTo =     "Gallery Feedback <$email>";
$subject =    "Feedback Submission";
$thanksUri =  "#thanks";
$bar =        "-----------------------";

//Create message
$msg =   $_SERVER["HTTP_HOST"] . "\n$bar";
$msg .= "\nName: " .    htmlspecialchars($_POST["name"]);
$msg .= "\nE-mail: " .  htmlspecialchars($_POST["email"]);
$msg .= "\nMessage: " . htmlspecialchars($_POST["message"]);
$msg .= "\n$bar\n";

//Send message
mail($sendTo, $subject, $msg, "From: $email");
header("Location: $thanksUri");
?>
