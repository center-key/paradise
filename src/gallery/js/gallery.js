///////////////////////////////////////////////////////////////
// Paradise ~ centerkey.com/paradise                         //
// GPLv3 ~ Copyright (c) individual contributors to Paradise //
///////////////////////////////////////////////////////////////

// Gallery application
const gallery = {
   start() {
      $('body >footer .hide-me').remove();
      $('form.send-message').attr({ method: 'post', action: 'server/send-message.php' });  //bots are lazy
      const options = {
         delegate: '>a',  //child items selector, click to open popup
         type:     'image',
         image:    { titleSrc: 'data-title' },
         gallery:  { enabled: true },
         };
      $('.gallery-images figure').magnificPopup(options);
      },
   };

$(gallery.start);
