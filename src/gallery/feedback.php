<?php
/////////////////////////////////////////////////
// PPAGES ~ centerkey.com/ppages               //
// GPL ~ Copyright (c) individual contributors //
/////////////////////////////////////////////////

include "console/php/database.php";
include "console/php/console-settings.php";
include "main.php";

//Initialize
$settingsDb = readSettings("data/settings-db.json");
$email =      $settingsDb->{$settingsFieldEmail};
$sendTo =     "Gallery Feedback <$email>";
$subject =    "Feedback Submission";
$thanksUri =  ".?page=thanks";
$bar =        "-----------------------";

//Create message
$msg =   $_SERVER["HTTP_HOST"] . "\n$bar";
$msg .= "\nName: " .    htmlspecialchars($_POST["name"]);
$msg .= "\nE-mail: " .  htmlspecialchars($_POST["email"]);
$msg .= "\nMessage: " . htmlspecialchars($_POST["message"]);
$msg .= "\n$bar\n";

//Send message
if (validVerification($_POST["verification"]))
   mail($sendTo, $subject, $msg, "From: $email");
else
   $thanksUrl .= "&invalid";
header("Location: $thanksUri");

?>
