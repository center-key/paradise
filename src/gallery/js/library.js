/////////////////////////////////////////////////
// PPAGES ~ www.centerkey.com/ppages           //
// GPL ~ Copyright (c) individual contributors //
/////////////////////////////////////////////////

// Library

'use strict';

var library = {};

library.util = {
   safeValue: function(value) {
      return value === undefined ? null : value;
      },
   contains: function(haystack, needle) {
      return haystack.indexOf(needle) != -1;
      },
   removeWhitespace: function(str) {
      return str.replace(/\s/g, '');
      },
   toCamelCase: function(dashStr) {  //ex: 'ready-set-go' ==> 'readySetGo'
      return dashStr.replace(/\-(.)/g, function() {
         return arguments[1].toUpperCase(); });
      },
   toDash: function(camelCaseStr) {  //ex: 'readySetGo' ==> 'ready-set-go'
      return camelCaseStr.replace(/([A-Z])/g, function() {
         return '-' + arguments[1].toLowerCase(); });
      },
   copyTemplate: function(templateId, holderElem) {
      // Usage:
      //    1) <div><p id=color-template><p>red</p></div>
      //    2) library.util.copyTemplate('color-template').text('aqua');
      //    3) <div><p id=color-template><p>red</p><p>aqua</p></div>
      var template = $('#' + templateId);
      if (!holderElem)
         holderElem = template.parent();
      return template.clone(true, true).removeAttr('id').appendTo(holderElem);
      },
   details: function(x) {
      var msg = typeof x + ' --> ';
      if (x && x.jquery)
         msg = msg + 'jquery:' + x.jquery + ' elems:' + x.length +
            (x.length == 0 ? '' : ' [#1' +
            ' elem:' +  x.first()[0].nodeName +
            ' id:' +    x.first().attr('id') +
            ' class:' + x.first().attr('class') +
            ' kids:' +  x.first().children().length + ']');
      else if (x == null)
         msg = msg + '[null]';
      else if (typeof x == 'object')
         for (var property in x)
            msg = msg + property + ':' + x[property] + ' ';
      else
         msg = msg + x;
      return msg;
      },
   callMeMaybe: function(objName, funcToCall) {
      // Calls supplied method (funcToCall) once designated object (exists)
      // Example: library.util.callMeMaybe('app', 'app.setup')
      if (window[objName])
         funcToCall();
      else
         window.setTimeout(function() { library.util.callMeMaybe(objName,
            funcToCall); }, 100);
      },
   stopPropagation: function(event) {
      if (event.stopPropagation)  //Modern browsers
          event.stopPropagation();
      else                        //IE
         window.event.cancelBubble = true;
      }
   };

library.db = {
   save: function(key, obj) {
      return localStorage[key] = JSON.stringify(obj);
      },
   read: function(key) {
      return JSON.parse(library.util.safeValue(localStorage[key]));
      }
   };

library.session = {
   save: function(key, value) {
      return sessionStorage[key] = JSON.stringify(value);
      },
   read: function(key) {
      return JSON.parse(library.util.safeValue(sessionStorage[key]));
      }
   };

library.counters = {
   store: 'counters',
   list: function() {
      var c = sessionStorage[library.counters.store];
      return c ? JSON.parse(c) : {};
      },
   get: function(name) {
      var c = library.counters.list();
      return c[name] ? c[name] : 0;
      },
   set: function(name, count) {
      var c = library.counters.list();
      c[name] = count;
      sessionStorage[library.counters.store] = JSON.stringify(c);
      return c[name];
      },
   reset: function(name) {
      return library.counters.set(name, 0);
      },
   increment: function(name) {
      return library.counters.set(name, library.counters.get(name)+1);
      }
   };

library.browser = {
   getUrlVariables: function() {
      // Example:
      //    http://example.com?lang=jp&code=7  ==>  { lang: 'jp', code: 7 }
      var vars = {};
      var pairs = location.search.substring(1).split('&');
      for (var count in pairs) {
         var pair = pairs[count].split('=');
         vars[pair[0]] = pair[1];
         }
      return vars;
      },
   isMac: function() {
      return navigator.platform.toLowerCase().indexOf('mac') >= 0;
      }
   };

