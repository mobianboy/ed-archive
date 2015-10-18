/*eslint-env mocha */
/*jscs:disable maximumLineLength*/
( function( window, document, polymer, sinon, chai ) {
  "use strict";
  var expect = chai.expect,
  // get wrapper from document or for karma, create a new div and append it to the DOM
    testingWrapper = document.getElementById( "radio-select-test-wrapper" ) ||
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

  suite( "<radio-select>", function() {
    suite( "Life Cycle", function() {
      teardown( function() {
        resetWrapper();
      });

      test( "ready: can create from document.createElement", function() {
        var createdSpy = sinon.spy(
          polymer.getRegisteredPrototype( "radio-select" ),
          "ready"
        );

        expect( document.createElement( "radio-select" ) )
          .to.have.property( "outerHTML" )
          .that.is.a( "string" )
          .and.equals( "<radio-select></radio-select>" );

        expect( createdSpy ).to.have.callCount( 1 );
        createdSpy.restore();
      });

      test( "attached: can be added to another DOM Element", function() {
        var radioSelect = document.createElement( "radio-select" ),
          attachedSpy = sinon.spy( radioSelect, "attached" );

        testingWrapper.appendChild( radioSelect );

        expect( attachedSpy ).to.have.callCount( 1 );

        expect( testingWrapper )
          .to.have.property( "innerHTML" )
          .that.is.a( "string" )
          .and.equals( "<radio-select></radio-select>" );

        attachedSpy.restore();
      });

      test.skip( "detached: can be removed from another DOM element", function() {
        var radioSelect = document.createElement( "radio-select" ),
          detachedSpy = sinon.spy( radioSelect, "detached" );

        testingWrapper.appendChild( radioSelect );
        testingWrapper.removeChild( radioSelect );

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
          var radioSelect = document.createElement( "radio-select" ),
              setTo = "Set via Attribute";

          radioSelect.setAttribute( "value", setTo );

          expect( radioSelect.hasAttribute( "value" ) ).to.equal( true );

          expect( radioSelect.getAttribute( "value" ) )
            .to.be.a( "string" )
            .that.equals( setTo );

          expect( radioSelect )
            .to.have.property( "outerHTML" )
            .that.equals( "<radio-select value=\"" + setTo + "\"></radio-select>" );
        });

        test( "can be set via property \"value\"", function() {
          var radioSelect = document.createElement( "radio-select" ),
              setTo = "Set via Property";

          radioSelect.value = setTo;

          expect( radioSelect )
            .to.have.property( "value" )
            .that.equals( setTo );
        });

        test( "setting via \"setAttribute\" reflects to property \"value\"", function() {
          var radioSelect = document.createElement( "radio-select" ),
              setTo = "Set via Attribute";

          radioSelect.setAttribute( "value", setTo );

          expect( radioSelect )
            .to.have.property( "value" )
            .that.equals( setTo )
            .and.equals( radioSelect.getAttribute( "value" ) );
        });

        test( "setting via property \"value\" reflects to attribute \"value\"", function() {
          var radioSelect = document.createElement( "radio-select" ),
              setTo = "Set via Property";

          radioSelect.value = setTo;

          expect( radioSelect.hasAttribute( "value" ) ).to.equal( true );
          expect( radioSelect.getAttribute( "value" ) )
            .to.be.a( "string" )
            .that.equals( setTo )
            .and.equal( radioSelect.value );
        });
      });

      // Tests for Name attribute and property
      suite( "name", function() {
        test( "can be set via setattribute", function() {
          var radioSelect = document.createElement( "radio-select" ),
              setTo = "Set via Attribute";

          radioSelect.setAttribute( "name", setTo );

          expect( radioSelect.hasAttribute( "name" ) ).to.equal( true );

          expect( radioSelect.getAttribute( "name" ) )
            .to.be.a( "string" )
            .that.equals( setTo );

          expect( radioSelect )
            .to.have.property( "outerHTML" )
            .that.equals( "<radio-select name=\"" + setTo + "\"></radio-select>" );
        });

        test( "can be set via property \"name\"", function() {
          var radioSelect = document.createElement( "radio-select" ),
              setTo = "Set via Property";

          radioSelect.name = setTo;

          expect( radioSelect )
            .to.have.property( "name" )
            .that.equals( setTo );
        });

        test( "setting via \"setAttribute\" reflects to property \"name\"", function() {
          var radioSelect = document.createElement( "radio-select" ),
              setTo = "Set via Attribute";

          radioSelect.setAttribute( "name", setTo );

          expect( radioSelect )
            .to.have.property( "name" )
            .that.equals( setTo )
            .and.equals( radioSelect.getAttribute( "name" ) );
        });

        test( "setting via property \"name\" reflects to attribute \"name\"", function() {
          var radioSelect = document.createElement( "radio-select" ),
              setTo = "Set via Property";

          radioSelect.name = setTo;

          expect( radioSelect.hasAttribute( "name" ) ).to.equal( true );
          expect( radioSelect.getAttribute( "name" ) )
            .to.be.a( "string" )
            .that.equals( setTo )
            .and.equal( radioSelect.name );
        });
      });

      // Tests for Required attribute and property
      suite( "required", function() {
        test( "has default value: false", function() {
          expect( document.createElement( "radio-select" ) )
            .to.have.property( "required" )
            .that.is.a( "boolean" )
            .and.equals( false );
        });

        test( "can be set via attribute", function() {
          var radioSelect = document.createElement( "radio-select" );

          radioSelect.setAttribute( "required", "" );

          expect( radioSelect.hasAttribute( "required" ) ).to.equal( true );
          expect( radioSelect.getAttribute( "required" ) )
            .to.be.a( "string" )
            .and.equals( "" );

          expect( radioSelect )
            .to.have.property( "outerHTML" )
            .that.is.a( "string" )
            .and.equals( "<radio-select required=\"\"></radio-select>" );
        });

        test( "can be set via attribute reflect property", function() {
          var radioSelect = document.createElement( "radio-select" );

          radioSelect.setAttribute( "required", "" );

          expect( radioSelect )
            .to.have.property( "required" )
            .that.is.a( "boolean" )
            .and.equals( true );
        });

        test( "can be set via property reflect attribute", function() {
          var radioSelect = document.createElement( "radio-select" ),
              observeFn = function( changes ) {
                expect( radioSelect.hasAttribute( "required" ) ).to.equal( true );
                expect( radioSelect.getAttribute( "required" ) )
                  .to.be.a( "string" )
                  .and.equals( "" );

                Object.unobserve( radioSelect, observeFn );
              };

          radioSelect.required = true;

          Object.observe( radioSelect, observeFn );
        });

        test( "can be removed via attribute", function() {
          var radioSelect = document.createElement( "radio-select" );

          radioSelect.setAttribute( "required", "" );
          radioSelect.removeAttribute( "required" );

          expect( radioSelect )
            .to.have.property( "outerHTML" )
            .that.is.a( "string" )
            .and.equals( "<radio-select></radio-select>" );
        });

        test( "can be removed via attribute reflect property", function() {
          var radioSelect = document.createElement( "radio-select" );

          radioSelect.required = true;
          radioSelect.removeAttribute( "required" );

          expect( radioSelect )
            .to.have.property( "outerHTML" )
            .that.is.a( "string" )
            .and.equals( "<radio-select></radio-select>" );
        });

        test( "can be removed via property reflect attribute", function() {
          var radioSelect = document.createElement( "radio-select" ),
              observeFn = function( changes ) {
                expect( radioSelect )
                  .to.have.property( "outerHTML" )
                  .that.is.a( "string" )
                  .and.equals( "<radio-select></radio-select>" );

                Object.unobserve( radioSelect, observeFn );
              };

          radioSelect.setAttribute( "required", "" );
          radioSelect.required = false;

          Object.observe( radioSelect, observeFn );
        });
      });

      // Tests for checked attribute and property
      suite( "checked", function() {
        test( "has default value: false", function() {
          expect( document.createElement( "radio-select" ) )
            .to.have.property( "checked" )
            .that.is.a( "boolean" )
            .and.equals( false );
        });

        test( "can be set via attribute", function() {
          var radioSelect = document.createElement( "radio-select" );

          radioSelect.setAttribute( "checked", "" );

          expect( radioSelect.hasAttribute( "checked" ) ).to.equal( true );
          expect( radioSelect.getAttribute( "checked" ) )
            .to.be.a( "string" )
            .and.equals( "" );

          expect( radioSelect )
            .to.have.property( "outerHTML" )
            .that.is.a( "string" )
            .and.equals( "<radio-select checked=\"\"></radio-select>" );
        });

        test( "can be set via attribute reflect property", function() {
          var radioSelect = document.createElement( "radio-select" );

          radioSelect.setAttribute( "checked", "" );

          expect( radioSelect )
            .to.have.property( "checked" )
            .that.is.a( "boolean" )
            .and.equals( true );
        });

        test( "can be set via property reflect attribute", function() {
          var radioSelect = document.createElement( "radio-select" ),
              observeFn = function( changes ) {
                expect( radioSelect.hasAttribute( "checked" ) ).to.equal( true );
                expect( radioSelect.getAttribute( "checked" ) )
                  .to.be.a( "string" )
                  .and.equals( "" );

                Object.unobserve( radioSelect, observeFn );
              };

          radioSelect.checked = true;

          Object.observe( radioSelect, observeFn );
        });

        test( "can be removed via attribute", function() {
          var radioSelect = document.createElement( "radio-select" );

          radioSelect.setAttribute( "checked", "" );
          radioSelect.removeAttribute( "checked" );

          expect( radioSelect )
            .to.have.property( "outerHTML" )
            .that.is.a( "string" )
            .and.equals( "<radio-select></radio-select>" );
        });

        test( "can be removed via attribute reflect property", function() {
          var radioSelect = document.createElement( "radio-select" );

          radioSelect.checked = true;
          radioSelect.removeAttribute( "checked" );

          expect( radioSelect )
            .to.have.property( "outerHTML" )
            .that.is.a( "string" )
            .and.equals( "<radio-select></radio-select>" );
        });

        test( "can be removed via property reflect attribute", function() {
          var radioSelect = document.createElement( "radio-select" ),
              observeFn = function( changes ) {
                expect( radioSelect )
                  .to.have.property( "outerHTML" )
                  .that.is.a( "string" )
                  .and.equals( "<radio-select></radio-select>" );

                Object.unobserve( radioSelect, observeFn );
              };

          radioSelect.setAttribute( "checked", "" );
          radioSelect.checked = false;

          Object.observe( radioSelect, observeFn );
        });
      });

      // Tests for disabled attribute and property
      suite( "disabled", function() {
        test( "has default value: false", function() {
          expect( document.createElement( "radio-select" ) )
            .to.have.property( "disabled" )
            .that.is.a( "boolean" )
            .and.equals( false );
        });

        test( "can be set via attribute", function() {
          var radioSelect = document.createElement( "radio-select" );

          radioSelect.setAttribute( "disabled", "" );

          expect( radioSelect.hasAttribute( "disabled" ) ).to.equal( true );
          expect( radioSelect.getAttribute( "disabled" ) )
            .to.be.a( "string" )
            .and.equals( "" );

          expect( radioSelect )
            .to.have.property( "outerHTML" )
            .that.is.a( "string" )
            .and.equals( "<radio-select disabled=\"\"></radio-select>" );
        });

        test( "can be set via attribute reflect property", function() {
          var radioSelect = document.createElement( "radio-select" );

          radioSelect.setAttribute( "disabled", "" );

          expect( radioSelect )
            .to.have.property( "disabled" )
            .that.is.a( "boolean" )
            .and.equals( true );
        });

        test( "can be set via property reflect attribute", function() {
          var radioSelect = document.createElement( "radio-select" ),
              observeFn = function( changes ) {
                expect( radioSelect.hasAttribute( "disabled" ) ).to.equal( true );
                expect( radioSelect.getAttribute( "disabled" ) )
                  .to.be.a( "string" )
                  .and.equals( "" );

                Object.unobserve( radioSelect, observeFn );
              };

          radioSelect.checked = true;

          Object.observe( radioSelect, observeFn );
        });

        test( "can be removed via attribute", function() {
          var radioSelect = document.createElement( "radio-select" );

          radioSelect.setAttribute( "disabled", "" );
          radioSelect.removeAttribute( "disabled" );

          expect( radioSelect )
            .to.have.property( "outerHTML" )
            .that.is.a( "string" )
            .and.equals( "<radio-select></radio-select>" );
        });

        test( "can be removed via attribute reflect property", function() {
          var radioSelect = document.createElement( "radio-select" );

          radioSelect.disabled = true;
          radioSelect.removeAttribute( "disabled" );

          expect( radioSelect )
            .to.have.property( "outerHTML" )
            .that.is.a( "string" )
            .and.equals( "<radio-select></radio-select>" );
        });

        test( "can be removed via property reflect attribute", function() {
          var radioSelect = document.createElement( "radio-select" ),
              observeFn = function( changes ) {
                expect( radioSelect )
                  .to.have.property( "outerHTML" )
                  .that.is.a( "string" )
                  .and.equals( "<radio-select></radio-select>" );

                Object.unobserve( radioSelect, observeFn );
              };

          radioSelect.setAttribute( "disabled", "" );
          radioSelect.checked = false;

          Object.observe( radioSelect, observeFn );
        });
      });
    });

    suite( "Events", function() {
      test( "uncheck other <radio-select> elements when one is checked", function() {
        var radioSelect = document.createElement( "radio-select" ),
            observeFn = function( changes ) {
              expect( radioSelect )
                .to.have.property( "outerHTML" )
                .that.is.a( "string" )
                .and.equals( "<radio-select></radio-select>" );

              Object.unobserve( radioSelect, observeFn );
            };

        radioSelect.setAttribute( "checked", "" );
        document.createElement( "radio-select" ).setAttribute( "checked", "" );

        Object.observe( radioSelect, observeFn );
      });
    });
  });
})( window, document, window.Polymer, window.sinon, window.chai );
