///////////////////////////////////////////////////
// PPAGES ~ centerkey.com/ppages                 //
// GPLv3 ~ Copyright (c) individual contributors //
///////////////////////////////////////////////////

// Library
// General purpose js functions

var library = {};

library.rest = {
   // Submits REST request and passes response data to the callback
   // Example:
   //    library.rest.get('book', { id: 21, callback: handle });
   makeUrl: function(resourceType, action, params) {
      var url = window.location.href.match(/^.*console/)[0] + '/rest/?type=' + resourceType;
      //TODO: figure out why trailing slash is needed for post requests, see: http://stackoverflow.com/questions/12195883/jquery-ajax-is-sending-get-instead-of-post
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
         if (json.error)
            console.error(url, json);
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
            console.error(url, json);
         else if (options.callback)
            options.callback(json);
         }
      return $.post(url, data, handleResponse, 'json');
      }
   };

library.ui = {
   start: function() {
      $('a.external-site').attr('target', '_blank');
      $('a img, a i.fa').parent().addClass('plain');
      }
   };

$(library.ui.start);
