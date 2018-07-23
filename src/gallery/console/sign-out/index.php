<?php
///////////////////////////////////////////////////////////////
// Paradise ~ centerkey.com/paradise                         //
// GPLv3 ~ Copyright (c) individual contributors to Paradise //
///////////////////////////////////////////////////////////////

// User logout
$noAuth = true;
require "../php/security.php";
logEvent("user-logout", session_id());
session_destroy();
header("Location: ../sign-in");
?>
