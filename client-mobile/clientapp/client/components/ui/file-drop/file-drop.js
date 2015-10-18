( function( polymer ) {
  "use strict";

  polymer( "file-drop", {
    publish: {
      fill: {
        value: "parent",
        reflect: true
      },
      accepts: {
        reflect: true
      },
      multiple: {
        value: false,
        reflect: true
      }
    },
    fileList: null,
    fileArr: [],
    get files() {
      return this.fileArr;
    },
    ready: function() {
      this.holder = this.shadowRoot.getElementById( "holder" );
    },
    attached: function() {
      // listens for drag events
      this.holder.addEventListener( "dragover", function( event ) {
        event.stopPropagation();
        event.preventDefault();
      });
      this.holder.addEventListener( "drop", function( event ) {
        event.stopPropagation();
        event.preventDefault();
        // filters files
        this.fileList = event.dataTransfer.files;
        // resets the fileArr
        this.fileArr = [];
        // checks for multiple attribute
        if ( ( this.hasAttribute( "multiple" ) && ( this.fileList.length > 1 ) ) ||
          this.fileList.length === 1 ) {
          this.fileArr = Array.from( this.fileList ).filter( function( file ) {
            var compare = new RegExp( file.type );
            return compare.test( this.accepts );
          }.bind( this ) );
        }
        this.formData = new FormData();
        this.formData.append( "userFiles", this.fileArr );
      }.bind( this ) );
    },
    onDrop: function( callback ) {
      this.holder.addEventListener( "drop", callback );
    },
    onDragOver: function( callback ) {
      this.holder.addEventListener( "dragover", callback );
    },
    fillChanged: function( oldVal, newVal ) {
      this.setAttribute( "fill", newVal );
    },
    multipleChanged: function( oldVal, newVal ) {
      if ( newVal ) {
        this.setAttribute( "multiple", "" );
      } else {
        this.removeAttribute( "multiple" );
      }
    },
    attributeChanged: function( attrName, oldVal, newVal ) {
      this[ attrName ] = newVal;
      this.multiple = this.hasAttribute( "multiple" );
    }
  });
})( window.Polymer );
