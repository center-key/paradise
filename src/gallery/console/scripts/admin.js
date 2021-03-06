///////////////////////////////////////////////////////////////
// Paradise ~ centerkey.com/paradise                         //
// GPLv3 ~ Copyright (c) individual contributors to Paradise //
///////////////////////////////////////////////////////////////

// Administrator console

const admin = {
   settings: {},
   setup() {
      window.fetchJson.enableLogger();
      admin.ui.loadSettings(admin.ui.loadPortfolio);
      admin.ui.loadAccounts();
      admin.ui.configureUploader();
      dna.clone('account-invite', window.clientData.invites);
      dna.clone('backup-file',    window.clientData.backupFiles);
      dna.insert('page-footer',   window.clientData);
      },
   };

admin.ui = {
   statusMsg(message) {
      return $('#status-msg').text(message).fadeOut(0).fadeIn();
      },
   showNotice(options) {
      const noticeBox = $('notice-box');
      noticeBox.find('>div >p').text(options.message);
      noticeBox.find('>div >b').text(options.listHeader || '');
      noticeBox.find('>div >ul').empty();
      if (options.list)
         options.list.forEach(item => noticeBox.find('>div >ul').append($('<li>').text(item)));
      noticeBox.addClass('show');
      },
   hideNotice() {
      $('notice-box').removeClass('show');
      },
   loadSettings(callback) {
      const handle = (data) => {
         admin.settings = data;
         if (callback)
            callback();
         data.fonts = window.clientData.fonts;
         dna.clone('gallery-settings', data);
         };
      admin.rest.get('settings').then(handle);
      },
   loadPortfolio() {
      const stampInfo = {
         icon:  admin.settings.stampIcon,
         title: admin.settings.stampTitle,
         };
      const addStampInfo = (image) => image.stampInfo = stampInfo;
      const handle = (data) => {
         dna.clone('portfolio-image', data, { empty: true, fade: true, transform: addStampInfo });
         admin.ui.statusMsg('Portfolio images: ' + data.length);
         };
      admin.rest.get('portfolio', { action: 'list' }).then(handle);
      },
   save(elem, type) {
      const field = elem.data().dnaField;
      const val = elem.is('input[type=checkbox]') ? elem.is(':checked') : elem.val();
      admin.ui.statusMsg('Saving ' + dna.util.toKebab(field).replace('-', ' ') + '...');
      const params = {};
      params[field] = val;
      const item = elem.closest('[data-item-id]').data();
      if (item && item.itemId)
         params.id = item.itemId;
      if (item && item.itemType)
         params.item = item.itemType;
      admin.rest.get(type, { action: 'update', params: params  });
      },
   savePortfolio(elem) {
      return admin.ui.save(elem, 'portfolio');
      },
   saveSettings(elem) {
      const item = elem.closest('[data-item-id]');  //workaround
      if (item.length)                              //workaround
         item.data().itemId = item.index() + 1;     //workaround
      admin.ui.save(elem, 'settings');
      },
   move(elem) {
      const params = {
         id:   dna.getModel(elem).id,
         move: elem.data().move,
         };
      const handle = () => params.move === 'up' ? dna.up(elem) : dna.down(elem);
      admin.rest.get('portfolio', { action: 'update', params: params }).then(handle);
      },
   delete(elem) {
      const params = { id: dna.getModel(elem).id };
      const handle = () => dna.bye(elem);
      admin.rest.get('portfolio', { action: 'delete', params: params }).then(handle);
      },
   loadAccounts() {
      const addDate = (account) => account.lastLogin = new Date(account.login).toDateString() +
         ' (' + account.valid + ')';
      const handle = (accounts) => dna.clone('user-account', accounts, { transform: addDate });
      admin.rest.get('account', { action: 'list' }).then(handle);
      },
   configureUploader() {
      const maxFileMB = 2;
      const maxNumFiles = 20;
      const options = {
         dictDefaultMessage:    '<p><button>Upload photos</button></p>(or just drag photos here)',
         url:                   'upload.php',
         acceptedFiles:         ['image/jpeg', 'image/png'].join(','),
         maxFilesize:           maxFileMB,
         maxFiles:              maxNumFiles,
         parallelUploads:       maxNumFiles,
         uploadMultiple:        true,
         createImageThumbnails: false,
         };
      const uploaderElem = $('#gallery-uploader').addClass('dropzone');
      const dropzone = new window.Dropzone(uploaderElem[0], options);
      const start = () => admin.ui.statusMsg('Uploading photos...');
      const done = () => {
         const handle = (uploads) => {
            if (uploads.fails.length)
               admin.ui.showNotice(
                  { message: uploads.message, listHeader: 'Invalid files:', list: uploads.fails });
            admin.ui.loadPortfolio();
            const resetDropzone = () => {
               const uploadBoxes = uploaderElem.find('.dz-preview');
               dna.ui.slideFadeOut(uploadBoxes, () => dropzone.removeAllFiles());
               };
            window.setTimeout(resetDropzone, 2000);
            };
         uploaderElem.addClass('pulse');
         admin.ui.statusMsg('Processing photos...');
         admin.rest.get('command', { action: 'process-uploads' }).then(handle);
         };
      dropzone.on('sendingmultiple',  start);
      dropzone.on('completemultiple', done);
      }
   };

admin.invites = {
   elem: {
      email:      $('.admin-accounts .send-invite input[type=email]'),
      sendButton: $('.admin-accounts .send-invite button'),
      },
   validate(input) {
      admin.invites.elem.sendButton.enable(libX.util.cleanupEmail(input.val()));
      },
   send(button) {
      button.disable();
      const email = libX.util.cleanupEmail(admin.invites.elem.email.val());
      const handle = (data) => {
         admin.ui.statusMsg(data.message);
         admin.invites.loadList();
         admin.invites.elem.email.trigger('focus').val('');
         };
      admin.rest.get('invite', { action: 'create', params: { email: email } }).then(handle);
      },
   loadList() {
      const handle = (data) => dna.clone('account-invite', data, { empty: true, fade: true });
      admin.rest.get('invite', { action: 'list' }).then(handle);
      },
   };

admin.backups = {
   create(button) {
      button.disable();
      admin.ui.statusMsg('Creating backup...');
      const handle = (data) => {
         admin.ui.statusMsg('Backup "' + data.filename +
            '" created in ' + data.milliseconds/1000 + ' seconds');
         dna.clone('backup-file', data, { top: true, fade: true });
         button.enable();
         };
      admin.rest.get('backup', { action: 'create' }).then(handle);
      },
   loadList() {
      const handle = (data) => dna.clone('backup-file', data);
      admin.rest.get('backup', { action: 'list' }).then(handle);
      },
   };
