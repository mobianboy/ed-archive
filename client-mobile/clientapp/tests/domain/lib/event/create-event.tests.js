/*globals suite, suiteSetup, test*/
/*eslint env:"mocha"*/
( function( win, doc, System, sinon, expect ) {
  "use strict";

  suite( "CreateEvent", function() {
    var createEvent;

    suiteSetup( function( done ) {
      System.import( "domain/lib/event/create-event" )
        .then( function( imported ) {
          createEvent = imported.default;
          done();
        }, function( error ) {
          console.warn( "Could not import 'create-event' for testing: ", error.message );
          console.error( error.stack );
          done( error );
        });
    });

    // Tests begin
    suite( "Properties", function() {
      test( "instance of Event", function() {
        var descriptor = {},
          event = createEvent( "open", descriptor );

        expect( event )
          .to.be.an.instanceOf( Event );
      });

      test( "passing descriptor parameter creates detail property on event object", function() {
        var descriptor = {
            detail: {
              something: "for nothing"
            }
          },
          event = createEvent( "open", descriptor );

        expect( event )
          .to.have.property( "detail" )
          .to.deep.equal( descriptor.detail );
      });

      test( "setting bubbles to true, makes event.bubbles = true", function() {
        var descriptor = {
            bubbles: true
          },
          event = createEvent( "open", descriptor );

        expect( event )
          .to.have.property( "bubbles" )
          .that.equals( descriptor.bubbles );
      });

      test( "setting cancelable to true, makes event.cancelable = true", function() {
        var descriptor = {
            cancelable: true
          },
          event = createEvent( "open", descriptor );

        expect( event )
          .to.have.property( "cancelable" )
          .that.equals( descriptor.cancelable );
      });

      test( "setting detail value, makes event.detail = detail.value", function() {
        var descriptor = {
            detail: {
              benny: "spaceship"
            }
          },
          event = createEvent( "click", descriptor ),
          checkbox = document.createElement( "input" ),
          clickHandler = function() {};

        document.body.appendChild( checkbox );
        checkbox.addEventListener( "click", clickHandler );

        checkbox.dispatchEvent( event );

        expect( event )
          .to.have.property( "detail" )
          .to.deep.equal({
            benny: "spaceship"
          });

        document.body.removeChild( checkbox );
      });

      test( "event.target should identify the element on which the event occurred", function() {
        var descriptor = {},
          event = createEvent( "click", descriptor ),
          checkbox = document.createElement( "input" ),
          clickHandler = sinon.spy();

        document.body.appendChild( checkbox );
        checkbox.addEventListener( "click", clickHandler );

        checkbox.dispatchEvent( event );

        expect( event )
          .to.have.property( "target" )
          .that.equals( checkbox );

        document.body.removeChild( checkbox );
      });
    });

    suite( "Acts like Native Event", function() {
      test( "can bubble through DOM", function( done ) {
        var descriptor = {
            bubbles: true,
            detail: {
              batman: "no parents"
            }
          },
          event = createEvent( "open", descriptor ),
          windowHandler = function( bubbledEvent ) {
            expect( bubbledEvent )
              .to.have.property( "detail" )
              .that.equals( descriptor.detail );

            window.removeEventListener( "open", windowHandler );
            done();
          };

        window.addEventListener( "open", windowHandler );

        document.body.dispatchEvent( event );
      });

      test( "if event is cancelable, stopPropagation will prevent bubbling", function() {
        var descriptor = {
            bubbles: true,
            cancelable: true,
            detail: {
              batman: "first try"
            }
          },
          event = createEvent( "open", descriptor ),
          stopHandler = event.stopPropagation(),
          windowHandler = sinon.spy();

        document.body.addEventListener( "open", stopHandler );

        window.addEventListener( "open", windowHandler );

        document.body.dispatchEvent( event );

        expect( windowHandler )
          .to.have.callCount( 0 );
      });

      test( "if event is not cancelable, calling stopPropagation will not prevent bubbling", function( done ) {
        var descriptor = {
            bubbles: true,
            cancelable: false,
            detail: {
              batman: "i only work in black"
            }
          },
          event = createEvent( "open", descriptor ),
          stopHandler = event.stopPropagation(),
          windowHandler = function( bubbledEvent ) {
            expect( bubbledEvent )
              .to.have.property( "detail" )
              .that.equals( descriptor.detail );

            done();
          };

        document.body.addEventListener( "open", stopHandler );
        done();

        window.addEventListener( "open", windowHandler );

        document.body.dispatchEvent( event );
      });
    });

    suite.skip( "Browser Support", function() {
      test( "when browser doesn't support CustomEvent constructor", function() {
        var descriptor = {
            detail: {
              wildstyle: "are you a dj"
            }
          },
          stub = sinon.stub( window, "CustomEvent", function() {
            throw new Error( "Throw all the things" );
          }),
          event = createEvent( "open", descriptor );

        expect( event )
          .to.be.an.instanceOf( Event );

        stub.restore();
      });

      test( "when browser supports CustomEvent constructor", function() {
        var descriptor = {
            detail: {
              unikitty: "marry a marshmallow"
            }
          },
          event = createEvent( "open", descriptor );

        expect( event )
          .to.be.an.instanceOf( Event );

        expect( event )
          .to.have.property( "detail" )
          .to.deep.equal({
            unikitty: "marry a marshmallow"
          });
      });
    });
  });
})( window, document, window.System, window.sinon, window.chai.expect );
