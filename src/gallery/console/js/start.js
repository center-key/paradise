/////////////////////////////////////////////////
// PPAGES ~ www.centerkey.com/ppages           //
// GPL ~ Copyright (c) individual contributors //
/////////////////////////////////////////////////

// Application

$(function() {
   library.social.setup();
   library.ui.setup();
   library.form.setup();
   library.bubbleHelp.setup();
   setupLinks();               //replace with new style
   setupActions();             //replace with new style
   setupStatusMsg();           //replace with new style
   autoFocus($('.login'));     //replace with new style
   configureMenuBarButtons();  //replace with new style
   });
