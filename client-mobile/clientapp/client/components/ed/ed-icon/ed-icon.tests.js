/*eslint-env mocha */
/*jscs:disable maximumLineLength*/
( function( window, document, polymer, sinon, chai ) {
  "use strict";
  var expect = chai.expect,
  // get wrapper from document or for karma, create a new div and append it to the DOM
    testingWrapper = document.getElementById( "ed-icon-test-wrapper" ) ||
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

  suite( "<ed-icon>", function() {
    suite( "Life Cycle", function() {
      teardown( function() {
        resetWrapper();
      });

      test( "ready: can create from document.createElement", function() {
        var createdSpy = sinon.spy(
          polymer.getRegisteredPrototype( "ed-icon" ),
          "ready"
        );

        expect( document.createElement( "ed-icon" ) )
          .to.have.property( "outerHTML" )
          .that.is.a( "string" )
          .and.equals( "<ed-icon></ed-icon>" );

        expect( createdSpy ).to.have.callCount( 1 );
        createdSpy.restore();
      });

      test.skip( "attached: can be added to another DOM Element", function() {
        var newElement = document.createElement( "ed-icon" ),
          attachedSpy = sinon.spy( newElement, "attached" );

        testingWrapper.appendChild( newElement );

        expect( attachedSpy ).to.have.callCount( 1 );

        expect( testingWrapper )
          .to.have.property( "innerHTML" )
          .that.is.a( "string" )
          .and.equals( "<ed-icon></ed-icon>" );

        attachedSpy.restore();
      });

      test.skip( "detached: can be removed from another DOM element", function() {
        var newElement = document.createElement( "ed-icon" ),
          detachedSpy = sinon.spy( newElement, "detached" );

        testingWrapper.appendChild( newElement );
        testingWrapper.removeChild( newElement );

        expect( detachedSpy ).to.have.callCount( 1 );

        expect( testingWrapper )
          .to.have.property( "outerHTML" )
          .that.is.a( "string" )
          .and.equals( originalWrapperOuterHTML );

        detachedSpy.restore();
      });
    });

    suite( "Attributes and Properties", function() {
      // Tests for Name attribute and property
      suite( "name", function() {
        test( "can be set via setattribute", function() {
          var edIcon = document.createElement( "ed-icon" ),
              setTo = "Set via Attribute";

          edIcon.setAttribute( "name", setTo );

          expect( edIcon.hasAttribute( "name" ) ).to.equal( true );

          expect( edIcon.getAttribute( "name" ) )
            .to.be.a( "string" )
            .that.equals( setTo );

          expect( edIcon )
            .to.have.property( "outerHTML" )
            .that.equals( "<ed-icon name=\"" + setTo + "\"></ed-icon>" );
        });

        test( "can be set via property \"name\"", function() {
          var edIcon = document.createElement( "ed-icon" ),
              setTo = "Set via Property";

          edIcon.name = setTo;

          expect( edIcon )
            .to.have.property( "name" )
            .that.equals( setTo );
        });

        test( "setting via \"setAttribute\" reflects to property \"name\"", function() {
          var edIcon = document.createElement( "ed-icon" ),
              setTo = "Set via Attribute";

          edIcon.setAttribute( "name", setTo );

          expect( edIcon )
            .to.have.property( "name" )
            .that.equals( setTo )
            .and.equals( edIcon.getAttribute( "name" ) );
        });

        test( "setting via property \"name\" reflects to attribute \"name\"", function() {
          var edIcon = document.createElement( "ed-icon" ),
              setTo = "Set via Property",
            observeFn = function( changes ) {
              expect( edIcon.hasAttribute( "name" ) ).to.equal( true );
              expect( edIcon.getAttribute( "name" ) )
                .to.be.a( "string" )
                .that.equals( setTo )
                .and.equal( edIcon.name );

              Object.unobserve( edIcon, observeFn );
            };

          edIcon.name = setTo;

          Object.observe( edIcon, observeFn );
        });
      });

      // Tests for rotation
      suite( "rotation", function() {
        test( "has default value: \"0\"", function() {
          expect( document.createElement( "ed-icon" ) )
            .to.have.property( "rotation" )
            .that.is.a( "number" )
            .and.equals( 0 );
        });

        test( "can be set via setattribute", function() {
          var edIcon = document.createElement( "ed-icon" ),
              setTo = "90";

          edIcon.setAttribute( "rotation", setTo );

          expect( edIcon.hasAttribute( "rotation" ) ).to.equal( true );

          expect( edIcon.getAttribute( "rotation" ) )
            .to.be.a( "string" )
            .that.equals( setTo );

          expect( edIcon )
            .to.have.property( "outerHTML" )
            .that.equals( "<ed-icon rotation=\"" + setTo + "\"></ed-icon>" );
        });

        test( "can be set via property \"rotation\"", function() {
          var edIcon = document.createElement( "ed-icon" );

          edIcon.rotation = 180;

          expect( edIcon )
            .to.have.property( "rotation" )
            .that.is.a( "number" )
            .and.equals( 180 );
        });

        test( "setting via \"setAttribute\" reflects to property \"rotation\"", function() {
          var edIcon = document.createElement( "ed-icon" );

          edIcon.setAttribute( "rotation", "90" );

          expect( edIcon )
            .to.have.property( "rotation" )
            .that.is.a( "number" )
            .and.equals( 90 );
        });

        test( "setting via property \"rotation\" reflects to attribute \"rotation\"", function() {
          var edIcon = document.createElement( "ed-icon" );

          edIcon.rotation = 90;

          expect( edIcon.hasAttribute( "rotation" ) ).to.equal( true );
          expect( edIcon.getAttribute( "rotation" ) )
            .to.be.a( "string" )
            .that.equals( "90" )
            .and.equal( edIcon.rotation.toString() );
        });

        test( "defaults to 0 if invalid when set via property", function() {
          var edIcon = document.createElement( "ed-icon" ),
            observeFn = function( changes ) {
              expect( edIcon )
                .to.have.property( "rotation" )
                .that.is.a( "number" )
                .and.equal( 0 );

              Object.unobserve( edIcon, observeFn );
            };

          edIcon.rotation = 999999;

          Object.observe( edIcon, observeFn );
        });

        test( "defaults to 0 if invalid when set via attribute", function() {
          var edIcon = document.createElement( "ed-icon" ),
            observeFn = function( changes ) {
              expect( edIcon.hasAttribute( "rotation" ) ).to.equal( true );
              expect( edIcon.getAttribute( "rotation" ) )
                .to.be.a( "string" )
                .that.equals( "0" )
                .and.equal( edIcon.rotation.toString() );

              Object.unobserve( edIcon, observeFn );
            };

          edIcon.setAttribute( "rotation", "pie" );

          Object.observe( edIcon, observeFn );
        });

        test( "removing attribute \"rotation\" sets property back to default value", function() {
          var edIcon = document.createElement( "ed-icon" ),
            observeFn = function( changes ) {
              expect( edIcon )
                .to.have.property( "rotation" )
                .that.is.a( "number" )
                .and.equals( 0 );

              Object.unobserve( edIcon, observeFn );
            };

          edIcon.setAttribute( "rotation", "90" );
          expect( edIcon.hasAttribute( "rotation" ) ).to.equal( true );

          edIcon.removeAttribute( "rotation" );
          expect( edIcon.hasAttribute( "rotation" ) ).to.equal( false );

          Object.observe( edIcon, observeFn );
        });

        test( "setting \"rotation\" to null resets property to default value", function() {
          var edIcon = document.createElement( "ed-icon" ),
            observeFn = function( changes ) {
              expect( edIcon )
                .to.have.property( "rotation" )
                .that.is.a( "number" )
                .and.equals( 0 );

              Object.unobserve( edIcon, observeFn );
            };

          edIcon.rotation = 90;
          expect( edIcon )
            .to.have.property( "rotation" )
            .that.is.a( "number" )
            .and.equals( 90 );

          edIcon.rotation = null;

          Object.observe( edIcon, observeFn );
        });

        test( "setting \"rotation\" to undefined resets property to default value", function() {
          var edIcon = document.createElement( "ed-icon" ),
            observeFn = function( changes ) {
              edIcon.rotation = 90;
              expect( edIcon )
                .to.have.property( "rotation" )
                .that.is.a( "number" )
                .and.equals( 0 );

              edIcon.rotation = undefined;
              expect( edIcon )
                .to.have.property( "rotation" )
                .that.is.a( "number" )
                .and.equals( 0 );

              Object.unobserve( edIcon, observeFn );
            };

          Object.observe( edIcon, observeFn );
        });
      });
    });
  });
})( window, document, window.Polymer, window.sinon, window.chai );
