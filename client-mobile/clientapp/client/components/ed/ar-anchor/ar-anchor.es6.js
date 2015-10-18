( function( polymer, document ) {
  "use strict";
  var router = null;

  polymer( "ar-anchor", {
    publish: {
      route: {
        value: "",
        reflect: true
      }
    },
    /* LIFECYCLE */
//    ready: function() {},
    attached: function() {
      if ( router == null ) {
        router = document.getElementById( "root-app-router" );
      }
    },
//    detached: function() {},
    routeHandler: function( event ) {
      var options = {
        replace: this.hasAttribute( "replace" )
      };

      if ( router == null ) {
        window.setTimeout(() => {
          router = document.getElementById( "root-app-router" );
          this.routeHandler( event );
        }, 100);
      }

      if ( this.route ) {
        router.go( this.route, options );
      }
    }
  });

})( window.Polymer, document );
