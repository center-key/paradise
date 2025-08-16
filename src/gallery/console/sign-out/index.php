<?php $authRequired = false; require "../admin-server/security.php"; ?>
<?php
///////////////////////////////////////////////////////////////
// Paradise ~ centerkey.com/paradise                         //
// GPLv3 ~ Copyright (c) Individual contributors to Paradise //
///////////////////////////////////////////////////////////////

// User logout
logEvent("user-logout", session_id());
session_destroy();
header("Location: ../sign-in");
?>
