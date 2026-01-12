<?php
///////////////////////////////////////////////////////////////
// Paradise ~ centerkey.com/paradise                         //
// GPLv3 ~ Copyright (c) Individual contributors to Paradise //
///////////////////////////////////////////////////////////////

// Security
// Redriects the browser to the "Sign In" page if the user is not authenticated.
//
// Put at first line of PHP file:
//    <?php $authRequired = false; $redirectAuth = "."; require "admin-server/security.php";
// $authRequired (optional): If false, redirect will not happen but $loggedIn will be set to true or false.
// $redirectAuth (optional): If set and user is authorized, redirects to named page.

$sessionTimeout = 3600000;  //one hour: 60 x 60 x 1000 = 3,600,000 milliseconds
$authRequired =   isset($authRequired) ? $authRequired : true;
$redirectAuth =   isset($redirectAuth) ? $redirectAuth : null;
session_start();
require "library.php";
require "startup.php";
require "console.php";
$dataFolder =   realpath(__DIR__ . "/../../~data~");
$loginMsgFile = "{$dataFolder}/login-message.html";
$loginMsg =     "<!--\n<section>\n   <h2>Custom message</h2>\n   <p>Text goes here.</p>\n</section>\n-->\n";

function redirectToPage($page) {
   logEvent("page-redirect", $page, $_SERVER["REQUEST_URI"]);
   header("Location: ./{$page}");
   exit();
   }

function getCurrentUser() {
   return isset($_SESSION["user"]) ? $_SESSION["user"] : null;
   }

function userEnabled() {
   $user =  getCurrentUser();
   $users = readAccountsDb()->users;
   return isset($users->$user) && $users->$user->enabled;
   }

function calculateHash($user, $password) {
   $blowfish = "$2y$10$";
   $salt =     md5($user->created);  //md5 (32 characters) used to meet 22 character minimum
   return crypt($password, $blowfish . $salt);  //expect to take about 100 ms
   }

function verifyPassword($user, $password) {
   usleep(rand(0, 500000));  //randomly wait up to half a second to counter timing attacks
   return $user && $user->enabled && $user->hash === calculateHash($user, $password);
   }

function loginUser($email) {
   $_SESSION["user"] =           $email;
   $_SESSION["active"] =         getTime();
   $_SESSION["read-only-user"] = isReadOnlyExampleEmailAddress($email);
   $accountsDb = readAccountsDb();
   $accountsDb->users->{$email}->login = getTime();
   $accountsDb->users->{$email}->valid++;
   saveAccountsDb($accountsDb);
   $type = $_SESSION["read-only-user"] ? "read-only" : "regular";
   logEvent("user-login", session_id(), $type, $accountsDb->users->{$email}->valid);
   return true;
   }

function createUser($accountsDb, $email, $password) {
   logEvent("create-user", $email);
   $user = (object)array(
      "email" =>   $email,
      "created" => getTime(),
      "enabled" => true,
      "login" =>   getTime(),
      "valid" =>   1,
      );
   $user->hash = calculateHash($user, $password);
   $accountsDb->users->{$email} = $user;
   saveAccountsDb($accountsDb);
   loginUser($email);
   }

function sendAccountInvite($email) {
   $daysValid = 3;
   $expires =   getTime() + daysToMsec($daysValid);
   $code =      "Q" . mt_rand() . mt_rand();
   $user =      getCurrentUser();
   $invite = (object)array(
      "code" =>     $code,
      "from" =>     $user,
      "to" =>       $email,
      "accepted" => false,
      "sent" =>     getTime(),
      "expires" =>  $expires,
      "date" =>     date("Y-m-d", intval($expires / 1000)),
      );
   $db = readAccountsDb();
   $db->invites->{$code} = $invite;
   saveAccountsDb($db);
   $inviteLink =  getGalleryUrl() . "/console/sign-in?invite={$code}&email={$email}";
   $subjectLine = "Sign up invitation";
   $messageLines = array(
      "You have been invited by {$user} to create an account to administer a Paradise Photo Gallery.",
      "",
      "To sign up and start uploading images, go to:",
      $inviteLink,
      "",
      "The above link expires in {$daysValid} days.",
      "",
      "The gallery can be viewed at: " . getGalleryUrl(),
      );
   $invite->message = sendEmail($invite->to, $subjectLine, $messageLines) ?
      "Account invitation sent to: {$email}" : "Error emailing invitation!";
   logEvent("send-account-invite", $code, $invite->to, $invite->expires);
   return $invite;
   }

