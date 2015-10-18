( function( System ) {
  "use strict";

  /*
   * Do any weird things to tweak globals and such
   * so tests work in Karma
   */

  // Tweak base url for System usage to karma'd url
  System.baseURL += "base/";
})( window.System );
