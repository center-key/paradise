///////////////////////////////////////////////////
// Paradise ~ centerkey.com/paradise             //
// GPLv3 ~ Copyright (c) individual contributors //
///////////////////////////////////////////////////

// Application console

app.ui = {
   statusMsg: function(message) {
      $('#status-msg').text(message).fadeOut(0).fadeIn();
      },
   loadSettings: function() {
      function handle(data) { dna.clone('gallery-settings', data); }
      library.rest.get('settings', { callback: handle });
      },
   loadPortfolio: function() {
      function handle(data) {
         dna.clone('portfolio-image', data, { empty: true, fade: true });
         app.ui.statusMsg('Portfolio images: ' + data.length);
         }
      library.rest.get('portfolio', { action: 'list', callback: handle });
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
   action: function(elem) {
      var action = elem.data().action;
      var params = { id: dna.getModel(elem).id };
      var erase = action === 'delete';
      var up =    action === 'up';
      function move() {
         var box = dna.getClone(elem);
         var submissiveBox = up ? box.prev() : box.next();
         var ghostBox = submissiveBox.clone();
         if (up)
            box.after(submissiveBox.hide()).before(ghostBox);
         else
            box.before(submissiveBox.hide()).after(ghostBox);
         dna.ui.slideFadeIn(submissiveBox);
         dna.ui.slideFadeDelete(ghostBox);
         }
      function handle(data) { return erase ? dna.bye(elem) : move(); }
      if (!erase)
         params.move = action;
      var restAction = erase ? action : 'update';
      library.rest.get('portfolio', { action: restAction, params: params, callback: handle });
      },
   loadAccounts: function(elem) {
      function handle(data) { dna.clone('user-account', data); }
      library.rest.get('account', { action: 'list', callback: handle });
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
         sizeLimit:         2 * 1024 * 1024,  //2 MB
         onSubmit:          start,
         onComplete:        done
         };
      return new qq.FileUploader(options);
      }
   };

app.invites = {
   elem: {
      startButton: $('.admin-accounts .send-invite >button'),
      form:        $('.admin-accounts .send-invite >button+div'),
      email:       $('.admin-accounts .send-invite >button+div input[type=email]'),
      sendButton:  $('.admin-accounts .send-invite >button+div button'),
      },
   component: $('.admin-accounts .send-invite'),
   prompt: function() {
      dna.ui.slideFadeOut(app.invites.elem.startButton);
      dna.ui.slideFadeIn(app.invites.elem.form);
      app.invites.elem.email.focus();
      },
   validate: function(elem) {
      var basicEmailPattern = /.+@.+[.].+/;
      var invalid = !elem.val().match(basicEmailPattern);
      app.invites.component.find('div button').prop('disabled', invalid);
      },
   send: function(elem) {
      app.invites.component.find('div button').prop('disabled', true);
      function handle(data) {
         app.ui.statusMsg(data.message);
         dna.ui.slideFadeIn(app.invites.elem.startButton);
         dna.ui.slideFadeOut(app.invites.elem.form);
         app.invites.loadList();
         }
      var email = app.invites.component.find('input[type=email]').val();
      library.rest.get('invite', { action: 'create', params: { email: email }, callback: handle  });
      },
   loadList: function() {
      function handle(data) { dna.clone('account-invite', data, { empty: true, fade: true }); }
      library.rest.get('invite', { action: 'list', callback: handle });
      }
   };

app.setup = {
   go: function() {
      app.ui.loadSettings();
      app.ui.loadPortfolio();
      app.ui.loadAccounts();
      app.invites.loadList();
      app.ui.createUploader();
      }
   };

$(app.setup.go);
