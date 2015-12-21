/////////////////////////////////////////////////
// PPAGES ~ centerkey.com/ppages               //
// GPL ~ Copyright (c) individual contributors //
/////////////////////////////////////////////////

// Application

var app = app ? app : {};

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
      var component = elem.closest('#component-security');
      component.find('button').prop('disabled', true);
      function calcHash(passwd) { return CryptoJS.SHA256(passwd + app.security.salt).toString(); }
      var action =   component.hasClass('create') ? 'create' : 'login';
      var email =    component.find('input[type=email]').val().trim().toLowerCase();
      var password = component.find('input[type=password]').first().val().trim();
      var confirm =  component.find('input[type=password]').last().val().trim();
      var credentials = { email: email, password: calcHash(password), confirm: calcHash(confirm) };
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
      if (action === 'create' && password.length < 8)
         displayError('Password must be at least 8 characters long.');
      else
         app.rest.post("security", credentials, { action: action, callback: handle });
      }
   };

app.start = {
   go: function() {
      $('a.external-site').attr('target', '_blank');
      $('a img, a i.fa').parent().addClass('plain');
      }
   };

$(app.start.go);
