( function( polymer ) {
  "use strict";

  polymer( "ed-form-input", {
    /* LIFECYCLE */
    publish: {
      icon: {
        reflect: true
      },
      type: {
        reflect: true
      },
      placeholder: {
        reflect: true
      },
      min: {
        reflect: true
      },
      max: {
        reflect: true
      },
      pattern: {
        reflect: true
      },
      required: {
        value: false,
        reflect: true
      },
      disabled: {
        value: false,
        reflect: true
      }
    },
    get value() {
      return this.$.input.value;
    },
    set value( value ) {
      this.$.input.value = value;
      return value;
    },
    get validity() {
      return this.$.input.validity;
    },
    focus: function() {
      return this.$.input.focus();
    },
    ready: function() {
      this.handlers = {
        focus: function() {
          this.focus();
        }.bind( this )
      };
    },
    attached: function() {
      this.addEventListener( "focus", this.handlers.focus );
    },
    domReady: function() {
      // after this.$.input is bound, cleanup attributes
      this.cleanupAttributes();
    },
    detached: function() {
      this.removeEventListener( "focus", this.handlers.focus );
    },
    attributeChanged: function( /*attrName, oldValue, newValue*/ ) {
      if ( this.$ ) {
        // attributeChanged gets called during bootstrap before
        // this.$ has the input bound to it, need to wrap in this
        // if to not cause "undefined lookups" on attached
        this.cleanupAttributes();
      }
    },
    cleanupAttributes: function() {
      [
        "pattern",
        "min",
        "max"
      ].forEach(function( attrName ) {
          var value = this.getAttribute( attrName );

          if ( value === "" || value == null ) {
            this.$.input.removeAttribute( attrName );
          }
        }, this );
    }
  });
})( window.Polymer );
