/*eslint-env mocha */
/*jscs:disable maximumLineLength*/
( function( window, document, chai ) {
  "use strict";
  var expect = chai.expect,
  // get wrapper from document or for karma, create a new div and append it to the DOM
    testingWrapper = document.getElementById( "flip-box-test-wrapper" ) ||
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

  suite( "<flip-box>", function() {
    suite( "Life Cycle", function() {
      test( "ready: can create from document.createElement", function() {
        expect( document.createElement( "flip-box" ) )
          .to.have.property( "outerHTML" )
          .that.is.a( "string" )
          .and.equals( "<flip-box></flip-box>" );
      });

      test( "attached: can be added to another DOM Element", function() {
        testingWrapper.appendChild( document.createElement( "flip-box" ) );

        expect( testingWrapper )
          .to.have.property( "innerHTML" )
          .that.is.a( "string" )
          .and.equals( "<flip-box trigger=\"box\" animation=\"horizontal\" rotation=\"toggle\"></flip-box>" );

        resetWrapper();
      });

      test( "detached: can be removed from another DOM Element", function() {
        var flipBox = document.createElement( "flip-box" );

        testingWrapper.appendChild( flipBox );
        testingWrapper.removeChild( flipBox );

        expect( testingWrapper )
          .to.have.property( "outerHTML" )
          .that.is.a( "string" )
          .and.equals( originalWrapperOuterHTML );

        resetWrapper();
      });
    });

    suite( "Attributes", function() {
    /** Animation **/
      suite( "animation", function() {
        // set attribute
        test( "can be set to default value of \"horizontal\" via setAttribute", function() {
          var flipBox = document.createElement( "flip-box" );

          flipBox.setAttribute( "animation", "horizontal" );

          expect( flipBox.hasAttribute( "animation" ) ).to.equal( true );

          expect( flipBox.getAttribute( "animation" ) )
            .to.be.a( "string" )
            .that.equals( "horizontal" );

          expect( flipBox )
            .to.have.property( "outerHTML" )
            .that.equals( "<flip-box animation=\"horizontal\"></flip-box>" );
        });

        test( "can be set to \"vertical\" via setAttribute", function() {
          var flipBox = document.createElement( "flip-box" );

          flipBox.setAttribute( "animation", "vertical" );

          expect( flipBox.hasAttribute( "animation" ) ).to.equal( true );

          expect( flipBox.getAttribute( "animation" ) )
            .to.be.a( "string" )
            .that.equals( "vertical" );

          expect( flipBox )
            .to.have.property( "outerHTML" )
            .that.equals( "<flip-box animation=\"vertical\"></flip-box>" );
        });

        test( "if attempting to set to an invalid value, value will default to \"horizontal\" via setAttribute", function() {
          var flipBox = document.createElement( "flip-box" );

          flipBox.setAttribute( "animation", "Set via Attribute" );

          expect( flipBox.hasAttribute( "animation" ) ).to.equal( true );

          expect( flipBox.getAttribute( "animation" ) )
            .to.be.a( "string" )
            .that.equals( "horizontal" );

          expect( flipBox )
            .to.have.property( "outerHTML" )
            .that.equals( "<flip-box animation=\"horizontal\"></flip-box>" );
        });

        // property
        test( "can be set to \"horizontal\" via \"animation\" property", function() {
            var flipBox = document.createElement( "flip-box" );

            flipBox.animation = "horizontal";

            expect( flipBox )
              .to.have.property( "animation" )
              .that.equals( "horizontal" );
          });

        test( "can be set \"vertical\" via \"animation\" property", function() {
            var flipBox = document.createElement( "flip-box" );

            flipBox.animation = "vertical";

            expect( flipBox )
              .to.have.property( "animation" )
              .that.equals( "vertical" );
          });

        test( "if attempting to set to an invalid value, value will default to \"horizontal\" via \"animation\" property", function() {
            var flipBox = document.createElement( "flip-box" );

            flipBox.animation = "Set via Property";

            expect( flipBox )
              .to.have.property( "animation" )
              .that.equals( "horizontal" );
          });

        // reflection
        test( "if attempting to set to an invalid value via \"setAttribute\", value will default to \"horizontal\", which reflects to \"animation\" property", function() {
            var flipBox = document.createElement( "flip-box" );

            flipBox.setAttribute( "animation", "Set via Property" );

            expect( flipBox )
              .to.have.property( "animation" )
              .that.equals( "horizontal" )
              .and.equals( flipBox.getAttribute( "animation" ) );
          });

        test( "setting to \"horizontal\" via \"setAttribute\" reflects to \"animation\" property",
          function() {
            var flipBox = document.createElement( "flip-box" );

            flipBox.setAttribute( "animation", "horizontal" );

            expect( flipBox )
              .to.have.property( "animation" )
              .that.equals( "horizontal" )
              .and.equals( flipBox.getAttribute( "animation" ) );
          });

        test( "setting to \"vertical\" via \"setAttribute\" reflects to \"animation\" property", function() {
            var flipBox = document.createElement( "flip-box" );

            flipBox.setAttribute( "animation", "vertical" );

            expect( flipBox )
              .to.have.property( "animation" )
              .that.equals( "vertical" )
              .and.equals( flipBox.getAttribute( "animation" ) );
          });

        test( "if attempting to set to an invalid value via \"animation\" property, value will default to \"horizontal\", which reflects to \"animation\" attribute", function() {
            var flipBox = document.createElement( "flip-box" );

            flipBox.animation = "Set via Property";
            expect( flipBox.hasAttribute( "animation" ) ).to.equal( true );
            expect( flipBox.getAttribute( "animation" ) )
              .to.be.a( "string" )
              .that.equals( "horizontal" )
              .and.equal( flipBox.animation );
          });

        test( "setting to \"horizontal\" via \"animation\" property reflects to \"animation\" attribute", function() {
            var flipBox = document.createElement( "flip-box" );

            flipBox.animation = "horizontal";
            expect( flipBox.hasAttribute( "animation" ) ).to.equal( true );
            expect( flipBox.getAttribute( "animation" ) )
              .to.be.a( "string" )
              .that.equals( "horizontal" )
              .and.equal( flipBox.animation );
          });

        test( "setting to \"vertical\" via \"animation\" property reflects to \"animation\" attribute", function() {
            var flipBox = document.createElement( "flip-box" );

            flipBox.animation = "vertical";
            expect( flipBox.hasAttribute( "animation" ) ).to.equal( true );
            expect( flipBox.getAttribute( "animation" ) )
              .to.be.a( "string" )
              .that.equals( "vertical" )
              .and.equal( flipBox.animation );
          });
      });

    /* Trigger */
      suite( "trigger", function() {
        test( "can be set to default value of \"box\" via setAttribute", function() {
          var flipBox = document.createElement( "flip-box" );

          flipBox.setAttribute( "trigger", "box" );

          expect( flipBox.hasAttribute( "trigger" ) ).to.equal( true );

          expect( flipBox.getAttribute( "trigger" ) )
            .to.be.a( "string" )
            .that.equals( "box" );

          expect( flipBox )
            .to.have.property( "outerHTML" )
            .that.equals( "<flip-box trigger=\"box\"></flip-box>" );
        });

        test( "can be set to \"btn\" via setAttribute", function() {
          var flipBox = document.createElement( "flip-box" );

          flipBox.setAttribute( "trigger", "btn" );

          expect( flipBox.hasAttribute( "trigger" ) ).to.equal( true );

          expect( flipBox.getAttribute( "trigger" ) )
            .to.be.a( "string" )
            .that.equals( "btn" );

          expect( flipBox )
            .to.have.property( "outerHTML" )
            .that.equals( "<flip-box trigger=\"btn\"></flip-box>" );
        });

        test( "if attempting to set to an invalid value, value will default to \"box\" via setAttribute", function() {
          var flipBox = document.createElement( "flip-box" );

          flipBox.setAttribute( "trigger", "box" );

          expect( flipBox.hasAttribute( "trigger" ) ).to.equal( true );

          expect( flipBox.getAttribute( "trigger" ) )
            .to.be.a( "string" )
            .that.equals( "box" );

          expect( flipBox )
            .to.have.property( "outerHTML" )
            .that.equals( "<flip-box trigger=\"box\"></flip-box>" );
        });

        test( "can be set to default value of \"box\" via \"trigger\" property",
          function() {
            var flipBox = document.createElement( "flip-box" );

            flipBox.trigger = "box";

            expect( flipBox )
              .to.have.property( "trigger" )
              .that.equals( "box" );
          });

        test( "can be set to \"btn\" via \"trigger\" property",
          function() {
            var flipBox = document.createElement( "flip-box" );

            flipBox.trigger = "btn";

            expect( flipBox )
              .to.have.property( "trigger" )
              .that.equals( "btn" );
          });

        test( "if attempting to set to an invalid value, value will default to \"box\" via \"trigger\" property", function() {
            var flipBox = document.createElement( "flip-box" );

            flipBox.trigger = "box";

            expect( flipBox )
              .to.have.property( "trigger" )
              .that.equals( "box" );
          });

        test( "if attempting to set to an invalid value via \"setAttribute\", value will default to \"box\", which reflects to \"trigger\" property", function() {
            var flipBox = document.createElement( "flip-box" );

            flipBox.setAttribute( "trigger", "Set via Property" );

            expect( flipBox )
              .to.have.property( "trigger" )
              .that.equals( "box" )
              .and.equals( flipBox.getAttribute( "trigger" ) );
          });

        test( "setting to \"box\" via \"setAttribute\" reflects to \"trigger\" property",
          function() {
            var flipBox = document.createElement( "flip-box" );

            flipBox.setAttribute( "trigger", "box" );

            expect( flipBox )
              .to.have.property( "trigger" )
              .that.equals( "box" )
              .and.equals( flipBox.getAttribute( "trigger" ) );
          });

        test( "setting to \"btn\" via \"setAttribute\" reflects to \"trigger\" property",
          function() {
            var flipBox = document.createElement( "flip-box" );

            flipBox.setAttribute( "trigger", "btn" );

            expect( flipBox )
              .to.have.property( "trigger" )
              .that.equals( "btn" )
              .and.equals( flipBox.getAttribute( "trigger" ) );
          });

        test( "if attempting to set to an invalid value via \"trigger\" property, value will default to \"box\", which reflects to \"trigger\" attribute", function() {
            var flipBox = document.createElement( "flip-box" );

            flipBox.trigger = "Set via Property";
            expect( flipBox.hasAttribute( "trigger" ) ).to.equal( true );
            expect( flipBox.getAttribute( "trigger" ) )
              .to.be.a( "string" )
              .that.equals( "box" )
              .and.equal( flipBox.trigger );
          });

        test( "setting value to \"box\" via \"trigger\" property reflects to \"trigger\" attribute",
          function() {
            var flipBox = document.createElement( "flip-box" );

            flipBox.trigger = "box";
            expect( flipBox.hasAttribute( "trigger" ) ).to.equal( true );
            expect( flipBox.getAttribute( "trigger" ) )
              .to.be.a( "string" )
              .that.equals( "box" )
              .and.equal( flipBox.trigger );
          });

        test( "setting value to \"btn\" via \"trigger\" property reflects to \"trigger\" attribute",
          function() {
            var flipBox = document.createElement( "flip-box" );

            flipBox.trigger = "btn";
            expect( flipBox.hasAttribute( "trigger" ) ).to.equal( true );
            expect( flipBox.getAttribute( "trigger" ) )
              .to.be.a( "string" )
              .that.equals( "btn" )
              .and.equal( flipBox.trigger );
          });
      });

    /* Rotation */
      suite( "rotation", function() {
        test( "can be set to default value of \"toggle\" via setAttribute", function() {
          var flipBox = document.createElement( "flip-box" );

          flipBox.setAttribute( "rotation", "toggle" );

          expect( flipBox.hasAttribute( "rotation" ) ).to.equal( true );

          expect( flipBox.getAttribute( "rotation" ) )
            .to.be.a( "string" )
            .that.equals( "toggle" );

          expect( flipBox )
            .to.have.property( "outerHTML" )
            .that.equals( "<flip-box rotation=\"toggle\"></flip-box>" );
        });

        test( "can be set to \"toggle\" via setAttribute", function() {
          var flipBox = document.createElement( "flip-box" );

          flipBox.setAttribute( "rotation", "toggle" );

          expect( flipBox.hasAttribute( "rotation" ) ).to.equal( true );

          expect( flipBox.getAttribute( "rotation" ) )
            .to.be.a( "string" )
            .that.equals( "toggle" );

          expect( flipBox )
            .to.have.property( "outerHTML" )
            .that.equals( "<flip-box rotation=\"toggle\"></flip-box>" );
        });

        test( "if attempting to set to an invalid value, value will default to \"toggle\" via setAttribute", function() {
          var flipBox = document.createElement( "flip-box" );

          flipBox.setAttribute( "rotation", "loop" );

          expect( flipBox.hasAttribute( "rotation" ) ).to.equal( true );

          expect( flipBox.getAttribute( "rotation" ) )
            .to.be.a( "string" )
            .that.equals( "loop" );

          expect( flipBox )
            .to.have.property( "outerHTML" )
            .that.equals( "<flip-box rotation=\"loop\"></flip-box>" );
        });

        test( "can be set to default value of \"toggle\" via \"rotation\" property",
          function() {
            var flipBox = document.createElement( "flip-box" );

            flipBox.rotation = "toggle";

            expect( flipBox )
              .to.have.property( "rotation" )
              .that.equals( "toggle" );
          });

        test( "can be set to \"loop\" via \"rotation\" property",
          function() {
            var flipBox = document.createElement( "flip-box" );

            flipBox.rotation = "loop";

            expect( flipBox )
              .to.have.property( "rotation" )
              .that.equals( "loop" );
          });

        test( "if attempting to set to an invalid value, value will default to \"toggle\" via \"rotation\" property", function() {
            var flipBox = document.createElement( "flip-box" );

            flipBox.rotation = "set via Property";

            expect( flipBox )
              .to.have.property( "rotation" )
              .that.equals( "toggle" );
          });

        test( "if attempting to set invalid value\"setAttribute\", value will default to \"horizontal\", which reflects to \"rotation\" property", function() {
            var flipBox = document.createElement( "flip-box" );

            flipBox.setAttribute( "rotation", "Set via Property" );

            expect( flipBox )
              .to.have.property( "rotation" )
              .that.equals( "toggle" )
              .and.equals( flipBox.getAttribute( "rotation" ) );
          });

        test( "setting to \"toggle\" via \"setAttribute\" reflects to \"rotation\" property",
          function() {
            var flipBox = document.createElement( "flip-box" );

            flipBox.setAttribute( "rotation", "toggle" );

            expect( flipBox )
              .to.have.property( "rotation" )
              .that.equals( "toggle" )
              .and.equals( flipBox.getAttribute( "rotation" ) );
          });

        test( "setting to \"loop\" via \"setAttribute\" reflects to \"rotation\" property",
          function() {
            var flipBox = document.createElement( "flip-box" );

            flipBox.setAttribute( "rotation", "loop" );

            expect( flipBox )
              .to.have.property( "rotation" )
              .that.equals( "loop" )
              .and.equals( flipBox.getAttribute( "rotation" ) );
          });

        test( "if attempting to set to an invalid value via \"rotation\" property, value will default to \"toggle\", which reflects to \"rotation\" attribute", function() {
            var flipBox = document.createElement( "flip-box" );

            flipBox.rotation = "Set via Property";
            expect( flipBox.hasAttribute( "rotation" ) ).to.equal( true );
            expect( flipBox.getAttribute( "rotation" ) )
              .to.be.a( "string" )
              .that.equals( "toggle" )
              .and.equal( flipBox.rotation );
          });

        test( "setting tp \"toggle\" via \"rotation\" property reflects to \"rotation\" attribute",
          function() {
            var flipBox = document.createElement( "flip-box" );

            flipBox.rotation = "toggle";
            expect( flipBox.hasAttribute( "rotation" ) ).to.equal( true );
            expect( flipBox.getAttribute( "rotation" ) )
              .to.be.a( "string" )
              .that.equals( "toggle" )
              .and.equal( flipBox.rotation );
          });

        test( "setting to \"loop\" via \"rotation\" property reflects to \"rotation\" attribute",
          function() {
            var flipBox = document.createElement( "flip-box" );

            flipBox.rotation = "loop";
            expect( flipBox.hasAttribute( "rotation" ) ).to.equal( true );
            expect( flipBox.getAttribute( "rotation" ) )
              .to.be.a( "string" )
              .that.equals( "loop" )
              .and.equal( flipBox.rotation );
          });
      });
    });

    suite( "Methods", function() {
      suite( "isFlipped", function() {
        test( "default value is \"false\"", function() {
          expect( document.createElement( "flip-box" ) )
            .to.have.property( "isFlipped" )
            .that.is.a( "boolean" )
            .and.equals( false );
        });

        test( "cannot be set via property \"isFlipped\"", function() {
          var flipBox = document.createElement( "flip-box" );

          flipBox.isFlipped = true;
          expect( flipBox )
            .to.have.property( "isFlipped" )
            .that.is.a( "boolean" )
            .and.equals( false );
        });

        test( "invoking .flip() method once sets \"isFlipped\" to \"true\"", function() {
          var flipBox = document.createElement( "flip-box" );

          flipBox.flip();

          expect( flipBox )
            .to.have.property( "isFlipped" )
            .that.is.a( "boolean" )
            .and.equals( true );
        });

        test( "when \"isFlipped\" is \"true\", invoking .flip() again, sets isFlipped to \"false\"", function() {
          var flipBox = document.createElement( "flip-box" );

          flipBox.flip();
          flipBox.flip();

          expect( flipBox )
            .to.have.property( "isFlipped" )
            .that.is.a( "boolean" )
            .and.equals( false );
        });
      });
    });
  });
})( window, document, window.chai );
