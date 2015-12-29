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
      function handle(data) { dna.clone('gallery-settings', data); }
      library.rest.get('settings',  { callback: handle });
      },
   loadPortfolio: function() {
      function handle(data) {
         dna.clone('portfolio-image', data, { empty: true, fade: true });
         app.ui.statusMsg('Portfolio images: ' + data.length);
         }
      library.rest.get('portfolio', { callback: handle });
      },
   save: function(elem, type, id) {
      var field = elem.attr('name');
      var val = elem.is('input[type=checkbox]') ? elem.is(':checked') : elem.val();
      app.ui.statusMsg('Saving ' + field.replace('-', ' ') + '...');
      var params = { [field]: val };
      var item = elem.closest('[data-item-id]').data();
      if (item && item.itemId)
         params.id = item.itemId;
      if (item && item.itemType)
         params.item = item.itemType;
      library.rest.get(type, { action: 'update', params: params  });
      },
   savePortfolio: function(elem) {
      app.ui.save(elem, 'portfolio');
      },
   saveSettings: function(elem) {
      var item = elem.closest('[data-item-id]');  //workaround
      if (item.length)                            //workaround
         item.data().itemId = item.index() + 1;   //workaround
      app.ui.save(elem, 'settings');
      },
   loadAccounts: function(elem) {
      function handle(data) { dna.clone('user-account', data); }
      library.rest.get('account', { callback: handle });
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
      }
   };

app.setup = {
   go: function() {
      app.ui.loadSettings();
      app.ui.loadPortfolio();
      app.ui.loadAccounts();
      app.ui.createUploader();
      }
   };

$(app.setup.go);
