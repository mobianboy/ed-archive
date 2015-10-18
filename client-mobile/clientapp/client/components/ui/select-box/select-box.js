( function( polymer ) {
  "use strict";

  polymer( "select-box", {
    publish: {
      size: {
        value: 5,
        reflect: true
      },
      disabled: {
        value: false,
        reflect: true
      }
    },
    _options: [],

    get selectedIndex() {
      var i;

      for ( i = 0; i < this.listItems.length; i++ ) {
        if ( this.listItems[ i ].children[ 0 ].checked ) {
          return i;
        }
      }
    },
    set selectedIndex( value ) {
      this.listItems[ parseInt( value, 10 ) ].children[ 0 ].checked = true;
    },

    get value() {
      var i;

      for ( i = 0; i < this.listItems.length; i++ ) {
        if ( this.listItems[ i ].children[ 0 ].checked ) {
          return this.listItems[ i ].children[ 0 ].value;
        }
      }
    },

    ready: function() {
      this.listItems = this.shadowRoot.querySelectorAll( "li" );
      this.mainBox = this.shadowRoot.getElementById( "is-focus" );
      this.getOptions = this.shadowRoot.querySelectorAll( ".options" )[ 0 ];

      if ( this.size > 0 ) {
        this.getOptions.style.maxHeight = this.size * 32.4 + "px";
      }

      if ( this.hasAttribute( "required" ) ) {
        this.mainBox.setAttribute( "required", this.getAttribute( "required" ) );
      } else {
        this.mainBox.removeAttribute( "required" );
      }
    },
    attached: function() {
      Array.prototype.forEach.call( this.children, function( elem ) {
        this._options.push({
          value: elem.value,
          label: elem.getAttribute( "label" ) || elem.textContent,
          selected: elem.selected
        });
      }.bind( this ) );
    },
    sizeChanged: function( oldVal, newVal ) {
      if ( this.size > 0 ) {
        this.size = newVal;
        this.setAttribute( "size", newVal );
        this.getOptions.style.maxHeight = newVal * 32 + "px";
      } else if ( !this.hasAttribute( "size" ) ) {
        this.getOptions.style.maxHeight = 5 * 32 + "px";
      }
    },
    disabledChanged: function( oldVal, newVal ) {
      if ( newVal ) {
        this.setAttribute( "disabled", newVal );
      } else {
        this.removeAttribute( "disabled" );
      }
    },
    // Listens for required
    attributeChanged: function( attrName, oldVal, newVal ) {
      switch ( attrName ) {
        case "required":
          this.mainBox[ newVal == null ? "removeAttribute" : "setAttribute" ]( attrName, "" );
          break;
        default:
          // do nothing
          break;
      }
    }
  });
})( window.Polymer );