library.ui = {
   safe: function(string) {
      return string ? string : '';
      },
   refresh: function(url) {
      if (url)
         window.location = url;
      else
         window.location.reload(true);
      },
   popup: function(url, options) {
      var settings = { width: 500, height: 300 };
      $.extend(settings, options);
      window.open(url, '_blank', 'width=' + settings.width + ',height=' +
         settings.height + ',left=200,location=no,scrollbars=yes,resizable=yes');
      },
   liveClick: function(selector, callback) {
      $(document).on('click', selector, callback);
      },
   displayAddr: function() {
      // Usage:
      //    <p class=display-addr data-name=sales data-level2=coterie data-code=c></p>
      $('.display-addr').each(function() { $(this).html($(this).data('name') +
         '<span>' + String.fromCharCode(57+7) + $(this).data('level2') +
         '.</span>' + {c:'com', o:'org', n:'net'}[$(this).data('code')]); });
      },
   jumpClick: function(event) {
      // Usage:
      //    <button class=jump-click data-url="/">Home</button>
      window.location = $(event.target).data('url');
      },
   popupClick: function(event) {
      // Usage (see popup() for default width and height):
      //    <button class=popup-click data-url="/" data-width=300>Home</button>
      var elem = $(event.target);
      var options = { width: elem.data('width'),  height: elem.data('height') };
      library.ui.popup(elem.data('url'), options);
      },
   revealSection: function(event) {
      // Usage:
      //    <div class=reveal-action data-reveal=more>More...</div>
      //    <div class=reveal-target data-reveal=more>Surprise!</div>
      $('.reveal-target[data-reveal=' + $(event.target).hide().data('reveal') + ']').slideToggle();
      },
   keepOnScreen: function(elem, px) {  //must be position: absolute with top/left
      var gap = elem.offset().left;
      var moveR = Math.max(-gap, -px) + px;
      var moveL = Math.max(gap + elem.width() - $(window).width(), -px) + px;
      return elem.css({ left: '+=' + (moveR - moveL) + 'px' });
      },
   setup: function() {
      $('a.external-site').attr('target', '_blank');
      $('a img').parent().addClass('plain');
      library.ui.liveClick('.jump-click',    library.ui.jumpClick);
      library.ui.liveClick('.popup-click',   library.ui.popupClick);
      library.ui.liveClick('.reveal-action', library.ui.revealSection);
      library.ui.liveClick('.popup-image',   library.popupImage.show);
      this.displayAddr();
      }
   };

library.infoRollover = {
   // Usage:
   //    <div class=info-rollover-area>
   //       Main content
   //       <div class=info-rollover-msg>Flyover content</div>
   //    </div>
   getElem: function(event) {
      return $(event.target).closest('.info-rollover-area')
         .find('.info-rollover-msg');
      },
   showMsg: function(event) {
      library.infoRollover.getElem(event).fadeIn();
      },
   hideMsg: function(event) {
      library.infoRollover.getElem(event).fadeOut();
      },
   setup: function() {
      $('.info-rollover-area').hoverIntent({
         over:        library.infoRollover.showMsg,
         out:         library.infoRollover.hideMsg,
         sensitivity: 30
         });
      }
   };

library.popupImage = {
   // Usage:
   //    <img src="thumb.png" class=popup-image data-url="full.jpg">
   closeImage: 'http://www.centerkey.com/graphics/icon-close-x.png',
   close: function(event) {
      $(event.target).closest('.popup-image-layer').fadeOut();
      },
   show: function(event) {
      var thumb = $(event.target);
      var layer = thumb.parent().css({ position: 'relative' }).find('.popup-image-layer');
      if (!layer.length) {
         var closeIcon =  $('<img>')
            .attr('src', library.popupImage.closeImage)
            .addClass('popup-image-close').click(library.popupImage.close);
         layer = $('<div>').addClass('popup-image-layer')
            .append(closeIcon)
            .append($('<img>').attr('src', thumb.data('url')));
         thumb.after(layer);
         layer.css({
            left: (layer.width() / -5) + 'px',
            top: (layer.height() / -5) + 'px'
            });
         library.ui.keepOnScreen(layer, 20);
         }
      layer.fadeIn();
      }
   };

library.animate = {
   rollIn: function(elems, options) {
      var settings = { startDelay: 'slow', fadeDelay: 'fast' };
      $.extend(settings, options);
      if (elems.eq(0).css('opacity') == 1) {
         elems.fadeTo(0, 0);
         $(window).load(function() { library.animate.rollIn(elems, settings); });
         }
      else {
         elems.eq(0).delay(settings.startDelay).fadeTo(settings.fadeDelay, 1,
            function() { (elems = elems.slice(1)).length && library.animate.rollIn(
               elems, { startDelay: 0, fadeDelay: settings.fadeDelay }) });
         }
      }
   };

