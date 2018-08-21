///////////////////////////////////////////////////////////////
// Paradise ~ centerkey.com/paradise                         //
// GPLv3 ~ Copyright (c) individual contributors to Paradise //
///////////////////////////////////////////////////////////////

// Gallery application
var gallery = {
   start: function() {
      $('body >footer .hide-me').remove();
      $('form.send-message').attr({ method: 'post', action: 'send-message.php' });  //bots are lazy
      var options = {
         delegate: '>a',  //child items selector, click to open popup
         type:     'image',
         image:    { titleSrc: 'data-title' },
         gallery:  { enabled: true }
         };
      $('.gallery-images .image').magnificPopup(options);
      }
   };

$(gallery.start);
