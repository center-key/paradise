///////////////////////////////////////////////////////////////
// Paradise ~ centerkey.com/paradise                         //
// GPLv3 ~ Copyright (c) individual contributors to Paradise //
///////////////////////////////////////////////////////////////

// Library
// General purpose js functions

var library = {
   initialize: function() {
      $.fn.id =      library.ui.id;
      $.fn.enable =  library.ui.enable;
      $.fn.disable = library.ui.disable;
      library.social.setup();
      dna.registerInitializer(library.ui.normalize);
      }
   };

library.ui = {
   id: function(value) {
      // Usage:
      //    const userElem = $('.user').id('J777');
      //    const userId = userElem.id();
      return value === undefined ? $(this).attr('id') : $(this).attr({ id: value });
      },
   enable: function(value) {
      // Usage:
      //    $('button').enable();
      return $(this).prop({ disabled: value !== undefined && !value });
      },
   disable: function(value) {
      // Usage:
      //    $('button').disable();
      return $(this).prop({ disabled: value === undefined || !!value });
      },
   normalize: function(holder) {
      holder = holder || $(window.document);
      function makeIcon(i, elem) { $(elem).addClass('fa-' + $(elem).data().icon); }
      function makeBrand(i, elem) { $(elem).addClass('fa-' + $(elem).data().brand); }
      holder.find('i[data-icon]').addClass('font-icon fas').each(makeIcon);
      holder.find('i[data-brand]').addClass('font-icon fab').each(makeBrand);
      holder.find('button:not([type])').attr({ type: 'button' });
      holder.find('input:not([type])').attr({ type: 'text' });
      holder.find('input[type=email]').attr({ autocorrect: 'off', spellcheck: false });
      holder.find('a img, a i.font-icon').closest('a').addClass('image-link');
      if (!dna.browser.iOS())
         holder.find('a.external-site, .external-site a').attr({ target: '_blank' });
      },
   popup: function(url, options) {
      const settings = $.extend({ width: 600, height: 400 }, options);
      const dimensions = 'left=200,top=100,width=' + settings.width + ',height=' + settings.height;
      window.open(url, '_blank', dimensions + ',scrollbars,resizable,status');
      }
   };

library.util = {
   cleanupEmail: function(email) {
      // Usage:
      //    library.util.cleanupEmail(' Lee@Example.Com ') === 'lee@exampe.com';
      //    library.util.cleanupEmail('bogus@example') === false;
      email = email && email.replace(/\s/g, '').toLowerCase();
      return /.+@.+[.].+/.test(email) ? email : false;  //rudimentary format check
      }
   };

// Social bookmarking
library.social = {
   // Usage:
   //    <div id=social-buttons></div>
   buttons: [
      { icon: 'google',      title: 'Google',   x: 480, y: 700, link: 'https://plus.google.com/share?url=${url}' },
      { icon: 'twitter',     title: 'Twitter',  x: 580, y: 350, link: 'https://twitter.com/share?text=${title}&url=${url}' },
      { icon: 'facebook-f',  title: 'Facebook', x: 580, y: 350, link: 'https://www.facebook.com/sharer.php?u=${url}' },
      { icon: 'linkedin-in', title: 'LinkedIn', x: 580, y: 350, link: 'https://www.linkedin.com/shareArticle?mini=true&url=${url}&title=${title}' },
      { icon: 'digg',        title: 'Digg',     x: 985, y: 700, link: 'https://digg.com/submit?url=${url}' },
      { icon: 'reddit',      title: 'Reddit',   x: 600, y: 750, link: 'https://www.reddit.com/submit?url=${url}$title=${title}' }
      ],
   share: function(elem) {
      const button = library.social.buttons[elem.data().icon];
      function insert(str, find, value) { return str.replace(find, encodeURIComponent(value)); }
      let link = insert(button.link, '${url}', window.location.href);
      link = insert(link, '${title}', window.document.title);
      library.ui.popup(link, { width: button.x, height: button.y });
      },
   setup: function() {
      function initializeSocialButtons() {
         const container = $('#social-buttons');
         const iconHtml = ['<i data-brand=', ' data-click=library.social.share></i>'];  //click by dna.js
         let html = '<span>';
         function addHtml(button) { html += iconHtml[0] + button.icon + iconHtml[1]; }
         if (container.length)
            library.social.buttons.forEach(addHtml);
         container.fadeTo(0, 0.0).html(html + '</span>').fadeTo('slow', 1.0);
         }
      initializeSocialButtons();
      }
   };

library.rest = {
   // Submits REST request and passes response data to the callback
   // Example:
   //    library.rest.get('book', { id: 21, callback: handle });
   makeUrl: function(resourceName, action) {
      let url = window.location.href.match(/^.*console/)[0] + '/rest/?resource=' + resourceName;
      if (action)
         url = url + '&action=' + action;
      return url;
      },
   get: function(resourceName, options) {
      const url = library.rest.makeUrl(resourceName, options.action);
      function handleResponse(json) {
         if (json.code === 401)
            window.location = '.';
         else if (json.error)
            console.error(json);
         else if (options.callback)
            options.callback(json);
         }
      return window.fetchJson.get(url, options.params).then(handleResponse);
      },
   post: function(resourceName, data, options) {
      const url = library.rest.makeUrl(resourceName, options.action);
      function handleResponse(json) {
         if (json.error)
            console.error(json);
         else if (options.callback)
            options.callback(json);
         }
      return window.fetchJson.post(url, data).then(handleResponse);
      }
   };

library.initialize();
