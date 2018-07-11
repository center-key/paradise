///////////////////////////////////////////////////////////////
// Paradise ~ centerkey.com/paradise                         //
// GPLv3 ~ Copyright (c) individual contributors to Paradise //
///////////////////////////////////////////////////////////////

// Application
var app = {};

// Social bookmarking
app.social = {
   // Usage:
   //    <div id=social-buttons></div>
   buttons: {
      twitter:  { title: 'Twitter',  x: 580, y: 350, link: 'https://twitter.com/share?text=${title}&url=${url}' },
      facebook: { title: 'Facebook', x: 580, y: 350, link: 'https://www.facebook.com/sharer.php?u=${url}' },
      linkedin: { title: 'LinkedIn', x: 580, y: 350, link: 'https://www.linkedin.com/shareArticle?mini=true&url=${url}&title=${title}' },
      digg:     { title: 'Digg',     x: 985, y: 700, link: 'https://digg.com/submit?url=${url}' },
      reddit:   { title: 'Reddit',   x: 600, y: 750, link: 'https://www.reddit.com/submit?url=${url}$title=${title}' }
      },
   popup: function(url, options) {
      var settings = { width: 500, height: 300 };
      $.extend(settings, options);
      window.open(url, '_blank', 'width=' + settings.width + ',height=' +
         settings.height + ',left=200,location=no,scrollbars=yes,resizable=yes');
      },
   share: function() {
      var button = app.social.buttons[$(this).data().social];
      function insert(str, find, value) { return str.replace(find, encodeURIComponent(value)); }
      var link = insert(button.link, '${url}', window.location.href);
      link = insert(link, '${title}', window.document.title);
      app.social.popup(link, { width: button.x, height: button.y });
      },
   setup: function() {
      $.getScript('https://apis.google.com/js/platform.js');
      var elem = $('#social-buttons');
      function initialize() {
         var buttons = app.social.buttons;
         elem.fadeTo(0, 0.0);
         var html = '<div class=g-plusone data-annotation=none></div><span>';
         function addButton(key) {
            html += '<i class="fa fa-' + key + '" data-social=' + key +
               ' data-click=app.social.share></i>';
            }
         Object.keys(buttons).forEach(addButton);
         elem.html(html + '</span>').fadeTo('slow', 1.0);
         }
      if (elem.length)
         initialize();
      }
   };

app.start = {
   go: function() {
      var iOS = /iPad|iPhone|iPod/.test(window.navigator.userAgent) &&
         /Apple/.test(window.navigator.vendor);
      app.social.setup();
      function makeIcon(i, el) { $(el).addClass('fa fa-' + $(el).data().icon); }
      $('i[data-icon]').each(makeIcon);
      $('a img').parent().addClass('plain');
      $('input[type=email]').attr({ spellcheck: false, autocorrect: 'off' });
      $('form.send-message').attr({ method: 'post', action: 'send-message.php' });  //bots are lazy
      if (!iOS)
         $('a.external-site, .external-site a').attr({ target: '_blank' });
      var options = {
         delegate: '>a',  //child items selector, click to open popup
         type:     'image',
         image:    { titleSrc: 'data-title' },
         gallery:  { enabled: true }
         };
      $('.gallery-images .image').magnificPopup(options);
      }
   };

$(app.start.go);
