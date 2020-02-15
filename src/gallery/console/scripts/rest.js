///////////////////////////////////////////////////////////////
// Paradise ~ centerkey.com/paradise                         //
// GPLv3 ~ Copyright (c) individual contributors to Paradise //
///////////////////////////////////////////////////////////////

// REST

admin.rest = {
   // Submits REST request and passes response data to the callback
   // Example:
   //    admin.rest.get('book', { id: 21, callback: handle });
   makeUrl(resourceName, action) {
      let url = window.location.href.match(/^.*console/)[0] + '/rest/?resource=' + resourceName;
      return action ? url + '&action=' + action : url;
      },
   get(resourceName, options) {
      const url = admin.rest.makeUrl(resourceName, options.action);
      const handleResponse = (json) => {
         if (json.code === 401)
            window.location = '.';
         else if (json.error)
            console.error(json);
         else if (options.callback)
            options.callback(json);
         };
      return window.fetchJson.get(url, options.params).then(handleResponse);
      },
   post(resourceName, data, options) {
      const url = admin.rest.makeUrl(resourceName, options.action);
      const handleResponse = (json) => {
         if (json.error)
            console.error(json);
         else if (options.callback)
            options.callback(json);
         };
      return window.fetchJson.post(url, data).then(handleResponse);
      },
   };
