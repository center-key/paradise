///////////////////////////////////////////////////////////////
// Paradise ~ centerkey.com/paradise                         //
// GPLv3 ~ Copyright (c) individual contributors to Paradise //
///////////////////////////////////////////////////////////////

// Login

admin.login = {
   salt: window.location.hostname.replace(/^www[.]/, ''),
   calcSha256(message) {
      const binaryMessage = new TextEncoder().encode(message + admin.login.salt);
      const toHex = (binary) => binary.toString(16).padStart(2, '0').slice(-2);
      const handleDigest = (digest) => Array.from(new Uint8Array(digest)).map(toHex).join('');
      return window.crypto.subtle.digest('SHA-256', binaryMessage).then(handleDigest);
      },
   submit(elem) {
      const minPaswordLength = 8;
      const component =  elem.closest('.component-security');
      const action =     component.hasClass('create') ? 'create' : 'login';
      const email =      component.find('input[type=email]').val().trim().toLowerCase();
      const password =   component.find('input[type=password]').first().val().trim();
      const confirm =    component.find('input[type=password]').last().val().trim();
      const inviteCode = component.find('.invite-code input').val().trim();
      const displayError = (msg) => {
         component.find('button').enable();
         dna.ui.pulse(component.find('.error-message').text(msg));
         };
      const handleAuth = (data) => {
         if (data.authenticated)
            window.location.href = '.';
         else
            displayError(data.message);
         };
      const handleHash = (hash) => {
         const credentials = {
            email:    email,
            password: hash,
            confirm:  hash,
            invite:   inviteCode,
            };
         admin.rest.post('security', credentials, { action: action, callback: handleAuth });
         };
      if (action === 'create' && password.length < minPaswordLength)
         displayError('Password must be at least ' + minPaswordLength + ' characters long.');
      else if (action === 'create' && password !== confirm)
         displayError('Passwords do not match.');
      else
         admin.login.calcSha256(password).then(handleHash);
      component.find('button').disable();
      },
   setup(component) {
      window.fetchJson.enableLogger();
      const params = dna.browser.getUrlParams();
      dna.insert('gallery-title', window.clientData);
      dna.insert('page-footer',   window.clientData);
      component.toggleClass('create', window.clientData.userListEmpty || !!params.invite);
      component.toggleClass('invite', !!params.invite).find('.invite-code input').val(params.invite);
      component.find('input[type=email]').val(params.email);
      component.find('input:invalid').filter(':visible').first().trigger('focus');
      },
   };
