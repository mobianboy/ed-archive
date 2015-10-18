/*eslint-env mocha */
( function( window, document, polymer, sinon, chai ) {
  "use strict";
  var expect = chai.expect,
    // get wrapper from document or for karma, create a new div and append it to the DOM
    testingWrapper = document.getElementById( "ed-paired-input-test-wrapper" ) ||
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

  suite( "<ed-paired-input>", function() {
    suite( "Life Cycle", function() {
      teardown( function() {
        resetWrapper();
      });

      test( "ready: can create from document.createElement", function() {
        var createdSpy = sinon.spy(
          polymer.getRegisteredPrototype( "ed-paired-input" ),
          "ready"
        );

        expect( document.createElement( "ed-paired-input" ) )
          .to.have.property( "outerHTML" )
          .that.is.a( "string" )
          .and.equals( "<ed-paired-input></ed-paired-input>" );

        expect( createdSpy ).to.have.callCount( 1 );
        createdSpy.restore();
      });

      test( "attached: can be added to another DOM Element", function() {
        var edInput = document.createElement( "ed-paired-input" ),
          attachedSpy = sinon.spy( edInput, "attached" );

        testingWrapper.appendChild( edInput );

        expect( attachedSpy ).to.have.callCount( 1 );

        expect( testingWrapper )
          .to.have.property( "innerHTML" )
          .that.is.a( "string" )
          .and.equals( "<ed-paired-input></ed-paired-input>" );

        attachedSpy.restore();
      });

      test( "detached: can be removed from another DOM element", function() {
        var edInput = document.createElement( "ed-paired-input" ),
          detachedSpy = sinon.spy( edInput, "detached" );

        testingWrapper.appendChild( edInput );
        testingWrapper.removeChild( edInput );

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
            var edInput = document.createElement( "ed-paired-input" );

            edInput.setAttribute( "type", "default" );

            expect( edInput.hasAttribute( "type" ) )
              .to.equal( true );

            expect( edInput.getAttribute( "type" ) )
              .to.be.a( "string" )
              .that.equals( "text" );

            expect( edInput )
              .to.have.property( "outerHTML" )
              .that.equals( "<ed-paired-input type=\"text\"></ed-paired-input>" );
          });

          test( "setting via setAttribute, default value \"text\" reflects to property",
            function() {
              var edInput = document.createElement( "ed-paired-input" );

              edInput.setAttribute( "type", "default" );

              expect( edInput )
                .to.have.property( "type" )
                .that.equals( "text" )
                .and.equals( edInput.getAttribute( "type" ) );
            });

          test( "setting via property, default value \"text\" reflects to attribute", function() {
            var edInput = document.createElement( "ed-paired-input" );

            edInput.type = "invalid-value";

            expect( edInput.getAttribute( "type" ) )
              .to.be.a( "string" )
              .that.equals( "text" );
          });
        });

        suite( "attempting to set invalid values", function() {
          test( "when set to an invalid value via setAttribute defaults to text", function() {
            var edInput = document.createElement( "ed-paired-input" );

            edInput.setAttribute( "type", "invalid-value" );

            expect( edInput.hasAttribute( "type" ) )
              .to.equal( true );

            expect( edInput.getAttribute( "type" ) )
              .to.be.a( "string" )
              .that.equals( "text" );
          });

          test( "when set to an invalid value via setAttribute reflects to property", function() {
            var edInput = document.createElement( "ed-paired-input" );

            edInput.setAttribute( "type", "invalid-value" );

            expect( edInput.hasAttribute( "type" ) )
              .to.equal( true );

            expect( edInput )
              .to.have.property( "type" )
              .that.equals( "text" )
              .and.equals( edInput.getAttribute( "type" ) );
          });

          test( "when set to an invalid value via property defaults to text", function() {
            var edInput = document.createElement( "ed-paired-input" );

            edInput.type = "invalid-value";

            expect( edInput )
              .to.have.property( "type" )
              .that.equals( "text" )
              .and.equals( edInput.getAttribute( "type" ) );
          });

          test( "when set to an invalid value via property reflects to attribute", function() {
            var edInput = document.createElement( "ed-paired-input" );

            edInput.type = "invalid-value";

            expect( edInput.getAttribute( "type" ) )
              .to.be.a( "string" )
              .that.equals( "text" );
          });
        });

        suite( "set acceptable values via setAttribute", function() {
          acceptableTypeValues.forEach( function( value ) {
            test( "can be set via setAttribute to " + value, function() {
              var edInput = document.createElement( "ed-paired-input" );

              edInput.setAttribute( "type", value );

              expect( edInput.hasAttribute( "type" ) )
                .to.equal( true );

              expect( edInput.getAttribute( "type" ) )
                .to.be.a( "string" )
                .that.equals( value );
            });
          });

          acceptableTypeValues.forEach( function( value ) {
            test( "setting via " + value + " reflects to property", function() {
              var edInput = document.createElement( "ed-paired-input" );

              edInput.setAttribute( "type", value );

              expect( edInput )
                .to.have.property( "type" )
                .that.equals( value )
                .and.equals( edInput.getAttribute( "type" ) );
            });
          });
        });

        suite( "set acceptable values via property", function() {
          acceptableTypeValues.forEach( function( value ) {
            test( "can be set via property to " + value, function() {
              var edInput = document.createElement( "ed-paired-input" );

              edInput.type = value;

              expect( edInput )
                .to.have.property( "type" )
                .that.equals( value )
                .and.equals( edInput.getAttribute( "type" ) );
            });
          });

          acceptableTypeValues.forEach( function( value ) {
            test( "setting via " + value + " reflects to attribute", function() {
              var edInput = document.createElement( "ed-paired-input" );

              edInput.type = value;

              expect( edInput.getAttribute( "type" ) )
                .to.be.a( "string" )
                .that.equals( value );
            });
          });
        });
      });

      suite( "placeholder", function() {
        test( "can be set via \"setAttribute\"", function() {
          var edInput = document.createElement( "ed-paired-input" );

          edInput.setAttribute( "placeholder", "type-name" );

          expect( edInput.hasAttribute( "placeholder" ) ).to.equal( true );

          expect( edInput.getAttribute( "placeholder" ) )
            .to.be.a( "string" )
            .that.equals( "type-name" );

          expect( edInput )
            .to.have.property( "outerHTML" )
            .that.equals( "<ed-paired-input placeholder=\"type-name\"></ed-paired-input>" );
        });

        test( "can be removed via removeAttribute", function() {
          var edInput = document.createElement( "ed-paired-input" );

          edInput.setAttribute( "placeholder", "type-name" );
          edInput.removeAttribute( "placeholder" );

          expect( edInput )
            .to.have.property( "outerHTML" )
            .that.is.a( "string" )
            .and.equals( "<ed-paired-input></ed-paired-input>" );
        });

        test( "when set via setAttribute, placeholder value should reflect to shadowDom",
          function() {
            var edInput = document.createElement( "ed-paired-input" ),
              observeFn = function( changes ) {
                expect( edInput.$.primaryBox.getAttribute( "placeholder" ) )
                  .to.be.a( "string" )
                  .that.equals( "type-name" );

                expect( edInput.$.confirmBox.getAttribute( "placeholder" ) )
                  .to.be.a( "string" )
                  .that.equals( "Confirm " + "type-name" );

                Object.unobserve( edInput, observeFn );
              };

            edInput.setAttribute( "placeholder", "type-name" );

            Object.observe( edInput, observeFn );
          });

        test( "when set via property, placeholder value should reflect to shadowDom",
          function() {
            var edInput = document.createElement( "ed-paired-input" ),
              observeFn = function( changes ) {
                expect( edInput.$.primaryBox )
                  .to.have.property( "placeholder" )
                  .that.equals( "type-name" );

                expect( edInput.$.confirmBox )
                  .to.have.property( "placeholder" )
                  .that.equals( "Confirm type-name" );

                Object.unobserve( edInput, observeFn );
              };

            edInput.placeholder = "type-name";

            Object.observe( edInput, observeFn );
          });
      });

      suite( "pattern", function() {
        test( "can be set via setAttribute", function() {
          var edInput = document.createElement( "ed-paired-input" );

          edInput.setAttribute( "pattern", "regex" );

          expect( edInput.hasAttribute( "pattern" ) ).to.equal( true );

          expect( edInput.getAttribute( "pattern" ) )
            .to.be.a( "string" )
            .that.equals( "regex" );

          expect( edInput )
            .to.have.property( "outerHTML" )
            .that.equals( "<ed-paired-input pattern=\"regex\"></ed-paired-input>" );
        });

        test( "can be removed via removeAttribute", function() {
          var edInput = document.createElement( "ed-paired-input" );

          edInput.setAttribute( "pattern", "regex" );
          edInput.removeAttribute( "pattern" );

          expect( edInput )
            .to.have.property( "outerHTML" )
            .that.is.a( "string" )
            .and.equals( "<ed-paired-input></ed-paired-input>" );
        });

        test( "setting via setAttribute reflects to property", function() {
          var edInput = document.createElement( "ed-paired-input" );

          edInput.setAttribute( "pattern", "regex" );

          expect( edInput )
            .to.have.property( "pattern" )
            .that.equals( "regex" )
            .and.equals( edInput.getAttribute( "pattern" ) );
        });

        test( "can be set via property to pattern", function() {
          var edInput = document.createElement( "ed-paired-input" );

          edInput.pattern = "regex";

          expect( edInput )
            .to.have.property( "pattern" )
            .that.equals( "regex" )
            .and.equals( edInput.getAttribute( "pattern" ) );

          expect( edInput )
            .to.have.property( "outerHTML" )
            .that.equals( "<ed-paired-input pattern=\"regex\"></ed-paired-input>" );
        });

        test( "setting via property reflects to attribute", function() {
          var edInput = document.createElement( "ed-paired-input" );

          edInput.pattern = "regex";

          expect( edInput.getAttribute( "pattern" ) )
            .to.be.a( "string" )
            .that.equals( "regex" );
        });
      });

      suite( "required", function() {
        test( "default value is false", function() {
          var edInput = document.createElement( "ed-paired-input" );

          expect( edInput )
            .to.have.property( "required" )
            .that.is.a( "boolean" )
            .and.equals( false );

          expect( edInput )
            .to.have.property( "outerHTML" )
            .that.is.a( "string" )
            .and.equals( "<ed-paired-input></ed-paired-input>" );
        });

        test( "can be set via setAttribute", function() {
          var edInput = document.createElement( "ed-paired-input" );

          edInput.setAttribute( "required", "" );

          expect( edInput.hasAttribute( "required" ) )
            .to.equal( true );

          expect( edInput.getAttribute( "required" ) )
            .to.be.a( "string" )
            .and.equals( "" );
        });

        test( "setting via setAttribute reflects to property", function() {
          var edInput = document.createElement( "ed-paired-input" );

          edInput.setAttribute( "required", "" );

          expect( edInput )
            .to.have.property( "required" )
            .and.equals( true );
        });

        test( "can be removed via removeAttribute", function() {
          var edInput = document.createElement( "ed-paired-input" );

          edInput.setAttribute( "required", "" );
          edInput.removeAttribute( "required" );

          expect( edInput )
            .to.have.property( "outerHTML" )
            .that.is.a( "string" )
            .and.equals( "<ed-paired-input></ed-paired-input>" );
        });

        test( "can be set via property to required", function() {
          var edInput = document.createElement( "ed-paired-input" );

          edInput.required = true;

          expect( edInput )
            .to.have.property( "required" )
            .that.is.a( "boolean" )
            .and.equals( true );
        });

        test( "setting via property reflects to attribute", function() {
          var edInput = document.createElement( "ed-paired-input" );

          edInput.required = true;

          expect( edInput.hasAttribute( "required" ) )
            .to.be.a( "boolean" )
            .that.equals( true );
        });

        test( "can be removed via property", function() {
          var edInput = document.createElement( "ed-paired-input" );

          edInput.required = true;
          edInput.required = false;

          expect( edInput.hasAttribute( "required" ) )
            .to.be.a( "boolean" )
            .that.equals( false );
        });
      });

      suite( "disabled", function() {
        test( "default value is false", function() {
          var edInput = document.createElement( "ed-paired-input" );

          expect( edInput )
            .to.have.property( "disabled" )
            .that.is.a( "boolean" )
            .and.equals( false );
        });

        test( "can be set via setAttribute", function() {
          var edInput = document.createElement( "ed-paired-input" );

          edInput.setAttribute( "disabled", "" );

          expect( edInput.hasAttribute( "disabled" ) )
            .to.equal( true );

          expect( edInput.getAttribute( "disabled" ) )
            .to.be.a( "string" )
            .and.equals( "" );
        });

        test( "setting via setAttribute reflects to property", function() {
          var edInput = document.createElement( "ed-paired-input" );

          edInput.setAttribute( "disabled", "" );

          expect( edInput )
            .to.have.property( "disabled" )
            .and.equals( true );
        });

        test( "can be removed via removeAttribute", function() {
          var edInput = document.createElement( "ed-paired-input" );

          edInput.setAttribute( "disabled", "" );
          edInput.removeAttribute( "disabled" );

          expect( edInput )
            .to.have.property( "outerHTML" )
            .that.is.a( "string" )
            .and.equals( "<ed-paired-input></ed-paired-input>" );
        });

        test( "can be set via property to disabled", function() {
          var edInput = document.createElement( "ed-paired-input" );

          edInput.disabled = true;

          expect( edInput )
            .to.have.property( "disabled" )
            .that.is.a( "boolean" )
            .and.equals( true );
        });

        test( "setting via property reflects to attribute", function() {
          var edInput = document.createElement( "ed-paired-input" );

          edInput.disabled = true;

          expect( edInput.hasAttribute( "disabled" ) )
            .to.be.a( "boolean" )
            .that.equals( true );
        });

        test( "can be removed via property", function() {
          var edInput = document.createElement( "ed-paired-input" );

          edInput.disabled = true;
          edInput.disabled = false;

          expect( edInput.hasAttribute( "disabled" ) )
            .to.be.a( "boolean" )
            .that.equals( false );
        });
      });

      suite( "isValid", function() {
        test( "default value is false", function() {
          var edInput = document.createElement( "ed-paired-input" );

          expect( edInput )
            .to.have.property( "isValid" )
            .that.is.a( "boolean" )
            .and.equals( false );
        });

        test( "is true when input matches the pattern and both values match", function() {
          var edInput = document.createElement( "ed-paired-input" );

          edInput.primaryBox.value = "input-text";
          edInput.confirmBox.value = "input-text";

          if ( edInput.primaryBox.validity.valid ) {
            expect( edInput )
              .to.have.property( "isValid" )
              .that.is.a( "boolean" )
              .and.equals( true );
          }
        });
      });

      suite( "val", function() {
        test( "returns undefined when element is not valid", function() {
          var edInput = document.createElement( "ed-paired-input" );

          edInput.primaryBox.value = "first value";
          edInput.confirmBox.value = "second value";

          expect( edInput )
            .to.have.property( "val" )
            .and.equals( undefined );
        });

        test( "returns value if both input fields match", function() {
          var edInput = document.createElement( "ed-paired-input" );

          edInput.primaryBox.value = "input-text";
          edInput.confirmBox.value = "input-text";

          expect( edInput )
            .to.have.property( "val" )
            .to.be.a( "string" )
            .that.equals( "input-text" );
        });

        test( "set value of both input fields via property", function() {
          var edInput = document.createElement( "ed-paired-input" ),
              genericValue = "random test value";

          edInput.val = genericValue;

          expect( edInput.primaryBox.value )
            .to.be.a( "string" )
            .that.equals( genericValue );

          expect( edInput.confirmBox.value )
            .to.be.a( "string" )
            .that.equals( genericValue );
        });
      });
    });
  });
})( window, document, window.Polymer, window.sinon, window.chai );
