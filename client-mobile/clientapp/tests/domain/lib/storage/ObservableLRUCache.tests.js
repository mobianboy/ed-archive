/*eslint-env mocha*/
/*global suite, test, console*/
(function( win, doc, System, sinon, expect ) {
  "use strict";

  suite( "ObservableLRUCache", function() {
    var ObservableLRUCache,
      keyMap, size, tail, head,
      observers;

    this.timeout( 5000 );

    suiteSetup(function( done ) {
      System.import( "domain/lib/storage/ObservableLRUCache" )
        .then(function( imported ) {
          var syms;

          ObservableLRUCache = imported.default;

          syms = Object.getOwnPropertySymbols( new ObservableLRUCache( 3 ) );

          keyMap = syms[ 0 ];
          size = syms[ 1 ];
          tail = syms[ 2 ];
          head = syms[ 3 ];
          observers = syms[ 4 ];

          done();
        }, function( error ) {
          console.warn( "Could not import 'ObservableLRUCache' for testing: ", error.message );
          console.error( error.stack );
          done( error );
        });
    });

    // Start Test
    suite( "Inherits from LRUCache", function() {
      var cache;

      setup(function() {
        cache = new ObservableLRUCache( 10 );
      });

      teardown(function() {
        cache = null;
      });

      test( "property limit", function() {
        expect( cache )
          .to.have.property( "limit" )
          .to.be.a( "number" )
          .that.equals( 10 );
      });

      test( "property size", function() {
        expect( cache )
          .to.have.property( "size" )
          .to.be.a( "number" )
          .that.equals( 0 );
      });

      test( "symbol @@keyMap", function() {
        expect( cache )
          .to.have.property( keyMap )
          .to.be.a( "object" );
      });

      test( "symbol @@head", function() {
        expect( cache )
          .to.have.property( head )
          .to.be.equal( null );
      });

      test( "symbol @@tail", function() {
        expect( cache )
          .to.have.property( tail )
          .to.be.equal( null );
      });

      test( "method set", function() {
        expect( cache )
          .to.have.property( "set" )
          .that.is.a( "function" );
      });

      test( "method get", function() {
        expect( cache )
          .to.have.property( "get" )
          .that.is.a( "function" );
      });

      test( "method peek", function() {
        expect( cache )
          .to.have.property( "peek" )
          .that.is.a( "function" );
      });

      test( "method has", function() {
        expect( cache )
          .to.have.property( "has" )
          .that.is.a( "function" );
      });

      test( "method shift", function() {
        expect( cache )
          .to.have.property( "shift" )
          .that.is.a( "function" );
      });

      test( "method remove", function() {
        expect( cache )
          .to.have.property( "remove" )
          .that.is.a( "function" );
      });

      test( "method clear", function() {
        expect( cache )
          .to.have.property( "clear" )
          .that.is.a( "function" );
      });

      test( "method keys", function() {
        expect( cache )
          .to.have.property( "keys" )
          .that.is.a( "function" );

        expect( cache.keys() )
          .to.be.an.instanceof( Array );
      });

      test( "method forEach", function() {
        expect( cache )
          .to.have.property( "forEach" )
          .that.is.a( "function" );
      });

      test( "method toArray", function() {
        expect( cache )
          .to.have.property( "toArray" )
          .that.is.a( "function" );

        expect( cache.toArray() )
          .to.be.an.instanceof( Array );
      });

      test( "method toString", function() {
        expect( cache )
          .to.have.property( "toString" )
          .that.is.a( "function" );

        expect( cache.toString() )
          .to.be.a( "string" );
      });

      test( "default @@iterator", function() {
        expect( cache )
          .to.have.property( Symbol.iterator )
          .that.is.a( "function" );
      });

      test( "iterator reverse", function() {
        expect( cache )
          .to.have.property( "reverse" )
          .that.is.a( "function" );
      });

      test( "iterator reverseOf", function() {
        expect( cache )
          .to.have.property( "reverseOf" )
          .that.is.a( "object" )
          .and.has.property( Symbol.iterator )
          .that.is.a( "function" );
      });
    });

    suite( "Own Properties & Symbols", function() {
      var cache;

      setup(function() {
        cache = new ObservableLRUCache( 3 );
      });

      teardown(function() {
        cache = null;
      });

      test( "@@observers, has object map for holding observer functions", function() {
        expect( cache )
          .to.have.property( observers )
          .that.is.an( "object" )
          .and.deep.equals( {} );
      });
    });

    suite( "Methods", function() {
      suite( "observe", function() {
        var cache;

        setup(function() {
          cache = new ObservableLRUCache( 3 );

          cache.set( "one", 1 );
          cache.set( "two", 2 );
          cache.set( "three", 3 );
        });

        teardown(function() {
          cache = null;
        });

        test( "observe adds callback to Symbol( observers )", function() {
          var observeFn = function() {};

          cache.observe( "one", observeFn );

          expect( cache )
            .to.have.property( observers )
            .that.has.property( "one" )
            .that.contains( observeFn );
        });

        test( "observe functions get an array of changes", function( done ) {
          var observeFn = function( changeList ) {
            expect( changeList )
              .to.be.an.instanceof( Array );

            done();
          };

          cache.observe( "one", observeFn );
          cache.set( "one", "one" );
        });

        test( "changes in the list of changes look like change objects used in Object.observe spec", function( done ) {
          var observeFn = function( changeList ) {
            var change = changeList[ 0 ];

            expect( change )
              .to.be.a( "object" );

            expect( change )
              .to.have.property( "name" )
              .that.equals( "one" );

            expect( change )
              .to.have.property( "object" )
              .that.equals( "setOne" );

            expect( change )
              .to.have.property( "type" )
              .that.equals( "set" );

            expect( change )
              .to.have.property( "oldValue" )
              .that.equals( 1 );

            done();
          };

          cache.observe( "one", observeFn );
          cache.set( "one", "setOne" );
        });

        test( "can specify, only observe 'set' operations", function() {
          var observeFn = sinon.spy();

          cache.observe( "one", observeFn, [ "set" ] );
          cache.remove( "one" );

          expect( observeFn )
            .to.have.callCount( 0 );

          cache.set( "one", "one" );

          expect( observeFn )
            .to.have.callCount( 1 );
        });

        test( "can specify, only observe 'remove' operations", function() {
          var observeFn = sinon.spy();

          cache.observe( "one", observeFn, [ "remove" ] );
          cache.set( "one", "one" );

          expect( observeFn )
            .to.have.callCount( 0 );

          cache.remove( "one" );

          expect( observeFn )
            .to.have.callCount( 1 );
        });

        test( "calling set fires 'set' observers", function() {
          var setObserver = sinon.spy(),
            removeObserver = sinon.spy();

          cache.observe( "one", setObserver, [ "set" ]);
          cache.observe( "one", removeObserver, [ "remove" ]);

          cache.set( "one", "one" );

          expect( setObserver )
            .to.have.callCount( 1 );

          expect( removeObserver )
            .to.have.callCount( 0 );
        });

        test( "calling remove fires 'remove' observers", function() {
          var setObserver = sinon.spy(),
            removeObserver = sinon.spy();

          cache.observe( "one", setObserver, [ "set" ]);
          cache.observe( "one", removeObserver, [ "remove" ]);

          cache.remove( "one" );

          expect( setObserver )
            .to.have.callCount( 0 );

          expect( removeObserver )
            .to.have.callCount( 1 );
        });

        test( "calling shift fires 'remove' for observers of 'head' item", function() {
          var setObserver = sinon.spy(),
            removeObserver = sinon.spy(),
            shiftSpy = sinon.spy( cache, "shift" );

          cache.observe( "one", setObserver, [ "set" ]);
          cache.observe( "one", removeObserver, [ "remove" ]);

          cache.set( "four", 4 );

          expect( setObserver )
            .to.have.callCount( 0 );

          expect( shiftSpy )
            .to.have.callCount( 1 );

          expect( removeObserver )
            .to.have.callCount( 1 );

          shiftSpy.restore();
        });

        test( "calling clear fires 'remove' on all observers", function() {
          var removeObserver = sinon.spy();

          cache.observe( "one", removeObserver, [ "remove" ]);
          cache.observe( "two", removeObserver, [ "remove" ]);
          cache.observe( "three", removeObserver, [ "remove" ]);

          cache.clear();

          expect( cache.size )
            .to.equal( 0 );

          expect( removeObserver )
            .to.have.callCount( 3 );
        });

        test( "observers can be used to put items back into the cache when cleared", function() {
          var removeObserver = sinon.spy(function( changes ) {
            changes.forEach(function( change ) {
              cache.set( change.name, change.oldValue );
            });
          });

          cache.observe( "one", removeObserver, [ "remove" ]);

          cache.clear();

          expect( removeObserver )
            .to.have.callCount( 1 );

          expect( cache )
            .to.have.property( "size", 1 );

          expect( cache.has( "one" ) ).to.equal( true );
        });

        test( "symbol ObservableLRUCache.OBSERVE_ALL can be used to watch all changes", function() {
          var allObserver = sinon.spy();

          cache.observe( ObservableLRUCache.OBSERVE_ALL, allObserver );

          cache.remove( "one" );
          cache.set( "two", "two" );
          cache.set( "five", 5 );

          expect( allObserver )
            .to.have.callCount( 3 );
        });

        test( "an ObservableLRUCache.OBSERVE_ALL observer can be removed", function() {
          var allObserver = sinon.spy();

          cache.observe( ObservableLRUCache.OBSERVE_ALL, allObserver );

          cache.remove( "one" );
          cache.set( "two", "two" );

          cache.unobserve( ObservableLRUCache.OBSERVE_ALL, allObserver );

          cache.set( "five", 5 );

          expect( allObserver )
            .to.have.callCount( 2 );
        });

        test( "an ObservableLRUCache.OBSERVE_ALL observer can be configured: set", function() {
          var allSetObserver = sinon.spy();

          cache.observe( ObservableLRUCache.OBSERVE_ALL, allSetObserver, [ "set" ]);

          cache.set( "two", "two" );
          cache.remove( "one" );
          cache.remove( "two" );

          expect( allSetObserver )
            .to.have.callCount( 1 );
        });

        test( "an ObservableLRUCache.OBSERVE_ALL observer can be configured: remove", function() {
          var allRemoveObserver = sinon.spy();

          cache.observe( ObservableLRUCache.OBSERVE_ALL, allRemoveObserver, [ "remove" ]);

          cache.set( "two", "two" );
          cache.remove( "one" );
          cache.remove( "two" );

          expect( allRemoveObserver )
            .to.have.callCount( 2 );
        });
      });

      suite( "unobserve", function() {
        var cache;

        setup(function() {
          cache = new ObservableLRUCache( 3 );

          cache.set( "one", 1 );
          cache.set( "two", 2 );
          cache.set( "three", 3 );
        });

        teardown(function() {
          cache = null;
        });

        test( "unobserve, removes observer functions", function() {
          var observer1 = function() {},
            observer2 = function() {};

          cache.observe( "one", observer1 );
          cache.observe( "one", observer2 );

          expect( cache[ observers ] )
            .to.have.property( "one" )
            .that.deep.equals([ observer1, observer2 ]);

          cache.unobserve( "one", observer1 );

          expect( cache[ observers ] )
            .to.have.property( "one" )
            .that.deep.equals([ observer2 ]);
        });

        test( "unobserve, removes key from Symbol( observers ) if last observer is removed", function() {
          var observeFn = function() {};

          cache.observe( "one", observeFn );

          expect( cache[ observers ] )
            .to.have.property( "one" )
            .that.contains( observeFn );

          cache.unobserve( "one", observeFn );

          expect( cache[ observers ] )
            .to.not.have.property( "one" );
        });
      });
    });
  });
})( window, document, window.System, window.sinon, window.chai.expect );
