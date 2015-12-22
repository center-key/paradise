/////////////////////////////////////////////////
// PPAGES ~ centerkey.com/ppages               //
// GPL ~ Copyright (c) individual contributors //
/////////////////////////////////////////////////

// Application

app.rest = {
   // Submits REST request and passes response data to the callback
   // Example:
   //    app.rest.get('book', { id: 21, callback: handle });
   makeUrl: function(resourceType, action, params) {
      var url = 'rest?type=' + resourceType;
      if (action)
         url = url + '&action=' + action;
      function appendParam(key) { url = url + '&' + key + '=' + encodeURIComponent(params[key]); }
      if (params)
         Object.keys(params).forEach(appendParam);
      return url;
      },
   get: function(resourceType, options) {
      var url = app.rest.makeUrl(resourceType, options.action, options.params);
      console.log('get:', url);
      function handleResponse(json) {
         if (json.error)
            console.error(url, json);
         else if (options.callback)
            options.callback(json);
         }
      return $.getJSON(url, handleResponse);
      },
   post: function(resourceType, data, options) {
      var url = app.rest.makeUrl(resourceType, options.action, options.params);
      console.log('post:', url);
      function handleResponse(json) {
         console.log('handleResponse', options, json);
         if (json.error)
            console.error(url, json);
         else if (options.callback)
            options.callback(json);
         }
      return $.post(url, data, handleResponse, 'json');
      }
   };

app.security = {
   salt: location.hostname,
   login: function(elem) {
      var minPaswordLength = 8;
      var component = elem.closest('.component-security');
      component.find('button').prop('disabled', true);
      function calcHash(passwd) { return CryptoJS.SHA256(passwd + app.security.salt).toString(); }
      var action =     component.hasClass('create') ? 'create' : 'login';
      var email =      component.find('input[type=email]').val().trim().toLowerCase();
      var password =   component.find('input[type=password]').first().val().trim();
      var confirm =    component.find('input[type=password]').last().val().trim();
      var inviteCode = component.find('.invite-code input').val().trim();
      var credentials = {
         email:    email,
         password: calcHash(password),
         confirm:  calcHash(confirm),
         intite:   inviteCode
         };
      function displayError(msg) {
         component.find('button').prop('disabled', false);
         dna.ui.slidingFlasher(component.find('.error-message').text(msg));
         }
      function handle(data) {
         console.log('response:', data);
         if (data.authenticated)
            window.location.href = '.';
         else
            displayError(data.message);
         };
      if (action === 'create' && password.length < minPaswordLength)
         displayError('Password must be at least ' + minPaswordLength + ' characters long.');
      else
         app.rest.post("security", credentials, { action: action, callback: handle });
      },
   loginSetup: function(component) {
      var params = dna.browser.getParams();
      component.toggleClass('create', app.clientData['user-list-empty'] || !!params.invite);
      component.toggleClass('invite', !!params.invite).find('.invite-code input').val(params.invite);
      component.find('input[type=email]').val(params.email);
      function isEmpty() { return !$(this).val().length; }
      component.find('input:visible').filter(isEmpty).first().focus();
      console.log('login-setup', component.attr('class'), component, params.invite);
      }
   };

app.start = {
   go: function() {
      $('a.external-site').attr('target', '_blank');
      $('a img, a i.fa').parent().addClass('plain');
      }
   };

$(app.start.go);
