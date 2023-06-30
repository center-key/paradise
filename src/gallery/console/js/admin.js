///////////////////////////////////////////////////////////////
// Paradise ~ centerkey.com/paradise                         //
// GPLv3 ~ Copyright (c) Individual contributors to Paradise //
///////////////////////////////////////////////////////////////

// Administrator console

const admin = {
   settings: {},
   setup() {
      globalThis.fetchJson.enableLogger();
      admin.ui.loadSettings(admin.ui.loadPortfolio);
      admin.ui.loadAccounts();
      admin.ui.configureUploader();
      dna.clone('account-invite', globalThis.clientData.invites);
      dna.clone('backup-file',    globalThis.clientData.backupFiles);
      dna.insert('page-footer',   globalThis.clientData);
      },
   };

admin.ui = {
   statusMsg(message) {
      const elem = globalThis.document.getElementById('status-msg');
      dna.ui.pulse(elem, { noFadeOut: true, text: message });
      },
   abort(message) {
      admin.ui.statusMsg('ERROR: ' + message);
      admin.rest.post('log', { message });
      throw Error(message);
      },
   showNotice(options) {
      // <notice-box>
      //    <header>
      //       <nav><i data-icon=times data-on-click=admin.ui.hideNotice></i></nav>
      //       <h2>Error</h2>
      //    </header>
      //    <div>
      //       <p></p>
      //       <b></b>
      //       <ul></ul>
      //    </div>
      // </notice-box>
      const noticeBox = globalThis.document.querySelector('notice-box');
      noticeBox.querySelector('div >p').textContent = options.message;
      noticeBox.querySelector('div >b').textContent = options.listHeader ?? '';
      const listContainer = noticeBox.querySelector('div >ul');
      while (listContainer.firstChild)
         parent.removeChild(listContainer.firstChild);
      if (options.list)
         options.list.forEach(item => listContainer.appendChild(dna.dom.create('li', { text: item })));
      noticeBox.classList.add('show');
      },
   hideNotice() {
      globalThis.document.querySelector('notice-box').classList.remove('show');
      },
   loadSettings(callback) {
      const handle = (data) => {
         admin.settings = data;
         if (callback)
            callback();
         data.fonts = globalThis.clientData.fonts;
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
      const field = dna.dom.state(elem).dnaField;
      const value =   elem.matches('input[type=checkbox]') ? elem.checked : elem.value;
      admin.ui.statusMsg('Saving ' + dna.util.toKebab(field).replace('-', ' ') + '...');
      const params = {};
      params[field] = value;
      const itemData = elem.closest('[data-item-id]')?.dataset;
      if (itemData?.itemId)
         params.id = itemData.itemId;
      if (itemData?.itemType)
         params.item = itemData.itemType;
      admin.rest.get(type, { action: 'update', params: params  });
      },
   savePortfolio(elem) {
      return admin.ui.save(elem, 'portfolio');
      },
   saveSettings(elem) {
      admin.ui.save(elem, 'settings');
      },
   move(elem) {
      // <i data-icon=arrow-up data-on-click=admin.ui.move data-move=up></i>
      const params = {
         id:   dna.getModel(elem).id,
         move: elem.dataset.move,
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
      const addDate = (account) =>
         account.lastLogin = new Date(account.login).toDateString() + ' (' + account.valid + ')';
      const handle = (accounts) => dna.clone('user-account', accounts, { transform: addDate });
      admin.rest.get('account', { action: 'list' }).then(handle);
      },
   configureUploader() {
      const maxFileMB =     2;
      const maxNumFiles =   20;
      const uploadHelp =    'Or just drag photos here<br>(2 MB limit per file)';
      const acceptedTypes = ['image/jpeg', 'image/png'];
      const options = {
         dictDefaultMessage:    '<p><button>Upload photos</button></p>' + uploadHelp,
         url:                   'upload.php',
         acceptedFiles:         acceptedTypes.join(','),
         maxFilesize:           maxFileMB,
         maxFiles:              maxNumFiles,
         parallelUploads:       maxNumFiles,
         uploadMultiple:        true,
         createImageThumbnails: false,
         };
      const uploaderElem = globalThis.document.getElementById('gallery-uploader');
      uploaderElem.classList.add('dropzone');
      const dropzone = new globalThis.Dropzone(uploaderElem, options);
      const start =    () => admin.ui.statusMsg('Uploading photos...');
      const done = () => {
         const handle = (uploads) => {
            if (!uploads.fails)
               admin.ui.abort(uploads.bodyText);
            if (uploads.fails.length)
               admin.ui.showNotice(
                  { message: uploads.message, listHeader: 'Invalid files:', list: uploads.fails });
            admin.ui.loadPortfolio();
            const resetDropzone = () => {
               const uploadBoxes = uploaderElem.querySelectorAll('.dz-preview');
               uploadBoxes.forEach(dna.ui.slideFadeOut);
               globalThis.setTimeout(() => dropzone.removeAllFiles(), 1000);
               };
            globalThis.setTimeout(resetDropzone, 5000);
            };
         uploaderElem.classList.add('pulse');
         admin.ui.statusMsg('Processing photos...');
         admin.rest.get('command', { action: 'process-uploads' }).then(handle);
         };
      dropzone.on('sendingmultiple',  start);
      dropzone.on('completemultiple', done);
      }
   };

admin.invites = {
   elem: {
      email:      globalThis.document.querySelector('.admin-accounts .send-invite input[type=email]'),
      sendButton: globalThis.document.querySelector('.admin-accounts .send-invite button'),
      },
   validate(input) {
      admin.invites.elem.sendButton.enable(libX.util.cleanupEmail(input.value));
      },
   send(button) {
      button.disabled = true;
      const email = libX.util.cleanupEmail(admin.invites.elem.email.value);
      const handle = (data) => {
         admin.ui.statusMsg(data.message);
         admin.invites.loadList();
         admin.invites.elem.email.value = '';
         admin.invites.elem.email.focus();
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
      button.disabled = true;
      admin.ui.statusMsg('Creating backup...');
      const handle = (data) => {
         const seconds = data.milliseconds / 1000;
         admin.ui.statusMsg(`Backup "${data.filename}" created in ${seconds} seconds`);
         dna.clone('backup-file', data, { top: true, fade: true });
         button.disabled = false;
         };
      admin.rest.get('backup', { action: 'create' }).then(handle);
      },
   loadList() {
      const handle = (data) => dna.clone('backup-file', data);
      admin.rest.get('backup', { action: 'list' }).then(handle);
      },
   };
