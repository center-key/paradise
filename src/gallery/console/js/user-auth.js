/////////////////////////////////////////////////
// PPAGES ~ www.centerkey.com/ppages           //
// GPL ~ Copyright (c) individual contributors //
/////////////////////////////////////////////////

// User Authentication

if (!gmc)
   var gmc = {};

gmc.user = {
   salt: location.hostname,
   validateAccountRules: function(uname, pass, pass2) {
      var valid = false;
      if (uname.length === 0)
         alert('Username cannot be blank.');
      else if (pass !== pass2)
         alert('Passwords do not match.');
      else if (pass.length < 8)
         alert('Password must be at least 8 characters long.');
      else
         valid = true;
      return valid;
      },
   doLogin: function(validate) {
      var uname = $('#username').value;
      var pass =  $('#password').value;
      var hash =  Sha1.hash(pass + gmc.user.salt);
      $('#submit-username').value = uname;
      $('#submit-hash').value = hash;
      if (!validate || this.validateAccountRules(uname, pass, $('#password2').value))
         $('#submit-login').submit();
      },
   changePassword: function() {
      var pass =   $('#password').value;
      var pass2 =  $('#password2').value;
      var hash = Sha1.hash(pass + gmc.user.salt);
      $('#submit-hash').value = hash;
      if (this.validateAccountRules('ignore', pass, pass2))
         $('#submit-change-password').submit();
      },
   confirmResetPassword: function(uname) {
      return confirm('You are about to reset the password for "' + uname +
         '".\n\nContinue?');
      },
   cleanupUsername: function(uname) {
      return uname.toLowerCase().replace(/[^a-z0-9-]/g,'');
      },
   randomPassword: function() {
      return Sha1.hash(new Date()).replace(/[01]/g,'').substring(0,8);
      },
   confirmAccountAction: function(uname) {
      alert('This feaure is not ready yet.');
      return false;
      },
   confirmCreateAccount: function(uname) {
      var pass = this.randomPassword();
      var hash = Sha1.hash(pass + gmc.user.salt);
      var msg = 'You are about to create the following user account.\n\tUsername: ' +
         uname + '\n\tPassword: ' + pass +
         '\n(Hint: You can select and copy the above password.)\n\nContinue?';
      $('#submit-new-username').value = uname;
      $('#submit-new-hash').value = hash;
      if (uname.length === 0)
         alert('Username cannot be blank.');
      else if (confirm(msg))
         $('#submit-create-account').submit();
      }
   };
