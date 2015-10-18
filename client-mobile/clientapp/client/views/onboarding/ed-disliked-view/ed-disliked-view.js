( function( polymer ) {
  "use strict";

  Promise.all([
    System.import( "domain/ed/services/ed-discover-service" ),
    System.import( "domain/ed/services/ed-player-service" )
  ]).then(function( imported ) {
    var
      discoverService = imported[ 0 ].default,
      playerService = imported[ 1 ].default,
      bubbleCounter = 0,
      bubbleArray = [],
      triggerBubblesHandler = function( event ) {
        var
          self = this,
          goToDiscover = function() { self.router.go( "/discover" ); };

        event.preventDefault();

        bubbleArray = Array.from( this.inputBubbles )
          .filter(function( element ) {
            return element.hasAttribute( "checked" );
          })
          .map(function( element ) {
            return parseInt( element.getAttribute( "data-id" ), 10 );
          });

        console.log( "trigger bubbles handler %o, %o", this.likedBubbles, bubbleArray );
        return discoverService.setCurrentProfileBlend( this.likedBubbles, bubbleArray )
          .then(function() {
            self.songCard.show();
            return playerService.startMusicDiscovery( "profileBlend" );
          })
          .then( goToDiscover, goToDiscover );
      },
      // hides bubbles on ready
      hideBubblesHandler = function( event ) {
        var self = this;

        this.likedBubbles.forEach(function( idNumber ) {
          var genre = self.shadowRoot.querySelector( "ed-bubble-select[data-id='" + idNumber + "']" );
          genre.style.visibility = "hidden";
          genre.setAttribute( "disabled", "" );
        });
      },
      triggerCounterHandler = function( event ) {
        if ( event.target.hasAttribute( "checked" ) &&
          event.target.hasAttribute( "data-id" ) &&
          bubbleCounter < 1 ) {
          bubbleCounter++;
        } else if ( !event.target.hasAttribute( "checked" ) &&
          !event.target.hasAttribute( "disabled" ) &&
          bubbleCounter !== 0 ) {
          bubbleCounter--;
        }

        // disables button
        if ( bubbleCounter > 0 ) {
          this.nextBtn.removeAttribute( "disabled" );
        } else {
          this.nextBtn.setAttribute( "disabled", "" );
        }

        return this.handlers.bubblesDisliked();
      },
      bubblesDislikedHandler = function( event ) {
        var i, j;

        if ( bubbleCounter < 1 ) {
          for ( i = 0; i < this.inputBubbles.length; i++ ) {
            if ( !this.inputBubbles[ i ].hasAttribute( "checked" ) ) {
              this.inputBubbles[ i ].removeAttribute( "disabled" );
            }
          }
        }

        if ( bubbleCounter === 1 ) {
          for ( j = 0; j < this.inputBubbles.length; j++ ) {
            if ( !this.inputBubbles[ j ].hasAttribute( "checked" ) ) {
              this.inputBubbles[ j ].setAttribute( "disabled", "" );
            }
          }
        }
      };

    polymer( "ed-disliked-view", {
      /* LIFECYCLE */
      ready: function() {
        this.inputBubbles     = this.shadowRoot.querySelectorAll( "ed-bubble-select" );
        this.nextBtn          = this.shadowRoot.getElementById( "next-button" );
        this.bubbleContainer  = this.shadowRoot.getElementById( "bubble-container" );
        this.songCard         = document.getElementById( "song-card" );
        this.likedBubbles     = this.getUrlVar();
        console.log( this.likedBubbles );
        // handlers
        this.handlers = {
          hideBubbles: hideBubblesHandler.bind( this ),
          triggerCounter: triggerCounterHandler.bind( this ),
          bubblesDisliked: bubblesDislikedHandler.bind( this ),
          triggerBubbles: triggerBubblesHandler.bind( this )
        };
      },
      attached: function() {
        this.bubbleContainer.addEventListener( "click", this.handlers.triggerCounter );
        this.nextBtn.addEventListener( "click", this.handlers.triggerBubbles );
        this.handlers.hideBubbles();
      },
      detached: function() {
        this.nextBtn.removeEventListener( "click", this.handlers.triggerBubbles );
        this.bubbleContainer.removeEventListener( "click", this.handlers.triggerCounter );
      },
      getUrlVar: function() {
        var query = location.hash.substr( 1 ),
           result = [];

        query.split( "?" ).forEach(function( part ) {
          var item = part.split( "=" );
          result[item[0]] = decodeURIComponent( item[1] );
        });

        return result.genresliked.split( "," ).map(function( num ) { return parseInt( num, 10 ); });
      }
      /* PROPERTIES */
      /* METHODS */
    });
  });
})( window.Polymer );
