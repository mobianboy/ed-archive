( function( polymer ) {
  "use strict";

  Promise.all([
    System.import( "domain/ed/services/ed-discover-service" ),
    System.import( "domain/ed/analytics/ed-analytics-service" )
  ]).then(function( imported ) {
    var
      discoverService = imported[ 0 ].default,
      bubbleCounter = 0,
      bubbleArray = [],
      triggerBubblesHandler = function( event ) {
        event.preventDefault();

        bubbleArray = Array.from( this.inputBubbles )
          .filter(function( element ) {
            return element.hasAttribute( "checked" );
          })
          .map(function( element ) {
            return parseInt( element.getAttribute( "data-id" ), 10 );
          });

        this.router.go( "/onboarding/dislike?genresliked=" + bubbleArray  );
      },
      triggerCounterHandler = function( event ) {
        if ( event.target.hasAttribute( "checked" ) &&
          event.target.hasAttribute( "data-id" ) &&
          bubbleCounter < 3 ) {
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

        return this.handlers.bubblesLiked();
      },
      bubblesLikedHandler = function( event ) {
        var i, j;

        if ( bubbleCounter < 3 ) {
          for ( i = 0; i < this.inputBubbles.length; i++ ) {
            if ( !this.inputBubbles[ i ].hasAttribute( "checked" ) ) {
              this.inputBubbles[ i ].removeAttribute( "disabled" );
            }
          }
        }

        if ( bubbleCounter === 3 ) {
          for ( j = 0; j < this.inputBubbles.length; j++ ) {
            if ( !this.inputBubbles[ j ].hasAttribute( "checked" ) ) {
              this.inputBubbles[ j ].setAttribute( "disabled", "" );
            }
          }
        }
      };

    polymer( "ed-liked-view", {
      /* LIFECYCLE */
      ready: function() {
        this.inputBubbles     = this.shadowRoot.querySelectorAll( "ed-bubble-select" );
        this.nextBtn          = this.shadowRoot.getElementById( "next-button" );
        this.bubbleContainer  = this.shadowRoot.getElementById( "bubble-container" );
        bubbleCounter = 0;

        // handlers
        this.handlers = {
          triggerBubbles: triggerBubblesHandler.bind( this ),
          triggerCounter: triggerCounterHandler.bind( this ),
          bubblesLiked: bubblesLikedHandler.bind( this )
        };
      },
      attached: function() {
        this.nextBtn.addEventListener( "click", this.handlers.triggerBubbles );
        this.bubbleContainer.addEventListener( "click", this.handlers.triggerCounter );
      },
      detached: function() {
        this.nextBtn.removeEventListener( "click", this.handlers.triggerBubbles );
        this.bubbleContainer.removeEventListener( "click", this.handlers.triggerCounter );
      },
      attributeChanged: function( attrName, oldValue, newValue ) {}
      /* PROPERTIES */
      /* METHODS */
    });
  });
})( window.Polymer );
