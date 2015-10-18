( function( polymer ) {
  "use strict";

  polymer( "ed-menu-button", {
    /* LIFECYCLE */
    ready: function() {
      this.edMenu = document.getElementById( "side-menu" );
    },
    // attached: function() {},
    // detached: function() {},
    // attributeChanged: function( attrName, oldValue, newValue ) {}
    triggerMenu: function() {
      this.edMenu.classList.toggle( "show-menu" );
    }
    /* PROPERTIES */
    /* METHODS */
  });

})( window.Polymer );
