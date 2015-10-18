( function( polymer ) {
  "use strict";

  Promise.all([
    System.import( "domain/ed/services/ed-player-service" ),
    System.import( "domain/lib/event/create-event" )
  ]).then(function( imported ) {
    var
      playerService = imported[ 0 ].default,
      createEvent = imported[ 1 ].default,
      currentRating,
      triggerRatingHandler,
      setArtistLink,
      playerUpdateHandler,
      viewArtistHandler;

    // helpers
    triggerRatingHandler = function( event ) {
      var value = event.target.getAttribute( "value" );

      this.resetRating();
      currentRating = value;

      while ( value > 0 ) {
        this.$[ "star-" + value ].classList.add( "selected" );
        value -= 1;
      }

      this.dispatchEvent( createEvent( "scrubberUpdate", {
        detail: {
          name: "rate"
        }
      }));
    };

    playerUpdateHandler = function( event ) {
      this.trackName.innerText = playerService.currentStats.playing.name;
      this.trackName.classList.remove( "loading" );

      if ( event.detail.type === "artistUpdate" ) {
        this.artistName.innerText = playerService.currentStats.currentArtist.displayName;
        this.artistName.classList.remove( "loading" );
      }
    };

    viewArtistHandler = function() {
      this.songCard.close();
      this.router.go( "/artist/" + playerService.currentStats.currentArtist.id );
    };

    polymer( "ed-star-rating", {
      /* LIFECYCLE */
      disable: false,
      get currentRating() {
        return currentRating;
      },
      ready: function() {
        this.handlers = {
          triggerRating: triggerRatingHandler.bind( this ),
          playerUpdate: playerUpdateHandler.bind( this ),
          viewArtist: viewArtistHandler.bind( this )
        };

        this.inputField = this.shadowRoot.getElementById( "input-field" );
        this.stars = this.$[ "star-field" ].querySelectorAll( "ed-icon" );
        this.artistName = this.$[ "artist-name" ];
        this.trackName = this.$[ "track-name" ];
        this.router = document.getElementById( "root-app-router" );
      },
      attached: function() {
        this.inputField.addEventListener( "touchstart", this.handlers.triggerRating );
        this.artistName.addEventListener( "touchstart", this.handlers.viewArtist );
        this.songCard = document.getElementById( "song-card" );
        this.songCardWrapper = document.getElementById( "song-card-wrapper" );
        this.mainPlayer = this.songCard.shadowRoot.querySelector( "#main-player-wrapper" );
        this.miniPlayer = this.songCard.shadowRoot.querySelector( "#mini-player-wrapper" );

        // bind player updates through the service
        playerService.emitter.on( "playerUpdate", this.handlers.playerUpdate );
      },
      detached: function() {
        this.inputField.removeEventListener( "touchstart", this.handlers.triggerRating );
        this.inputField.removeEventListener( "mouseover", this.handlers.triggerRating );

        playerService.emitter.off( "playerUpdate", this.handlers.playerUpdate );
      },
      resetRating: function() {
        var i;
        currentRating = null;

        for ( i = 0; i < this.stars.length; i++ ) {
          this.stars[ i ].classList.remove( "selected" );
        }
      }
    });
  });
})( window.Polymer );
