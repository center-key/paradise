///////////////////////////////////////////////////////////////
// Paradise ~ centerkey.com/paradise                         //
// GPLv3 ~ Copyright (c) individual contributors to Paradise //
///////////////////////////////////////////////////////////////

// Library
// General purpose js functions

var library = {};

library.rest = {
   // Submits REST request and passes response data to the callback
   // Example:
   //    library.rest.get('book', { id: 21, callback: handle });
   makeUrl: function(resourceType, action, params) {
      var url = window.location.href.match(/^.*console/)[0] + '/rest/?type=' + resourceType;
      //TODO: figure out why trailing slash is needed for post requests, see: https://stackoverflow.com/questions/12195883/jquery-ajax-is-sending-get-instead-of-post
      if (action)
         url = url + '&action=' + action;
      function appendParam(key) { url = url + '&' + key + '=' + encodeURIComponent(params[key]); }
      if (params)
         Object.keys(params).forEach(appendParam);
      return url;
      },
   get: function(resourceType, options) {
      var url = library.rest.makeUrl(resourceType, options.action, options.params);
      console.log('get:', url);
      function handleResponse(json) {
         if (json.code === 401)
            window.location = '.';
         else if (json.error)
            window.console.error(url, json);
         else if (options.callback)
            options.callback(json);
         }
      return $.getJSON(url, handleResponse);
      },
   post: function(resourceType, data, options) {
      var url = library.rest.makeUrl(resourceType, options.action, options.params);
      console.log('post:', url);
      function handleResponse(json) {
         if (json.error)
            window.console.error(url, json);
         else if (options.callback)
            options.callback(json);
         }
      return $.post(url, data, handleResponse, 'json');
      }
   };

library.ui = {
   id: function(value) {
      return value === undefined ? $(this).attr('id') : $(this).attr({ id: value });
      },
   enable: function(value) {
      return $(this).prop({ disabled: value !== undefined && !value });
      },
   disable: function(value) {
      return $(this).prop({ disabled: value === undefined || !!value });
      },
   displayAddr: function() {
      // Usage:
      //    <p class=display-addr data-name=sales data-domain=ibm.com></p>
      function display(i, elem) {
         var data = $(elem).data();
         $(elem).html(data.name + '<span>' + String.fromCharCode(64) + data.domain + '</span>');
         }
      $('.display-addr').each(display);
      },
   start: function() {
      $.fn.id =      library.ui.id;
      $.fn.enable =  library.ui.enable;
      $.fn.disable = library.ui.disable;
      function makeIcon(i, elem) { $(elem).addClass('fa fa-' + $(elem).data().icon); }
      $('i.font-icon').each(makeIcon);
      function setup() {
         $('a img, a i.font-icon, footer a').parent().addClass('plain');
         if (!dna.browser.iOS())
            $('.external-site a, a.external-site').attr({ target: '_blank' });
         }
      $(setup);
      }
   };

library.ui.start();
