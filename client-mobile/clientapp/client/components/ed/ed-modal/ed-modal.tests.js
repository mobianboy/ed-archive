/*eslint-env mocha */
/*eslint-env mocha */
( function( window, document, chai ) {
  "use strict";
  var expect = chai.expect,
    // get wrapper from document or for karma, create a new div and append it to the DOM
    testingWrapper = document.getElementById( "ed-modal-test-wrapper" ) ||
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

  suite( "<ed-modal>", function() {
    suite( "Life Cycle", function() {
      test( "ready: can create from document.createElement", function() {
        expect( document.createElement( "ed-modal" ) )
          .to.have.property( "outerHTML" )
          .that.is.a( "string" )
          .and.equals( "<ed-modal></ed-modal>" );
      });

      test( "attached: can be added to another DOM Element", function() {
        testingWrapper.appendChild( document.createElement( "ed-modal" ) );

        expect( testingWrapper )
          .to.have.property( "innerHTML" )
          .that.is.a( "string" )
          .and.equals( "<ed-modal></ed-modal>" );

        resetWrapper();
      });

      test( "detached: can be removed from another DOM element", function() {
        var edModal = document.createElement( "ed-modal" );

        testingWrapper.appendChild( edModal );
        testingWrapper.removeChild( edModal );

        expect( testingWrapper )
          .to.have.property( "outerHTML" )
          .that.is.a( "string" )
          .and.equals( originalWrapperOuterHTML );

        resetWrapper();
      });
    });

    suite( "Attributes and Properties", function() {
      /** trigger **/
      suite( "trigger", function() {
        test( "value can be set via \"setAttribute\"", function() {
          var edModal = document.createElement( "ed-modal" ),
              setTo = "Set via Attribute";

          edModal.setAttribute( "trigger", setTo );

          expect( edModal.hasAttribute( "trigger" ) ).to.equal( true );

          expect( edModal.getAttribute( "trigger" ) )
            .to.be.a( "string" )
            .that.equals( setTo );

          expect( edModal )
            .to.have.property( "outerHTML" )
            .that.equals( "<ed-modal trigger=\"" + setTo + "\"></ed-modal>" );
        });

        test( "value can be set via property", function() {
          var edModal = document.createElement( "ed-modal" ),
              setTo = "Set via Property";

          edModal.trigger = setTo;

          expect( edModal )
            .to.have.property( "trigger" )
            .that.equals( setTo );
        });

        test( "setting value via \"setAttribute\" reflects to property", function() {
          var edModal = document.createElement( "ed-modal" ),
              setTo = "Set via Attribute";

          edModal.setAttribute( "trigger", setTo );

          expect( edModal )
            .to.have.property( "trigger" )
            .that.equals( setTo )
            .and.equals( edModal.getAttribute( "trigger" ) );
        });

        test( "setting value via property reflects to attribute", function() {
          var edModal = document.createElement( "ed-modal" ),
              setTo = "Set via Property";

          edModal.trigger = setTo;

          expect( edModal.hasAttribute( "trigger" ) ).to.equal( true );
          expect( edModal.getAttribute( "trigger" ) )
            .to.be.a( "string" )
            .that.equals( setTo )
            .and.equal( edModal.trigger );
        });
      });

      /** clickoff **/
      suite( "clickoff", function() {
        test( "default value is \"false\"", function() {
          expect( document.createElement( "ed-modal" ) )
            .to.have.property( "clickoff" )
            .that.is.a( "boolean" )
            .and.equals( false );
        });

        test( "value can be set via \"setAttribute\"", function() {
          var edModal = document.createElement( "ed-modal" );

          edModal.setAttribute( "clickoff", "" );

          expect( edModal.hasAttribute( "clickoff" ) ).to.equal( true );
          expect( edModal.getAttribute( "clickoff" ) )
            .to.be.a( "string" )
            .and.equal( "" );
        });

        test( "value can be set via property", function() {
          var edModal = document.createElement( "ed-modal" );

          edModal.clickoff = true;
          expect( edModal )
            .to.have.property( "clickoff" )
            .that.is.a( "boolean" )
            .and.equals( true );
        });

        test( "setting value via setAttribute reflects to property", function() {
          var edModal = document.createElement( "ed-modal" );

          edModal.setAttribute( "clickoff", "" );
          expect( edModal )
            .to.have.property( "clickoff" )
            .that.is.a( "boolean" )
            .and.equals( true );
        });

        test( "setting value via property reflects to attribute", function() {
          var edModal = document.createElement( "ed-modal" );

          edModal.clickoff = true;
          expect( edModal.hasAttribute( "clickoff" ) )
            .to.be.a( "boolean" )
            .and.to.equal( true );
        });

        // remove attribute sets to false
        test( "removing attribute reflects to attribute", function() {
          var edModal = document.createElement( "ed-modal" );

          edModal.setAttribute( "clickoff", "" );
          expect( edModal.hasAttribute( "clickoff" ) )
            .to.be.a( "boolean" )
            .and.to.equal( true );

          edModal.removeAttribute( "clickoff" );
          expect( edModal )
            .to.have.property( "clickoff" )
            .that.is.a( "boolean" )
            .and.equals( false );
        });

        test( "setting property to false removes attribute", function() {
          var edModal = document.createElement( "ed-modal" );

          edModal.clickoff = true;
          expect( edModal )
            .to.have.property( "clickoff" )
            .that.is.a( "boolean" )
            .and.equals( true );

          expect( edModal.hasAttribute( "clickoff" ) )
            .to.be.a( "boolean" )
            .that.equals( true );

          edModal.clickoff = false;
          expect( edModal )
            .to.have.property( "clickoff" )
            .that.is.a( "boolean" )
            .and.equals( false );

          expect( edModal.hasAttribute( "clickoff" ) )
            .to.be.a( "boolean" )
            .and.to.equal( false );
        });
      });
      /** closebutton **/
      suite( "closebutton", function() {
        test( "default value is \"false\"", function() {
          expect( document.createElement( "ed-modal" ) )
            .to.have.property( "closebutton" )
            .that.is.a( "boolean" )
            .and.equals( false );
        });

        test( "value can be set via \"setAttribute\"", function() {
          var edModal = document.createElement( "ed-modal" );

          edModal.setAttribute( "closebutton", "" );
          expect( edModal.hasAttribute( "closebutton" ) ).to.equal( true );
          expect( edModal.getAttribute( "closebutton" ) )
            .to.be.a( "string" )
            .and.equal( "" );
        });

        test( "value can be set via property", function() {
          var edModal = document.createElement( "ed-modal" );

          edModal.closebutton = true;
          expect( edModal )
            .to.have.property( "closebutton" )
            .that.is.a( "boolean" )
            .and.equals( true );
        });

        test( "setting value via setAttribute reflects to property", function() {
          var edModal = document.createElement( "ed-modal" );

          edModal.setAttribute( "closebutton", "" );
          expect( edModal )
            .to.have.property( "closebutton" )
            .that.is.a( "boolean" )
            .and.equals( true );
        });

        test( "setting value via property reflects to attribute", function() {
          var edModal = document.createElement( "ed-modal" );

          edModal.closebutton = true;
          expect( edModal.hasAttribute( "closebutton" ) )
            .to.be.a( "boolean" )
            .and.to.equal( true );
        });

        test( "removing attribute reflects to attribute", function() {
          var edModal = document.createElement( "ed-modal" );

          edModal.setAttribute( "closebutton", "" );
          expect( edModal.hasAttribute( "closebutton" ) )
            .to.be.a( "boolean" )
            .and.to.equal( true );

          edModal.removeAttribute( "closebutton" );
          expect( edModal )
            .to.have.property( "closebutton" )
            .that.is.a( "boolean" )
            .and.equals( false );
        });

        test( "setting property to false removes attribute", function() {
          var edModal = document.createElement( "ed-modal" );

          edModal.closebutton = true;
          expect( edModal )
            .to.have.property( "closebutton" )
            .that.is.a( "boolean" )
            .and.equals( true );

          expect( edModal.hasAttribute( "closebutton" ) )
            .to.be.a( "boolean" )
            .that.equals( true );

          edModal.closebutton = false;
          expect( edModal )
            .to.have.property( "closebutton" )
            .that.is.a( "boolean" )
            .and.equals( false );

          expect( edModal.hasAttribute( "closebutton" ) )
            .to.be.a( "boolean" )
            .and.to.equal( false );
        });
      });

      suite( "isOpen", function() {
        test( "default value is \"false\"", function() {
          expect( document.createElement( "ed-modal" ) )
            .to.have.property( "isOpen" )
            .that.is.a( "boolean" )
            .and.equals( false );
        });

        test( "cannot be set via \"isOpen\" property", function() {
          var edModal = document.createElement( "ed-modal" );

          edModal.isOpen = true;
          expect( edModal )
            .to.have.property( "isOpen" )
            .that.is.a( "boolean" )
            .and.equals( false );
        });

        test( "invoking .open() method sets isOpen to \"true\"", function() {
          var div = document.createElement( "div" ),
              edModal = document.createElement( "ed-modal" );
          div.appendChild( edModal );

          edModal.open();
          expect( edModal )
            .to.have.property( "isOpen" )
            .that.is.a( "boolean" )
            .and.equals( true );
        });

        test( "invoking .close() method sets isOpen to \"false\"", function() {
          var edModal = document.createElement( "ed-modal" );

          edModal.close();

          expect( edModal )
            .to.have.property( "isOpen" )
            .that.is.a( "boolean" )
            .and.equals( false );
        });
      });
    });
  });
})( window, document, window.chai );
