/*eslint-env mocha*/
/*global suite, test, console*/
( function( win, doc, System, sinon, expect ) {
  "use strict";

  suite( "EDAnalyticsEvent", function() {
    var EDAnalyticsEvent;

    suiteSetup( function( done ) {
      System.import( "domain/ed/analytics/events/EDAnalyticsEvent" )
        .then( function( imported ) {

          EDAnalyticsEvent = imported.default;
          done();

        }, function( error ) {
          console.warn( "Could not import 'EDAnalyticsEvent' for testing: ", error.message );
          console.error( error.stack );
          done( error );
        }
      );
    });

    // Start Testing
    suite( "EDAnalyticsEvent creation", function() {
      test("starts with appropriate properties", function(){
        var event = {
            "type": "play",
            "eventBlock": {},
            "common": {}
          },
          analyticsEvent = new EDAnalyticsEvent( event );

        expect( analyticsEvent )
          .to.have.property( "type" )
          .to.be.a("string")
          .that.equals( event.type );

        expect( analyticsEvent )
          .to.have.property( "eventBlock" )
          .to.be.an("object")
          .that.equals( event.eventBlock );

      });
    });

    suite( "EDAnalyticsEvent invoked", function() {
      test("returns an event instance", function(){
        var event = {
            "type": "play",
            "eventBlock": {},
            "common": {}
          },
          analyticsEvent;

        analyticsEvent = new EDAnalyticsEvent( event );

        expect( analyticsEvent )
          .to.be.an.instanceof( EDAnalyticsEvent );

      });

    });


  });
})( window, document, window.System, window.sinon, window.chai.expect );
