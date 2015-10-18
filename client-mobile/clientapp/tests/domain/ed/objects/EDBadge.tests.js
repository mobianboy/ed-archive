/*eslint-env mocha*/
/*global suite, test, console*/
(function( win, doc, System, sinon, expect ) {
  "use strict";

  suite( "EDBadge", function() {
    var EDBadge;

    suiteSetup( function( done ) {
      System.import( "domain/ed/objects/EDBadge" )
        .then( function( imported ) {
          EDBadge = imported.default;
          done();
        }, function( error ) {
          console.warn( "Could not import 'EDBadge' for testing: ", error.message );
          console.error( error.stack );
          done( error );
        });
    });

    suite( "EDBadge creation", function() {
      test( "constructor has expected properties", function() {
        var badgeObj = {
            id: "001",
            name: "Highest Rated Song",
            badgeType: "empty"
          },
          badge = new EDBadge( badgeObj );

        expect( badge )
          .to.have.property( "id" );

        expect( badge )
          .to.have.property( "name" );

        expect( badge )
          .to.have.property( "badgeType" );

        expect( EDBadge )
          .to.have.property( "TYPE" );

      });
    });

    suite( "Property type & value checks", function() {
      test( "id is correct type", function() {
       var badgeObj = {
            id: "001",
            name: "Highest Rated Song",
            badgeType: "empty"
          },
          badge = new EDBadge( badgeObj );

        expect( badge.id )
          .to.be.a( "string" )
          .to.equal( "001" );

      });

      test( "name should equal badge name", function() {
       var badgeObj = {
            id: "001",
            name: "Highest Rated Song",
            badgeType: "empty"
          },
          badge = new EDBadge( badgeObj );

        expect( badge.name )
          .to.be.a( "string" )
          .to.equal( "Highest Rated Song" );

      });

      test( "badge type should equal 'badge'", function() {
        var badgeObj = {
            id: "001",
            name: "Highest Rated Song",
            badgeType: "empty"
          },
          badge = new EDBadge( badgeObj );

        expect( badge.badgeType )
          .to.be.a( "string" )
          .to.equal( "empty" );

      });
    });

    suite( "Type Property", function() {
      test( "get static type", function() {
        expect( EDBadge.TYPE )
          .to.equal( "badge" );

      });
    });
  });
})( window, document, window.System, window.sinon, window.chai.expect );
