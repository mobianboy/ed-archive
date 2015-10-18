/*eslint-env mocha*/
/*global suite, test, console*/
( function( win, doc, System, sinon, expect ) {
  "use strict";

  suite( "EDUser", function() {
    var EDUser;

    suiteSetup( function( done ) {
      System.import( "domain/ed/objects/EDUser" )
        .then( function( imported ) {
          EDUser = imported.default;
          done();
        }, function( error ) {
          console.warn( "Could not import 'EDUser' for testing: ", error.message );
          console.error( error.stack );
          done( error );
        });
    });

    suite( "EDUser creation", function() {
      test( "Constructor has expected property", function() {
        var data = {
            id: "test",
            type: "user"
          },
          edData = new EDUser( data );

        expect( edData )
          .to.have.property( "id" )
          .to.be.a( "string" )
          .that.equals( data.id );

        expect( edData )
          .to.have.property( "type" )
          .to.be.a( "string" )
          .that.equals( data.type );
      });

      test( "Constructor has deep properties", function() {
        var data = {
            id: "test",
            type: "user",
            name: {
              first: "Khoa"
            }
          },
          edData = new EDUser( data );

        expect( edData )
          .to.have.property( "name" )
          .to.deep.equals( data.name );
      });

      // Skip until browsers support @@toStringTag
      test.skip( "Constructor toStringTag functional", function() {
        var data = {
            id: "test",
            type: "user"
          },
          edData = new EDUser( data );

        expect( edData.toString() )
          .to.equal( "[object EDUser]" );
      });
    });
  });
})( window, document, window.System, window.sinon, window.chai.expect );
