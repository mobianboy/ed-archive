/*eslint-env mocha*/
/*global suite, test, console*/
( function( win, doc, System, sinon, expect ) {
  "use strict";

  suite( "EDWebSocket", function() {
    var EDWebSocket;

    suiteSetup( function( done ) {
      System.import( "domain/ed/connection/EDWebSocket" )
        .then( function( imported ) {
          EDWebSocket = imported.default;
          done();
        }, function( error ) {
          console.warn( "Could not import 'EDWebSocket' for testing: ", error.message );
          console.error( error.stack );
          done( error );
        }
      );
    });

    // Start Testing
    suite( "EDWebSocket creation", function() {
      test( "starts with appropriate properties & functions", function() {
        var socket = new EDWebSocket();

        expect( socket )
          .to.have.property( "isAuthenticated" )
          .that.equals( false );

        expect( socket.authenticate )
          .to.be.a( "function" );

        expect( socket.send )
          .to.be.a( "function" );

        expect( socket.request )
          .to.be.a( "function" );

      });
    });

    suite( "authenticate method", function() {
      test.skip( "calls the request method on the edWebSocket instance", function( done ) {
        var edSocket = new EDWebSocket(),
          authenticateSpy = sinon.spy( edSocket, "authenticate" );

        edSocket.authenticate( "intdev@eardish.com", "intdevpass" )
          .then(function( response ) {
            authenticateSpy.restore();
            done();
          });

        expect( authenticateSpy )
          .to.have.callCount( 1 );
      });

      test.skip( "returns a promise", function( done ) {
        var edSocket = new EDWebSocket(),
          authPromise;

        authPromise = edSocket.authenticate( "intdev@eardish.com", "intdevpass" )
          .then(function( response ) {
            done();
            return response;
          });

        // needs more testing...
        expect( authPromise )
          .to.be.an( "object" );
      });
    });

    suite.skip( "send Method", function() {
      test( "calls the method", function( done ) {
        var socket = new EDWebSocket(),
          socketData = {
            action: {
              route: "string",
              priority: "string"
            }
          },
          sendSpy;

        sendSpy = sinon.spy( socket, "send" );
        socket.send( socketData );

        expect( sendSpy )
          .to.have.callCount( 1 );

        sendSpy.restore();
        done();
      });
    });

    suite.skip( "request Method", function() {
      test( "calls the method", function( done ) {
        var socket = new EDWebSocket(),
          socketData = {
            auth: {
              email: "intdev@eardish.com",
              password: "intdevpass"
            }
          },
          requestSpy;

        requestSpy = sinon.spy( socket, "request" );
        socket.request( socketData );

        expect( requestSpy )
          .to.have.callCount( 1 );

        requestSpy.restore();
        done();
      });
    });
  });
})( window, document, window.System, window.sinon, window.chai.expect );
