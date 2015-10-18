( function( polymer ) {
  "use strict";

  var copyAttributes = function( elemFrom, elemTo, attrs ) {
    attrs.forEach( function( attr ) {
      if ( elemFrom.hasAttribute( attr ) ) {
        elemTo.setAttribute( attr, elemFrom.getAttribute( attr ) );
      } else {
        elemTo.removeAttribute( attr );
      }
    });
  };

  polymer( "ed-bubble-select", {
    required: false,
    disabled: false,
    checked: false,
    publish: {
      value: {
        reflect: true
      },
      name: {
        reflect: true
      }
    },
    ready: function() {
      this.checkboxField = this.shadowRoot.getElementById( "checkbox-field" );
      this.bubbleSelect = this.shadowRoot.getElementById( "ed-bubble" );

      this.addEventListener( "click", function() {
        if ( this.hasAttribute( "disabled" ) ) {
          return false;
        }

        this.checked = !this.checked;
        this[ this.checked ? "setAttribute" : "removeAttribute" ]( "checked", "" );
      });

      if ( this.hasAttribute( "checked" ) && this.hasAttribute( "image" ) ) {
        this.bubbleSelect.style.backgroundImage = "url(\"" + this.getAttribute( "image" ) + "\");";
      }
    },
    attached: function() {
      copyAttributes( this, this.checkboxField, [ "checked", "disabled", "required" ]);
    },
    requiredChanged: function( oldValue, newValue ) {
      if ( newValue ) {
        this.setAttribute( "required", "" );
      } else {
        this.removeAttribute( "required" );
      }
    },
    disabledChanged: function( oldValue, newValue ) {
      if ( newValue ) {
        this.setAttribute( "disabled", "" );
      } else {
        this.removeAttribute( "disabled" );
      }
    },
    checkedChanged: function( oldValue, newValue ) {
      if ( newValue ) {
        this.setAttribute( "checked", "" );
      } else {
        this.removeAttribute( "checked" );
      }
    }
  });
})( Polymer );
