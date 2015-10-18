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

  polymer( "on-off", {
    publish: {
      "on-text": {
        value: "On",
        reflect: true
      },
      "off-text": {
        value: "Off",
        reflect: true
      },
      checked: {
        value: false,
        reflect: true
      },
      disabled: {
        value: false,
        reflect: true
      }
    },
    observe: {
      "$.checkbox.checked": function( oldValue, newValue ) {
        var state = newValue ? "on" : "off";
        this.fire( "toggle", {
          msg: "toggle",
          state: state
        });

        this.fire( state, {
          msg: state
        });
      }
    },
    get onText() {
      return this["on-text"];
    },
    set onText( value ) {
      if ( value == null ) {
        value = "On";
      }
      return this["on-text"] = value;
    },
    get offText() {
      return this["off-text"];
    },
    set offText( value ) {
      if ( value == null ) {
        value = "Off";
      }
      return this["off-text"] = value;
    },
    ready: function() {
      var checkedBox = this.$.checkbox;

      // Set Defaults
      this.onText = this["on-text"];
      this.offText = this["off-text"];

      copyAttributes( this, checkedBox, [ "checked", "disabled" ]);
    },
    attributeChanged: function( attrName, oldVal, newVal ) {
      if ( newVal == null && ( /^(on|off)/ ).test( attrName ) ) {
        this[attrName] = this.publish[attrName].value;
        return;
      }

      switch ( attrName ) {
        case "disabled":
          // fallthrough
        case "checked":
          this.$.checkbox[ newVal == null ? "removeAttribute" : "setAttribute" ]( attrName, "" );
          break;
        default:
          // do nothing
          break;
      }
    }
  });
})( window.Polymer );
