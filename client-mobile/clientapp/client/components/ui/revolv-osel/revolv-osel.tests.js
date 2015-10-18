/*eslint-env mocha */
/*jscs:disable maximumLineLength*/
( function( window, document, polymer, sinon, chai ) {
  "use strict";
  var expect = chai.expect,
    // get wrapper from document or for karma, create a new div and append it to the DOM
    testingWrapper = document.getElementById( "revolv-osel-test-wrapper" ) ||
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

  suite( "<revolv-osel>", function() {
    suite( "Life Cycle", function() {
      teardown( function() {
        resetWrapper();
      });

      test( "ready: can create from document.createElement", function() {
        var createdSpy = sinon.spy(
          polymer.getRegisteredPrototype( "revolv-osel" ),
          "ready"
        );

        expect( document.createElement( "revolv-osel" ) )
          .to.have.property( "outerHTML" )
          .that.is.a( "string" )
          .and.equals( "<revolv-osel></revolv-osel>" );

        expect( createdSpy ).to.have.callCount( 1 );
        createdSpy.restore();
      });

      test( "attached: can be added to another DOM Element", function() {
        var revolv = document.createElement( "revolv-osel" ),
          attachedSpy = sinon.spy( revolv, "attached" );

        testingWrapper.appendChild( revolv );

        expect( attachedSpy ).to.have.callCount( 1 );

        expect( testingWrapper )
          .to.have.property( "innerHTML" )
          .that.is.a( "string" )
          .and.equals( "<revolv-osel></revolv-osel>" );

        attachedSpy.restore();
      });

      test.skip( "detached: can be removed from another DOM element", function() {
        var revolv = document.createElement( "revolv-osel" ),
          detachedSpy = sinon.spy( revolv, "detached" );

        testingWrapper.appendChild( revolv );
        testingWrapper.removeChild( revolv );

        expect( detachedSpy ).to.have.callCount( 1 );

        expect( testingWrapper )
          .to.have.property( "outerHTML" )
          .that.is.a( "string" )
          .and.equals( originalWrapperOuterHTML );

        detachedSpy.restore();
      });
    });

    suite( "Attributes & Associated Properties", function() {
      suite( "Loop", function() {
        test( "has default value: true", function() {
          var revolv = document.createElement( "revolv-osel" );

          expect( revolv )
            .to.have.property( "loop" )
            .that.is.a( "boolean" )
            .and.equals( true );
        });

        test( "can be set via \"setAttribute\"", function() {
          var revolv = document.createElement( "revolv-osel" );

          revolv.setAttribute( "loop", "" );
          expect( revolv.hasAttribute( "loop" ) ).to.equal( true );
          expect( revolv.getAttribute( "loop" ) )
            .to.be.a( "string" )
            .and.equal( "" );
        });

        test( "can be set via property \"loop\"", function() {
          var revolv = document.createElement( "revolv-osel" );

          revolv.loop = true;
          expect( revolv )
            .to.have.property( "loop" )
            .that.is.a( "boolean" )
            .and.equals( true );
        });

        test( "setting via attribute reflects to property", function() {
          var revolv = document.createElement( "revolv-osel" );

          revolv.setAttribute( "loop", "" );
          expect( revolv )
            .to.have.property( "loop" )
            .that.is.a( "boolean" )
            .and.equals( true );
        });

        test( "setting via property reflects to attribute", function( done ) {
          var revolv = document.createElement( "revolv-osel" ),
            observeFn = function( changes ) {
              expect( revolv.hasAttribute( "loop" ) )
                .to.be.a( "boolean" )
                .and.to.equal( true );

              Object.unobserve( revolv, observeFn );
              done();
            };

          Object.observe( revolv, observeFn );

          revolv.loop = true;
        });

        test( "removing attribute reflects to attribute", function() {
          var revolv = document.createElement( "revolv-osel" );

          revolv.setAttribute( "loop", "" );
          expect( revolv.hasAttribute( "loop" ) )
            .to.be.a( "boolean" )
            .and.to.equal( true );

          revolv.removeAttribute( "loop" );
          expect( revolv )
            .to.have.property( "loop" )
            .that.is.a( "boolean" )
            .and.equals( false );
        });

        test( "setting property to false removes attribute", function( done ) {
          var revolv = document.createElement( "revolv-osel" ),
            observeFn = function( changes ) {
              expect( revolv )
                .to.have.property( "loop" )
                .that.is.a( "boolean" )
                .and.equals( false );

              expect( revolv.hasAttribute( "loop" ) )
                .to.be.a( "boolean" )
                .and.to.equal( false );

              Object.unobserve( revolv, observeFn );
              done();
            };

          Object.observe( revolv, observeFn );

          revolv.setAttribute( "loop", true );
          expect( revolv.hasAttribute( "loop" ) )
            .to.be.a( "boolean" )
            .that.equals( true );

          revolv.loop = false;
        });
      });

      suite( "Pagination", function() {
        test( "has default value: true", function() {
          var revolv = document.createElement( "revolv-osel" );

          expect( revolv )
            .to.have.property( "pagination" )
            .that.is.a( "boolean" )
            .and.equals( true );
        });

        test( "can be set via \"setAttribute\"", function() {
          var revolv = document.createElement( "revolv-osel" );

          revolv.setAttribute( "pagination", "" );
          expect( revolv.hasAttribute( "pagination" ) ).to.equal( true );
          expect( revolv.getAttribute( "pagination" ) )
            .to.be.a( "string" )
            .and.equal( "" );
        });

        test( "can be set via property \"pagination\"", function() {
          var revolv = document.createElement( "revolv-osel" );

          revolv.pagination = true;
          expect( revolv )
            .to.have.property( "pagination" )
            .that.is.a( "boolean" )
            .and.equals( true );
        });

        test( "setting via attribute reflects to property", function() {
          var revolv = document.createElement( "revolv-osel" );

          revolv.setAttribute( "pagination", "" );
          expect( revolv )
            .to.have.property( "pagination" )
            .that.is.a( "boolean" )
            .and.equals( true );
        });

        test( "setting via property reflects to attribute", function( done ) {
          var revolv = document.createElement( "revolv-osel" ),
            observeFn = function( changes ) {
              expect( revolv.hasAttribute( "pagination" ) )
                .to.be.a( "boolean" )
                .and.to.equal( true );

              Object.unobserve( revolv, observeFn );
              done();
            };

          Object.observe( revolv, observeFn );

          revolv.pagination = true;
        });

        test( "removing attribute reflects to attribute", function() {
          var revolv = document.createElement( "revolv-osel" );

          revolv.setAttribute( "pagination", "" );
          expect( revolv.hasAttribute( "pagination" ) )
            .to.be.a( "boolean" )
            .and.to.equal( true );

          revolv.removeAttribute( "pagination" );
          expect( revolv )
            .to.have.property( "pagination" )
            .that.is.a( "boolean" )
            .and.equals( false );
        });

        test( "setting property to false removes attribute", function( done ) {
          var revolv = document.createElement( "revolv-osel" ),
            observeFn = function( changes ) {
              expect( revolv )
                .to.have.property( "pagination" )
                .that.is.a( "boolean" )
                .and.equals( false );

              expect( revolv.hasAttribute( "pagination" ) )
                .to.be.a( "boolean" )
                .and.to.equal( false );

              Object.unobserve( revolv, observeFn );
              done();
            };

          revolv.setAttribute( "pagination", true );
          expect( revolv.hasAttribute( "pagination" ) )
            .to.be.a( "boolean" )
            .that.equals( true );

          Object.observe( revolv, observeFn );

          revolv.pagination = false;
        });
      });

      suite( "show-buttons", function() {
        test( "has default value: false", function() {
          var revolv = document.createElement( "revolv-osel" );

          expect( revolv )
            .to.have.property( "showButtons" )
            .that.is.a( "boolean" )
            .and.equals( false );
        });

        test( "can be set via \"setAttribute\"", function() {
          var revolv = document.createElement( "revolv-osel" );

          revolv.setAttribute( "show-buttons", "" );
          expect( revolv.hasAttribute( "show-buttons" ) ).to.equal( true );
          expect( revolv.getAttribute( "show-buttons" ) )
            .to.be.a( "string" )
            .and.equal( "" );
        });

        test( "can be set via property \"show-buttons\"", function() {
          var revolv = document.createElement( "revolv-osel" );

          revolv.showButtons = true;
          expect( revolv )
            .to.have.property( "showButtons" )
            .that.is.a( "boolean" )
            .and.equals( true );
        });

        test( "setting via attribute reflects to property", function() {
          var revolv = document.createElement( "revolv-osel" );

          revolv.setAttribute( "show-buttons", "" );
          expect( revolv )
            .to.have.property( "showButtons" )
            .that.is.a( "boolean" )
            .and.equals( true );
        });

        test( "setting via property reflects to attribute", function( done ) {
          var revolv = document.createElement( "revolv-osel" ),
            observeFn = function( changes ) {
              expect( revolv.hasAttribute( "show-buttons" ) )
                .to.be.a( "boolean" )
                .and.to.equal( true );

              Object.unobserve( revolv, observeFn );
              done();
            };

          Object.observe( revolv, observeFn );

          revolv.showButtons = true;
        });

        // remove attribute sets to false
        test( "removing attribute reflects to attribute", function() {
          var revolv = document.createElement( "revolv-osel" );

          revolv.setAttribute( "show-buttons", "" );
          expect( revolv.hasAttribute( "show-buttons" ) )
            .to.be.a( "boolean" )
            .and.to.equal( true );

          revolv.removeAttribute( "show-buttons" );
          expect( revolv )
            .to.have.property( "showButtons" )
            .that.is.a( "boolean" )
            .and.equals( false );
        });

        test( "setting property to false removes attribute", function( done ) {
          var revolv = document.createElement( "revolv-osel" ),
            observeFn = function( changes ) {
              expect( revolv )
                .to.have.property( "showButtons" )
                .that.is.a( "boolean" )
                .and.equals( false, "property: showButtons is false" );

              expect( revolv.hasAttribute( "show-buttons" ) )
                .to.be.a( "boolean" )
                .and.to.equal( false, "show-buttons attribute has been removed" );

              Object.unobserve( revolv, observeFn );
              done();
            };

          revolv.setAttribute( "show-buttons", true );
          expect( revolv.hasAttribute( "show-buttons" ) )
            .to.be.a( "boolean" )
            .that.equals( true, "show-buttons attribute doesn't exist" );

          Object.observe( revolv, observeFn );

          revolv.showButtons = false;
        });
      });

      // skipping visible until reiteration with grid
      suite.skip( "Visible", function() {
        test( "can be set via \"setAttribute\"", function() {
          var revolv = document.createElement( "revolv-osel" );

          revolv.setAttribute( "visible", "3" );

          expect( revolv.hasAttribute( "visible" ) ).to.equal( true );

          expect( revolv.getAttribute( "visible" ) )
            .to.be.a( "string" )
            .that.equals( "3" );

          expect( revolv )
            .to.have.property( "outerHTML" )
            .that.equals( "<revolv-osel visible=\"3\"></revolv-osel>" );
        });

        test( "can be set via property \"visible\"", function() {
          var revolv = document.createElement( "revolv-osel" );

          revolv.visible = 3;

          expect( revolv )
            .to.have.property( "visible" )
            .that.is.a( "number" )
            .and.equals( 3 );
        });

        test( "setting via \"setAttribute\" reflects to property \"visible\"", function() {
          var revolv = document.createElement( "revolv-osel" );

          revolv.setAttribute( "visible", "3" );

          expect( revolv )
            .to.have.property( "visible" )
            .that.is.a( "number" )
            .and.equals( 3 );
        });

        test( "setting via property \"visible\" reflects to attribute \"visible\"", function() {
          var revolv = document.createElement( "revolv-osel" );

          revolv.visible = 3;

          expect( revolv.hasAttribute( "visible" ) ).to.equal( true );
          expect( revolv.getAttribute( "visible" ) )
            .to.be.a( "string" )
            .that.equals( "3" );
        });
      });
    });
  });
})( window, document, window.Polymer, window.sinon, window.chai );
