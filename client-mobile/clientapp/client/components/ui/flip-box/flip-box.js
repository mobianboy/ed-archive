( function( polymer ) {
  "use strict";

  polymer( "flip-box", {
    publish: {
      trigger: {
        reflect: true
      },
      rotation: {
        reflect: true
      },
      animation: {
        reflect: true
      }
    },
    _isFlipped: false,
    get isFlipped() {
      return this._isFlipped;
    },
    set isFlipped( value ) {
      return this._isFlipped;
    },
    /* LIFECYCLE */
    ready: function() {
      this.eventList = [ "mousedown", "touchleave" ];

      this.flipBoxContainer = this.shadowRoot.getElementsByClassName( "flipbox-container" )[ 0 ];

      // Add transition end
      this.flipBoxContainer.addEventListener( "transitionend", function() {
        if ( this.needsReset ) {
          this.flipBoxContainer.classList.remove( "flipbox-transition", "continue" );
          this.flipBoxContainer.classList.add( "no-transform" );
        }
      }. bind( this ) );
    },
    attached: function() {
      this.trigger = !( /btn|box/ ).test( this.trigger ) ? "box" : this.trigger;
      this.listener( this.trigger, "add" );

      this.animation = !( /vertical|horizontal/ ).test( this.animation ) ? "horizontal" : this.animation;
      this.rotation = !( /loop|toggle/ ).test( this.rotation ) ? "toggle" : this.rotation;
    },
    /* FUNCTIONS */
    flip: function() {
      this.flipBoxContainer.classList.remove( "no-transform" );
      this.flipBoxContainer.classList.add( "flipbox-transition" );
      this.flipBoxContainer.classList.toggle( "flip" );
      this._isFlipped = this.flipBoxContainer.classList.contains( "flip" );
    },
    loop: function() {
      if ( this.flipBoxContainer.classList.contains( "flip" ) ) {
        this.needsReset = true;
        this.flipBoxContainer.classList.remove( "flip" );
        this.flipBoxContainer.classList.add( "flipbox-transition", "continue" );
        this._isFlipped = false;
      } else {
        this.flipBoxContainer.classList.remove( "no-transform" );
        this.flipBoxContainer.classList.add( "flip", "flipbox-transition" );
        this._isFlipped = true;
      }
    },
    /* EVENT LISTENERS */
    btnListener: function( e ) {
      if ( e.path[ 0 ].classList.contains( "flipbox-button" ) ) {
        this[ this.rotation === "loop" ? "loop" : "flip" ]();
      }
    },
    boxListener: function() {
      this[ this.rotation === "loop" ? "loop" : "flip" ]();
    },
    listener: function( listener, flag ) {
      this.eventList.forEach( function( event ) {
        this[ flag + "EventListener" ]( event, this[ listener + "Listener" ] );
      }.bind( this ) );
    },
    // resetting event listeners
    listenerPackage: function( listener ) {
      this.listener( listener, "add" );
      this.listener( listener === "btn" ? "box" : "btn", "remove" );
    },
    /* ATTRIBUTE CHANGE */
    attributeChanged: function( attrName, oldVal, newVal ) {
      // trigger
      if ( attrName === "trigger" ) {
        this.trigger = newVal === "btn" ? newVal : "box";
        this.listenerPackage( newVal );
      // animation
      } else if ( attrName === "animation" ) {
        this.animation = newVal === "vertical" ? newVal : "horizontal";
      // rotation
      } else if ( attrName === "rotation" ) {
        this.rotation = newVal === "loop" ? newVal : "toggle";
      }
    }
    /* END FUNCTIONS */
  });
})( window.Polymer );
