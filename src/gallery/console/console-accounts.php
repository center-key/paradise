<?php
/////////////////////////////////////////////////
// PPAGES ~ www.centerkey.com/ppages           //
// GPL ~ Copyright (c) individual contributors //
/////////////////////////////////////////////////

$accountsDbFilter = $dataFolder . dbFileName("accounts-*");
$accountsDbFile =   null;  //set at run-time
$accountsDb =       null;

$accountFieldUsername = "username";  //used in html forms, but not db
$accountFieldHash =     "hash";
$accountFieldType =     "type";  //"regular"(TBD) or "administrator"

$salt = ltrim($_SERVER["HTTP_HOST"], "w.");  //transmit: sha1(password + salt)

function getAccountsDb() {
   global $accountsDbFilter, $accountsDbFile, $accountsDb;
   if ($accountsDb == null) {
      $dbFileSearch = glob($accountsDbFilter);
      if (count($dbFileSearch) == 0) {
         $accountsDbFile = str_replace("*" , mt_rand(0, 9999999999999999),
            $accountsDbFilter);
         saveDb($accountsDbFile, createEmptyDb());
         }
      else
         $accountsDbFile = $dbFileSearch[0];
      $accountsDb = readDb($accountsDbFile);
      }
   return $accountsDb;
   }

function saveAccountsDb($newAccountsDb) {
   global $accountsDbFile, $accountsDb;
   saveDb($accountsDbFile, $newAccountsDb);
   $accountsDb = readDb($accountsDbFile);
   }

function getNumAccounts() {
   return count((array)getAccountsDb());
   }

function cleanupUsername($username) {
   $username = preg_replace("/[^a-z0-9-]/", "", strtolower($username));
   return strlen($username) ? $username : " ";
   }

function lookupAccount($username) {
   return getAccountsDb()->{cleanupUsername($username)};
   }

/*
function addAccount($username, $hash, $type) {
   global $accountsDbFile, $accountsDb;
   global $accountFieldHash, $accountFieldType;
   if (!lookupAccount($username)) {
      $accountsDb->{cleanupUsername($username)} = array (
         $accountFieldHash => sha1($hash),
         $accountFieldType => $type,
         );
      saveDb($accountsDbFile, $accountsDb);
      $accountsDb = readDb($accountsDbFile);
      }
   }
*/

function displayChangePassword() {
   global $actionField, $actionChangePassword, $accountFieldHash;
   echo "
      <fieldset>
         <legend>Your Password</legend>
         <label>New Password: <input type=password id=password></label>
         <label>Verify Password: <input type=password id=password2></label>
         <button id=change-password>Change Password</button>
      </fieldset>
      <form action='.' method=post id=submit-change-password>
         <input type=hidden name=$actionField value=$actionChangePassword>
         <input type=hidden name=$accountFieldHash id=submit-hash>
      </form>\n";
   }

function updateAccount($username, $hash, $type, $msg) {
   global $accountFieldHash, $accountFieldType;
   $accountsDb = getAccountsDb();
   if ($hash)
      $accountsDb->{$username}->{$accountFieldHash} = sha1($hash);
   if ($type)
      $accountsDb->{$username}->{$accountFieldType} = $type;
   saveAccountsDb($accountsDb);
   echo $msg;
   }

function processChangePassword() {
   global $accountFieldUsername, $accountFieldHash;
   $username = $_SESSION[$accountFieldUsername];
   $hash = $_POST[$accountFieldHash];
   if (lookupAccount($username))
      updateAccount($username, $hash, null, "Your password has been updated.");
   else
      echo "<span class=advisory>Account not found: $username</span>";
   }

function processCreateAccount() {
   global $accountFieldUsername, $accountFieldHash;
   $username = cleanupUsername($_POST[$accountFieldUsername]);
   $hash = $_POST[$accountFieldHash];
   if (lookupAccount($username))
      echo "<span class=advisory>The user account &quot;$username&quot; already exists.</span>";
   else
      updateAccount($username, $hash, "administrator", "New user account created: $username");
   }

function displayAccountsListHtml($username) {
   global $actionField, $actionAccountAdmin, $accountFieldUsername;
   $disabled = $username == $_SESSION[$accountFieldUsername];
   $adminOptions = "<option>Reset Password</option><option>Delete Account</option>";
   echo "
      <form method=post onsubmit='return confirmAccountAction(\"$username\");'>
         <input type=hidden name=$actionField value=$actionAccountAdmin>
         <p>" . ($disabled ? "<i>" : "") . "$username" . ($disabled ? "</i>" : "") . "
            <select name='' id=account-account" . ($disabled ? " disabled" : "") .
               ">$adminOptions</select>
            <button" . ($disabled ? " disabled" : "") . ">Go</button>
         </p>
      </form>\n";
   }

function displayAccountsList() {
   echo "<fieldset><legend>All Accounts</legend>\n";
   foreach (getAccountsDb() as $username => $account)
      displayAccountsListHtml($username);
   echo "</fieldset>\n";
   }

function displayCreateAccount() {
   global $accountFieldUsername, $accountFieldHash, $actionField, $actionCreateAccount;
   echo "
      <fieldset><legend>New Account</legend>
         <label>Username: <input type=text id=$accountFieldUsername
            onblur='this.value=cleanupUsername(this.value);'></label>
         <button id=create-account>Create New User Account</button>
      </fieldset>
      <form action='.' method=post id=submit-create-account>
         <input type=hidden name=$actionField          value=$actionCreateAccount>
         <input type=hidden name=$accountFieldUsername id=submit-new-username>
         <input type=hidden name=$accountFieldHash     id=submit-new-hash>
      </form>\n";
   }

function displayAccounts() {
   displayChangePassword();
   displayAccountsList();
   displayCreateAccount();
   }

function accountValidHash($username, $hash) {
   global $accountFieldHash;
   if (getNumAccounts() == 0)
      processCreateAccount();
   $_SESSION["username"] = cleanupUsername($username);
   $account = lookupAccount($username);
   return sha1($hash) == $account->{$accountFieldHash};
   }

?>
