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

  polymer( "ed-scrubber", {
    publish: {
      min: {
        value: 0,
        reflect: true
      },
      max: {
        value: 100,
        reflect: true
      },
      format: {
        value: "percent",
        reflect: true
      },
      type: {
        value: "default",
        reflect: true
      }
    },
    ready: function() {
      this.scrubberWrapper = this.shadowRoot.getElementById( "scrubber-wrapper" );
      this.scrubberInput = this.shadowRoot.getElementById( "scrubber-input" );
      this.valBox = this.shadowRoot.getElementById( "val-box" );
      this.backFill = this.shadowRoot.getElementsByClassName( "back-fill" )[ 0 ];
    },
    attached: function() {
      copyAttributes( this, this.scrubberInput, [ "min", "max", "format" ]);
      copyAttributes( this, this.scrubberWrapper, [ "type" ]);
    },
    showVal: function() {
      var inputValue = this.scrubberInput.value,
          topVal = parseInt( ( inputValue - this.scrubberInput.min ), 10 ),
          bottomVal = parseInt( ( this.scrubberInput.max - this.scrubberInput.min ), 10 ),
          valPercent = parseInt( ( topVal / bottomVal ) * 100, 10 ) + "%",
          valTime = inputValue + " seconds";

      this.valBox.innerHTML = this.format === "percent" ? valPercent : valTime;
      this.backFill.style.width = valPercent;
    },
    attributeChanged: function( attrName, oldVal, newVal ) {
      if ( newVal == null && ( /^(min|max|format|type)/ ).test( attrName ) ) {
        this[ attrName ] = this.publish[ attrName ].value;
        return;
      }
    }
  });
})( window.Polymer );
