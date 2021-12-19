///////////////////////////////////////////////////////////////
// Paradise ~ centerkey.com/paradise                         //
// GPLv3 ~ Copyright (c) individual contributors to Paradise //
///////////////////////////////////////////////////////////////

// Login

admin.login = {
   salt: window.location.hostname.replace(/^www[.]/, ''),
   getRemmeberMe() {
      const rememberMe = window.localStorage.getItem('remember-me');
      if (rememberMe)
         admin.login.elem.email.val(rememberMe);
      admin.login.elem.rememberMe.prop('checked', !!rememberMe);
      },
   saveRemmeberMe(email) {
      if (admin.login.elem.rememberMe.is(':checked'))
         window.localStorage.setItem('remember-me', email);
      else
         window.localStorage.removeItem('remember-me');
      },
   calcSha256(message) {
      const byteArray = new TextEncoder().encode(message + admin.login.salt);
      const toHex = (byte) => byte.toString(16).padStart(2, '0').slice(-2);
      const handleDigest = (digest) => Array.from(new Uint8Array(digest)).map(toHex).join('');
      return window.crypto.subtle.digest('SHA-256', byteArray).then(handleDigest);
      },
   submit() {
      const elem = admin.login.elem;
      const minPaswordLength = 8;
      const action =     elem.component.hasClass('create') ? 'create' : 'login';
      const inviteCode = elem.inviteCode.val().trim();
      const email =      elem.email.val().trim().toLowerCase();
      const password =   elem.password.val().trim();
      const confirm =    elem.confirm.val().trim();
      const displayError = (msg) => {
         elem.submitButton.enable();
         dna.ui.pulse(elem.errorMessage.text(msg));
         };
      const handleAuth = (data) => {
         const redirect = () => {
            admin.login.saveRemmeberMe(email);
            window.location.href = '.';
            };
         return data.authenticated ? redirect() : displayError(data.message);
         };
      const handleHash = (hash) => {
         const credentials = {
            email:    email,
            password: hash,
            confirm:  hash,
            invite:   inviteCode,
            };
         admin.rest.post('security', credentials, { action: action }).then(handleAuth);
         };
      if (action === 'create' && password.length < minPaswordLength)
         displayError('Password must be at least ' + minPaswordLength + ' characters long.');
      else if (action === 'create' && password !== confirm)
         displayError('Passwords do not match.');
      else
         admin.login.calcSha256(password).then(handleHash);
      elem.submitButton.disable();
      },
   setup(component) {
      admin.login.elem = {
         component:    component,
         errorMessage: component.find('>form .error-message'),
         inviteCode:   component.find('>form .invite-code input'),
         email:        component.find('>form input[type=email]'),
         password:     component.find('>form input[type=password]').first(),
         confirm:      component.find('>form input[type=password]').last(),
         rememberMe:   component.find('>form >label.remember-me input'),
         submitButton: component.find('>form >nav button'),
         };
      window.fetchJson.enableLogger();
      const params = dna.browser.getUrlParams();
      dna.insert('gallery-title', window.clientData);
      dna.insert('page-footer',   window.clientData);
      admin.login.getRemmeberMe();
      component.toggleClass('create', window.clientData.userListEmpty || !!params.invite);
      component.toggleClass('invite', !!params.invite).find('.invite-code input').val(params.invite);
      if (params.email)
         admin.login.elem.email.val(params.email);
      component.find('>form input:invalid').filter(':visible').first().trigger('focus');
      },
   };
