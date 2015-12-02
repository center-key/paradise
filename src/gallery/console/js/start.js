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
      gmc.ui.setupActions();
      gmc.ui.setupStatusMsg();
      gmc.ui.configureMenuBarButtons();
      }
   };

$(gmc.start.go);
