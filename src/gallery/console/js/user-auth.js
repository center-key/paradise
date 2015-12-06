/////////////////////////////////////////////////
// PPAGES ~ centerkey.com/ppages               //
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
   login: function(elem) {
      var validate = elem.data().action == 'create-account';
      var uname = $('#username').val();
      var pass =  $('#password').val();
      var hash =  CryptoJS.SHA1(pass + gmc.user.salt);
      $('#submit-username').val(uname);
      $('#submit-hash').val(hash);
      if (!validate || gmc.user.validateAccountRules(uname, pass, $('#password2').val()))
         $('#submit-login').submit();
      },
   changePassword: function() {
      var pass =   $('#password').val();
      var pass2 =  $('#password2').val();
      var hash = CryptoJS.SHA1(pass + gmc.user.salt);
      $('#submit-hash').val(hash);
      if (gmc.user.validateAccountRules('ignore', pass, pass2))
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
      return CryptoJS.SHA1(new Date()).replace(/[01]/g,'').substring(0,8);
      },
   confirmAccountAction: function(uname) {
      alert('This feaure is not ready yet.');
      return false;
      },
   confirmCreateAccount: function(uname) {
      var pass = gmc.user.randomPassword();
      var hash = CryptoJS.SHA1(pass + gmc.user.salt);
      var msg = 'You are about to create the following user account.\n\tUsername: ' +
         uname + '\n\tPassword: ' + pass +
         '\n(Hint: You can select and copy the above password.)\n\nContinue?';
      $('#submit-new-username').val(uname);
      $('#submit-new-hash').val(hash);
      if (uname.length === 0)
         alert('Username cannot be blank.');
      else if (confirm(msg))
         $('#submit-create-account').submit();
      }
   };
