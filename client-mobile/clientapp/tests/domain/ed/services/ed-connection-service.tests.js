/*eslint-env mocha*/
/*global suite, test, console*/
(function( win, doc, System, sinon, expect ) {
  "use strict";

  suite( "edConnectionService", function() {
    var edConnectionService;

    suiteSetup(function( done ) {
      System.import( "domain/ed/services/ed-connection-service" )
        .then( function( imported ) {
          edConnectionService = imported.default;
          done();
        }, function( error ) {
          console.warn( "Could not import 'edConnectionService' for testing: ", error.message );
          console.error( error.stack );
          done( error );
        }
      );
    });

    // Start Testing
    suite( "edConnectionService creation", function() {
      test( "edConnectionService have appropriate properties", function() {

        expect( edConnectionService.send )
          .to.be.an( "function" );

        expect( edConnectionService.request )
          .to.be.an( "function" );

        expect( edConnectionService.formattedSend )
          .to.be.a( "function" );

        expect( edConnectionService.formattedRequest )
          .to.be.a( "function" );
      });
    });

    suite.skip( "authenticateConnection method", function() {
      test( "calls the method", function( done ) {
        var authenticateConnectionSpy = sinon.spy( edConnectionService, "authenticateConnection" );

        edConnectionService.authenticateConnection( "intdev@eardish.com", "intdevpass" )
          .then(function( response ) {
            authenticateConnectionSpy.restore();
            done();
          });

        expect( authenticateConnectionSpy )
          .to.have.callCount( 1 );
      });
    });

    suite.skip( "send method", function() {
      test( "calls the send method on the edWebSocket instance", function( done ) {
        var dataObj = {
            data: {
              id: 1
            }
          },
          sendSpy = sinon.spy( edConnectionService, "send" );

        //edConnectionService.send( "user/login", 10, dataObj );

        expect( sendSpy )
          .to.have.callCount( 1 );

        sendSpy.restore();
        done();
      });
    });

    suite( "request method", function() {
      test( "calls the request method on the edWebSocket instance", function( done ) {
        var dataObj = {
            id: 1
          },
          requestSpy = sinon.spy( edConnectionService, "request" );

        edConnectionService.request( "profile/get", 10, { data: dataObj } )
          .then(function( response ) {
            requestSpy.restore();
            done();
          });

        expect( requestSpy )
          .to.have.callCount( 1 );


      });
    });

    suite.skip( "formattedSend method", function() {
      test( "calls the send method with formatted data", function( done ) {
        var dataObj = {
            id: 1
          },
          formattedSendSpy = sinon.spy( edConnectionService, "formattedSend" );

        edConnectionService.formattedSend( { data: dataObj } );

        expect( formattedSendSpy )
          .to.have.callCount( 1 );

        formattedSendSpy.restore();
        done();
      });
    });

    suite.skip( "formattedRequest method", function() {
      test( "calls the request method with formatted data", function( done ) {
        var dataObj = {
            id: 1
          },
          formattedSendSpy = sinon.spy( edConnectionService, "formattedSend" );

        edConnectionService.formattedSend( { data: dataObj } );

        expect( formattedSendSpy )
          .to.have.callCount( 1 );

        formattedSendSpy.restore();
        done();
      });
    });

    suite( "formatDataObject method", function() {
      test( "method invocation", function() {
        var dataObj = {
            id: 1
          },
          formatDataObjectSpy = sinon.spy( edConnectionService, "formatDataObject" );

        edConnectionService.formatDataObject( { data: dataObj } );

        expect( formatDataObjectSpy )
          .to.have.callCount( 1 );

        formatDataObjectSpy.restore();
      });

      test( "returns a stringified json object", function() {
        var dataObj = {
            id: 1
          };

        expect( edConnectionService.formatDataObject( { data: dataObj } ) )
          .to.be.a( "string" );
      });
    });
  });
})( window, document, window.System, window.sinon, window.chai.expect );
