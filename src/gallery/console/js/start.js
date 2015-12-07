/////////////////////////////////////////////////
// PPAGES ~ centerkey.com/ppages               //
// GPL ~ Copyright (c) individual contributors //
/////////////////////////////////////////////////

// Application

if (!gmc)
   var gmc = {};

gmc.start = {
   go: function() {
      $('a.external-site').attr('target', '_blank');
      $('a img, a i.fa').parent().addClass('plain');
      gmc.ui.setupActions();
      gmc.ui.setupStatusMsg();
      gmc.ui.configureMenuBarButtons();
      }
   };

$(gmc.start.go);
