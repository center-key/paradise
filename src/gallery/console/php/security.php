<?php
/////////////////////////////////////////////////
// PPAGES ~ centerkey.com/ppages               //
// GPL ~ Copyright (c) individual contributors //
/////////////////////////////////////////////////

// Security
// Redriects browser to Sign In page if user is not authenticted.
//
// Put at first line of PHP file:
//    <?php $noAuth = true; $redirectAuth = "."; require "php/security.php";
// $noAuth (optional): If true, redirect will not happen but $loggedIn will be set to true or false.
// $redirectAuth (optional): If set and user is authorized, redirects to named page.

$sessionTimout =  1200;  //1200 seconds --> 20 mintues
session_start();
require "php/library.php";
require "php/startup.php";

function getHash($password) {
   global $installKey;
   return crypt($password, "$2a$10$" . $installKey);
   }

function redirectToPage($page) {
   logEvent("page-redirect", $page, $_SERVER["REQUEST_URI"]);
   header("Location: ./{$page}");
   exit();
   }

function userListEmpty() {
   global $accountsDbFile;
   return empty(readDb($accountsDbFile)->users);
   }

function verifyPassword($user, $hash) {
   return $user && $user->enabled && $user->hash === $hash;
   }

function loginUser($email) {
   $_SESSION["user"] = $email;
   $_SESSION["active"] = time();
   }

function createUser($accountsDb, $email, $hash) {
   global $accountsDbFile;
   logEvent("create-user", $email);
   $user = array("created" => time(), "hash" => $hash, "enabled" => true);
   $accountsDb->users[$email] = $user;
   saveDb($accountsDbFile, $accountsDb);
   loginUser($email);
   }

function validateCreateUser($accountsDb, $email, $password, $confirm, $hash, $securityMsgs) {
   $basicEmailPattern = "/.+@.+[.].+/";
   if (!preg_match($basicEmailPattern, $email))
      $code = "invalid-email";
   elseif ($accountsDb->users->{$email})
      $code = "user-exists";
   elseif ($password !== $confirm)
      $code = "mismatch";
   elseif (!empty($accountsDbFile->users))
      $code = "create-fail";
   else
      createUser($accountsDb, $email, $hash);
   logEvent("validate-create-user", is_null($code), $code);
   return $code ? $securityMsgs[$code] : null;
   }

function securityRequest($action, $email, $password, $confirm) {
   global $accountsDbFile;
   $securityMsgs  = array(
      "bad-credentials" => "The email address or password you entered is incorrect",
      "invalid-email" =>   "Please enter a valid email address.",
      "mismatch" =>        "Passwords do not match.",
      "user-exists" =>     "That email address is already in use.",
      "create-fail" =>     "Cannot create user."
      );
   $hash = getHash($password);  //always run crypt to counter timing attacks
   $email = strtolower(trim($email));
   $accountsDb = readDb($accountsDbFile);
   $user = array_key_exists($email, $accountsDb->users) ? $accountsDb->users->{$email} : null;
   if ($action === "login")
      $msg = verifyPassword($user, $hash) ? loginUser($email) : $securityMsgs["bad-credentials"];
   elseif ($action === "create")
      $msg = validateCreateUser($accountsDb, $email, $password, $confirm, $hash, $securityMsgs);
   else
      $msg = "Invalid request.";
   $success = is_null($msg);
   logEvent("security-request", $action, $success, $email, $msg);
   return array(
      "authenticated" => $success,
      "email" =>         $email,
      "message" =>       $success ? "Success." : $msg
      );
   }

$loggedIn = isset($_SESSION["active"]) && time() < $_SESSION["active"] + $sessionTimout;
if ($loggedIn)
   $_SESSION["active"] = time();
if ($loggedIn && $redirectAuth)
   redirectToPage($redirectAuth);
elseif (!$loggedIn && !$noAuth)
   redirectToPage("sign-in");
?>
