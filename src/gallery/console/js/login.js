///////////////////////////////////////////////////////////////
// Paradise ~ centerkey.com/paradise                         //
// GPLv3 ~ Copyright (c) Individual contributors to Paradise //
///////////////////////////////////////////////////////////////

// Login

admin.login = {
   salt: globalThis.location.hostname.replace(/^www[.]/, ''),
   rememberMeKey: 'remember-me',  //localStorage key to save email address
   getRemmeberMe() {
      // Automatically fill in email address field on sign in screen
      const rememberMe = globalThis.localStorage.getItem(admin.login.rememberMeKey);
      if (rememberMe)
         admin.login.elem.email.value = rememberMe;
      admin.login.elem.rememberMe.checked = rememberMe;
      },
   saveRemmeberMe(email) {
      // Store or clear email address based on user checked "Remember me" option
      if (admin.login.elem.rememberMe.checked)
         globalThis.localStorage.setItem(admin.login.rememberMeKey, email);
      else
         globalThis.localStorage.removeItem(admin.login.rememberMeKey);
      },
   calcSha256(message) {
      const byteArray =    new TextEncoder().encode(message + admin.login.salt);
      const toHex =        (byte) => byte.toString(16).padStart(2, '0').slice(-2);
      const handleDigest = (digest) => Array.from(new Uint8Array(digest)).map(toHex).join('');
      return globalThis.crypto.subtle.digest('SHA-256', byteArray).then(handleDigest);
      },
   submit() {
      const elem =           admin.login.elem;
      const minPasswordLen = 8;
      const action =         elem.component.classList.contains('create') ? 'create' : 'login';
      const inviteCode =     elem.inviteCode.value.trim();
      const email =          elem.email.value.trim().toLowerCase();
      const password =       elem.password.value.trim();
      const confirm =        elem.confirm.value.trim();
      const displayError = (msg) => {
         elem.submitButton.disabled = false;
         elem.errorMessage.textContent = msg;
         dna.ui.slideFadeIn(elem.errorMessage);
         };
      const handleAuth = (data) => {
         const redirect = () => {
            admin.login.saveRemmeberMe(email);
            globalThis.location.href = '.';
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
      if (action === 'create' && password.length < minPasswordLen)
         displayError('Password must be at least ' + minPasswordLen + ' characters long.');
      else if (action === 'create' && password !== confirm)
         displayError('Passwords do not match.');
      else
         admin.login.calcSha256(password).then(handleHash);
      elem.submitButton.disabled = true;
      },
   setup(component) {
      admin.login.elem = {
         component:    component,
         errorMessage: component.querySelector('form .error-message'),
         inviteCode:   component.querySelector('form .invite-code input'),
         email:        component.querySelector('form input[type=email]'),
         password:     component.querySelectorAll('form input[type=password]')[0],
         confirm:      component.querySelectorAll('form input[type=password]')[1],
         rememberMe:   component.querySelector('form >label.remember-me input'),
         submitButton: component.querySelector('form >nav button'),
         };
      globalThis.fetchJson.enableLogger();
      const params = dna.browser.getUrlParams();
      dna.insert('gallery-title', globalThis.clientData);
      dna.insert('page-footer',   globalThis.clientData);
      admin.login.getRemmeberMe();
      dna.dom.toggleClass(component, 'create', globalThis.clientData.userListEmpty || !!params.invite);
      dna.dom.toggleClass(component, 'invite', !!params.invite);
      component.querySelector('.invite-code input').value = params.invite;
      if (params.email)
         admin.login.elem.email.value = params.email;
      const errors = component.querySelectorAll('form input:invalid');
      dna.dom.find(errors, elem => dna.ui.isVisible(elem.closest('label')))?.focus();
      },
   };
