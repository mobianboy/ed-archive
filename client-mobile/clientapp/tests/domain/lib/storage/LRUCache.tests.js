/*eslint-env mocha*/
/*global suite, test, console*/
( function( win, doc, System, sinon, expect ) {
  "use strict";

  suite( "LRUCache", function() {
    var LRUCache, LRUNode,
      tail, head, keyMap, size;

    suiteSetup( function( done ) {
      System.import( "domain/lib/storage/LRUCache" )
        .then( function( imported ) {
          var tmp;

          LRUCache = imported.default;
          LRUNode = imported.LRUNode;

          tmp = Object.getOwnPropertySymbols( new LRUCache( 3 ) );

          keyMap = tmp[ 0 ];
          size = tmp[ 1 ];
          tail = tmp[ 2 ];
          head = tmp[ 3 ];

          done();
        }, function( error ) {
          console.warn( "Could not import 'LRUCache' for testing: ", error.message );
          console.error( error.stack );
          done( error );
        });
    });

    // Start Test
    suite( "LRUCache creation", function() {
      test( "starts with appropriate properties and symbols", function() {
        var cache = new LRUCache( 10 );

        expect( cache )
          .to.have.property( "limit" )
          .to.be.a( "number" )
          .that.equals( 10 );

        expect( cache )
          .to.have.property( "size" )
          .to.be.a( "number" )
          .that.equals( 0 );

        expect( cache )
          .to.have.property( size )
          .to.be.a( "number" )
          .to.be.equal( 0 );

        expect( cache )
          .to.have.property( keyMap )
          .to.be.a( "object" );

        expect( cache )
          .to.have.property( head )
          .to.be.equal( null );

        expect( cache )
          .to.have.property( tail )
          .to.be.equal( null );
      });
    });

    suite( "Properties", function() {
      test( "Adding an item increases size", function() {
        var cache = new LRUCache( 5 );

        expect( cache )
          .to.have.property( "size" )
          .that.equals( 0 );

        cache.set( "one", 1 );

        expect( cache )
          .to.have.property( "size" )
          .that.equals( 1 );
      });

      test( "Re-setting an item does not increase size", function() {
        var cache = new LRUCache( 5 );

        expect( cache )
          .to.have.property( "size" )
          .that.equals( 0 );

        cache.set( "one", 1 );

        expect( cache )
          .to.have.property( "size" )
          .that.equals( 1 );

        cache.set( "one", "one" );

        expect( cache )
          .to.have.property( "size" )
          .that.equals( 1 );
      });

      test( "Removing an item decreases size", function() {
        var cache = new LRUCache( 5 );

        expect( cache )
          .to.have.property( "size" )
          .that.equals( 0 );

        cache.set( "one", 1 );
        cache.set( "two", 2 );

        expect( cache )
          .to.have.property( "size" )
          .that.equals( 2 );

        cache.remove( "one" );

        expect( cache )
          .to.have.property( "size" )
          .that.equals( 1 );
      });

      test( "when at limit, shift is called, size stays the same", function() {
        var cache = new LRUCache( 3 ),
          shiftSpy = sinon.spy( cache, "shift" );

        expect( cache )
          .to.have.property( "limit" )
          .that.equals( 3 );

        expect( cache )
          .to.have.property( "size" )
          .that.equals( 0 );

        cache.set( "one", 1 );
        cache.set( "two", 2 );
        cache.set( "three", 3 );

        expect( cache )
          .to.have.property( "size" )
          .that.equals( 3 );

        expect( shiftSpy )
          .to.have.callCount( 0 );

        cache.set( "four", 4 );

        expect( cache )
          .to.have.property( "size" )
          .that.equals( 3 );

        expect( shiftSpy )
          .to.have.callCount( 1 );

        shiftSpy.restore();
      });
    });

    // TODO fix names, properly group in suites
    suite( "LRUCache Properties and Methods", function() {
      test( "Setting for initial object", function() {
        var cache = new LRUCache( 10 );

        cache.set( "A", 1 );

        expect( cache[ keyMap ].A )
          .to.have.property( "key" )
          .to.be.a( "string" )
          .that.equals( "A" );

        expect( cache[ keyMap ].A )
          .to.have.property( "data" )
          .to.be.a( "number" )
          .that.equals( 1 );

        expect( cache[ keyMap ].A )
          .to.have.property( "newer" )
          .and.equals( null );

        expect( cache[ keyMap ].A )
          .to.have.property( "older" )
          .and.equals( null );
      });

      test( "Setting for non-initial object", function() {
        var cache = new LRUCache( 10 );

        cache.set( "A", 1 );

        cache.set( "B", 2 );

        expect( cache[ keyMap ].B )
          .to.have.property( "key" )
          .to.be.a( "string" )
          .that.equals( "B" );

        expect( cache[ keyMap ].B )
          .to.have.property( "data" )
          .to.be.a( "number" )
          .that.equals( 2 );

        expect( cache[ keyMap ].B )
          .to.have.property( "newer" )
          .and.equals( null );

        expect( cache[ keyMap ].B )
          .to.have.property( "older" )
          .to.be.an.instanceof( LRUNode )
          .that.equals( cache[ head ] );
      });

      test( "Setting returns null when not at limit", function() {
        var cache = new LRUCache( 10 );

        cache.set( "A", 1 );
        cache.set( "B", 2 );

        expect( cache.set( "C", 3 ) )
          .to.equal( null );
      });

      test( "Get returns the object stored at the key", function() {
        var cache = new LRUCache( 10 );

        cache.set( "A", 1 );
        cache.set( "B", 2 );
        cache.set( "C", 3 );

        expect( cache.get( "B" ) )
          .to.be.a( "number" )
          .that.equals( 2 );

        expect( cache[ tail ].key )
          .to.equal( "B" );
      });

      test( "Peek to return the data object", function() {
        var cache = new LRUCache( 10 );

        cache.set( "A", 1 );
        cache.set( "B", 2 );
        cache.set( "C", 3 );

        expect( cache.peek( "B" ) )
          .to.be.an( "number" )
          .and.equals( 2 );

        expect( cache[ tail ].key )
          .to.equal( "C" )
          .and.to.not.equal( "B" );
      });

      test( "Peek to return null when there is no key", function() {
        var cache = new LRUCache( 10 );

        expect( cache.peek( "B" ) )
          .to.be.equals( null );
      });

      test( "Has returns boolean if item exists", function() {
        var cache = new LRUCache( 10 );

        cache.set( "A", 1 );

        expect( cache.has( "A" ) )
          .to.be.a( "boolean" )
          .that.equals( true );

        expect( cache.has( "B" ) )
          .to.be.a( "boolean" )
          .that.equals( false );
      });

      test( "Shift, pops off the head node and returns removed node", function() {
        var cache = new LRUCache( 10 );

        cache.set( "A", 1 );
        cache.set( "B", 2 );
        cache.set( "C", 3 );

        expect( cache.shift() )
          .to.be.a( "number" )
          .to.equal( 1 );

        expect( cache.has( "A" ) )
          .to.be.a( "boolean" )
          .that.equals( false );

        expect( cache[ head ] )
          .to.be.a.instanceof( LRUNode )
          .and.to.have.property( "key" )
          .that.equals( "B" );
      });

      test( "Remove, takes node out of cache", function() {
        var cache = new LRUCache( 10 );

        cache.set( "B", 2 );

        expect( cache[ head ] )
          .to.have.property( "key", "B" );

        expect( cache.remove( "B" ) )
          .to.equal( 2 );

        expect( cache.has( "B" ) )
          .to.be.a( "boolean" )
          .that.equals( false );
      });

      test( "Remove returns null if key is not in cache", function() {
        var cache = new LRUCache( 10 );

        cache.set( "B", 2 );

        expect( cache.remove( "C" ) )
          .to.equal( null );
      });

      test( "Clear", function() {
        var cache = new LRUCache( 10 );

        cache.set( "A", 1 );
        cache.set( "B", 2 );
        cache.set( "C", 3 );
        cache.clear();

        expect( cache[ keyMap ] )
          .to.be.empty;

        expect( cache[ head ] )
          .to.equal( cache[ tail ] );

        expect( cache[ size ] )
          .to.equal( 0 );
      });

      test( "Keys returns an array of stored keys in the map", function() {
        var cache = new LRUCache( 10 );

        cache.set( "A", 1 );
        cache.set( "B", 2 );
        cache.set( "C", 3 );

        expect( cache.keys() )
          .to.be.an( "array" )
          .that.deep.equals( [ "A", "B", "C" ] );
      });

      test( "forEach iterates in order", function( done ) {
        var cache = new LRUCache( 10 ),
          index = 0,
          order = [
            {
              key: "A",
              data: 1
            },
            {
              key: "B",
              data: 2
            },
            {
              key: "C",
              data: 3
            }
          ];

        order.forEach( function( keyValue ) {
          cache.set( keyValue.key, keyValue.data );
        });

        cache.forEach( function( key, data ) {
          expect( key )
            .to.be.a( "string" )
            .that.equals( order[ index ].key );

          expect( data )
            .to.be.a( "number" )
            .that.equals( order[ index ].data );

          index += 1;

          if ( index === 3 ) {
            expect( this )
              .to.equal( undefined );
            done();
          }
        });
      });

      test( "forEach works in reverse", function( done ) {
        var cache = new LRUCache( 10 ),
          index = 2,
          order = [
            {
              key: "A",
              data: 1
            },
            {
              key: "B",
              data: 2
            },
            {
              key: "C",
              data: 3
            }
          ];

        order.forEach( function( keyValue ) {
          cache.set( keyValue.key, keyValue.data );
        });

        cache.forEach( function( key, data ) {
          expect( key )
            .to.be.a( "string" )
            .that.equals( order[ index ].key );

          expect( data )
            .to.be.a( "number" )
            .that.equals( order[ index ].data );

          index -= 1;

          if ( index === -1 ) {
            expect( this )
              .to.equal( undefined );
            done();
          }
        }, true );
      });

      test( "toArray to return an array containing the data in order", function() {
        var cache = new LRUCache( 10 );

        cache.set( "A", 1 );
        cache.set( "B", 2 );
        cache.set( "C", 3 );

        expect( cache.toArray() )
          .to.be.an( "array" )
          .that.deep.equals([
            {
              key: "A",
              data: 1
            },
            {
              key: "B",
              data: 2
            },
            {
              key: "C",
              data: 3
            }
          ]);
      });

      test( "toString to return a string on the objects", function() {
        var cache = new LRUCache( 10 );

        cache.set( "A", 1 );
        cache.set( "B", 2 );
        cache.set( "C", 3 );

        expect( cache.toString() )
          .to.be.a( "string" )
          .that.equals( "A:1 < B:2 < C:3" );
      });
    });
  });
})( window, document, window.System, window.sinon, window.chai.expect );
