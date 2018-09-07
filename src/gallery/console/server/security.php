<?php
///////////////////////////////////////////////////////////////
// Paradise ~ centerkey.com/paradise                         //
// GPLv3 ~ Copyright (c) individual contributors to Paradise //
///////////////////////////////////////////////////////////////

// Security
// Redriects browser to Sign In page if user is not authenticted.
//
// Put at first line of PHP file:
//    <?php $authRequired = false; $redirectAuth = "."; require "server/security.php";
// $authRequired (optional): If false, redirect will not happen but $loggedIn will be set to true or false.
// $redirectAuth (optional): If set and user is authorized, redirects to named page.

$sessionTimout =  3600;  //60x60 seconds --> 1 hour
$authRequired = isset($authRequired) ? $authRequired : true;
$redirectAuth = isset($redirectAuth) ? $redirectAuth : null;
session_start();
require "library.php";
require "startup.php";
$loginMsgFile = __DIR__ . "/../../~data~/login-message.html";
$loginMsg = "<!--\n<section>\n   <h2>Custom message</h2>\n   <p>Text goes here.</p>\n</section>\n-->\n";

function redirectToPage($page) {
   logEvent("page-redirect", $page, $_SERVER["REQUEST_URI"]);
   header("Location: ./{$page}");
   exit();
   }

function getCurrentUser() {
   return isset($_SESSION["user"]) ? $_SESSION["user"] : null;
   }

function userEnabled() {
   $user = getCurrentUser();
   return readAccountsDb()->users->$user->enabled;
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
   $_SESSION["read-only-user"] = isReadOnlyExampleEmailAddress($email);
   logEvent("user-login", session_id());
   }

function createUser($accountsDb, $email, $password) {
   logEvent("create-user", $email);
   $user = array("created" => time(), "enabled" => true);
   $user["hash"] = calculateHash($user, $password);
   $accountsDb->users->{$email} = $user;
   saveAccountsDb($accountsDb);
   loginUser($email);
   }

function sendAccountInvite($email) {
   $daysValid = 3;
   $invite = array(
      "from" =>     getCurrentUser(),
      "to" =>       $email,
      "accepted" => false,
      "expires" =>  time() + $daysValid * (24 * 60 * 60)
      );
   $code = "Q" . mt_rand() . mt_rand();
   $db = readAccountsDb();
   $db->invites->{$code} = $invite;
   saveAccountsDb($db);
   $inviteLink = getGalleryUrl() . "/console/sign-in?invite={$code}&email={$email}";
   $subjectLine = "Sign up invitation";
   $messageLines = array(
      "You have been invited to create an account to administer the Paradise PHP Photo Gallery gallery at:",
      getGalleryUrl(),
      "",
      "To sign up and start uploading images, go to:",
      $inviteLink,
      "",
      "The above link expires in {$daysValid} days.",
      );
   $invite["message"] = sendEmail($invite["to"], $subjectLine, $messageLines) ?
      "Account invitation sent to: {$email}" : "Error emailing invitation!";
   logEvent("send-account-invite", $code, $invite["to"], $invite["expires"]);
   return $invite;
   }

function outstanding($invite) {
   return $invite && !$invite->accepted && time() < $invite->expires;
   }

function displayDate($invite) {
   $invite->date = date("Y-m-d", $invite->expires);
   return $invite;
   }

function restRequestInvite($action, $email) {
   if ($action === "create")
      $resource = validEmailFormat($email) ? sendAccountInvite($email) : restError(404);
   elseif (readOnlyMode())
      $resource = array(array("to" => "lee@example.com", "date" => date("Y-m-d")));
   else
      $resource = array_values(array_map("displayDate",
         array_filter(array_values((array)readAccountsDb()->invites), "outstanding")));
   return $resource;
   }

function useInvite($accountsDb, $code) {
   $invite = getProperty($accountsDb->invites, $code);
   $now = time();
   if (outstanding($invite)) {
      $invite->accepted = true;
      saveAccountsDb($accountsDb);
      logEvent("use-invite", $invite);
      }
   return $invite && $invite->accepted;
   }

function validateCreateUser($accountsDb, $email, $password, $confirm, $inviteCode, $securityMsgs) {
   if (!validEmailFormat($email))
      $code = "invalid-email";
   elseif ($accountsDb->users->{$email})
      $code = "user-exists";
   elseif ($password !== $confirm)
      $code = "mismatch";
   elseif (emptyObj($accountsDb->users) || useInvite($accountsDb, $inviteCode))
      createUser($accountsDb, $email, $password);
   else
      $code = "bad-invite-code";
   logEvent("validate-create-user", is_null($code), $code, $email, $inviteCode);
   return $code ? $securityMsgs[$code] : null;
   }

function restRequestSecurity($action, $httpBody) {
   $securityMsgs = array(
      "bad-invite-code" => "Invite code is missing, expired, or invalid.",
      "bad-credentials" => "The email address or password you entered is incorrect.",
      "invalid-email" =>   "Please enter a valid email address.",
      "mismatch" =>        "Passwords do not match.",
      "user-exists" =>     "That email address is already in use.",
      "create-fail" =>     "Cannot create user."
      );
   $email =      strtolower(trim($httpBody->email));
   $password =   $httpBody->password;
   $confirm =    $httpBody->confirm;
   $inviteCode = $httpBody->invite;
   $accountsDb = readAccountsDb();
   $user =       array_key_exists($email, $accountsDb->users) ? $accountsDb->users->{$email} : null;
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

function readOnlyMode() {
   return isset($_SESSION["read-only-user"]) ? $_SESSION["read-only-user"] : true;
   }

$loggedIn = getCurrentUser() && time() < $_SESSION["active"] + $sessionTimout && userEnabled();
if ($loggedIn)
   $_SESSION["active"] = time();
if ($loggedIn && $redirectAuth)
   redirectToPage($redirectAuth);
elseif (!$loggedIn && $authRequired)
   redirectToPage("sign-in");
initializeFile($loginMsgFile, $loginMsg);
?>
