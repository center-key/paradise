///////////////////////////////////////////////////////////////
// Paradise ~ centerkey.com/paradise                         //
// GPLv3 ~ Copyright (c) Individual contributors to Paradise //
///////////////////////////////////////////////////////////////

// REST

admin.rest = {
   // Submits REST request and passes back response data
   // Example:
   //    admin.rest.get('book', { id: 21 }).then(handle);
   makeUrl(resourceName, action) {
      const url = globalThis.location.href.match(/^.*console/)[0] + '/rest/?resource=' + resourceName;
      return action ? url + '&action=' + action : url;
      },
   handleResponse(resource) {
      if (resource.code === 401)
         globalThis.location = '.';
      else if (resource.error)
         globalThis.console.error(resource);
      return resource;
      },
   get(resourceName, options) {
      const url = admin.rest.makeUrl(resourceName, options?.action);
      return globalThis.fetchJson.get(url, options?.params).then(admin.rest.handleResponse);
      },
   post(resourceName, data, options) {
      const url = admin.rest.makeUrl(resourceName, options?.action);
      return globalThis.fetchJson.post(url, data).then(admin.rest.handleResponse);
      },
   };
