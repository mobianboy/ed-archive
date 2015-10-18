( function( polymer ) {
  "use strict";

  polymer( "ed-paired-input", {
    publish: {
      type: {
        reflect: true
      },
      icon: {
        reflect: true
      },
      placeholder: {
        reflect: true
      },
      pattern: {
        reflect: true
      },
      required: {
        reflect: true,
        value: false
      },
      disabled: {
        reflect: true,
        value: false
      }
    },
    get primaryBox() {
      return this._primaryBox;
    },
    get confirmBox() {
      return this._confirmBox;
    },
    get errorDiv() {
      return this._errorDiv;
    },
    get boxes() {
      return this._boxes;
    },
    get isValid() {
      return this.primaryBox.validity.valid && this.primaryBox.value !== "" &&
        this.primaryBox.value === this.confirmBox.value;
    },
    get regexConfirm() {
      return this.primaryBox.validity.valid && this.primaryBox.value !== "";
    },
    get inputMatchConfirm() {
      return this.primaryBox.value !== "" && this.primaryBox.value === this.confirmBox.value;
    },
    get val() {
      if ( this.primaryBox.value !== this.confirmBox.value ) {
        return;
      }

      return this.primaryBox.value;
    },
    set val( val ) {
      this.primaryBox.value = this.confirmBox.value = val;
      return true;
    },
    ready: function() {
      this._primaryBox = this.shadowRoot.getElementById( "primary-box" ).$.input;
      this._confirmBox = this.shadowRoot.getElementById( "confirm-box" ).$.input;
      this._errorDiv = this.shadowRoot.getElementById( "error" );
      this._boxes = [ this.primaryBox, this.confirmBox ];
    },
    attributeChanged: function( attrName, oldValue, newValue ) {
      var boxId;

      if ( attrName === "type" ) {
        if ( ( /text|password|email|tel|number|url|search/ ).test( newValue ) ) {
          this.type = newValue;
        } else {
          this.type = "text";
        }
      }

      if ( attrName === "invalid-primary" || attrName === "invalid-confirm" ) {
        console.log( "saw %s change, %o, %o", attrName, oldValue, newValue );
        boxId = attrName.split( "-" )[ 1 ] + "-box";

        if ( newValue == null ) {
          // remove
          this.$[ boxId ].classList.remove( "invalid" );
        } else {
          // add
          this.$[ boxId ].classList.add( "invalid" );
        }
      }
    }
  });
})( window.Polymer );
