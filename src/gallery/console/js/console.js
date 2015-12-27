///////////////////////////////////////////////////
// PPAGES ~ centerkey.com/ppages                 //
// GPLv3 ~ Copyright (c) individual contributors //
///////////////////////////////////////////////////

// Application console

app.ui = {
   statusMsg: function(message) {
      $('#status-msg').text(message).hide().fadeIn();
      },
   loadSettings: function() {
      function handle(data) { dna.clone('gallery-settings', data); };
      library.rest.get('settings',  { callback: handle });
      },
   loadPortfolio: function() {
      function handle(data) { dna.clone('portfolio-image', data, { empty: true, fade: true }); };
      library.rest.get('portfolio', { callback: handle });
      },
   createUploader: function() {
      var spinner = $('#processing-files').hide();
      function handle(data) {
         app.ui.statusMsg(data.message);
         spinner.delay(2000).fadeOut(app.ui.loadPortfolio);
         }
      function start() { spinner.fadeIn(); }
      var lastTimeoutId = null;
      function done(id, fileName, responseJSON) {
         function processUploads() {
            if (timeoutId === lastTimeoutId)
               library.rest.get('command', { action: 'process-uploads', callback: handle });
            }
         var timeoutId = window.setTimeout(processUploads, 3000);  //workaround to guess when last upload in done
         lastTimeoutId = timeoutId;
         }
      var options = {
         element:           $('#file-uploader')[0],
         uploadButtonText:  'Upload images',
         action:            'file-uploader.php',
         allowedExtensions: ['jpg', 'jpeg', 'png'],
         sizeLimit:         1048576,  //1MB
         onSubmit:          start,
         onComplete:        done
         };
      return new qq.FileUploader(options);
      },
   saveSettings: function(elem) {
      var field = elem.attr('name');
      var val = elem.is('input[type=checkbox]') ? elem.is(':checked') : elem.val();
      app.ui.statusMsg('Saving ' + field.replace('-', ' ') + '...');
      var params = { [field]: val };
      function addItemParams() {
         params.id =   elem.closest('div').index() + 1;  /* elem.data().itemId; */
         params.item = elem.data().item;
         }
      if (elem.data().item)
         addItemParams();
      library.rest.get('settings', { action: 'update', params: params  });
      }
   };

app.setup = {
   go: function() {
      app.ui.loadSettings();
      app.ui.loadPortfolio();
      app.ui.createUploader();
      }
   };

$(app.setup.go);

// Gallery Management Console

if (!gmc)
   var gmc = {};

gmc.error = {
   codeAuthFail: 100
   };

