( function( polymer ) {
  "use strict";

  polymer( "poc-viewer", {
    /* LIFECYCLE */
    ready: function() {
      this.link = document.createElement( "link" );
    },
    attached: function() {
      if ( this.attributes.path ) {
        this.setUrl( this.attributes.path.value );
      }
      this.createLink();
    },
    /* FUNCTIONS */
    setUrl: function( path ) {
      return this.exampleUrl = `components/${path}/example.html`;
    },
    updateExample: function( loadEvent ) {
      var linkImport = loadEvent.path[ 0 ].import,
        // todo account for multiple templates
        content = linkImport.querySelector( "template" ).content;
      Array.prototype.forEach.call(
        linkImport.querySelectorAll( this.attributes[ "element-name" ].value ),
          e => this.appendChild( e )
      );
      this.appendChild( content.cloneNode( true ) );
    },
    createLink: function() {
      this.link.rel = "import";
      this.link.href = this.exampleUrl;
      this.link.addEventListener( "load", this.updateExample.bind( this ) );
      this.appendChild( this.link );
    }
  });
})( window.Polymer );
