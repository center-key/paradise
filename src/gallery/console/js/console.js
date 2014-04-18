// PPAGES ~ www.centerkey.com/ppages ~ Copyright (c) individual contributors
// Rights granted under GNU General Public License ~ ppages/src/gallery/license.txt

var errorCodeAuthFail = 100;

function confirmMenuBarAction(form, title) {
   var action = $('select[name=menu-bar-action]', form).val();
   return action != 'del' ||
      confirm('You are about to delete the &quot;' + title + '&quot; page.  Continue?');
   }

function slowDimOut(elem) {
   var delayVerySlow = 8000;
   elem.stop().fadeTo('fast', 1.0).fadeTo(delayVerySlow, 0.4);
   }

function statusMsg(msg) {
   slowDimOut($('#status-msg').html(msg));
   }

function setupStatusMsg() {
   var msg = '' + $('#status-msg').html();
   if (msg.length == 0)
      msg = 'Ready';
   statusMsg(msg);
   }

function webServiceUrl(params) {
   var baseUrl = 'service';
   var url = baseUrl;
   for (var param in params)
      if (params[param] != undefined)
         url += (url == baseUrl ? '?' : '&') +
            param + '=' + encodeURIComponent(params[param]);
   return url;
   }

function processServiceResponce(data, successMsg) {
   if (data.error == errorCodeAuthFail)
      window.location.reload();
   statusMsg(data.error ? 'Error: ' + data.message : successMsg);
   return !data.error
   }

function updatePortfolio(id, field, value) {
   var params = {
      action: 'portfolio',
      id:     id,
      field:  field,
      value:  value
      };
   $.getJSON(webServiceUrl(params), function(data) {
      processServiceResponce(data, 'Image &quot;' + data.caption + '&quot; updated');
      });
   }

function updateSettingsWebsite(field, value) {
   var params = {
      action: 'settings',
      field:  field,
      value:  value
      };
   //alert('f:' + field + ' v:' + value + ' url:' + webServiceUrl(params));
   $.getJSON(webServiceUrl(params), function(data) {
      processServiceResponce(data, 'Gallery settings updated');
      });
   }

var menuBarPages = ['gallery', 'artist', 'contact'];  //temporary solution
var menuBarButtons = { up: '&uarr;', down: '&darr;', show: 'Show', hide: 'Hide',
   edit: 'Edit', del: '&times;'};

function menuBarActionAllowed(action, page, loc, len, show) {
   return (action == 'up'   && (loc > 1)) ||
      (action == 'down' && (loc != len - 1 && page != 'gallery')) ||
      (action == 'show' && (!show)) ||
      (action == 'hide' && (show && page != 'gallery')) ||
      (action == 'edit' && (page != 'gallery' && page != 'contact')) ||
      (action == 'del'  && (page != 'gallery' && page != 'contact'));
   }

function configureMenuBarButtonsSinglePage(data, loc) {
   for (var action in menuBarButtons)
      $('#settings-menu-bar button.' + action + '[name=' + menuBarPages[loc] + ']')
         .attr('disabled',
            !menuBarActionAllowed(action, menuBarPages[loc], loc, 3, data[loc].show));
   }

function configureMenuBarButtons() {
   var params = {
      action: 'menu-bar'
      };
   $.getJSON(webServiceUrl(params), function(data) {
      for (var loc in menuBarPages)
         configureMenuBarButtonsSinglePage(data, loc);
      });
   }

function updateSettingsMenuBar(page, task, value) {
   var params = {
      action: 'menu-bar',
      page:   page,
      task:   task,
      value:  value
      };
   //alert('p:' + page + ' t:' + task + ' v:' + value + ' url:' + webServiceUrl(params));
   $.getJSON(webServiceUrl(params), function(data) {
      processServiceResponce(data, 'Gallery menu bar updated');
      var loc = menuBarPages.indexOf(page);
         configureMenuBarButtonsSinglePage(data, loc);
      });
   }

function setupActions() {
   $('.login input').keyup(function(event) {
      if(event.keyCode == 13)
        $('.login button').click();
      });
   $('#create-account').click(function() { doLogin(true); });
   $('#do-login').click(function() { doLogin(false); });
   $('.portfolio-display').change(function() {
      updatePortfolio($(this).attr('name'), 'display', $(this).is(':checked'));
      });
   $('.portfolio-caption').blur(function() {
      updatePortfolio($(this).attr('name'), 'caption', $(this).val());
      });
   $('.portfolio-description').blur(function() {
      updatePortfolio($(this).attr('name'), 'description', $(this).val());
      });
   $('.portfolio-badge').blur(function() {
      updatePortfolio($(this).attr('name'), 'badge', $(this).val());
      });
   $('.portfolio-move-up').blur(function() {
      updatePortfolio($(this).attr('name'), 'move', 'up');
      });
   $('.portfolio-move-down').blur(function() {
      updatePortfolio($(this).attr('name'), 'move', 'down');
      });
   $('.portfolio-delete').blur(function() {
      updatePortfolio($(this).attr('name'), 'command', 'delete');
      });
   $('#settings-website input[type=text]').blur(function() {
      updateSettingsWebsite($(this).attr('name'), $(this).val());
      });
   $('#settings-website select').change(function() {
      updateSettingsWebsite($(this).attr('name'), $(this).find('option:selected').text());
      });
   $('#settings-website input[type=checkbox]').change(function() {
      updateSettingsWebsite($(this).attr('name'), $(this).is(':checked'));
      });

   $('#settings-menu-bar input[type=text]').blur(function() {
      updateSettingsMenuBar($(this).attr('name'), 'save', $(this).val());
      });
   $('#settings-menu-bar button').click(function() {
      updateSettingsMenuBar($(this).attr('name'), $(this).attr('class'));
      });

   $('#change-password').click(function() { changePassword(); });
   $('#create-account').click(function() {
      confirmCreateAccount($('#username').value);
      });
   }
