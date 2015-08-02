/////////////////////////////////////////////////
// PPAGES ~ centerkey.com/ppages               //
// GPL ~ Copyright (c) individual contributors //
/////////////////////////////////////////////////

// Gallery Management Console

if (!gmc)
   var gmc = {};

gmc.error = {
   codeAuthFail: 100
   };

gmc.ui = {
   menuBarPages: ['gallery', 'artist', 'contact'],  //temporary solution
   menuBarButtons: { up: '&uarr;', down: '&darr;', show: 'Show', hide: 'Hide',
      edit: 'Edit', del: '&times;'},
   confirmMenuBarAction: function(form, title) {
      var action = $('select[name=menu-bar-action]', form).val();
      return action !== 'del' ||
         confirm('You are about to delete the &quot;' + title + '&quot; page.  Continue?');
      },
   slowDimOut: function(elem) {
      var delayVerySlow = 8000;
      elem.stop().fadeTo('fast', 1.0).fadeTo(delayVerySlow, 0.4);
      },
   statusMsg: function(msg) {
      this.slowDimOut($('#status-msg').html(msg));
      },
   setupStatusMsg: function() {
      var msg = '' + $('#status-msg').html();
      if (msg.length === 0)
         msg = 'Ready';
      this.statusMsg(msg);
      },
   webServiceUrl: function(params) {
      var baseUrl = 'service';
      var url = baseUrl;
      function buildUrl(param) {
         if (params[param] !== undefined)
            url += (url === baseUrl ? '?' : '&') +
               param + '=' + encodeURIComponent(params[param]);
         }
      params.forEach(buildUrl);
      return url;
      },
   processServiceResponce: function(data, successMsg) {
      if (data.error === gmc.error.codeAuthFail)
         window.location.reload();
      this.statusMsg(data.error ? 'Error: ' + data.message : successMsg);
      return !data.error;
      },
   updatePortfolio: function(id, field, value) {
      var params = {
         action: 'portfolio',
         id:     id,
         field:  field,
         value:  value
         };
      $.getJSON(this.webServiceUrl(params), function(data) {
         this.processServiceResponce(data, 'Image &quot;' + data.caption + '&quot; updated');
         });
      },
   updateSettingsWebsite: function(field, value) {
      var params = {
         action: 'settings',
         field:  field,
         value:  value
         };
      console.log('f:' + field, 'v:' + value, 'url:' + this.webServiceUrl(params));
      $.getJSON(this.webServiceUrl(params), function(data) {
         this.processServiceResponce(data, 'Gallery settings updated');
         });
      },
   menuBarActionAllowed: function(action, page, loc, len, show) {
      return (action === 'up'   && (loc > 1)) ||
         (action === 'down' && (loc !== len - 1 && page !== 'gallery')) ||
         (action === 'show' && (!show)) ||
         (action === 'hide' && (show && page !== 'gallery')) ||
         (action === 'edit' && (page !== 'gallery' && page !== 'contact')) ||
         (action === 'del'  && (page !== 'gallery' && page !== 'contact'));
      },
   configureMenuBarButtonsSinglePage: function(data, loc) {
      var buttons = $('#settings-menu-bar buttons');
      function cfg(action) {
         var allowed = this.menuBarActionAllowed(action,
            this.menuBarPages[loc], loc, 3, data[loc].show);
         buttons.find('.' + action + '[name=' + this.menuBarPages[loc] + ']')
            .attr('disabled', allowed);
         }
      if (buttons.length)
         this.menuBarButtons.forEach(cfg);
      },
   configureMenuBarButtons: function() {
      var params = {
         action: 'menu-bar'
         };
      $.getJSON(this.webServiceUrl(params), function(data) {
         this.menuBarPages.forEach(function(page, loc) {
            this.configureMenuBarButtonsSinglePage(data, loc);
            });
         });
      },
   updateSettingsMenuBar: function(page, task, value) {
      var params = {
         action: 'menu-bar',
         page:   page,
         task:   task,
         value:  value
         };
      console.log('p:' + page, 't:' + task, 'v:' + value,
         'url:' + this.webServiceUrl(params));
      $.getJSON(this.webServiceUrl(params), function(data) {
         this.processServiceResponce(data, 'Gallery menu bar updated');
         var loc = this.menuBarPages.indexOf(page);
         this.configureMenuBarButtonsSinglePage(data, loc);
         });
      },
   setupActions: function() {
      $('.login input').keyup(function(event) {
         if(event.keyCode === 13)
           $('.login button').click();
         });
      $('#create-account').click(function() { this.doLogin(true); });
      $('#do-login').click(function() { this.doLogin(false); });
      $('.portfolio-display').change(function() {
         this.updatePortfolio($(this).attr('name'), 'display', $(this).is(':checked'));
         });
      $('.portfolio-caption').blur(function() {
         this.updatePortfolio($(this).attr('name'), 'caption', $(this).val());
         });
      $('.portfolio-description').blur(function() {
         this.updatePortfolio($(this).attr('name'), 'description', $(this).val());
         });
      $('.portfolio-badge').blur(function() {
         this.updatePortfolio($(this).attr('name'), 'badge', $(this).val());
         });
      $('.portfolio-move-up').blur(function() {
         this.updatePortfolio($(this).attr('name'), 'move', 'up');
         });
      $('.portfolio-move-down').blur(function() {
         this.updatePortfolio($(this).attr('name'), 'move', 'down');
         });
      $('.portfolio-delete').blur(function() {
         this.updatePortfolio($(this).attr('name'), 'command', 'delete');
         });
      $('#settings-website input[type=text]').blur(function() {
         this.updateSettingsWebsite($(this).attr('name'), $(this).val());
         });
      $('#settings-website select').change(function() {
         this.updateSettingsWebsite($(this).attr('name'),
            $(this).find('option:selected').text());
         });
      $('#settings-website input[type=checkbox]').change(function() {
         this.updateSettingsWebsite($(this).attr('name'), $(this).is(':checked'));
         });
      $('#settings-menu-bar input[type=text]').blur(function() {
         this.updateSettingsMenuBar($(this).attr('name'), 'save', $(this).val());
         });
      $('#settings-menu-bar button').click(function() {
         this.updateSettingsMenuBar($(this).attr('name'), $(this).attr('class'));
         });
      $('#change-password').click(function() { this.changePassword(); });
      $('#create-account').click(function() {
         this.confirmCreateAccount($('#username').value);
         });
      }
   };
