( function( polymer ) {
  "use strict";

  polymer( "progress-bar", {
    showValue: true,
    publish: {
      value: {
        value: 0,
        reflect: true
      },
      animation: {
        reflect: true
      },
      max: {
        value: 1,
        reflect: true
      },
      direction: {
        value: "RTL",
        reflect: true
      }
    },
    // percentage
    get valuePercentage() {
      return this._valuePercentage;
    },
    set valuePercentage( value ) {
      this._valuePercentage = value;
      return value;
    },
    ready: function() {
      this.innerBar = this.shadowRoot.getElementsByClassName( "inner-bar" )[ 0 ];
      this.currentText = this.shadowRoot.getElementsByClassName( "current-text" )[ 0 ];
      this.maxText = this.shadowRoot.getElementsByClassName( "max-text" )[ 0 ];
      this.progressDisplay = this.shadowRoot.getElementsByClassName( "progress-display" )[ 0 ];

      // showValue
      if ( this.hasAttribute( "show-value" ) ) {
        this.showValue = this.attributes[ "show-value" ].value;
      }
      // animation
      if ( this.hasAttribute( "animation" ) ) {
        this.animation = this.attributes.animation.value;
      }
      this.updatePercentage();
    },
    attributeChanged: function( attrName, oldVal, newVal ) {

      if ( newVal == null && ( /^(value|max|direction)/ ).test( attrName ) ) {
        this[attrName] = this.publish[attrName].value;
        return;
      }

      if ( newVal == null && attrName === "show-value" ) {
        this.showValue = false;
      }

      this.updatePercentage();
    },
    valueChanged: function( oldValue, newValue ) {
      if ( newValue == null ) {
        this.value = this.publish.value.value;
      }
    },
    maxChanged: function( oldValue, newValue ) {
      if ( newValue == null || newValue < 1 ) {
        this.max = this.publish.max.value;
      }
    },
    directionChanged: function( oldValue, newValue ) {
      if ( newValue == null || !(/^(RTL|LTR)$/).test( newValue ) ) {
        this.direction = this.publish.direction.value;
      }
    },
    showValueChanged: function( oldValue, newValue ) {
      if ( newValue ) {
        this.setAttribute( "show-value", "" );
      } else {
        this.removeAttribute( "show-value" );
      }
    },
    updatePercentage: function() {
      this.valuePercentage = Math.round( this.value * 100 / this.max );
      this.innerBar.style.width = this.valuePercentage + "%";
    }
  });
})( window.Polymer );
