///////////////////////////////////////////////////////////////
// Paradise ~ centerkey.com/paradise                         //
// GPLv3 ~ Copyright (c) individual contributors to Paradise //
///////////////////////////////////////////////////////////////

// REST

admin.rest = {
   // Submits REST request and passes back response data
   // Example:
   //    admin.rest.get('book', { id: 21 }).then(handle);
   makeUrl(resourceName, action) {
      const url = window.location.href.match(/^.*console/)[0] + '/rest/?resource=' + resourceName;
      return action ? url + '&action=' + action : url;
      },
   handleResponse(resource) {
      if (resource.code === 401)
         window.location = '.';
      else if (resource.error)
         window.console.error(resource);
      return resource;
      },
   get(resourceName, options) {
      const url = admin.rest.makeUrl(resourceName, options && options.action);
      return window.fetchJson.get(url, options && options.params).then(admin.rest.handleResponse);
      },
   post(resourceName, data, options) {
      const url = admin.rest.makeUrl(resourceName, options && options.action);
      return window.fetchJson.post(url, data).then(admin.rest.handleResponse);
      },
   };
