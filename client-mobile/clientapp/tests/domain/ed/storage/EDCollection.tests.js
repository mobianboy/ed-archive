/*global suite, suiteSetup, test, console*/
( function( win, doc, System, sinon, expect ) {
  "use strict";

  suite( "EDCollection", function() {
    var EDCollection, datalistSym;

    suiteSetup( function( done ) {
      System.import( "domain/ed/storage/EDCollection" )
      .then( function( imported ) {
        EDCollection = imported.default;

        datalistSym = Object.getOwnPropertySymbols( new EDCollection( "profile", [ 7, 12, 99, 44 ] ) )[0];

        done();
      }, function( error ) {
        console.warn( "Could not import 'EDCollection' for testing: ", error.message );
        console.error( error.stack );
        done( error );
      });
    });

// Tests begin
    suite( "Properties", function() {
      suite( "ids", function() {
        test( "cannot be set via \"ids\" property", function() {
          var edc = new EDCollection( "profile", [ 7, 12, 99, 44 ]),
            setId = function() {
              edc.ids = [ 1, 2, 4 ];
            };
          expect( setId )
            .to.throw( TypeError );
        });
      });

      suite( "type", function() {
        test( "cannot be set via \"type\" property", function() {
          var edc = new EDCollection( "profile", [ 7, 12, 99, 44 ]),
            setType = function() {
              edc.type = "profile" ;
            };
          expect( setType )
            .to.throw( TypeError );
        });
      });

      suite( "datalist", function() {
        test( "is a copy of ids parameter", function() {
          var edc = new EDCollection( "profile", [ 7, 12, 99, 44 ] );

          expect( edc[ datalistSym ] )
            .to.include( 7, 12, 99, 44 );
        });
      });
    });

    suite( "Methods", function() {
      suite( "get( index )", function() {
        test( "returns a promise that resolves to the data for the id at the given index", function( done ) {
          var edc = new EDCollection( "profile", [ 7, 12, 99, 44 ]),
            getFxn = edc.get( 1 );

          expect( getFxn )
            .to.be.an.instanceof( Promise );

          done();
        });
      });

      suite( "getRange( indexFrom, indexTo )", function() {
        test( "if indexFrom is not specified, default value is 0", function() {
          var edc = new EDCollection( "profile", [ 7 ]),
            getRangeFxn = edc.getRange();

          expect( getRangeFxn )
            .to.have.property( "length" )
            .that.equals( 1 ) ;
        });

        test( "if indexTo is not specified, getRange will extract to end of array", function() {
          var edc = new EDCollection( "profile", [ 7, 12, 99, 44 ]),
            getRangeFxn = edc.getRange( 1 );

          expect( getRangeFxn )
            .to.have.property( "length" )
            .that.equals( 3 ) ;
        });

        test( "returns an array of promises for ids in the range", function() {
          var edc = new EDCollection( "profile", [ 7, 12, 99, 44 ]),
            getRangeFxn = edc.getRange( 1, 3 );

          expect( getRangeFxn )
            .to.have.property( "length" )
            .that.equals( 2 ) ;

          expect( getRangeFxn[0] )
            .to.be.an.instanceof( Promise );

          expect( getRangeFxn[1] )
            .to.be.an.instanceof( Promise );
        });
      });

      suite( "getAll()", function() {
        test( "returns an array of promises for all items in the this.ids array", function() {
          var edc = new EDCollection( "profile", [ 7, 12, 99, 44 ]),
            getAllFxn = edc.getAll();

          expect( getAllFxn )
            .to.have.property( "length" )
            .that.equals( 4 ) ;

          expect( getAllFxn[0] )
            .to.be.an.instanceof( Promise );

          expect( getAllFxn[1] )
            .to.be.an.instanceof( Promise );

          expect( getAllFxn[2] )
            .to.be.an.instanceof( Promise );

          expect( getAllFxn[3] )
            .to.be.an.instanceof( Promise );
        });
      });
    });
  });
})( window, document, window.System, window.sinon, window.chai.expect );
