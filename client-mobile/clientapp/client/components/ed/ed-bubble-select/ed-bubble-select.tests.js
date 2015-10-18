/*eslint-env mocha */
/*jscs:disable maximumLineLength*/
( function( window, document, polymer, sinon, chai ) {
  "use strict";
  var expect = chai.expect,
  // get wrapper from document or for karma, create a new div and append it to the DOM
    testingWrapper = document.getElementById( "ed-bubble-select-test-wrapper" ) ||
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

  suite( "<ed-bubble-select>", function() {
    suite( "Life Cycle", function() {
      teardown( function() {
        resetWrapper();
      });

      test( "ready: can create from document.createElement", function() {
        var createdSpy = sinon.spy(
          polymer.getRegisteredPrototype( "ed-bubble-select" ),
          "ready"
        );

        expect( document.createElement( "ed-bubble-select" ) )
          .to.have.property( "outerHTML" )
          .that.is.a( "string" )
          .and.equals( "<ed-bubble-select></ed-bubble-select>" );

        expect( createdSpy ).to.have.callCount( 1 );
        createdSpy.restore();
      });

      test( "attached: can be added to another DOM Element", function() {
        var bubbleSelect = document.createElement( "ed-bubble-select" ),
          attachedSpy = sinon.spy( bubbleSelect, "attached" );

        testingWrapper.appendChild( bubbleSelect );

        expect( attachedSpy ).to.have.callCount( 1 );

        expect( testingWrapper )
          .to.have.property( "innerHTML" )
          .that.is.a( "string" )
          .and.equals( "<ed-bubble-select></ed-bubble-select>" );

        attachedSpy.restore();
      });

      test.skip( "detached: can be removed from another DOM element", function() {
        var bubbleSelect = document.createElement( "ed-bubble-select" ),
          detachedSpy = sinon.spy( bubbleSelect, "detached" );

        testingWrapper.appendChild( bubbleSelect );
        testingWrapper.removeChild( bubbleSelect );

        expect( detachedSpy ).to.have.callCount( 1 );

        expect( testingWrapper )
          .to.have.property( "outerHTML" )
          .that.is.a( "string" )
          .and.equals( originalWrapperOuterHTML );

        detachedSpy.restore();
      });
    });

    suite( "Attributes and Properties", function() {
      // Tests for Value attribute and property
      suite( "value", function() {
        test( "can be set via setattribute", function() {
          var bubbleSelect = document.createElement( "ed-bubble-select" ),
              setTo = "Set via Attribute";

          bubbleSelect.setAttribute( "value", setTo );

          expect( bubbleSelect.hasAttribute( "value" ) ).to.equal( true );

          expect( bubbleSelect.getAttribute( "value" ) )
            .to.be.a( "string" )
            .that.equals( setTo );

          expect( bubbleSelect )
            .to.have.property( "outerHTML" )
            .that.equals( "<ed-bubble-select value=\"" + setTo + "\"></ed-bubble-select>" );
        });

        test( "can be set via property \"value\"", function() {
          var bubbleSelect = document.createElement( "ed-bubble-select" ),
              setTo = "Set via Property";

          bubbleSelect.value = setTo;

          expect( bubbleSelect )
            .to.have.property( "value" )
            .that.equals( setTo );
        });

        test( "setting via \"setAttribute\" reflects to property \"value\"", function() {
          var bubbleSelect = document.createElement( "ed-bubble-select" ),
              setTo = "Set via Attribute";

          bubbleSelect.setAttribute( "value", setTo );

          expect( bubbleSelect )
            .to.have.property( "value" )
            .that.equals( setTo )
            .and.equals( bubbleSelect.getAttribute( "value" ) );
        });

        test( "setting via property \"value\" reflects to attribute \"value\"", function() {
          var bubbleSelect = document.createElement( "ed-bubble-select" ),
              setTo = "Set via Property";

          bubbleSelect.value = setTo;

          expect( bubbleSelect.hasAttribute( "value" ) ).to.equal( true );
          expect( bubbleSelect.getAttribute( "value" ) )
            .to.be.a( "string" )
            .that.equals( setTo )
            .and.equal( bubbleSelect.value );
        });
      });

      // Tests for Name attribute and property
      suite( "name", function() {
        test( "can be set via setattribute", function() {
          var bubbleSelect = document.createElement( "ed-bubble-select" ),
              setTo = "Set via Attribute";

          bubbleSelect.setAttribute( "name", setTo );

          expect( bubbleSelect.hasAttribute( "name" ) ).to.equal( true );

          expect( bubbleSelect.getAttribute( "name" ) )
            .to.be.a( "string" )
            .that.equals( setTo );

          expect( bubbleSelect )
            .to.have.property( "outerHTML" )
            .that.equals( "<ed-bubble-select name=\"" + setTo + "\"></ed-bubble-select>" );
        });

        test( "can be set via property \"name\"", function() {
          var bubbleSelect = document.createElement( "ed-bubble-select" ),
              setTo = "Set via Property";

          bubbleSelect.name = setTo;

          expect( bubbleSelect )
            .to.have.property( "name" )
            .that.equals( setTo );
        });

        test( "setting via \"setAttribute\" reflects to property \"name\"", function() {
          var bubbleSelect = document.createElement( "ed-bubble-select" ),
              setTo = "Set via Attribute";

          bubbleSelect.setAttribute( "name", setTo );

          expect( bubbleSelect )
            .to.have.property( "name" )
            .that.equals( setTo )
            .and.equals( bubbleSelect.getAttribute( "name" ) );
        });

        test( "setting via property \"name\" reflects to attribute \"name\"", function() {
          var bubbleSelect = document.createElement( "ed-bubble-select" ),
              setTo = "Set via Property";

          bubbleSelect.name = setTo;

          expect( bubbleSelect.hasAttribute( "name" ) ).to.equal( true );
          expect( bubbleSelect.getAttribute( "name" ) )
            .to.be.a( "string" )
            .that.equals( setTo )
            .and.equal( bubbleSelect.name );
        });
      });

      // Tests for Required attribute and property
      suite( "required", function() {
        test( "has default value: false", function() {
          expect( document.createElement( "ed-bubble-select" ) )
            .to.have.property( "required" )
            .that.is.a( "boolean" )
            .and.equals( false );
        });

        test( "can be set via attribute", function() {
          var bubbleSelect = document.createElement( "ed-bubble-select" );

          bubbleSelect.setAttribute( "required", "" );

          expect( bubbleSelect.hasAttribute( "required" ) ).to.equal( true );
          expect( bubbleSelect.getAttribute( "required" ) )
            .to.be.a( "string" )
            .and.equals( "" );

          expect( bubbleSelect )
            .to.have.property( "outerHTML" )
            .that.is.a( "string" )
            .and.equals( "<ed-bubble-select required=\"\"></ed-bubble-select>" );
        });

        test( "can be set via attribute reflect property", function() {
          var bubbleSelect = document.createElement( "ed-bubble-select" );

          bubbleSelect.setAttribute( "required", "" );

          expect( bubbleSelect )
            .to.have.property( "required" )
            .that.is.a( "boolean" )
            .and.equals( true );
        });

        test( "can be set via property reflect attribute", function() {
          var bubbleSelect = document.createElement( "ed-bubble-select" ),
              observeFn = function( changes ) {
                expect( bubbleSelect.hasAttribute( "required" ) ).to.equal( true );
                expect( bubbleSelect.getAttribute( "required" ) )
                  .to.be.a( "string" )
                  .and.equals( "" );

                Object.unobserve( bubbleSelect, observeFn );
              };

          bubbleSelect.required = true;

          Object.observe( bubbleSelect, observeFn );
        });

        test( "can be removed via attribute", function() {
          var bubbleSelect = document.createElement( "ed-bubble-select" );

          bubbleSelect.setAttribute( "required", "" );
          bubbleSelect.removeAttribute( "required" );

          expect( bubbleSelect )
            .to.have.property( "outerHTML" )
            .that.is.a( "string" )
            .and.equals( "<ed-bubble-select></ed-bubble-select>" );
        });

        test( "can be removed via attribute reflect property", function() {
          var bubbleSelect = document.createElement( "ed-bubble-select" );

          bubbleSelect.required = true;
          bubbleSelect.removeAttribute( "required" );

          expect( bubbleSelect )
            .to.have.property( "outerHTML" )
            .that.is.a( "string" )
            .and.equals( "<ed-bubble-select></ed-bubble-select>" );
        });

        test( "can be removed via property reflect attribute", function() {
          var bubbleSelect = document.createElement( "ed-bubble-select" ),
            observeFn = function( changes ) {
              expect( bubbleSelect )
                .to.have.property( "outerHTML" )
                .that.is.a( "string" )
                .and.equals( "<ed-bubble-select></ed-bubble-select>" );

              Object.unobserve( bubbleSelect, observeFn );
            };

          bubbleSelect.setAttribute( "required", "" );
          bubbleSelect.required = false;

          Object.observe( bubbleSelect, observeFn );
        });
      });

      // Tests for checked attribute and property
      suite( "checked", function() {
        test( "has default value: false", function() {
          expect( document.createElement( "ed-bubble-select" ) )
            .to.have.property( "checked" )
            .that.is.a( "boolean" )
            .and.equals( false );
        });

        test( "can be set via attribute", function() {
          var bubbleSelect = document.createElement( "ed-bubble-select" );

          bubbleSelect.setAttribute( "checked", "" );

          expect( bubbleSelect.hasAttribute( "checked" ) ).to.equal( true );
          expect( bubbleSelect.getAttribute( "checked" ) )
            .to.be.a( "string" )
            .and.equals( "" );

          expect( bubbleSelect )
            .to.have.property( "outerHTML" )
            .that.is.a( "string" )
            .and.equals( "<ed-bubble-select checked=\"\"></ed-bubble-select>" );
        });

        test( "can be set via attribute reflect property", function() {
          var bubbleSelect = document.createElement( "ed-bubble-select" );

          bubbleSelect.setAttribute( "checked", "" );

          expect( bubbleSelect )
            .to.have.property( "checked" )
            .that.is.a( "boolean" )
            .and.equals( true );
        });

        test( "can be set via property reflect attribute", function() {
          var bubbleSelect = document.createElement( "ed-bubble-select" ),
              observeFn = function( changes ) {
                expect( bubbleSelect.hasAttribute( "checked" ) ).to.equal( true );
                expect( bubbleSelect.getAttribute( "checked" ) )
                  .to.be.a( "string" )
                  .and.equals( "" );

                Object.unobserve( bubbleSelect, observeFn );
              };

          bubbleSelect.checked = true;

          Object.observe( bubbleSelect, observeFn );
        });

        test( "can be removed via attribute", function() {
          var bubbleSelect = document.createElement( "ed-bubble-select" );

          bubbleSelect.setAttribute( "checked", "" );
          bubbleSelect.removeAttribute( "checked" );

          expect( bubbleSelect )
            .to.have.property( "outerHTML" )
            .that.is.a( "string" )
            .and.equals( "<ed-bubble-select></ed-bubble-select>" );
        });

        test( "can be removed via attribute reflect property", function() {
          var bubbleSelect = document.createElement( "ed-bubble-select" );

          bubbleSelect.checked = true;
          bubbleSelect.removeAttribute( "checked" );

          expect( bubbleSelect )
            .to.have.property( "outerHTML" )
            .that.is.a( "string" )
            .and.equals( "<ed-bubble-select></ed-bubble-select>" );
        });

        test( "can be removed via property reflect attribute", function() {
          var bubbleSelect = document.createElement( "ed-bubble-select" ),
              observeFn = function( changes ) {
                expect( bubbleSelect )
                  .to.have.property( "outerHTML" )
                  .that.is.a( "string" )
                  .and.equals( "<ed-bubble-select></ed-bubble-select>" );

                Object.unobserve( bubbleSelect, observeFn );
              };

          bubbleSelect.setAttribute( "checked", "" );
          bubbleSelect.checked = false;

          Object.observe( bubbleSelect, observeFn );
        });
      });

      // Tests for disabled attribute and property
      suite( "disabled", function() {
        test( "has default value: false", function() {
          expect( document.createElement( "ed-bubble-select" ) )
            .to.have.property( "disabled" )
            .that.is.a( "boolean" )
            .and.equals( false );
        });

        test( "can be set via attribute", function() {
          var bubbleSelect = document.createElement( "ed-bubble-select" );

          bubbleSelect.setAttribute( "disabled", "" );

          expect( bubbleSelect.hasAttribute( "disabled" ) ).to.equal( true );
          expect( bubbleSelect.getAttribute( "disabled" ) )
            .to.be.a( "string" )
            .and.equals( "" );

          expect( bubbleSelect )
            .to.have.property( "outerHTML" )
            .that.is.a( "string" )
            .and.equals( "<ed-bubble-select disabled=\"\"></ed-bubble-select>" );
        });

        test( "can be set via attribute reflect property", function() {
          var bubbleSelect = document.createElement( "ed-bubble-select" );

          bubbleSelect.setAttribute( "disabled", "" );

          expect( bubbleSelect )
            .to.have.property( "disabled" )
            .that.is.a( "boolean" )
            .and.equals( true );
        });

        test( "can be set via property reflect attribute", function() {
          var bubbleSelect = document.createElement( "ed-bubble-select" ),
              observeFn = function( changes ) {
                expect( bubbleSelect.hasAttribute( "disabled" ) ).to.equal( true );
                expect( bubbleSelect.getAttribute( "disabled" ) )
                  .to.be.a( "string" )
                  .and.equals( "" );

                Object.unobserve( bubbleSelect, observeFn );
              };

          bubbleSelect.disabled = true;

          Object.observe( bubbleSelect, observeFn );
        });

        test( "can be removed via attribute", function() {
          var bubbleSelect = document.createElement( "ed-bubble-select" );

          bubbleSelect.setAttribute( "disabled", "" );
          bubbleSelect.removeAttribute( "disabled" );

          expect( bubbleSelect )
            .to.have.property( "outerHTML" )
            .that.is.a( "string" )
            .and.equals( "<ed-bubble-select></ed-bubble-select>" );
        });

        test( "can be removed via attribute reflect property", function() {
          var bubbleSelect = document.createElement( "ed-bubble-select" );

          bubbleSelect.disabled = true;
          bubbleSelect.removeAttribute( "disabled" );

          expect( bubbleSelect )
            .to.have.property( "outerHTML" )
            .that.is.a( "string" )
            .and.equals( "<ed-bubble-select></ed-bubble-select>" );
        });

        test( "can be removed via property reflect attribute", function() {
          var bubbleSelect = document.createElement( "ed-bubble-select" ),
              observeFn = function( changes ) {
                expect( bubbleSelect )
                  .to.have.property( "outerHTML" )
                  .that.is.a( "string" )
                  .and.equals( "<ed-bubble-select></ed-bubble-select>" );

                Object.unobserve( bubbleSelect, observeFn );
              };

          bubbleSelect.setAttribute( "disabled", "" );
          bubbleSelect.disabled = false;

          Object.observe( bubbleSelect, observeFn );
        });
      });
    });
  });
})( window, document, window.Polymer, window.sinon, window.chai );
