/*eslint-env mocha */
/*jscs:disable maximumLineLength*/
( function( window, document, polymer, sinon, chai ) {
  "use strict";
  var expect = chai.expect,
    // get wrapper from document or for karma, create a new div and append it to the DOM
    testingWrapper = document.getElementById( "file-drop-test-wrapper" ) ||
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

  suite( "<file-drop>", function() {
    suite( "Life Cycle", function() {
      teardown( function() {
        resetWrapper();
      });

      test( "ready: can create from document.createElement", function() {
        var createdSpy = sinon.spy(
          polymer.getRegisteredPrototype( "file-drop" ),
          "ready"
        );

        expect( document.createElement( "file-drop" ) )
          .to.have.property( "outerHTML" )
          .that.is.a( "string" )
          .and.equals( "<file-drop></file-drop>" );

        expect( createdSpy ).to.have.callCount( 1 );
        createdSpy.restore();
      });

      // Omitted due to polymer not generating an attached function
      test.skip( "attached: can be added to another DOM Element", function() {
        var newElement = document.createElement( "file-drop" ),
          attachedSpy = sinon.spy( newElement, "attached" );

        testingWrapper.appendChild( newElement );

        expect( attachedSpy ).to.have.callCount( 1 );

        expect( testingWrapper )
          .to.have.property( "innerHTML" )
          .that.is.a( "string" )
          .and.equals( "<file-drop></file-drop>" );

        attachedSpy.restore();
      });

      // Omitted due to polymer not generating an detached function
      test.skip( "detached: can be removed from another DOM element", function() {
        var newElement = document.createElement( "file-drop" ),
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

    suite( "Attributes", function() {
      suite( "Fill", function() {
        test( "can be set via \"setAttribute\"", function() {
          var fileDrop = document.createElement( "file-drop" );

          fileDrop.setAttribute( "fill", "parent" );

          expect( fileDrop.hasAttribute( "fill" ) ).to.equal( true );

          expect( fileDrop.getAttribute( "fill" ) )
            .to.be.a( "string" )
            .that.equals( "parent" );

          expect( fileDrop )
            .to.have.property( "outerHTML" )
            .that.equals( "<file-drop fill=\"parent\"></file-drop>" );
        });

        test( "can be set via property \"fill\"", function() {
          var fileDrop = document.createElement( "file-drop" );

          fileDrop.fill = "parent";

          expect( fileDrop )
            .to.have.property( "fill" )
            .that.equals( "parent" );
        });

        test( "setting via \"setAttribute\" reflects to property \"fill\"", function() {
          var fileDrop = document.createElement( "file-drop" );

          fileDrop.setAttribute( "fill", "parent" );

          expect( fileDrop )
            .to.have.property( "fill" )
            .that.equals( "parent" )
            .and.equals( fileDrop.getAttribute( "fill" ) );
        });

        test( "setting via property \"fill\" reflects to attribute \"fill\"", function() {
          var fileDrop = document.createElement( "file-drop" );

          fileDrop.fill = "window";

          expect( fileDrop.hasAttribute( "fill" ) ).to.equal( true );
          expect( fileDrop.getAttribute( "fill" ) )
            .to.be.a( "string" )
            .that.equals( "window" )
            .and.equal( fileDrop.fill );
        });
      });

      suite( "Accepts", function() {
        test( "can be set via \"setAttribute\"", function() {
          var fileDrop = document.createElement( "file-drop" );

          fileDrop.setAttribute( "accepts", "image/png" );

          expect( fileDrop.hasAttribute( "accepts" ) ).to.equal( true );

          expect( fileDrop.getAttribute( "accepts" ) )
            .to.be.a( "string" )
            .that.equals( "image/png" );

          expect( fileDrop )
            .to.have.property( "outerHTML" )
            .that.equals( "<file-drop accepts=\"image/png\"></file-drop>" );
        });

        test( "can be set via property \"accepts\"", function() {
          var fileDrop = document.createElement( "file-drop" );

          fileDrop.accepts = "image/png";

          expect( fileDrop )
            .to.have.property( "accepts" )
            .that.equals( "image/png" );
        });

        test( "setting via \"setAttribute\" reflects to property \"accepts\"", function() {
          var fileDrop = document.createElement( "file-drop" );

          fileDrop.setAttribute( "accepts", "image/png" );

          expect( fileDrop )
            .to.have.property( "accepts" )
            .that.equals( "image/png" )
            .and.equals( fileDrop.getAttribute( "accepts" ) );
        });

        test( "setting via property \"accepts\" reflects to attribute \"accepts\"", function() {
          var fileDrop = document.createElement( "file-drop" );

          fileDrop.accepts = "image/png";

          expect( fileDrop.hasAttribute( "accepts" ) ).to.equal( true );
          expect( fileDrop.getAttribute( "accepts" ) )
            .to.be.a( "string" )
            .that.equals( "image/png" )
            .and.equal( fileDrop.accepts );
        });
      });

      suite( "multiple", function() {
        test( "can be set via \"setAttribute\"", function() {
          var fileDrop = document.createElement( "file-drop" );

          fileDrop.setAttribute( "multiple", "" );
          expect( fileDrop.hasAttribute( "multiple" ) ).to.equal( true );
          expect( fileDrop.getAttribute( "multiple" ) )
            .to.be.a( "string" )
            .and.equal( "" );
        });

        test( "can be set via property \"multiple\"", function() {
          var fileDrop = document.createElement( "file-drop" );

          fileDrop.multiple = true;
          expect( fileDrop )
            .to.have.property( "multiple" )
            .that.is.a( "boolean" )
            .and.equals( true );
        });

        test( "setting via attribute reflects to property", function() {
          var fileDrop = document.createElement( "file-drop" );

          fileDrop.setAttribute( "multiple", "" );
          expect( fileDrop )
            .to.have.property( "multiple" )
            .that.is.a( "boolean" )
            .and.equals( true );
        });

        test( "setting via property reflects to attribute", function() {
          var fileDrop = document.createElement( "file-drop" );

          fileDrop.multiple = true;
          expect( fileDrop.hasAttribute( "multiple" ) )
            .to.be.a( "boolean" )
            .and.to.equal( true );
        });

        // remove attribute sets to false
        test( "removing attribute reflects to property", function() {
          var fileDrop = document.createElement( "file-drop" );

          fileDrop.setAttribute( "multiple", "" );
          expect( fileDrop.hasAttribute( "multiple" ) )
            .to.be.a( "boolean" )
            .and.to.equal( true );

          fileDrop.removeAttribute( "multiple" );
          expect( fileDrop )
            .to.have.property( "multiple" )
            .that.is.a( "boolean" )
            .and.equals( false );
        });

        test( "setting property to false removes attribute", function() {
          var fileDrop = document.createElement( "file-drop" );

          fileDrop.multiple = true;
          expect( fileDrop )
            .to.have.property( "multiple" )
            .that.is.a( "boolean" )
            .and.equals( true );

          expect( fileDrop.hasAttribute( "multiple" ) )
            .to.be.a( "boolean" )
            .that.equals( true );

          fileDrop.multiple = false;
          expect( fileDrop )
            .to.have.property( "multiple" )
            .that.is.a( "boolean" )
            .and.equals( false );

          expect( fileDrop.hasAttribute( "multiple" ) )
            .to.be.a( "boolean" )
            .and.to.equal( false );
        });
      });

      suite( "Methods", function() {
        suite( "onDrop", function() {
          // Skiping Test for Selenium
          test.skip( "should fire on a drop event", function() {
            var fileDrop = document.createElement( "file-drop" );
            // Skipping tests until Selenium gets integrated for drag and drop
            fileDrop.onDrop( function( evt ) {
            });
          });
        });

        suite( "onDragOver", function() {
          // Skiping Test for Selenium
          test.skip( "should fire on a drag over event", function() {
            var fileDrop = document.createElement( "file-drop" );
            // Skipping tests until Selenium gets integrated for drag and drop
            fileDrop.onDragOver( function( evt ) {
            });
          });
        });
      });
    });
  });
})( window, document, window.Polymer, window.sinon, window.chai );
