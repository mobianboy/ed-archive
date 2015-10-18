( function( win, doc, System, sinon, expect ) {
  "use strict";

  suite( "EDGenre", function() {
    var EDGenre;

    suiteSetup( function( done ) {
      System.import( "domain/ed/objects/EDGenre" )
        .then( function( imported ) {
          EDGenre = imported.default;
          done();
        }, function( error ) {
          console.warn( "Could not import 'EDGenre' for testing: ", error.message );
          console.error( error.stack );
          done( error );
        });
    });

    suite( "Properties", function() {
      test( "should have 'name' property", function() {
        var args = {},
          edGenre = new EDGenre( args );

        expect( edGenre )
          .to.have.property( "name" );
      });

      test( "should not be able to set 'name' property", function() {
        var args = {},
          inputtedValue = "what's wrong with country music?",
          edGenre = new EDGenre( args ),
          setProperty = function() {
            edGenre.name = inputtedValue;
          };

        expect( setProperty )
          .to.throw( TypeError );
      });
    });
  });
})( window, document, window.System, window.sinon, window.chai.expect );
