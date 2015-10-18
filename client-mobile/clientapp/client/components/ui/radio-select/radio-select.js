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

  polymer( "radio-select", {
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
      this.radioButton = this.shadowRoot.getElementById( "radioSelect" );

      this.addEventListener( "click", function() {
        if ( this.hasAttribute( "disabled" ) ) {
          return false;
        }

        if ( !this.checked ) {
          this.checked = true;
          this.setAttribute( "checked", "" );
          this.children[ 0 ].style.transform = "scale(1)";
          this.clearOthers();
        }
      });
    },
    attached: function() {
      copyAttributes( this, this.radioButton, [ "checked", "disabled", "required" ]);
    },
    get otherMembers() {
      return Array.prototype.slice.call(
        document.querySelectorAll( "radio-select[name=\"" + this.attributes.name.value + "\"]" ),
        0
      ).filter( function( elm ) {
          return this !== elm;
        }, this );
    },
    clearOthers: function() {
      this.otherMembers.forEach( function( elm ) {
        elm.checked = false;
        elm.removeAttribute( "checked" );
        elm.children[ 0 ].style.transform = "scale(1)";
      }, this );
    }
  });
})( Polymer );
