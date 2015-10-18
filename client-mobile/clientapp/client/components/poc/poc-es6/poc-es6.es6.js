( function( polymer ) {
  "use strict";
  polymer( "poc-es6", {
    attached() {
      System.import( "domain/hello-goodbye" ).then( imports => {
        var hello = imports.default.hello;
        this.addEventListener( "click", () => {
          hello( this.getAttribute( "my-name" ) );
        });
      });
    }
  });
})( window.Polymer );
