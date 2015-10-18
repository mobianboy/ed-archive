( function( polymer ) {
  "use strict";

  polymer( "paired-input", {
    publish: {
      type: {
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
      },
      singleLine: {
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
    ready: function() {
      this._primaryBox = this.shadowRoot.getElementById( "primaryBox" );
      this._confirmBox = this.shadowRoot.getElementById( "confirmBox" );
      this._errorDiv = this.shadowRoot.getElementById( "error" );
      this._boxes = [ this.primaryBox, this.confirmBox ];

      if ( this.hasAttribute( "placeholder" ) ) {
        this.confirmBox.setAttribute(
          "placeholder",
          "Confirm " + this.getAttribute( "placeholder" )
        );
      }

      // TODO fix maybe?
      if ( this.primaryBox.hasAttribute( "single-line" ) ) {
        this.primaryBox.style.display = this.confirmBox.style.display = "inline";
      }

      if ( this.hasAttribute( "type" ) &&
          !( /text|password|email|tel|number|url|search/ ).test( this.attributes.type.value ) ) {
        this.setAttribute( "type", "text" );
      }

      if ( !this.hasAttribute( "type" ) ) {
        this.setAttribute( "type", "text" );
      }
    },
    attached: function() {
      this.addEventListener( "keyup", this.keyConfirm );
    },
    detached: function() {
      this.removeEventListener( "keyup", this.keyConfirm );
    },
    keyConfirm: function() {
      this.errorDiv.innerHTML =
        this.isValid &&
        this.primaryBox !== "" &&
        this.confirmBox !== "" ? "fields are valid" : "fields are not valid";
    },
    get isValid() {
      return this.primaryBox.validity.valid && this.primaryBox.value !== "" &&
        this.primaryBox.value === this.confirmBox.value;
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
    attributeChanged: function( attrName, oldVal, newVal ) {
      if ( attrName === "type" ) {
        if ( ( /text|password|email|tel|number|url|search/ ).test( newVal ) ) {
          this.type = newVal;
        } else {
          this.type = "text";
        }
      } else if ( attrName === "single-line" ) {
        if ( newVal == null ) {
          this.primaryBox.style.display = this.confirmBox.style.display = "block";
        } else {
          this.primaryBox.style.display = this.confirmBox.style.display = "inline";
        }
      }
    }
  });
})( window.Polymer );
