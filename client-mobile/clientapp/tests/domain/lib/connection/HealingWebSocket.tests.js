/*eslint-env mocha*/
/*global suite, test, console*/
( function( win, doc, System, sinon, expect ) {
  "use strict";

  suite( "HealingWebSocket", function() {
    var HealingWebSocket,
      EventEmitter,
      socketSym,
      healSym;

    this.timeout( 5000 );

    suiteSetup( function( done ) {
      Promise.all([
        System.import( "domain/lib/connection/HealingWebSocket" ),
        System.import( "domain/lib/event/EventEmitter" )
      ])
        .then( function( imported ) {
          var hws;
          HealingWebSocket = imported[ 0 ].default;
          EventEmitter = imported[ 1 ].default;

          hws = new HealingWebSocket( "wss://echo.websocket.org" );
          socketSym = Object.getOwnPropertySymbols( hws )[ 1 ];
          healSym = Object.getOwnPropertySymbols( Object.getPrototypeOf( hws ))[ 0 ];

          done();
        }, function( error ) {
          console.warn( "Could not import 'HealingWebSocket' for testing: ", error.message );
          console.error( error.stack );
          done( error );
        });
    });

    // Start Tests
    suite( "websocket creation", function() {
      test( "starts in non-open state when created", function() {
        var hws = new HealingWebSocket( "wss://echo.websocket.org" );

        expect( hws )
          .to.have.property( "readyState" )
          .that.equals( WebSocket.CONNECTING );

        expect( hws )
          .to.have.property( "isOpen" )
          .that.equals( false );
      });

      test( "when websocket is open, isOpen property is true", function( done ) {
        var hws = new HealingWebSocket( "wss://echo.websocket.org" );

        hws.on( "open", function() {
          expect( hws )
            .to.have.property( "isOpen" )
            .that.equals( true );

          done();
        });
      });

      test( "when websocket is closed, new websocket is opened", function( done ) {
        var hws = new HealingWebSocket( "wss://echo.websocket.org" );

        hws.on( "close", function() {
          expect( hws )
            .to.have.property( "isOpen" )
            .that.equals( true );
        });

        done();
      });
    });

    suite( "properties", function() {
      test( "can get the value of the isOpen boolean", function() {
        var hws = new HealingWebSocket( "wss://echo.websocket.org" );

        expect( hws )
          .to.have.property( "isOpen" )
          .that.is.a( "boolean" );
      });

      test( "can get the value of websocket's ready state", function() {
        var hws = new HealingWebSocket( "wss://echo.websocket.org" );

        expect( hws )
          .to.have.property( "readyState" );
      });

      test( "can get the type of binary data transmitted by the connection", function() {
        var hws = new HealingWebSocket( "wss://echo.websocket.org" );

        expect( hws )
          .to.have.property( "binaryType" );
      });

      test( "can set websocket's binary type via binaryType property", function() {
        var hws = new HealingWebSocket( "wss://echo.websocket.org" );

        hws[ socketSym ].binaryType = "blob";

        expect( hws[ socketSym ] )
         .to.have.property( "binaryType" )
         .that.equals( "blob" );
      });

      test( "can get bytes of data in queue via bufferedAmount property", function() {
        var hws = new HealingWebSocket( "wss://echo.websocket.org" );

        expect( hws[ socketSym ] )
          .to.have.property( "bufferedAmount" )
          .that.is.a( "number" );
      });

      test( "can get extensions selected by the server", function() {
        var hws = new HealingWebSocket( "wss://echo.websocket.org" );

        expect( hws )
          .to.have.property( "extensions" )
          .and.to.equal( "" );
      });

      test( "can get name of the sub-protocol server selected", function() {
        var hws = new HealingWebSocket( "wss://echo.websocket.org" );

        expect( hws )
          .to.have.property( "protocol" )
          .that.is.a( "string" );
      });

      test( "can get websocket's absolute url", function() {
        var hws = new HealingWebSocket( "wss://echo.websocket.org" );

        expect( hws )
          .to.have.property( "url" );
      });
    });

    suite( "methods", function() {
      suite( "close method", function() {
        test( "calling close disconnects the websocket", function() {
          var hws = new HealingWebSocket( "wss://echo.websocket.org" );

          hws.on( "close", function() {
            expect( hws )
              .to.have.property( "readyState" )
              .that.equals( WebSocket.CLOSED );

            expect( hws )
              .to.have.property( "isOpen" )
              .that.equals( false );
          });
        });
      });

      suite( "send method", function() {
        test( "can send strings", function( done ) {
          var hws = new HealingWebSocket( "wss://echo.websocket.org" ),
            strData = "string data";

          hws.on( "message", function( event ) {
            expect( event.data )
              .to.be.a( "string" )
              .and.equal( strData );

            done();
          });

          hws.send( strData );
        });

        test( "can send blob data", function( done ) {
          var hws = new HealingWebSocket( "wss://echo.websocket.org" ),
            fileReader = new FileReader(),
            blobString = "<a id=\"a\"><b id=\"b\">oh my blob</b></a>",
            blobData = new Blob( [ blobString ], {
              type: "text/html"
            });

          hws.on( "message", function( event ) {
            expect( event.data )
              .to.be.an.instanceOf( Blob )
              .and.to.have.property( "size", blobData.size );

            fileReader.readAsText( event.data );
          });

          fileReader.addEventListener( "loadend", function( event ) {
            expect( fileReader.result )
              .to.be.a( "string" )
              .and.to.equal( blobString );

            done();
          });

          hws.send( blobData );
        });

        test.skip( "can send array buffer data", function( done ) {
          var hws = new HealingWebSocket( "wss://echo.websocket.org" ),
            arrayBufferLength = new ArrayBuffer( 256 );

          hws.binaryType = "arraybuffer";

          hws.on( "message", function( event ) {
            expect( event.data )
              .to.be.an.instanceOf( ArrayBuffer )
              .and.equal( arrayBufferLength );

            done();
          });

          hws.send( arrayBufferLength );
        });

        test( "can send other data types", function( done ) {
          var hws = new HealingWebSocket( "wss://echo.websocket.org" ),
            objectData = {
              artist: {
                name: "Ryan",
                genre: "indie hipster in a coffeeshop"
              }
            };

          hws.on( "message", function( event ) {
            expect( event.data )
              .to.equal( JSON.stringify( objectData ));

            done();
          });

          hws.send( objectData );
        });

        test( "can receive string data via message event", function( done ) {
          var hws = new HealingWebSocket( "wss://echo.websocket.org" ),
            strData = "string data";

          hws.on( "message", function( eventName ) {
            expect( eventName.data )
              .to.be.a( "string" )
              .and.equal( strData );

            done();
          });

          hws.send( strData );
        });

        test( "can receive blob data via message event", function( done ) {
          var hws = new HealingWebSocket( "wss://echo.websocket.org" ),
            fileReader = new FileReader(),
            blobString = "<a id=\"a\"><b id=\"b\">oh my blob</b></a>",
            blobData = new Blob( [ blobString ], {
              type: "text/html"
            });

          hws.on( "message", function( event ) {
            expect( event.data )
              .to.be.an.instanceOf( Blob )
              .and.to.have.property( "size", blobData.size );

            fileReader.readAsText( event.data );
          });

          fileReader.addEventListener( "loadend", function( event ) {
            expect( fileReader.result )
              .to.be.a( "string" )
              .and.to.equal( blobString );

            done();
          });

          hws.send( blobData );
        });

        test.skip( "can receive array buffer via message event", function( done ) {
          var hws = new HealingWebSocket( "wss://echo.websocket.org" ),
            arrayBuffer = new ArrayBuffer( 256 );

          hws.binaryType = "arraybuffer";

          hws.on( "message", function( event ) {
            expect( event.data )
              .to.be.an.instanceOf( ArrayBuffer )
              .and.equal( arrayBuffer );

            done();
          });

          hws.send( arrayBuffer );
        });
      });

      suite( "Symbol(heal) method", function() {
        test( "heal should be called if socket is closed and new data is sent", function( done ) {
          var hws = new HealingWebSocket( "wss://echo.websocket.org" ),
            healSpy = sinon.spy( hws, healSym ),
            sendData = "Should Re-open Socket";

          hws.once( "open", function() {
            hws.close();
            hws.send( sendData );

            hws.once( "open", function() {
              expect( healSpy )
                .to.have.callCount( 1 )
                .and.to.have.been.calledWith( sendData );

              healSpy.restore();
              done();
            });
          });
        });

        test( "heal creates a new WebSocket internally", function( done ) {
          var hws = new HealingWebSocket( "wss://echo.websocket.org" ),
            oldSocket = hws[ socketSym ];

          hws.once( "open", function() {
            hws.close();

            expect( oldSocket )
              .to.have.property( "readyState" )
              .that.is.within( WebSocket.CLOSING, WebSocket.CLOSED );

            hws.send( "Reopen Socket" );

            expect( hws )
              .to.have.property( socketSym )
              .to.not.equal( oldSocket );

            done();
          });
        });
      });

      suite( "should inherit EventEmitter's methods", function() {
        test( "should be an instance of EventEmitter", function() {
          var hws = new HealingWebSocket( "wss://echo.websocket.org" );

          expect( hws )
            .to.be.an.instanceof( EventEmitter );
        });

        test( "should inherit on", function() {
          var hws = new HealingWebSocket( "wss://echo.websocket.org" );

          expect( hws )
            .to.have.property( "on" )
            .that.is.a( "function" );
        });

        test( "should inherit off", function() {
          var hws = new HealingWebSocket( "wss://echo.websocket.org" );

          expect( hws )
            .to.have.property( "off" )
            .that.is.a( "function" );
        });

        test( "should inherit once", function() {
          var hws = new HealingWebSocket( "wss://echo.websocket.org" );

          expect( hws )
            .to.have.property( "once" )
            .that.is.a( "function" );
        });

        test( "should inherit clear", function() {
          var hws = new HealingWebSocket( "wss://echo.websocket.org" );

          expect( hws )
            .to.have.property( "clear" )
            .that.is.a( "function" );
        });

        test( "should inherit dispatch", function() {
          var hws = new HealingWebSocket( "wss://echo.websocket.org" );

          expect( hws )
            .to.have.property( "dispatch" )
            .that.is.a( "function" );
        });
      });
    });

    suite( "events", function() {
      test( "heal event fires when socket is healed", function( done ) {
        var hws = new HealingWebSocket( "wss://echo.websocket.org" ),
          oldSocket = hws[ socketSym ];

        hws.on( "heal", function( event ) {
          expect( event )
            .to.be.an.instanceof( CustomEvent )
            .to.have.property( "type", "heal" );

          expect( event )
            .to.have.property( "detail" )
            .to.deep.equal({
              oldSocket: oldSocket
            });

          done();
        });

        hws.once( "open", function() {
          hws.close();
          hws.send( "Heal should be called" );
        });
      });
    });
    // End Tests
  });
})( window, document, window.System, window.sinon, window.chai.expect );

