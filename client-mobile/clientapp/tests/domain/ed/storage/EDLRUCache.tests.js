/*eslint-env mocha*/
/*global suite, test, console*/
( function( win, doc, System, sinon, expect ) {
  "use strict";

  suite( "EDLRUCache", function() {
    var EDLRUCache, EDArtist;

    suiteSetup( function( done ) {
      System.import( "domain/ed/storage/EDLRUCache" )
        .then( function( imported ) {
          EDLRUCache = imported.default;
          done();
        }, function( error ) {
          console.warn( "Could not import 'EDLRUCache' for testing: ", error.message );
          console.error( error.stack );
          done( error );
        });

      System.import( "domain/ed/objects/profile/EDArtist" )
        .then( function( imported ) {
          EDArtist = imported.default;
          done();
        }, function( error ) {
          console.warn( "Could not import 'EDLRUCache' for testing: ", error.message );
          console.error( error.stack );
          done( error );
        });
    });

    suite( "EDLRUCache creation", function() {
      test( "instance has expected properties & methods", function() {
        var edCache = new EDLRUCache( 10 );

        expect( edCache )
          .to.have.property( "set" )
          .to.be.a( "function" );

        expect( edCache )
          .to.have.property( "remove" )
          .to.be.a( "function" );
      });
    });

    suite( "Set Method", function() {
      test( "set method call count", function() {
        var artistObj = {
            id: "001",
            yearFounded: 2009
          },
          edCache = new EDLRUCache( 10 ),
          edArtist = new EDArtist( artistObj ),
          setSpy = sinon.spy( edCache, "set" );

        edCache.set( edArtist );

        expect( setSpy )
          .to.have.callCount( 1 );

        setSpy.restore();
      });
    });

    suite( "Remove Method", function() {
      test( "remove method call count", function() {
        var artistObj = {
            id: "001",
            yearFounded: 2009
          },
          edCache = new EDLRUCache( 10 ),
          edArtist = new EDArtist( artistObj ),
          removeSpy = sinon.spy( edCache, "remove" );

        edCache.remove( edArtist );

        expect( removeSpy )
          .to.have.callCount( 1 );

        removeSpy.restore();
      });
    });
  });
})( window, document, window.System, window.sinon, window.chai.expect );
