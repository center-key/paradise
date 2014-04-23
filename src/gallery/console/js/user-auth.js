/////////////////////////////////////////////////
// PPAGES ~ www.centerkey.com/ppages           //
// GPL ~ Copyright (c) individual contributors //
/////////////////////////////////////////////////

// User Authentication

var salt = location.hostname;

function validateAccountRules(uname, pass, pass2) {
   var valid = false;
   if (uname.length == 0)
      alert('Username cannot be blank.');
   else if (pass != pass2)
      alert('Passwords do not match.');
   else if (pass.length < 8)
      alert('Password must be at least 8 characters long.');
   else
      valid = true;
   return valid;
   }

function doLogin(validate) {
   var uname = document.getElementById('username').value;
   var pass =  document.getElementById('password').value;
   var hash =  Sha1.hash(pass + salt);
   document.getElementById('submit-username').value = uname;
   document.getElementById('submit-hash').value = hash;
   if (!validate || validateAccountRules(uname, pass,
         document.getElementById('password2').value))
      document.getElementById('submit-login').submit();
   }

function changePassword() {
   var pass =   document.getElementById('password').value;
   var pass2 =  document.getElementById('password2').value;
   var hash = Sha1.hash(pass + salt);
   document.getElementById('submit-hash').value = hash;
   if (validateAccountRules('ignore', pass, pass2))
      document.getElementById('submit-change-password').submit();
   }

function confirmResetPassword(uname) {
   return confirm('You are about to reset the password for "' + uname +
      '".\n\nContinue?');
   }

function cleanupUsername(uname) {
   return uname.toLowerCase().replace(/[^a-z0-9-]/g,'');
   }

function randomPassword() {
   return Sha1.hash(new Date()).replace(/[01]/g,'').substring(0,8);
   }

function confirmAccountAction(uname) {
   alert('This feaure is not ready yet.');
   return false;
   }

function confirmCreateAccount(uname) {
   var pass = randomPassword();
   var hash = Sha1.hash(pass + salt);
   var msg = 'You are about to create the following user account.\n\tUsername: ' +
      uname + '\n\tPassword: ' + pass +
      '\n(Hint: You can select and copy the above password.)\n\nContinue?';
   document.getElementById('submit-new-username').value = uname;
   document.getElementById('submit-new-hash').value = hash;
   if (uname.length == 0)
      alert('Username cannot be blank.');
   else if (confirm(msg))
      document.getElementById('submit-create-account').submit();
   }
