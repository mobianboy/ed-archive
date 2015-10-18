( function( polymer ) {
  "use strict";
  polymer( "ed-modal", {
    publish: {
      clickoff: {
        value: false,
        reflect: true
      },
      closebutton: {
        value: false,
        reflect: true
      },
      trigger: {
        reflect: true
      }
    },
    _isOpen: false,
    get isOpen() {
      return this._isOpen;
    },
    set isOpen( value ) {
      return this._isOpen;
    },
    /* LIFECYCLE */
    ready: function() {
      this.closeListener = this.close.bind( this );
      this.closeModalListener = this.closeModal.bind( this );
      this.eventList = [ "mousedown", "touchstart" ];
      this.modalButton = this.shadowRoot.getElementsByClassName( "modal-button" )[ 0 ];
      this.modalContainer = this.shadowRoot.getElementsByClassName( "modal-container" )[ 0 ];
      this.openListener = this.open.bind( this );
      this._isOpen = this.modalContainer.classList.contains( "modal-container-opened" );
    },
    attached: function() {
      this.triggerListener( "add", this.trigger );

      this.eventList.forEach( function( e ) {
        this.modalButton.addEventListener( e, this.closeListener );
      }.bind( this ) );

      if ( !this.hasAttribute( "closebutton" ) ) {
        this.modalButton.classList.add( "modal-button-closed" );
      }

      if ( this.hasAttribute( "clickoff" ) ) {
        this.clickoffListener( "add" );
      }
    },
    /* FUNCTIONS */
    /* event listeners */
    triggerListener: function( flag, value ) {
      this.eventList.forEach( function( e ) {
        var elems = document.querySelectorAll( value );

        if ( elems.length === 0 ) {
          return;
        } else if ( elems.length === 1 ) {
          elems[ 0 ][ flag + "EventListener" ]( e, this.openListener );
        } else {
          Array.from( elems ).forEach( function( elem ) {
            elem[ flag + "EventListener" ]( e, this.openListener );
          }.bind( this ) );
        }
      }.bind( this ) );
    },
    clickoffListener: function( flag ) {
      this.eventList.forEach( function( e ) {
        this.modalContainer[ flag + "EventListener" ]( e, this.closeModalListener );
      }.bind( this ) );
    },
    /* show/hide modal */
    open: function() {
      this._isOpen = true;
      this.setZIndex();
      this.modalContainer.classList.add( "modal-container-opened" );
    },
    close: function() {
      this._isOpen = false;
      this.modalContainer.classList.remove( "modal-container-opened" );
    },
    closeModal: function( e ) {
      if ( e.target === this.modalContainer ) {
        this.modalContainer.classList.remove( "modal-container-opened" );
      }
      this._isOpen = false;
    },
    /* attribute change */
    attributeChanged: function( attrName, oldVal, newVal ) {
      switch ( attrName ) {
        case "trigger":
          this.triggerListener( "remove", oldVal );
          this.trigger = newVal;
          this.triggerListener( "add", newVal );
          break;
        case "close-button":
          this.modalButton.classList[ !newVal && newVal !== "" ? "add" : "remove" ]( "hidden" );
          break;
        case "clickoff":
          this.clickoffListener( !newVal && newVal !== "" ? "remove" : "add" );
          break;
        default:
          // do nothing
          break;
      }
    },
    /* z-index */
    findHighestZIndex: function() {
      var highestZ = 1,
          siblingsList = Array.from( this.parentNode.children ).filter( function( elem ) {
            return this !== elem;
          }, this );

      if ( !siblingsList.length ) {
        return highestZ;
      }

      siblingsList.forEach( function( sib ) {
        var getZ = window.getComputedStyle( sib, null ).getPropertyValue( "z-index" ),
            currentZ = parseInt( getZ, 10 );

        if ( getZ && getZ !== "auto" ) {
          highestZ = currentZ > highestZ ? currentZ : highestZ;
          highestZ = highestZ >= 2147483647 ? 2147483646 : highestZ;
        }
      });
      return highestZ;
    },
    setZIndex: function() {
      var baseZ = this.findHighestZIndex();
      this.modalContainer.style.zIndex = baseZ + 1;
    }
  });
})( window.Polymer );
