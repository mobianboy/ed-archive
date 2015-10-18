( function( polymer ) {
  "use strict";

  Promise.all([
    System.import( "domain/ed/services/ed-player-service" ),
    System.import( "domain/lib/event/create-event" )
  ]).then(function( imported ) {
    var
      playerService = imported[ 0 ].default,
      createEvent = imported[ 1 ].default,
      createUpdateEvent = function( name, detail ) {
        detail = detail || {};
        detail.name = name;

        return createEvent( "scrubberUpdate", {
          detail: detail
        });
      },
      playerUpdateHandler = function( event ) {
        var eventType = event.detail.type;

        if ( eventType === "play" ) {
          this.playIcon.setAttribute( "name", "pause" );
          this.$[ "title" ].innerText = playerService.currentStats.playing.name;
        }

        if ( eventType === "artistUpdate" ) {
          this.$[ "name" ].innerText = playerService.currentStats.currentArtist.displayName;
        }

        if ( eventType === "pause" ) {
          this.playIcon.setAttribute( "name", "play" );
        }

        if ( eventType === "pause" ) {
          this.playIcon.setAttribute( "name", "play" );
        }
      };

    polymer( "ed-mini-player", {
      /* LIFECYCLE */
      ready: function() {
        this.playBtn = this.shadowRoot.getElementById( "play-btn" );
        this.playIcon = this.shadowRoot.getElementById( "play-icon" );

        // event handler
        this.handler = {
          playerUpdate: playerUpdateHandler.bind( this )
        };
      },
      attached: function() {
        playerService.emitter.on( "playerUpdate", this.handler.playerUpdate );
      },
      detached: function() {
        playerService.emitter.off( "playerUpdate", this.handler.playerUpdate );
      },
      toggleIcon: function( event ) {
        var state = this.playIcon.getAttribute( "name" );

        event.stopPropagation();

        if ( this.playIcon.getAttribute( "name" ) === "play" ) {
          this.playIcon.setAttribute( "name", "pause" );
        } else {
          this.playIcon.setAttribute( "name", "play" );
        }

        this.dispatchEvent( createUpdateEvent( state ));
      },
      hideMini: function() {
        this.classList.add( "hidden" );
      }
    });
  });
})( window.Polymer );
