/////////////////////////////////////////////////
// PPAGES ~ centerkey.com/ppages               //
// GPL ~ Copyright (c) individual contributors //
/////////////////////////////////////////////////

// Application

if (!gmc)
   var gmc = {};

gmc.start = {
   go: function() {
      library.social.setup();
      library.ui.setup();
      library.form.setup();
      library.bubbleHelp.setup();
      gmc.ui.setupLinks();
      gmc.ui.setupActions();
      gmc.ui.setupStatusMsg();
      gmc.ui.autoFocus($('.login'));
      gmc.ui.configureMenuBarButtons();
      }
   };

$(gmc.start.go);
