<?php
/////////////////////////////////////////////////
// PPAGES ~ centerkey.com/ppages               //
// GPL ~ Copyright (c) individual contributors //
/////////////////////////////////////////////////

// Login

function displayLoginHtml($createAccount) {
   global $salt;
   $button = "Login";
   $action = "do-login";
   if ($createAccount) {
      $msg = "
         <p class=advisory>
            No user accounts exists yet.&nbsp;
            Create your account by entering a username and password.&nbsp;
            Enter your password a second time for verification
         </p>";
      $verify = "
         <label>
            <span>Password:</span>
            <input type=password id=password2 size=25 autocomplete=off>
         </label>";
      $button = "Create Account";
      $action = "create-account";
      }
   echo "$msg
      <div class=login>
         <label><span>Username:</span>
               <input type=text id=username size=30></label>
         <label><span>Password:</span>
            <input type=password id=password size=25 autocomplete=off></label>
         $verify
         <p class=sans-label><button id=$action>$button</button></p>
      </div>
      <form action='.' method=post id=submit-login>
         <input type=hidden name=action   value=login>
         <input type=hidden name=username id=submit-username>
         <input type=hidden name=hash     id=submit-hash>
      </form>\n";
   }

function displayLogin() {
   echo "<div class=col1>\n";
   echo "<div class=block5><h3>Login</h3>\n";
   if ($_POST["action"] == "login")
      echo "<p class=advisory>Invalid username or password.</p>\n";
   displayloginHtml(getNumAccounts() == 0);
   echo "</div>  <!-- end block -->";
   }

?>
