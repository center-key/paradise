<?php
///////////////////////////////////////////////////
// PPAGES ~ centerkey.com/ppages                 //
// GPLv3 ~ Copyright (c) individual contributors //
///////////////////////////////////////////////////

// Security
// Redriects browser to Sign In page if user is not authenticted.
//
// Put at first line of PHP file:
//    <?php $noAuth = true; $redirectAuth = "."; require "php/security.php";
// $noAuth (optional): If true, redirect will not happen but $loggedIn will be set to true or false.
// $redirectAuth (optional): If set and user is authorized, redirects to named page.

$sessionTimout =  3600;  //60x60 seconds --> 1 hour
session_start();
require "library.php";
require "startup.php";

function redirectToPage($page) {
   logEvent("page-redirect", $page, $_SERVER["REQUEST_URI"]);
   header("Location: ./{$page}");
   exit();
   }

function userEnabled() {
   return readAccountsDb()->users->{$_SESSION["user"]}->enabled;  //TODO: optimize to prevent re-reading db later
   }

function calculateHash($user, $password) {
   $blowfish = "$2y$10$";
   $salt = md5(getProperty($user, "created"));  //md5 (32 characters) used to meet 22 character minimum
   return crypt($password, $blowfish . $salt);
   }

function verifyPassword($user, $password) {
   $hash = calculateHash($user, $password);  //always calculate hash to counter timing attacks
   return $user && $user->enabled && $user->hash === $hash;
   }

function loginUser($email) {
   $_SESSION["user"] = $email;
   $_SESSION["active"] = time();
   logEvent("user-login", session_id());
   }

function createUser($accountsDb, $email, $password) {
   logEvent("create-user", $email);
   $user = array("created" => time(), "enabled" => true);
   $user["hash"] = calculateHash($user, $password);
   $accountsDb->users[$email] = $user;
   saveAccountsDb($accountsDb);
   loginUser($email);
   }

function useInvite($accountsDb, $inviteCode) {
   $invite = getProperty($accountsDb->invites, $inviteCode);
   $now = time();
   if ($invite && $invite->accepted === null && $now < $invite->expires) {
      $invite->accepted = $now;
      saveAccountsDb($accountsDb);
      logEvent("use-invite", $invite);
      }
   return $invite && $invite->accepted === $now;
   }

function validateCreateUser($accountsDb, $email, $password, $confirm, $inviteCode, $securityMsgs) {
   $basicEmailPattern = "/^.+@.+[.].+$/";
   if (!preg_match($basicEmailPattern, $email))
      $code = "invalid-email";
   elseif ($accountsDb->users->{$email})
      $code = "user-exists";
   elseif ($password !== $confirm)
      $code = "mismatch";
   elseif (empty($accountsDb->users) || useInvite($accountsDb, $inviteCode))
      createUser($accountsDb, $email, $password);
   else
      $code = "bad-invite-code";
   logEvent("validate-create-user", is_null($code), $code);
   return $code ? $securityMsgs[$code] : null;
   }

function securityRequest($action, $email, $password, $confirm, $inviteCode) {
   $securityMsgs  = array(
      "bad-invite-code" => "Invite code is missing, expired, or invalid.",
      "bad-credentials" => "The email address or password you entered is incorrect.",
      "invalid-email" =>   "Please enter a valid email address.",
      "mismatch" =>        "Passwords do not match.",
      "user-exists" =>     "That email address is already in use.",
      "create-fail" =>     "Cannot create user."
      );
   $email = strtolower(trim($email));
   $accountsDb = readAccountsDb();
   $user = array_key_exists($email, $accountsDb->users) ? $accountsDb->users->{$email} : null;
   if ($action === "login")
      $msg = verifyPassword($user, $password) ? loginUser($email) : $securityMsgs["bad-credentials"];
   elseif ($action === "create")
      $msg = validateCreateUser($accountsDb, $email, $password, $confirm, $inviteCode, $securityMsgs);
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

$loggedIn = isset($_SESSION["user"]) && time() < $_SESSION["active"] + $sessionTimout && userEnabled();
if ($loggedIn)
   $_SESSION["active"] = time();
if ($loggedIn && $redirectAuth)
   redirectToPage($redirectAuth);
elseif (!$loggedIn && !$noAuth)
   redirectToPage("sign-in");
?>
