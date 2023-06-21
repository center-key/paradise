///////////////////////////////////////////////////////////////
// Paradise ~ centerkey.com/paradise                         //
// GPLv3 ~ Copyright (c) Individual contributors to Paradise //
///////////////////////////////////////////////////////////////

// Gallery application
const gallery = {
   configureForm(elem) {
      const perfectForm = elem.closest('form.send-message');
      perfectForm.method = 'post';
      perfectForm.action = 'frontend-server/send-message.php';
      },
   start() {
      globalThis.document.querySelectorAll('body >footer .hide-me').forEach(elem => elem.remove());
      const options = {
         delegate: '>a',  //child items selector, click to open popup
         type:     'image',
         image:    { titleSrc: 'data-title' },
         gallery:  { enabled: true },
         };
      const figures = globalThis.document.querySelectorAll('.gallery-images figure');
      globalThis.$(figures).magnificPopup(options);
      },
   };

dna.dom.onReady(gallery.start);
