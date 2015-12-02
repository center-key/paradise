/////////////////////////////////////////////////
// PPAGES ~ centerkey.com/ppages               //
// GPL ~ Copyright (c) individual contributors //
/////////////////////////////////////////////////

// Application

'use strict';

if (!app)
   var app = {};

app.start = {
   go: function() {
      library.social.setup();
      library.ui.setup();
      library.form.setup();
      }
   };

$(app.start.go);
