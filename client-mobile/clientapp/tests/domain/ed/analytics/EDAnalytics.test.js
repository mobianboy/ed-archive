/*eslint-env mocha*/
/*global suite, test, console*/
( function( win, doc, System, sinon, expect ) {
  "use strict";

  suite( "EDAnalytics", function() {
    var EDAnalytics;

    suiteSetup( function( done ) {
      System.import( "domain/ed/analytics/ed-analytics-service" )
        .then( function( imported ) {

          EDAnalytics = imported.default;
          done();

        }, function( error ) {
          console.warn( "Could not import 'EDAnalytics' for testing: ", error.message );
          console.error( error.stack );
          done( error );
        }
      );
    });

    // Start Testing
    suite( "EDAnalytics creation", function() {
      test("starts with appropiate properties", function(){

        expect( EDAnalytics.commonBlock )
          .to.be.an( "object" );

        expect( EDAnalytics.deviceBlock )
          .to.be.an( "object" );

        expect( EDAnalytics.sessionBlock )
          .to.be.an( "object" );

        expect( EDAnalytics.locationBlock )
          .to.be.an( "object" );

        expect( EDAnalytics.version )
          .to.be.an( "string" );

        expect( EDAnalytics.time )
          .to.be.an( "string" );

      });

    });

    suite( "Property Functions", function() {
      test("can get value of commonBlock object", function(){
        var commonObj = EDAnalytics.commonBlock;

        expect( commonObj )
          .to.be.an( "object" )
          .to.have.all.keys([
                              "device",
                              "client-version",
                              "location",
                              "time",
                              "user",
                              "view-route",
                              "view-state",
                              "session"
                            ]);
      });

      test("can get value of deviceBlock object", function(){
        var deviceObj = EDAnalytics.deviceBlock;

        //TODO use cordova plugins to grab data
        expect( deviceObj )
          .to.be.an( "object" )
          .to.have.all.keys([
                              "type",
                              "make",
                              "model",
                              "carrier",
                              "OS"
                            ]);
      });

      test("can get value of sessionBlock object", function(){
        var sessionObj = EDAnalytics.sessionBlock;

        expect( sessionObj )
          .to.be.an( "object" )
          .to.have.all.keys( ["duration"] );

      });

      test("can get value of locationBlock object", function( done ){
        var locationObj, latitude, longitude;

        locationObj = EDAnalytics.locationBlock;
        latitude = locationObj.lat;
        longitude = locationObj.lon ;

        expect( locationObj )
          .to.be.an( "object" )
          .to.have.all.keys( ["lat", "lon"] );

        expect( latitude )
          .to.be.a( "number" );

        expect( longitude )
          .to.be.a( "number" );

        done();

      });

      test("can get value of version", function(){
        var version = EDAnalytics.version;

        expect( version )
          .to.be.an( "string" )
          .and.to.equal("001");

      });

      test("can get time value", function(){
        var time = EDAnalytics.time;

        expect( time )
          .to.be.an( "string" );

      });
    });

    suite( "send method", function() {
      test( "calls the send method", function() {
        var edEvent = {
            eventBlock: {
              type: "play",
              value: {
                key: "value"
              }
            }
          },

          sendStub;

        sendStub = sinon.spy( EDAnalytics, "send" );
        EDAnalytics.send( edEvent );

        expect( sendStub )
          .to.have.callCount( 1 );

        sendStub.restore();
      });

      test( "returns undefined", function() {
        var edEvent = {
            event: {
              type: "play"
            }
          },
          invokeEvt;

        invokeEvt = EDAnalytics.send( edEvent );

        expect( invokeEvt )
          .to.be.an( "undefined" );

      });

    });

    suite( "createEvent method", function() {
      test.skip( "calls the createEvent method", function() {
      });
    });
  });
})( window, document, window.System, window.sinon, window.chai.expect );
