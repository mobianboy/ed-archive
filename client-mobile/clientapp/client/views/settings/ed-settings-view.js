(function( polymer ) {
  "use strict";
  Promise.all([
    System.import( "domain/ed/services/ed-user-service" )
  ]).then(function( imported ) {
    var
      userService = imported[ 0 ].default;

    polymer( "ed-settings-view", {
      // ready: function() {},
      // attached: function() {},
      // detached: function() {},
      redirectInAppHandler: function( event ) {
        var
          url,
          cordova = window.cordova;

        switch ( event.path[ 0 ].id ) {
          case "Terms-button":
            url = "http://www.eardish.com/terms";
            break;
          case "About-button":
            url = "http://www.eardish.com/privacy";
            break;
          case "Support-button":
            url = "http://www.eardish.com/support";
            break;
          default:
            // do nothing
            return;
        }

        if ( cordova && cordova.InAppBrowser && typeof cordova.InAppBrowser.open === "function" ) {
          cordova.InAppBrowser.open( url, "_system" );
        } else {
          window.open( url, "_blank" );
        }
      },
      logOutTriggerHandler: function() {
        userService.logout();
        this.router.go( "/login" );
        return false;
      }
    });
  });
})( window.Polymer );
