/*eslint-env mocha */
/*jscs:disable maximumLineLength*/
( function( window, document, polymer, sinon, chai ) {
  "use strict";
  var expect = chai.expect,
  // get wrapper from document or for karma, create a new div and append it to the DOM
    testingWrapper = document.getElementById( "ed-scrubber-test-wrapper" ) ||
      ( function() {
        var wrapper = document.createElement( "div" );
        document.body.appendChild( wrapper );
        return wrapper;
      })(),
  // original state to test against
    originalWrapperOuterHTML = testingWrapper.outerHTML,
  // re-sets wrapper to blank
    resetWrapper = function() {
      testingWrapper.innerHTML = "";
    };

  suite( "<ed-scrubber>", function() {
    suite( "Life Cycle", function() {
      teardown( function() {
        resetWrapper();
      });

      test( "ready: can create from document.createElement", function() {
        var createdSpy = sinon.spy(
          polymer.getRegisteredPrototype( "ed-scrubber" ),
          "ready"
        );

        expect( document.createElement( "ed-scrubber" ) )
          .to.have.property( "outerHTML" )
          .that.is.a( "string" )
          .and.equals( "<ed-scrubber></ed-scrubber>" );

        expect( createdSpy ).to.have.callCount( 1 );
        createdSpy.restore();
      });

      test( "attached: can be added to another DOM Element", function() {
        var edScrubber = document.createElement( "ed-scrubber" ),
          attachedSpy = sinon.spy( edScrubber, "attached" );

        testingWrapper.appendChild( edScrubber );

        expect( attachedSpy ).to.have.callCount( 1 );

        expect( testingWrapper )
          .to.have.property( "innerHTML" )
          .that.is.a( "string" )
          .and.equals( "<ed-scrubber></ed-scrubber>" );

        attachedSpy.restore();
      });

      test.skip( "detached: can be removed from another DOM element", function() {
        var edScrubber = document.createElement( "ed-scrubber" ),
          detachedSpy = sinon.spy( edScrubber, "detached" );

        testingWrapper.appendChild( edScrubber );
        testingWrapper.removeChild( edScrubber );

        expect( detachedSpy ).to.have.callCount( 1 );

        expect( testingWrapper )
          .to.have.property( "outerHTML" )
          .that.is.a( "string" )
          .and.equals( originalWrapperOuterHTML );

        detachedSpy.restore();
      });
    });

    suite( "Attributes and Properties", function() {
      suite( "Min", function() {
        test( "has default value: 0", function() {
          expect( document.createElement( "ed-scrubber" ) )
            .to.have.property( "min" )
            .that.is.a( "number" )
            .and.equals( 0 );
        });

        test( "can be set via \"setAttribute\"", function() {
          var edScrubber = document.createElement( "ed-scrubber" );

          edScrubber.setAttribute( "min", "5" );

          expect( edScrubber.hasAttribute( "min" ) ).to.equal( true );

          expect( edScrubber.getAttribute( "min" ) )
            .to.be.a( "string" )
            .that.equals( "5" );

          expect( edScrubber )
            .to.have.property( "outerHTML" )
            .that.equals( "<ed-scrubber min=\"5\"></ed-scrubber>" );
        });

        test( "can be set via property \"min\"", function() {
          var edScrubber = document.createElement( "ed-scrubber" );

          edScrubber.min = 5;

          expect( edScrubber )
            .to.have.property( "min" )
            .that.equals( 5 );
        });

        test( "setting via \"setAttribute\" reflects to property \"min\"", function() {
          var edScrubber = document.createElement( "ed-scrubber" );

          edScrubber.setAttribute( "min", "5" );

          expect( edScrubber )
            .to.have.property( "min" )
            .that.is.a( "number" )
            .and.equals( 5 );
        });

        test( "setting via property \"min\" reflects to attribute \"min\"", function() {
          var edScrubber = document.createElement( "ed-scrubber" );

          edScrubber.min = 5;

          expect( edScrubber.hasAttribute( "min" ) ).to.equal( true );
          expect( edScrubber.getAttribute( "min" ) )
            .to.be.a( "string" )
            .that.equals( "5" )
            .and.equal( edScrubber.min.toString() );
        });

        test( "removing attribute \"min\" sets property back to default value", function() {
          var edScrubber = document.createElement( "ed-scrubber" ),
              observeFn = function( changes ) {
                expect( edScrubber.hasAttribute( "min" ) ).to.equal( false );
                expect( edScrubber )
                  .to.have.property( "min" )
                  .that.is.a( "number" )
                  .and.equals( 0 );

                Object.unobserve( edScrubber, observeFn );
              };

          edScrubber.setAttribute( "min", "5" );
          expect( edScrubber.hasAttribute( "min" ) ).to.equal( true );
          edScrubber.removeAttribute( "min" );
          Object.observe( edScrubber, observeFn );
        });

        test( "setting \"min\" to null resets property to default value", function() {
          var edScrubber = document.createElement( "ed-scrubber" ),
              observeFn = function( changes ) {
                expect( edScrubber )
                  .to.have.property( "min" )
                  .that.is.a( "number" )
                  .and.equals( 0 );

                Object.unobserve( edScrubber, observeFn );
              };

          edScrubber.min = 5;
          expect( edScrubber )
            .to.have.property( "min" )
            .that.is.a( "number" )
            .and.equals( 5 );

          edScrubber.min = null;

          Object.observe( edScrubber, observeFn );
        });

        test( "setting \"min\" to undefined resets property to default value", function() {
          var edScrubber = document.createElement( "ed-scrubber" ),
              observeFn = function( changes ) {
                expect( edScrubber )
                  .to.have.property( "min" )
                  .that.is.a( "number" )
                  .and.equals( 0 );

                Object.unobserve( edScrubber, observeFn );
              };

          edScrubber.min = 5;
          expect( edScrubber )
            .to.have.property( "min" )
            .that.is.a( "number" )
            .and.equals( 5 );

          edScrubber.min = undefined;

          Object.observe( edScrubber, observeFn );
        });
      });

      suite( "Max", function() {
        test( "has default value: 100", function() {
          expect( document.createElement( "ed-scrubber" ) )
            .to.have.property( "max" )
            .that.is.a( "number" )
            .and.equals( 100 );
        });

        test( "can be set via \"setAttribute\"", function() {
          var edScrubber = document.createElement( "ed-scrubber" );

          edScrubber.setAttribute( "max", "50" );

          expect( edScrubber.hasAttribute( "max" ) ).to.equal( true );

          expect( edScrubber.getAttribute( "max" ) )
            .to.be.a( "string" )
            .that.equals( "50" );

          expect( edScrubber )
            .to.have.property( "outerHTML" )
            .that.equals( "<ed-scrubber max=\"50\"></ed-scrubber>" );
        });

        test( "can be set via property \"max\"", function() {
          var edScrubber = document.createElement( "ed-scrubber" );

          edScrubber.max = 50;

          expect( edScrubber )
            .to.have.property( "max" )
            .that.equals( 50 );
        });

        test( "setting via \"setAttribute\" reflects to property \"max\"", function() {
          var edScrubber = document.createElement( "ed-scrubber" );

          edScrubber.setAttribute( "max", "50" );

          expect( edScrubber )
            .to.have.property( "max" )
            .that.is.a( "number" )
            .and.equals( 50 );
        });

        test( "setting via property \"max\" reflects to attribute \"max\"", function() {
          var edScrubber = document.createElement( "ed-scrubber" );

          edScrubber.max = 50;

          expect( edScrubber.hasAttribute( "max" ) ).to.equal( true );
          expect( edScrubber.getAttribute( "max" ) )
            .to.be.a( "string" )
            .that.equals( "50" )
            .and.equal( edScrubber.max.toString() );
        });

        test( "removing attribute \"max\" sets property back to default value", function() {
          var edScrubber = document.createElement( "ed-scrubber" ),
              observeFn = function( changes ) {
                expect( edScrubber.hasAttribute( "max" ) ).to.equal( false );
                expect( edScrubber )
                  .to.have.property( "max" )
                  .that.is.a( "number" )
                  .and.equals( 100 );

                Object.unobserve( edScrubber, observeFn );
              };

          edScrubber.setAttribute( "max", "50" );
          expect( edScrubber.hasAttribute( "max" ) ).to.equal( true );

          edScrubber.removeAttribute( "max" );

          Object.observe( edScrubber, observeFn );
        });

        test( "setting \"max\" to null resets property to default value", function() {
          var edScrubber = document.createElement( "ed-scrubber" ),
              observeFn = function( changes ) {
                expect( edScrubber )
                  .to.have.property( "max" )
                  .that.is.a( "number" )
                  .and.equals( 100 );

                Object.unobserve( edScrubber, observeFn );
              };

          edScrubber.max = 50;
          expect( edScrubber )
            .to.have.property( "max" )
            .that.is.a( "number" )
            .and.equals( 50 );

          edScrubber.max = null;

          Object.observe( edScrubber, observeFn );
        });

        test( "setting \"max\" to undefined resets property to default value", function() {
          var edScrubber = document.createElement( "ed-scrubber" ),
              observeFn = function( changes ) {
                expect( edScrubber )
                  .to.have.property( "max" )
                  .that.is.a( "number" )
                  .and.equals( 100 );

                Object.unobserve( edScrubber, observeFn );
              };

          edScrubber.max = 50;
          expect( edScrubber )
            .to.have.property( "max" )
            .that.is.a( "number" )
            .and.equals( 50 );

          edScrubber.max = undefined;

          Object.observe( edScrubber, observeFn );
        });
      });

      suite( "Format", function() {
        test( "has default \"percent\"", function() {
          expect( document.createElement( "ed-scrubber" ) )
            .to.have.property( "format" )
            .that.is.a( "string" )
            .and.equals( "percent" );
        });

        test( "can be set via \"setAttribute\"", function() {
          var edScrubber = document.createElement( "ed-scrubber" );

          edScrubber.setAttribute( "format", "time" );

          expect( edScrubber.hasAttribute( "format" ) ).to.equal( true );

          expect( edScrubber.getAttribute( "format" ) )
            .to.be.a( "string" )
            .that.equals( "time" );

          expect( edScrubber )
            .to.have.property( "outerHTML" )
            .that.equals( "<ed-scrubber format=\"time\"></ed-scrubber>" );
        });

        test( "can be set via property \"format\"", function() {
          var edScrubber = document.createElement( "ed-scrubber" );

          edScrubber.format = "time";

          expect( edScrubber )
            .to.have.property( "format" )
            .that.equals( "time" );
        });

        test( "setting via \"setAttribute\" reflects to property \"format\"", function() {
          var edScrubber = document.createElement( "ed-scrubber" );

          edScrubber.setAttribute( "format", "time" );

          expect( edScrubber )
            .to.have.property( "format" )
            .that.equals( "time" )
            .and.equals( edScrubber.getAttribute( "format" ) );
        });

        test( "setting via property \"format\" reflects to attribute \"format\"", function() {
          var edScrubber = document.createElement( "ed-scrubber" );

          edScrubber.format = "time";

          expect( edScrubber.hasAttribute( "format" ) ).to.equal( true );
          expect( edScrubber.getAttribute( "format" ) )
            .to.be.a( "string" )
            .that.equals( "time" )
            .and.equal( edScrubber.format );
        });

        test( "removing attribute \"format\" sets property back to default value", function() {
          var edScrubber = document.createElement( "ed-scrubber" ),
              observeFn = function( changes ) {
                expect( edScrubber.hasAttribute( "format" ) ).to.equal( false );
                expect( edScrubber )
                  .to.have.property( "format" )
                  .that.is.a( "string" )
                  .and.equals( "percent" );

                Object.unobserve( edScrubber, observeFn );
              };

          edScrubber.setAttribute( "format", "time" );
          expect( edScrubber.hasAttribute( "format" ) ).to.equal( true );

          edScrubber.removeAttribute( "format" );

          Object.observe( edScrubber, observeFn );
        });

        test( "setting \"format\" to null resets property to default value", function() {
          var edScrubber = document.createElement( "ed-scrubber" ),
              observeFn = function( changes ) {
                expect( edScrubber )
                  .to.have.property( "format" )
                  .that.is.a( "string" )
                  .and.equals( "percent" );

                Object.unobserve( edScrubber, observeFn );
              };

          edScrubber.format = "time";
          expect( edScrubber )
            .to.have.property( "format" )
            .that.is.a( "string" )
            .and.equals( "time" );

          edScrubber.format = null;

          Object.observe( edScrubber, observeFn );
        });

        test( "setting \"format\" to undefined resets property to default value", function() {
          var edScrubber = document.createElement( "ed-scrubber" ),
              observeFn = function( changes ) {
                expect( edScrubber )
                  .to.have.property( "format" )
                  .that.is.a( "string" )
                  .and.equals( "percent" );

                Object.unobserve( edScrubber, observeFn );
              };

          edScrubber.format = "time";
          expect( edScrubber )
            .to.have.property( "format" )
            .that.is.a( "string" )
            .and.equals( "time" );

          edScrubber.format = undefined;

          Object.observe( edScrubber, observeFn );
        });
      });

      suite( "Type", function() {
        test( "has default \"default\"", function() {
          expect( document.createElement( "ed-scrubber" ) )
            .to.have.property( "type" )
            .that.is.a( "string" )
            .and.equals( "default" );
        });

        test( "can be set via \"setAttribute\"", function() {
          var edScrubber = document.createElement( "ed-scrubber" );

          edScrubber.setAttribute( "type", "fill" );

          expect( edScrubber.hasAttribute( "type" ) ).to.equal( true );

          expect( edScrubber.getAttribute( "type" ) )
            .to.be.a( "string" )
            .that.equals( "fill" );

          expect( edScrubber )
            .to.have.property( "outerHTML" )
            .that.equals( "<ed-scrubber type=\"fill\"></ed-scrubber>" );
        });

        test( "can be set via property \"type\"", function() {
          var edScrubber = document.createElement( "ed-scrubber" );

          edScrubber.type = "fill";

          expect( edScrubber )
            .to.have.property( "type" )
            .that.equals( "fill" );
        });

        test( "setting via \"setAttribute\" reflects to property \"type\"", function() {
          var edScrubber = document.createElement( "ed-scrubber" );

          edScrubber.setAttribute( "type", "fill" );

          expect( edScrubber )
            .to.have.property( "type" )
            .that.equals( "fill" )
            .and.equals( edScrubber.getAttribute( "type" ) );
        });

        test( "setting via property \"type\" reflects to attribute \"type\"", function() {
          var edScrubber = document.createElement( "ed-scrubber" );

          edScrubber.type = "fill";

          expect( edScrubber.hasAttribute( "type" ) ).to.equal( true );
          expect( edScrubber.getAttribute( "type" ) )
            .to.be.a( "string" )
            .that.equals( "fill" )
            .and.equal( edScrubber.type );
        });

        test( "removing attribute \"type\" sets property back to default value", function() {
          var edScrubber = document.createElement( "ed-scrubber" ),
              observeFn = function( changes ) {
                expect( edScrubber.hasAttribute( "type" ) ).to.equal( false );
                expect( edScrubber )
                  .to.have.property( "type" )
                  .that.is.a( "string" )
                  .and.equals( "default" );

                Object.unobserve( edScrubber, observeFn );
              };

          edScrubber.setAttribute( "type", "fill" );
          expect( edScrubber.hasAttribute( "type" ) ).to.equal( true );

          edScrubber.removeAttribute( "type" );

          Object.observe( edScrubber, observeFn );
        });

        test( "setting \"type\" to null resets property to default value", function() {
          var edScrubber = document.createElement( "ed-scrubber" ),
              observeFn = function( changes ) {
                expect( edScrubber )
                  .to.have.property( "type" )
                  .that.is.a( "string" )
                  .and.equals( "default" );

                Object.unobserve( edScrubber, observeFn );
              };

          edScrubber.type = "fill";
          expect( edScrubber )
            .to.have.property( "type" )
            .that.is.a( "string" )
            .and.equals( "fill" );

          edScrubber.type = null;

          Object.observe( edScrubber, observeFn );
        });

        test( "setting \"type\" to undefined resets property to default value", function() {
          var edScrubber = document.createElement( "ed-scrubber" ),
            observeFn = function( changes ) {
              expect( edScrubber )
                .to.have.property( "type" )
                .that.is.a( "string" )
                .and.equals( "default" );

              Object.unobserve( edScrubber, observeFn );
            };

          edScrubber.type = "fill";
          expect( edScrubber )
            .to.have.property( "type" )
            .that.is.a( "string" )
            .and.equals( "fill" );

          edScrubber.type = undefined;

          Object.observe( edScrubber, observeFn );
        });
      });
    });
  });
})( window, document, window.Polymer, window.sinon, window.chai );
