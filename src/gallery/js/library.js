// PPAGES ~ www.centerkey.com/ppages ~ Copyright (c) individual contributors
// Rights granted under GNU General Public License ~ ppages/src/gallery/license.txt

//jQuery configuration
$.fx.speeds.xslow = 1000;   //1 second
$.fx.speeds.xxslow = 2000;  //2 seconds
$.fn.exists = function () { return this.length !== 0; }  //selector has an elem

//General string utilities
function contains(haystack, needle) {
   return haystack.indexOf(needle) != -1;
   }
function removeWhitespace(str) {
   return str.replace(/\s/g, '');
   }

//UI utilities
function popupPage(url, width, height) {
   window.open(url, '_blank', 'width=' + width + ',height=' + height +
      ',left=200,location=no,scrollbars=yes,resizable=yes');
   }
function setupLinks() {
   $('a.external-site').each(
      function() { this.title=this.href; this.target='_blank'; });
   $('a img').parent().addClass('plain');
   }
function displayAddr() {
   $('.display-addr').each(function() { $(this).html($(this).data('name') +
      '<span>' + String.fromCharCode(57+7) + $(this).data('level2') +
      '.</span>' + {c:'com', o:'org', n:'net'}[$(this).data('code')]); });
   }
function autoFocus(elem) {
   elem.find('input[type=text]:first').not('.no-auto-focus').focus();
   }
function fadeInMsg(elem, msg) {
   return elem.html(msg).hide().fadeIn();
   }
function rollIn(elems, startDelay, fadeDelay) {
   startDelay = typeof startDelay !== 'undefined' ? startDelay : 'slow';
   fadeDelay = typeof fadeDelay !== 'undefined' ? fadeDelay : 'fast';
   if (elems.eq(0).css('opacity') == 1) {
      elems.fadeTo(0, 0);
      $(window).load(function() { rollIn(elems, startDelay, fadeDelay); });
      }
   else
      elems.eq(0).delay(startDelay).fadeTo(fadeDelay, 1, function() {
         (elems = elems.slice(1)).length && rollIn(elems, 0, fadeDelay); });
   }

//Browser utilities
function getUrlVariables() {
   var vars = [];
   var pairs = location.search.substring(1).split('&');
   for (var count in pairs) {
      var pair = pairs[count].split('=');
      vars[pair[0]] = pair[1];
      }
   return vars;
   }

//Ajax helpers
function toCamelCase(dashStr) {  //ex: 'ready-set-go' ==> 'readySetGo'
   return dashStr.replace(/\-(.)/g, function() {
      return arguments[1].toUpperCase(); });
   }
function toDash(camelCaseStr) {  //ex: 'readySetGo' ==> 'ready-set-go'
   return camelCaseStr.replace(/([A-Z])/g, function() {
      return '-' + arguments[1].toLowerCase(); });
   }
function objDetails(obj) {
   var msg = typeof obj + '->';
   for (var property in obj)
      msg = msg + ' ' + property + ':' + obj[property];
   return msg;
   }

//jQuery dialog boxes
//The button: <button id=button-{NAME} class=action-button>
//opens the box: <div id=dialog-{NAME} class=dialog[-wide|-xwide]>
//Or open without button: <div id=dialog-auto-open class=dialog[-wide|-xwide]>
function showDialog(dilogId, defaultValueId) {
   var dialogElem = $('#' + dilogId);
   if (defaultValueId)
      dialogElem.find('input').val($('#' + defaultValueId).html());
   if (dialogElem.find('.error-msg').length == 0)
      dialogElem.append('<p class=error-msg></p>');
   dialogElem.find('.error-msg').hide();
   dialogElem.dialog('open');
   return dialogElem;
   }
function dialogErrMsg(dilogId, msg) {
   return fadeInMsg($('#' + dilogId + ' .error-msg'), msg);
   }
function hideDialog(dilogId) {
   $('#' + dilogId).dialog('close');
   }
function setupDialogBoxes() {
   $('.action-button').click(function() {
      showDialog($(this).attr('id').replace('button', 'dialog'));
      });
   var dialogOptions = { modal: true, autoOpen: false,
      overlay: { opacity: 0.5, background: "black" } };
   $('.dialog').dialog(dialogOptions);
   dialogOptions["width"] = 400;
   $('.dialog-wide').dialog(dialogOptions).addClass('dialog');
   dialogOptions["width"] = 550;
   $('.dialog-xwide').dialog(dialogOptions).addClass('dialog');
   dialogOptions["width"] = 700;
   $('.dialog-xxwide').dialog(dialogOptions).addClass('dialog');
   $('.dialog p input:text').addClass(function(index, currentClass) {
      return contains(currentClass, 'field') ? '' : 'field-small'; });
   $('.dialog').each(function() {
      if ($('button', $(this)).exists()) {
         $('button', this).wrapAll('<p></p>');
         $('button:last', this).after('<button type=button class=cancel>Cancel</button>');
         }
      else
         $('<p><button type=button class=cancel>Ok</button></p>').appendTo($(this));
      });
   $('.dialog button.cancel').click(function() {
      $(this).closest('.dialog').dialog('close');
      });
   if ($('#dialog-auto-display').exists())
      showDialog('dialog-auto-display');
   }

//Social bookmarking
(function() {
   var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
   po.src = 'https://apis.google.com/js/plusone.js';
   var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
   })();
var socialButtons = {
   google:      { title: 'Google',      x: 680, y: 450, link: 'http://www.google.com/bookmarks/mark?op=edit&amp;output=popup&amp;bkmk=${url}$title=${title}' },
   linkedin:    { title: 'LinkedIn',    x: 580, y: 350, link: 'http://www.linkedin.com/shareArticle?mini=true&url=${url}&title=${title}' },
   stumbleupon: { title: 'StumbleUpon', x: 950, y: 600, link: 'http://www.stumbleupon.com/submit?url=${url}&title=${title}' },
   delicious:   { title: 'Delicious',   x: 550, y: 550, link: 'http://delicious.com/save?noui&amp;url=${url}$title=${title}' },
   digg:        { title: 'Digg',        x: 985, y: 700, link: 'http://digg.com/submit?url=${url}$title=${title}' },
   reddit:      { title: 'Reddit',      x: 600, y: 750, link: 'http://www.reddit.com/submit?url=${url}$title=${title}' }
   };
function openSocialButton(name) {
   var link = socialButtons[name].link.replace('${url}',
      encodeURIComponent(location.href)).replace('${title}', encodeURIComponent(document.title));
   popupPage(link, socialButtons[name].x, socialButtons[name].y);
   }
function displaySocialButtons(elem) {
   elem.hide();
   var html = '<div class=g-plusone></div><span>';
   for (var name in socialButtons)
      html = html + '<a><img src="graphics\/icon-social-' +
         name + '.png" onclick="openSocialButton(\'' + name +
         '\');" title="Bookmark to ' + socialButtons[name].title +
         '" alt="Social bookmark"><\/a>';
   elem.html(html + '</span>').delay('slow').fadeIn();
   }
