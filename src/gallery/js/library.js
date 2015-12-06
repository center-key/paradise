/////////////////////////////////////////////////
// PPAGES ~ centerkey.com/ppages               //
// GPL ~ Copyright (c) individual contributors //
/////////////////////////////////////////////////

// Library

var library = {};

library.ui = {
   popup: function(url, options) {
      var settings = { width: 500, height: 300 };
      $.extend(settings, options);
      window.open(url, '_blank', 'width=' + settings.width + ',height=' +
         settings.height + ',left=200,location=no,scrollbars=yes,resizable=yes');
      },
   setup: function() {
      $('a.external-site').attr('target', '_blank');
      $('a img').parent().addClass('plain');
      }
   };

library.form = {
   setup: function() {
      $('form.feedback').attr('action', 'feedback.php');  //bots are lazy
      }
   };

// Social bookmarking
library.social = {
   // Usage:
   //    <div id=social-buttons></div>
   buttons: {
      twitter:     { title: 'Twitter',     x: 580, y: 350, link: 'http://twitter.com/share?text=${title}&url=${url}' },
      facebook:    { title: 'Facebook',    x: 580, y: 350, link: 'http://www.facebook.com/sharer.php?u=${url}' },
      linkedin:    { title: 'LinkedIn',    x: 580, y: 350, link: 'http://www.linkedin.com/shareArticle?mini=true&url=${url}&title=${title}' },
      stumbleupon: { title: 'StumbleUpon', x: 950, y: 600, link: 'http://www.stumbleupon.com/submit?url=${url}&title=${title}' },
      delicious:   { title: 'Delicious',   x: 550, y: 550, link: 'http://delicious.com/save?noui&amp;url=${url}$title=${title}' },
      digg:        { title: 'Digg',        x: 985, y: 700, link: 'http://digg.com/submit?url=${url}' },
      reddit:      { title: 'Reddit',      x: 600, y: 750, link: 'http://www.reddit.com/submit?url=${url}$title=${title}' }
      },
   share: function() {
      var button = library.social.buttons[$(this).data().social];
      var link = button.link.replace('${url}',
         encodeURIComponent(location.href)).replace('${title}', encodeURIComponent(document.title));
      library.ui.popup(link, { width: button.x, height: button.y });
      },
   setup: function() {
      $.getScript('https://apis.google.com/js/platform.js');
      var elem = $('#social-buttons');
      function initialize() {
         var buttons = library.social.buttons;
         elem.fadeTo(0, 0.0);
         var html = '<div class=g-plusone data-annotation=none></div><span>';
         function addButton(key) {
            html += '<img src="graphics/icon-social-' + key + '.png" data-social=' + key +
               ' class=social-button title="Share to ' + buttons[key].title +
               '" alt="Social bookmark">';
            }
         Object.keys(buttons).forEach(addButton);
         elem.html(html + '</span>').delay('slow').fadeTo('normal', 1.0);
         $('.social-button').click(library.social.share);
         }
      if (elem.length)
         initialize();
      }
   };
