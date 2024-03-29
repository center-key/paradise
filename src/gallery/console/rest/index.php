<?php
///////////////////////////////////////////////////////////////
// Paradise ~ centerkey.com/paradise                         //
// GPLv3 ~ Copyright (c) Individual contributors to Paradise //
///////////////////////////////////////////////////////////////

// REST Web Services
//
// Example read resource:
//    HTTP GET gallery/console/rest?resource=gallery
// Update value:
//    HTTP GET gallery/console/rest?resource=settings&action=update&captionItalic=true
//
// Resource   Action
// ---------  ------
// security   login, create
// command    process-uploads, generate-gallery
// settings   get, update
// gallery    get
// portfolio  get, update, delete, list
// account    list
// invite     list, create
// backup     list, create
//
// Note:
//    Query parameters are used instead of path parameters to avoid the need for
//    URL (.htaccess) configuration.

$authRequired = false;
require "../admin-server/security.php";
require "../admin-server/image-processing.php";
require "../admin-server/rest.php";

httpJsonResponse(resource($loggedIn));
?>
