( function( polymer, System ) {
  "use strict";

  Promise.all([
    System.import( "domain/ed/services/ed-discover-service" ),
    System.import( "domain/ed/services/ed-player-service" )
  ]).then(function( imported ) {
      var
        discoverService = imported[ 0 ].default,
        playerService = imported[ 1 ].default,
        getGenreIdFromPath = function( pathArray ) {
          return pathArray.reduce(function( prev, curr ) {
            if ( prev != null ) {
              return prev;
            }

            if ( curr && curr.hasAttribute( "data-id" ) ) {
              return curr.getAttribute( "data-id" );
            }

            return null;
          }, null );
        };

      polymer( "ed-discover-view", {
        ready: function() {
          this.discoverList = this.shadowRoot.querySelector( ".discover-list" );
          this.songCard = document.getElementById( "song-card" );
        },
        // attached: function() {},
        // detached: function() {},
        discoverGenreHandler: function( event ) {
          var tmpId = getGenreIdFromPath( event.path );

          if ( tmpId !== "profileBlend" ) {
            tmpId = parseInt( tmpId, 10 );
          }

          if ( tmpId != null ) {
            playerService.startMusicDiscovery( tmpId );
            this.songCard.open();
          }
        }
      });
    });
})( window.Polymer, window.System );
