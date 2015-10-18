/*eslint-env mocha*/
/*global suite, test, console*/
( function( win, doc, System, sinon, expect ) {
  "use strict";

  suite( "EDModel", function() {
    var EDModel;

    suiteSetup( function( done ) {
      System.import( "domain/ed/objects/EDModel" )
        .then( function( imported ) {
          EDModel = imported.default;
          done();
        }, function( error ) {
          console.warn( "Could not import 'EDModel' for testing: ", error.message );
          console.error( error.stack );
          done( error );
        });
    });

    suite( "EDModel creation", function() {
      test( "Constructor has expected property", function() {
        var data = {
            id: "test",
            type: "object"
          },
          edData = new EDModel( data );

        expect( edData )
          .to.have.property( "id" )
          .to.be.a( "string" )
          .that.equals( data.id );

        expect( edData )
          .to.have.property( "type" )
          .to.be.a( "string" )
          .that.equals( data.type );
      });

      // Skip until browsers support @@toStringTag
      test.skip( "Constructor toStringTag functional", function() {
        var data = {
            id: "test",
            type: "object"
          },
          edData = new EDModel( data );

        expect( edData.toString() )
          .to.equal( "[object EDModel]" );
      });
    });
  });
})( window, document, window.System, window.sinon, window.chai.expect );
