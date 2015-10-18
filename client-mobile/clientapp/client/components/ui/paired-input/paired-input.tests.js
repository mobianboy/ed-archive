/*eslint-env mocha */
( function( window, document, polymer, sinon, chai ) {
  "use strict";
  var expect = chai.expect,
    // get wrapper from document or for karma, create a new div and append it to the DOM
    testingWrapper = document.getElementById( "paired-input-test-wrapper" ) ||
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
    },
    acceptableTypeValues = [
      "text",
      "password",
      "email",
      "tel",
      "number",
      "url",
      "search"
    ];

  suite( "<paired-input>", function() {
    suite( "Life Cycle", function() {
      teardown( function() {
        resetWrapper();
      });

      test( "ready: can create from document.createElement", function() {
        var createdSpy = sinon.spy(
          polymer.getRegisteredPrototype( "paired-input" ),
          "ready"
        );

        expect( document.createElement( "paired-input" ) )
          .to.have.property( "outerHTML" )
          .that.is.a( "string" )
          .and.equals( "<paired-input type=\"text\"></paired-input>" );

        expect( createdSpy ).to.have.callCount( 1 );
        createdSpy.restore();
      });

      test( "attached: can be added to another DOM Element", function() {
        var pairedInput = document.createElement( "paired-input" ),
          attachedSpy = sinon.spy( pairedInput, "attached" );

        testingWrapper.appendChild( pairedInput );

        expect( attachedSpy ).to.have.callCount( 1 );

        expect( testingWrapper )
          .to.have.property( "innerHTML" )
          .that.is.a( "string" )
          .and.equals( "<paired-input type=\"text\"></paired-input>" );

        attachedSpy.restore();
      });

      test( "detached: can be removed from another DOM element", function() {
        var pairedInput = document.createElement( "paired-input" ),
          detachedSpy = sinon.spy( pairedInput, "detached" );

        testingWrapper.appendChild( pairedInput );
        testingWrapper.removeChild( pairedInput );

        expect( detachedSpy ).to.have.callCount( 1 );

        expect( testingWrapper )
          .to.have.property( "outerHTML" )
          .that.is.a( "string" )
          .and.equals( originalWrapperOuterHTML );

        detachedSpy.restore();
      });
    });

    suite( "Attributes and Properties", function() {
      suite( "type", function() {
        suite( "default values", function() {
          test( "default value is \"text\"", function() {
            var pairedInput = document.createElement( "paired-input" );

            pairedInput.setAttribute( "type", "default" );

            expect( pairedInput.hasAttribute( "type" ) )
              .to.equal( true );

            expect( pairedInput.getAttribute( "type" ) )
              .to.be.a( "string" )
              .that.equals( "text" );

            expect( pairedInput )
              .to.have.property( "outerHTML" )
              .that.equals( "<paired-input type=\"text\"></paired-input>" );
          });

          test( "setting via setAttribute, default value \"text\" reflects to property",
            function() {
              var pairedInput = document.createElement( "paired-input" );

              pairedInput.setAttribute( "type", "default" );

              expect( pairedInput )
                .to.have.property( "type" )
                .that.equals( "text" )
                .and.equals( pairedInput.getAttribute( "type" ) );
            });

          test( "setting via property, default value \"text\" reflects to attribute", function() {
            var pairedInput = document.createElement( "paired-input" );

            pairedInput.type = "invalid-value";

            expect( pairedInput.getAttribute( "type" ) )
              .to.be.a( "string" )
              .that.equals( "text" );
          });
        });

        suite( "attempting to set invalid values", function() {
          test( "when set to an invalid value via setAttribute defaults to text", function() {
            var pairedInput = document.createElement( "paired-input" );

            pairedInput.setAttribute( "type", "invalid-value" );

            expect( pairedInput.hasAttribute( "type" ) )
              .to.equal( true );

            expect( pairedInput.getAttribute( "type" ) )
              .to.be.a( "string" )
              .that.equals( "text" );
          });

          test( "when set to an invalid value via setAttribute reflects to property", function() {
            var pairedInput = document.createElement( "paired-input" );

            pairedInput.setAttribute( "type", "invalid-value" );

            expect( pairedInput.hasAttribute( "type" ) )
              .to.equal( true );

            expect( pairedInput )
              .to.have.property( "type" )
              .that.equals( "text" )
              .and.equals( pairedInput.getAttribute( "type" ) );
          });

          test( "when set to an invalid value via property defaults to text", function() {
            var pairedInput = document.createElement( "paired-input" );

            pairedInput.type = "invalid-value";

            expect( pairedInput )
              .to.have.property( "type" )
              .that.equals( "text" )
              .and.equals( pairedInput.getAttribute( "type" ) );
          });

          test( "when set to an invalid value via property reflects to attribute", function() {
            var pairedInput = document.createElement( "paired-input" );

            pairedInput.type = "invalid-value";

            expect( pairedInput.getAttribute( "type" ) )
              .to.be.a( "string" )
              .that.equals( "text" );
          });
        });

        suite( "set acceptable values via setAttribute", function() {
          acceptableTypeValues.forEach( function( value ) {
            test( "can be set via setAttribute to " + value, function() {
              var pairedInput = document.createElement( "paired-input" );

              pairedInput.setAttribute( "type", value );

              expect( pairedInput.hasAttribute( "type" ) )
                .to.equal( true );

              expect( pairedInput.getAttribute( "type" ) )
                .to.be.a( "string" )
                .that.equals( value );
            });
          });

          acceptableTypeValues.forEach( function( value ) {
            test( "setting via " + value + " reflects to property", function() {
              var pairedInput = document.createElement( "paired-input" );

              pairedInput.setAttribute( "type", value );

              expect( pairedInput )
                .to.have.property( "type" )
                .that.equals( value )
                .and.equals( pairedInput.getAttribute( "type" ) );
            });
          });
        });

        suite( "set acceptable values via property", function() {
          acceptableTypeValues.forEach( function( value ) {
            test( "can be set via property to " + value, function() {
              var pairedInput = document.createElement( "paired-input" );

              pairedInput.type = value;

              expect( pairedInput )
                .to.have.property( "type" )
                .that.equals( value )
                .and.equals( pairedInput.getAttribute( "type" ) );
            });
          });

          acceptableTypeValues.forEach( function( value ) {
            test( "setting via " + value + " reflects to attribute", function() {
              var pairedInput = document.createElement( "paired-input" );

              pairedInput.type = value;

              expect( pairedInput.getAttribute( "type" ) )
                .to.be.a( "string" )
                .that.equals( value );
            });
          });
        });
      });

      suite( "placeholder", function() {
        test( "can be set via \"setAttribute\"", function() {
          var pairedInput = document.createElement( "paired-input" );

          pairedInput.setAttribute( "placeholder", "type-name" );

          expect( pairedInput.hasAttribute( "placeholder" ) ).to.equal( true );

          expect( pairedInput.getAttribute( "placeholder" ) )
            .to.be.a( "string" )
            .that.equals( "type-name" );

          expect( pairedInput )
            .to.have.property( "outerHTML" )
            .that.equals( "<paired-input type=\"text\" placeholder=\"type-name\"></paired-input>" );
        });

        test( "can be removed via removeAttribute", function() {
          var pairedInput = document.createElement( "paired-input" );

          pairedInput.setAttribute( "placeholder", "type-name" );
          pairedInput.removeAttribute( "placeholder" );

          expect( pairedInput )
            .to.have.property( "outerHTML" )
            .that.is.a( "string" )
            .and.equals( "<paired-input type=\"text\"></paired-input>" );
        });

        test( "when set via setAttribute, placeholder value should reflect to shadowDom",
          function() {
            var pairedInput = document.createElement( "paired-input" ),
              observeFn = function( changes ) {
                expect( pairedInput.$.primaryBox.getAttribute( "placeholder" ) )
                  .to.be.a( "string" )
                  .that.equals( "type-name" );

                expect( pairedInput.$.confirmBox.getAttribute( "placeholder" ) )
                  .to.be.a( "string" )
                  .that.equals( "Confirm " + "type-name" );

                Object.unobserve( pairedInput, observeFn );
              };

            pairedInput.setAttribute( "placeholder", "type-name" );

            Object.observe( pairedInput, observeFn );
          });

        test( "when set via property, placeholder value should reflect to shadowDom",
          function() {
            var pairedInput = document.createElement( "paired-input" ),
              observeFn = function( changes ) {
                expect( pairedInput.$.primaryBox )
                  .to.have.property( "placeholder" )
                  .that.equals( "type-name" );

                expect( pairedInput.$.confirmBox )
                  .to.have.property( "placeholder" )
                  .that.equals( "Confirm type-name" );

                Object.unobserve( pairedInput, observeFn );
              };

            pairedInput.placeholder = "type-name";

            Object.observe( pairedInput, observeFn );
          });
      });

      suite( "pattern", function() {
        test( "can be set via setAttribute", function() {
          var pairedInput = document.createElement( "paired-input" );

          pairedInput.setAttribute( "pattern", "regex" );

          expect( pairedInput.hasAttribute( "pattern" ) ).to.equal( true );

          expect( pairedInput.getAttribute( "pattern" ) )
            .to.be.a( "string" )
            .that.equals( "regex" );

          expect( pairedInput )
            .to.have.property( "outerHTML" )
            .that.equals( "<paired-input type=\"text\" pattern=\"regex\"></paired-input>" );
        });

        test( "can be removed via removeAttribute", function() {
          var pairedInput = document.createElement( "paired-input" );

          pairedInput.setAttribute( "pattern", "regex" );
          pairedInput.removeAttribute( "pattern" );

          expect( pairedInput )
            .to.have.property( "outerHTML" )
            .that.is.a( "string" )
            .and.equals( "<paired-input type=\"text\"></paired-input>" );
        });

        test( "setting via setAttribute reflects to property", function() {
          var pairedInput = document.createElement( "paired-input" );

          pairedInput.setAttribute( "pattern", "regex" );

          expect( pairedInput )
            .to.have.property( "pattern" )
            .that.equals( "regex" )
            .and.equals( pairedInput.getAttribute( "pattern" ) );
        });

        test( "can be set via property to pattern", function() {
          var pairedInput = document.createElement( "paired-input" );

          pairedInput.pattern = "regex";

          expect( pairedInput )
            .to.have.property( "pattern" )
            .that.equals( "regex" )
            .and.equals( pairedInput.getAttribute( "pattern" ) );

          expect( pairedInput )
            .to.have.property( "outerHTML" )
            .that.equals( "<paired-input type=\"text\" pattern=\"regex\"></paired-input>" );
        });

        test( "setting via property reflects to attribute", function() {
          var pairedInput = document.createElement( "paired-input" );

          pairedInput.pattern = "regex";

          expect( pairedInput.getAttribute( "pattern" ) )
            .to.be.a( "string" )
            .that.equals( "regex" );
        });
      });

      suite( "single-line", function() {
        test( "default value is false", function() {
          var pairedInput = document.createElement( "paired-input" );

          expect( pairedInput )
            .to.have.property( "singleLine" )
            .that.is.a( "boolean" )
            .and.equals( false );
        });

        test( "can be set via attribute", function() {
          var pairedInput = document.createElement( "paired-input" );

          pairedInput.setAttribute( "single-line", "" );

          expect( pairedInput.hasAttribute( "single-line" ) )
            .to.equal( true );

          expect( pairedInput.getAttribute( "single-line" ) )
            .to.be.a( "string" )
            .that.equals( "" );
        });

        test( "can be removed via attribute", function() {
          var pairedInput = document.createElement( "paired-input" );

          pairedInput.setAttribute( "single-line", "" );
          pairedInput.removeAttribute( "single-line" );

          expect( pairedInput )
            .to.have.property( "outerHTML" )
            .that.is.a( "string" )
            .and.equals( "<paired-input type=\"text\"></paired-input>" );
        });

        test( "can be set via property to single-line", function() {
          var pairedInput = document.createElement( "paired-input" );

          pairedInput.singleLine = true;

          expect( pairedInput )
            .to.have.property( "singleLine" )
            .that.is.a( "boolean" )
            .and.equals( true );
        });
      });

      suite( "required", function() {
        test( "default value is false", function() {
          var pairedInput = document.createElement( "paired-input" );

          expect( pairedInput )
            .to.have.property( "required" )
            .that.is.a( "boolean" )
            .and.equals( false );

          expect( pairedInput )
            .to.have.property( "outerHTML" )
            .that.is.a( "string" )
            .and.equals( "<paired-input type=\"text\"></paired-input>" );
        });

        test( "can be set via setAttribute", function() {
          var pairedInput = document.createElement( "paired-input" );

          pairedInput.setAttribute( "required", "" );

          expect( pairedInput.hasAttribute( "required" ) )
            .to.equal( true );

          expect( pairedInput.getAttribute( "required" ) )
            .to.be.a( "string" )
            .and.equals( "" );
        });

        test( "setting via setAttribute reflects to property", function() {
          var pairedInput = document.createElement( "paired-input" );

          pairedInput.setAttribute( "required", "" );

          expect( pairedInput )
            .to.have.property( "required" )
            .and.equals( true );
        });

        test( "can be removed via removeAttribute", function() {
          var pairedInput = document.createElement( "paired-input" );

          pairedInput.setAttribute( "required", "" );
          pairedInput.removeAttribute( "required" );

          expect( pairedInput )
            .to.have.property( "outerHTML" )
            .that.is.a( "string" )
            .and.equals( "<paired-input type=\"text\"></paired-input>" );
        });

        test( "can be set via property to required", function() {
          var pairedInput = document.createElement( "paired-input" );

          pairedInput.required = true;

          expect( pairedInput )
            .to.have.property( "required" )
            .that.is.a( "boolean" )
            .and.equals( true );
        });

        test( "setting via property reflects to attribute", function() {
          var pairedInput = document.createElement( "paired-input" );

          pairedInput.required = true;

          expect( pairedInput.hasAttribute( "required" ) )
            .to.be.a( "boolean" )
            .that.equals( true );
        });

        test( "can be removed via property", function() {
          var pairedInput = document.createElement( "paired-input" );

          pairedInput.required = true;
          pairedInput.required = false;

          expect( pairedInput.hasAttribute( "required" ) )
            .to.be.a( "boolean" )
            .that.equals( false );
        });
      });

      suite( "disabled", function() {
        test( "default value is false", function() {
          var pairedInput = document.createElement( "paired-input" );

          expect( pairedInput )
            .to.have.property( "disabled" )
            .that.is.a( "boolean" )
            .and.equals( false );
        });

        test( "can be set via setAttribute", function() {
          var pairedInput = document.createElement( "paired-input" );

          pairedInput.setAttribute( "disabled", "" );

          expect( pairedInput.hasAttribute( "disabled" ) )
            .to.equal( true );

          expect( pairedInput.getAttribute( "disabled" ) )
            .to.be.a( "string" )
            .and.equals( "" );
        });

        test( "setting via setAttribute reflects to property", function() {
          var pairedInput = document.createElement( "paired-input" );

          pairedInput.setAttribute( "disabled", "" );

          expect( pairedInput )
            .to.have.property( "disabled" )
            .and.equals( true );
        });

        test( "can be removed via removeAttribute", function() {
          var pairedInput = document.createElement( "paired-input" );

          pairedInput.setAttribute( "disabled", "" );
          pairedInput.removeAttribute( "disabled" );

          expect( pairedInput )
            .to.have.property( "outerHTML" )
            .that.is.a( "string" )
            .and.equals( "<paired-input type=\"text\"></paired-input>" );
        });

        test( "can be set via property to disabled", function() {
          var pairedInput = document.createElement( "paired-input" );

          pairedInput.disabled = true;

          expect( pairedInput )
            .to.have.property( "disabled" )
            .that.is.a( "boolean" )
            .and.equals( true );
        });

        test( "setting via property reflects to attribute", function() {
          var pairedInput = document.createElement( "paired-input" );

          pairedInput.disabled = true;

          expect( pairedInput.hasAttribute( "disabled" ) )
            .to.be.a( "boolean" )
            .that.equals( true );
        });

        test( "can be removed via property", function() {
          var pairedInput = document.createElement( "paired-input" );

          pairedInput.disabled = true;
          pairedInput.disabled = false;

          expect( pairedInput.hasAttribute( "disabled" ) )
            .to.be.a( "boolean" )
            .that.equals( false );
        });
      });

      suite( "isValid", function() {
        test( "default value is false", function() {
          var pairedInput = document.createElement( "paired-input" );

          expect( pairedInput )
            .to.have.property( "isValid" )
            .that.is.a( "boolean" )
            .and.equals( false );
        });

        test( "is true when input matches the pattern and both values match", function() {
          var pairedInput = document.createElement( "paired-input" );

          pairedInput.$.primaryBox.value = "input-text";
          pairedInput.$.confirmBox.value = "input-text";

          if ( pairedInput.$.primaryBox.validity.valid ) {
            expect( pairedInput )
              .to.have.property( "isValid" )
              .that.is.a( "boolean" )
              .and.equals( true );
          }
        });
      });

      suite( "val", function() {
        test( "returns undefined when element is not valid", function() {
          var pairedInput = document.createElement( "paired-input" );

          pairedInput.$.primaryBox.value = "first value";
          pairedInput.$.confirmBox.value = "second value";

          expect( pairedInput )
            .to.have.property( "val" )
            .and.equals( undefined );
        });

        test( "returns value if both input fields match", function() {
          var pairedInput = document.createElement( "paired-input" );

          pairedInput.$.primaryBox.value = "input-text";
          pairedInput.$.confirmBox.value = "input-text";

          expect( pairedInput )
            .to.have.property( "val" )
            .to.be.a( "string" )
            .that.equals( "input-text" );
        });

        test( "set value of both input fields via property", function() {
          var pairedInput = document.createElement( "paired-input" ),
              genericValue = "random test value";

          pairedInput.val = genericValue;

          expect( pairedInput.$.primaryBox.value )
            .to.be.a( "string" )
            .that.equals( genericValue );

          expect( pairedInput.$.confirmBox.value )
            .to.be.a( "string" )
            .that.equals( genericValue );
        });
      });
    });
  });
})( window, document, window.Polymer, window.sinon, window.chai );
