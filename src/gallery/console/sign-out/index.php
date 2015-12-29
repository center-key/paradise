<?php
///////////////////////////////////////////////////
// PPAGES ~ centerkey.com/ppages                 //
// GPLv3 ~ Copyright (c) individual contributors //
///////////////////////////////////////////////////

// User logout
$noAuth = true;
require "../php/security.php";
logEvent("user-logout", session_id());
session_destroy();
header("Location: ../sign-in");
?>
