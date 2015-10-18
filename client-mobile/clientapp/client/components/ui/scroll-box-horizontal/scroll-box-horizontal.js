( function( polymer ) {
  "use strict";
  polymer( "scroll-box-horizontal", {
    showArrows: false,
    publish: {
      disabled: {
        value: false,
        reflect: true
      }
    },
    ready: function() {
      this.innerBox = this.shadowRoot.getElementsByClassName( "inner-box" )[ 0 ];

      if ( this.hasAttribute( "show-arrows" ) ) {
        this.showArrows = true;
      }
    },
    scrollBoxRight: function( value ) {
      var currentPosition = this.innerBox.scrollLeft;
      this.innerBox.scrollLeft = currentPosition + parseInt( value, 10 );
    },
    // Scroll Left
    scrollBoxLeft: function( value ) {
      var currentPosition = this.innerBox.scrollLeft;
      this.innerBox.scrollLeft = currentPosition - parseInt( value, 10 );
    },
    // Listens for disabled
    disabledChanged: function( oldVal, newVal ) {
      this.disabled = newVal;
      this.setAttribute( "disabled", newVal );
    },
    showArrowsChanged: function( oldValue, newValue ) {
      if ( newValue ) {
        this.setAttribute( "show-arrows", "" );
      } else {
        this.removeAttribute( "show-arrows" );
      }
    },
    attributeChanged: function( attrName, oldVal, newVal ) {
      if ( newVal == null && attrName === "disabled" ) {
        this[attrName] = this.publish[attrName].value;
        return;
      }

      if ( this.hasAttribute( "show-arrows" ) ) {
        this.showArrows = true;
      } else {
        this.showArrows = false;
      }
    }
  });
})( window.Polymer );