gmc.ui = {
   menuBarPages: ['gallery', 'artist', 'contact'],  //temporary solution
   menuBarButtons: { up: '&uarr;', down: '&darr;', show: 'Show', hide: 'Hide',
      edit: 'Edit', del: '&times;'},
   confirmMenuBarAction: function(form, title) {
      var action = $('select[name=menu-bar-action]', form).val();
      return action !== 'del' ||
         confirm('You are about to delete the &quot;' + title + '&quot; page.  Continue?');
      },
   slowDimOut: function(elem) {
      var delayVerySlow = 8000;
      elem.stop().fadeTo('fast', 1.0).fadeTo(delayVerySlow, 0.4);
      },
   statusMsg: function(msg) {
      gmc.ui.slowDimOut($('#status-msg').html(msg));
      },
   setupStatusMsg: function() {
      var msg = '' + $('#status-msg').html();
      if (msg.length === 0)
         msg = 'Ready';
      gmc.ui.statusMsg(msg);
      },
   processServiceResponce: function(data, successMsg) {
      if (data.error === gmc.error.codeAuthFail)
         window.location.reload();
      gmc.ui.statusMsg(data.error ? 'Error: ' + data.message : successMsg);
      return !data.error;
      },
   updatePortfolio: function(id, field, value) {
      var params = {
         action: 'portfolio',
         id:     id,
         field:  field,
         value:  value
         };
      function handle(data) {
         console.log(data);
         gmc.ui.processServiceResponce(data, 'Image &quot;' + data.caption + '&quot; updated');
         }
      return gmc.rest.service({ params: params, callback: handle });
      },
   updateSettingsWebsite: function(field, value) {
      var params = {
         action: 'settings',
         field:  field,
         value:  value
         };
      function handle(data) {
         gmc.ui.processServiceResponce(data, 'Gallery settings updated');
         }
      return gmc.rest.service({ params: params, callback: handle });
      },
   menuBarActionAllowed: function(action, page, loc, len, show) {
      return (action === 'up' && (loc > 1)) ||
         (action === 'down' && (loc !== len - 1 && page !== 'gallery')) ||
         (action === 'show' && (!show)) ||
         (action === 'hide' && (show && page !== 'gallery')) ||
         (action === 'edit' && (page !== 'gallery' && page !== 'contact')) ||
         (action === 'del'  && (page !== 'gallery' && page !== 'contact'));
      },
   configureMenuBarButtonsSinglePage: function(data, loc) {
      var buttons = $('#settings-menu-bar buttons');
      function cfg(action) {
         var allowed = gmc.ui.menuBarActionAllowed(action,
            gmc.ui.menuBarPages[loc], loc, 3, data[loc].show);
         buttons.find('.' + action + '[name=' + gmc.ui.menuBarPages[loc] + ']')
            .attr('disabled', allowed);
         }
      if (buttons.length)
         gmc.ui.menuBarButtons.forEach(cfg);
      },
   configureMenuBarButtons: function() {
      var params = {
         action: 'menu-bar'
         };
      function handle(data) {
         gmc.ui.menuBarPages.forEach(function(page, loc) {
            gmc.ui.configureMenuBarButtonsSinglePage(data, loc);
            });
         }
      return gmc.rest.service({ params: params, callback: handle });
      },
   updateSettingsMenuBar: function(page, task, value) {
      var params = {
         action: 'menu-bar',
         page:   page,
         task:   task,
         value:  value
         };
      function handle(data) {
         gmc.ui.processServiceResponce(data, 'Gallery menu bar updated');
         var loc = gmc.ui.menuBarPages.indexOf(page);
         gmc.ui.configureMenuBarButtonsSinglePage(data, loc);
         }
      return gmc.rest.service({ params: params, callback: handle });
      },
   setupActions: function() {
      $('.login input').keyup(function(event) {
         if(event.keyCode === 13)
           $('.login button').click();
         });

      $('.portfolio-display').change(function() {
         gmc.ui.updatePortfolio($(this).attr('name'), 'display', $(this).is(':checked'));
         });
      $('.portfolio-caption').blur(function() {
         gmc.ui.updatePortfolio($(this).attr('name'), 'caption', $(this).val());
         });
      $('.portfolio-description').blur(function() {
         gmc.ui.updatePortfolio($(this).attr('name'), 'description', $(this).val());
         });
      $('.portfolio-badge').blur(function() {
         gmc.ui.updatePortfolio($(this).attr('name'), 'badge', $(this).val());
         });

      $('.portfolio-move-up').blur(function() {
         gmc.ui.updatePortfolio($(this).attr('name'), 'move', 'up');
         });
      $('.portfolio-move-down').blur(function() {
         gmc.ui.updatePortfolio($(this).attr('name'), 'move', 'down');
         });
      $('.portfolio-delete').blur(function() {
         gmc.ui.updatePortfolio($(this).attr('name'), 'command', 'delete');
         });
      $('#settings-website input[type=text]').blur(function() {
         gmc.ui.updateSettingsWebsite($(this).attr('name'), $(this).val());
         });
      $('#settings-website select').change(function() {
         gmc.ui.updateSettingsWebsite($(this).attr('name'),
            $(this).find('option:selected').text());
         });
      $('#settings-website input[type=checkbox]').change(function() {
         gmc.ui.updateSettingsWebsite($(this).attr('name'), $(this).is(':checked'));
         });
      $('#settings-menu-bar input[type=text]').blur(function() {
         gmc.ui.updateSettingsMenuBar($(this).attr('name'), 'save', $(this).val());
         });
      $('#settings-menu-bar button').click(function() {
         gmc.ui.updateSettingsMenuBar($(this).attr('name'), $(this).attr('class'));
         });
      $('#change-password').click(function() { gmc.user.changePassword(); });
      $('#create-account').click(function() {
         gmc.user.confirmCreateAccount($('#username').val());
         });
      }
   };

gmc.rest = {
   // Submits REST request and passes response data to the callback
   // Example:
   //    gmc.rest.get('book', { callback: handle });
   makeUrl: function(params) {
      var url = 'service/';
      function appendParam(key) { url = url + '&' + key + '=' + encodeURIComponent(params[key]); }
      if (params)
         Object.keys(params).forEach(appendParam);
      return url.replace(/&/, '?');
      },
   service: function(options) {
      var url = gmc.rest.makeUrl(options.params);
      console.log('service:', url);
      function handleResponse(json) {
         if (json.error)
            console.error(url, json);
         else if (options.callback)
            options.callback(json);
         }
      return $.getJSON(url, handleResponse);
      }
   };
