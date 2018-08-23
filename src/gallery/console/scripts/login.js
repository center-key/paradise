///////////////////////////////////////////////////////////////
// Paradise ~ centerkey.com/paradise                         //
// GPLv3 ~ Copyright (c) individual contributors to Paradise //
///////////////////////////////////////////////////////////////

// Login

admin.login = {
   salt: window.location.hostname.replace(/^www[.]/, ''),
   submit: function(elem) {
      var minPaswordLength = 8;
      var component = elem.closest('.component-security');
      component.find('button').prop({ disabled: true });
      function calcHash(passwd) { return CryptoJS.SHA256(passwd + admin.login.salt).toString(); }
      var action =     component.hasClass('create') ? 'create' : 'login';
      var email =      component.find('input[type=email]').val().trim().toLowerCase();
      var password =   component.find('input[type=password]').first().val().trim();
      var confirm =    component.find('input[type=password]').last().val().trim();
      var inviteCode = component.find('.invite-code input').val().trim();
      var credentials = {
         email:    email,
         password: calcHash(password),
         confirm:  calcHash(confirm),
         invite:   inviteCode
         };
      function displayError(msg) {
         component.find('button').enable();
         dna.ui.pulse(component.find('.error-message').text(msg));
         }
      function handle(data) {
         if (data.authenticated)
            window.location.href = '.';
         else
            displayError(data.message);
         }
      if (action === 'create' && password.length < minPaswordLength)
         displayError('Password must be at least ' + minPaswordLength + ' characters long.');
      else
         library.rest.post('security', credentials, { action: action, callback: handle });
      },
   setup: function(component) {
      var params = dna.browser.getUrlParams();
      component.toggleClass('create', window.clientData.userListEmpty || !!params.invite);
      component.toggleClass('invite', !!params.invite).find('.invite-code input').val(params.invite);
      component.find('input[type=email]').val(params.email);
      component.find('input:invalid').filter(':visible').first().focus();
      }
   };