library.bubbleHelp = {
   // Usage:
   //    <div>Hover over me<div class=bubble-help>Help!</div></div>
   elem: null,
   hi: function(event) {
      var hoverElem = $(event.target).closest('.bubble-help-hover');
      library.bubbleHelp.elem = hoverElem.find('.bubble-wrap');
      if (library.bubbleHelp.elem.length == 0)
         library.bubbleHelp.elem = hoverElem.find('.bubble-help')
            .wrap('<div class=bubble-wrap></div>')
            .parent().append('<div>&#9660;</div>');
      library.bubbleHelp.elem.find('.bubble-help').show();
      library.bubbleHelp.elem.css({ top: -library.bubbleHelp.elem.height() })
         .hide().fadeIn();
      },
   bye: function() {
      library.bubbleHelp.elem.stop(true).fadeOut('slow');
      },
   setup: function() {
      $('.bubble-help').parent().addClass('bubble-help-hover')
         .hover(this.hi, this.bye);
      }
   };

library.form = {
   setup: function() {
      $('.perfect').closest('form').attr('action', 'feedback.php');  //deprecated version
      $('.perfect').attr('action', 'feedback.php');  //bots are lazy
      }
   };

library.kaleidoscopic = {
   // Usage:
   //    <ul class=kaleidoscopic-menu><li>See X1</li><li>See X2</li></ul>
   //    <div class=kaleidoscopic-panel><div>X1</div><div>X2</div></div>
   //    .kaleidoscopic-menu li          { opacity: 0.6; }
   //    .kaleidoscopic-menu li.selected { opacity: 1.0; }
   rotate: function(event) {
      var loc = event ? $(event.target).closest('li').index() : 0;
      $('.kaleidoscopic-menu li').removeClass('selected').eq(loc).addClass('selected');
      $('.kaleidoscopic-panels >div').hide().eq(loc).fadeIn();
      },
   setup: function() {
      this.rotate();
      library.ui.liveClick('.kaleidoscopic-menu li', this.rotate);
      }
   };

library.json = {
   replacer: function(match, pIndent, pKey, pVal, pEnd) {
      var key = '<span class=json-key>';
      var val = '<span class=json-value>';
      var str = '<span class=json-string>';
      var r = pIndent || '';
      if (pKey)
         r = r + key + pKey.replace(/[": ]/g, '') + '</span>: ';
      if (pVal)
         r = r + (pVal[0] == '"' ? str : val) + pVal + '</span>';
      return r + (pEnd || '');
      },
   prettyPrint: function(obj) {
      var jsonLine = /^( *)("[\w]+": )?("[^"]*"|[\w.+-]*)?([,[{])?$/mg;
      return JSON.stringify(obj, null, 3)
         .replace(/&/g, '&amp;').replace(/\\"/g, '&quot;')
         .replace(/</g, '&lt;').replace(/>/g, '&gt;')
         .replace(jsonLine, library.json.replacer);
      }
   };

//Social bookmarking
(function() {
   var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
   po.src = 'https://apis.google.com/js/plusone.js';
   var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
   })();
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
      var button = library.social.buttons[$(event.target).data('social')];
      var link = button.link.replace('${url}',
         encodeURIComponent(location.href)).replace('${title}', encodeURIComponent(document.title));
      library.ui.popup(link, { width: button.x, height: button.y });
      },
   setup: function() {
      var elem = $('#social-buttons');
      if (elem.length) {
         elem.fadeTo(0, 0.0);
         var html = '<div class=g-plusone></div><span>';
         for (var name in this.buttons)
            html = html + '<img src="http://www.centerkey.com/graphics\/icon-social-' +
               name + '.png" data-social=' + name +
               ' class=social-button title="Share to ' + this.buttons[name].title +
               '" alt="Social bookmark">';
         elem.html(html + '</span>').delay('slow').fadeTo('normal', 1.0);
         $('.social-button').click(library.social.share);
         }
      }
   };

//Debug message to console
function debug(thing) { console.log(new Date().getTime() + ': ' +
   library.util.details(thing)); }