function outstanding($invite) {
   return $invite && !$invite->accepted && getTime() < $invite->expires;
   }

function restRequestInvite($action, $email) {
   if ($action === "create")
      $resource = validEmailFormat($email) ? sendAccountInvite($email) : restError(404);
   else
      $resource = getOutstandingInvites();
   return $resource;
   }

function useInvite($accountsDb, $code) {
   $invite = isset($accountsDb->invites->{$code}) ? $accountsDb->invites->{$code} : null;
   if (outstanding($invite)) {
      $invite->accepted = true;
      saveAccountsDb($accountsDb);
      logEvent("use-invite", $code, $invite);
      }
   return $invite && $invite->accepted;
   }

function validateCreateUser($accountsDb, $email, $password, $confirm, $inviteCode) {
   $errorCode = null;
   if (!validEmailFormat($email))
      $errorCode = "invalid-email";
   elseif (isset($accountsDb->users->{$email}))
      $errorCode = "user-exists";
   elseif ($password !== $confirm)
      $errorCode = "mismatch";
   elseif (emptyObj($accountsDb->users) || useInvite($accountsDb, $inviteCode))
      createUser($accountsDb, $email, $password);
   else
      $errorCode = "bad-invite-code";
   logEvent("validate-create-user", $email, is_null($errorCode), $errorCode, $inviteCode);
   return $errorCode;
   }

function restRequestSecurity($action, $httpBody) {
   $securityMsgs = (object)array(
      "bad-invite-code" => "Invite code is missing, expired, or invalid.",
      "bad-credentials" => "The email address or password you entered is incorrect.",
      "invalid-email" =>   "Please enter a valid email address.",
      "mismatch" =>        "Passwords do not match.",
      "user-exists" =>     "That email address is already in use.",
      "create-fail" =>     "Cannot create user.",
      "invalid-action" =>  "Invalid action.",
      );
   $email =      strtolower(trim($httpBody->email));
   $password =   $httpBody->password;
   $confirm =    $httpBody->confirm;
   $inviteCode = $httpBody->invite;
   $accountsDb = readAccountsDb();
   $user =       isset($accountsDb->users->{$email}) ? $accountsDb->users->{$email} : null;
   $errorCode =  null;
   if ($action === "login")
      $errorCode = verifyPassword($user, $password) && loginUser($email) ? null : "bad-credentials";
   elseif ($action === "create")
      $errorCode = validateCreateUser($accountsDb, $email, $password, $confirm, $inviteCode);
   else
      $errorCode = "invalid-action";
   $msg = $errorCode ? $securityMsgs->{$errorCode} : "Success";
   logEvent("security-request", $email, $action, $errorCode, $msg);
   return (object)array(
      "authenticated" => $errorCode === null,
      "email" =>         $email,
      "message" =>       $msg,
      );
   }

function readOnlyMode() {
   return isset($_SESSION["read-only-user"]) ? $_SESSION["read-only-user"] : false;
   }

$loggedIn = getCurrentUser() && getTime() < $_SESSION["active"] + $sessionTimeout && userEnabled();
if ($loggedIn)
   $_SESSION["active"] = getTime();
else
   session_unset();
if ($loggedIn && $redirectAuth)
   redirectToPage($redirectAuth);
elseif (!$loggedIn && $authRequired)
   redirectToPage("sign-in");
initializeFolder($dataFolder, true);
initializeFile($loginMsgFile, $loginMsg);
?>
