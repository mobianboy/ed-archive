/*globals CustomEvent */
/*eslint-env mocha */
( function( window, document, chai ) {
  "use strict";
  var expect = chai.expect,
    // get wrapper from document or for karma, create a new div and append it to the DOM
    testingWrapper = document.getElementById( "on-off-test-wrapper" ) ||
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

  suite( "<on-off>", function() {
    suite( "Life Cycle", function() {
      test( "ready: can create from document.createElement", function() {
        expect( document.createElement( "on-off" ) )
          .to.have.property( "outerHTML" )
          .that.is.a( "string" )
          .and.equals( "<on-off></on-off>" );
      });

      test( "attached: can be added to another DOM Element", function() {
        var onOff = document.createElement( "on-off" );

        testingWrapper.appendChild( onOff );

        expect( testingWrapper )
          .to.have.property( "innerHTML" )
          .that.is.a( "string" )
          .and.equals( "<on-off></on-off>" );

        resetWrapper();
      });

      test( "detached: can be removed from another DOM element", function() {
        var onOff = document.createElement( "on-off" );

        testingWrapper.appendChild( onOff );
        testingWrapper.removeChild( onOff );

        expect( testingWrapper )
          .to.have.property( "outerHTML" )
          .that.is.a( "string" )
          .and.equals( originalWrapperOuterHTML );

        resetWrapper();
      });
    });

    suite( "Attributes & Associated Properties", function() {
      suite( "on-text / onText", function() {
        test( "has default value: \"On\"", function() {
          expect( document.createElement( "on-off" ) )
            .to.have.property( "onText" )
            .that.is.a( "string" )
            .and.equals( "On" );
        });

        test( "can be set via \"setAttribute\"", function() {
          var onOff = document.createElement( "on-off" ),
            setTo = "Set via Attribute";

          onOff.setAttribute( "on-text", setTo );

          expect( onOff.hasAttribute( "on-text" ) ).to.equal( true );

          expect( onOff.getAttribute( "on-text" ) )
            .to.be.a( "string" )
            .that.equals( setTo );

          expect( onOff )
            .to.have.property( "outerHTML" )
            .that.equals( "<on-off on-text=\"" + setTo + "\"></on-off>" );
        });

        test( "can be set via property \"onText\"", function() {
          var onOff = document.createElement( "on-off" ),
            setTo = "Set via Property";

          onOff.onText = setTo;

          expect( onOff )
            .to.have.property( "onText" )
            .that.equals( setTo );
        });

        test( "setting via \"setAttribute\" reflects to property \"onText\"", function() {
          var onOff = document.createElement( "on-off" ),
            setTo = "Set via Attribute";

          onOff.setAttribute( "on-text", setTo );

          expect( onOff )
            .to.have.property( "onText" )
            .that.equals( setTo )
            .and.equals( onOff.getAttribute( "on-text" ) );
        });

        test( "setting via property \"onText\" reflects to attribute \"on-text\"", function() {
          var onOff = document.createElement( "on-off" ),
            setTo = "Set via Property";

          onOff.onText = setTo;

          expect( onOff.hasAttribute( "on-text" ) ).to.equal( true );
          expect( onOff.getAttribute( "on-text" ) )
            .to.be.a( "string" )
            .that.equals( setTo )
            .and.equal( onOff.onText );
        });

        test( "removing attribute \"on-text\" sets property back to default value", function() {
          var onOff = document.createElement( "on-off" ),
            setTo = "Set via Attribute";

          onOff.setAttribute( "on-text", setTo );
          expect( onOff.hasAttribute( "on-text" ) ).to.equal( true );

          onOff.removeAttribute( "on-text" );
          // expect( onOff.hasAttribute( "on-text" ) ).to.equal( false );

          expect( onOff )
            .to.have.property( "onText" )
            .that.is.a( "string" )
            .and.equals( "On" );
        });

        test( "setting \"onText\" to null resets property to default value", function() {
          var onOff = document.createElement( "on-off" ),
            setTo = "Set via Property";

          onOff.onText = setTo;
          expect( onOff )
            .to.have.property( "onText" )
            .that.is.a( "string" )
            .and.equals( setTo );

          onOff.onText = null;
          expect( onOff )
            .to.have.property( "onText" )
            .that.is.a( "string" )
            .and.equals( "On" );
        });

        test( "setting \"onText\" to undefined resets property to default value", function() {
          var onOff = document.createElement( "on-off" ),
            setTo = "Set via Property";

          onOff.onText = setTo;
          expect( onOff )
            .to.have.property( "onText" )
            .that.is.a( "string" )
            .and.equals( setTo );

          onOff.onText = undefined;
          expect( onOff )
            .to.have.property( "onText" )
            .that.is.a( "string" )
            .and.equals( "On" );
        });
      });

      suite( "off-text / offText", function() {
        test( "has default value: \"Off\"", function() {
          expect( document.createElement( "on-off" ) )
            .to.have.property( "offText" )
            .that.is.a( "string" )
            .and.equals( "Off" );
        });

        test( "can be set via \"setAttribute\"", function() {
          var onOff = document.createElement( "on-off" ),
            setTo = "Set via Attribute";

          onOff.setAttribute( "off-text", setTo );

          expect( onOff.hasAttribute( "off-text" ) ).to.equal( true );

          expect( onOff.getAttribute( "off-text" ) )
            .to.be.a( "string" )
            .that.equals( setTo );

          expect( onOff )
            .to.have.property( "outerHTML" )
            .that.equals( "<on-off off-text=\"" + setTo + "\"></on-off>" );
        });

        test( "can be set via property \"offText\"", function() {
          var onOff = document.createElement( "on-off" ),
            setTo = "Set via Property";

          onOff.offText = setTo;

          expect( onOff )
            .to.have.property( "offText" )
            .that.equals( setTo );
        });

        test( "setting via \"setAttribute\" reflects to property \"offText\"", function() {
          var onOff = document.createElement( "on-off" ),
            setTo = "Set via Attribute";

          onOff.setAttribute( "off-text", setTo );

          expect( onOff )
            .to.have.property( "offText" )
            .that.equals( setTo )
            .and.equals( onOff.getAttribute( "off-text" ) );
        });

        test( "setting via property \"offText\" reflects to attribute \"off-text\"", function() {
          var onOff = document.createElement( "on-off" ),
            setTo = "Set via Property";

          onOff.offText = setTo;

          expect( onOff.hasAttribute( "off-text" ) ).to.equal( true );
          expect( onOff.getAttribute( "off-text" ) )
            .to.be.a( "string" )
            .that.equals( setTo )
            .and.equal( onOff.offText );
        });

        test( "removing attribute \"off-text\" sets property back to default value", function() {
          var onOff = document.createElement( "on-off" ),
            setTo = "Set via Attribute";

          onOff.setAttribute( "off-text", setTo );
          expect( onOff.hasAttribute( "off-text" ) ).to.equal( true );

          onOff.removeAttribute( "off-text" );
          expect( onOff.hasAttribute( "off-text" ) ).to.equal( true );

          expect( onOff )
            .to.have.property( "offText" )
            .that.is.a( "string" )
            .and.equals( "Off" );
        });

        test( "setting \"offText\" to null resets property to default value", function() {
          var onOff = document.createElement( "on-off" ),
            setTo = "Set via Property";

          onOff.offText = setTo;
          expect( onOff )
            .to.have.property( "offText" )
            .that.is.a( "string" )
            .and.equals( setTo );

          onOff.offText = null;
          expect( onOff )
            .to.have.property( "offText" )
            .that.is.a( "string" )
            .and.equals( "Off" );
        });

        test( "setting \"offText\" to undefined resets property to default value", function() {
          var onOff = document.createElement( "on-off" ),
            setTo = "Set via Property";

          onOff.offText = setTo;
          expect( onOff )
            .to.have.property( "offText" )
            .that.is.a( "string" )
            .and.equals( setTo );

          onOff.offText = undefined;
          expect( onOff )
            .to.have.property( "offText" )
            .that.is.a( "string" )
            .and.equals( "Off" );
        });
      });

      suite( "checked / checked", function() {
        test( "has default value: false", function() {
          var onOff = document.createElement( "on-off" );

          // Check default setup
          expect( onOff )
            .to.have.property( "checked" )
            .that.is.a( "boolean" )
            .and.equals( false );
        });

        test( "can be set via \"setAttribute\"", function() {
          var onOff = document.createElement( "on-off" );

          // Set to be checked
          onOff.setAttribute( "checked", "" );
          expect( onOff.hasAttribute( "checked" ) ).to.equal( true );
          expect( onOff.getAttribute( "checked" ) )
            .to.be.a( "string" )
            .and.equal( "" );
        });

        test( "can be set via property \"checked\"", function() {
          var onOff = document.createElement( "on-off" );

          onOff.checked = true;
          expect( onOff )
            .to.have.property( "checked" )
            .that.is.a( "boolean" )
            .and.equals( true );
        });

        test( "setting via attribute reflects to property", function() {
          var onOff = document.createElement( "on-off" );

          onOff.setAttribute( "checked", "" );
          expect( onOff )
            .to.have.property( "checked" )
            .that.is.a( "boolean" )
            .and.equals( true );
        });

        test( "setting via property reflects to attribute", function() {
          var onOff = document.createElement( "on-off" );

          onOff.checked = true;
          expect( onOff.hasAttribute( "checked" ) )
            .to.be.a( "boolean" )
            .and.to.equal( true );
        });

        // remove attribute sets to false
        test( "removing attribute reflects to attribute", function() {
          var onOff = document.createElement( "on-off" );

          onOff.setAttribute( "checked", "" );
          expect( onOff.hasAttribute( "checked" ) )
            .to.be.a( "boolean" )
            .and.to.equal( true );

          onOff.removeAttribute( "checked" );
          expect( onOff )
            .to.have.property( "checked" )
            .that.is.a( "boolean" )
            .and.equals( false );
        });

        test( "setting property to false removes attribute", function() {
          var onOff = document.createElement( "on-off" );

          onOff.checked = true;
          expect( onOff )
            .to.have.property( "checked" )
            .that.is.a( "boolean" )
            .and.equals( true );

          expect( onOff.hasAttribute( "checked" ) )
            .to.be.a( "boolean" )
            .that.equals( true );

          onOff.checked = false;
          expect( onOff )
            .to.have.property( "checked" )
            .that.is.a( "boolean" )
            .and.equals( false );

          expect( onOff.hasAttribute( "checked" ) )
            .to.be.a( "boolean" )
            .and.to.equal( false );
        });
      });

      suite( "disabled / disabled", function() {
        test( "has default value: false", function() {
          var onOff = document.createElement( "on-off" );

          // Check default setup
          expect( onOff )
            .to.have.property( "disabled" )
            .that.is.a( "boolean" )
            .and.equals( false );
        });

        test( "can be set via \"setAttribute\"", function() {
          var onOff = document.createElement( "on-off" );

          // Set to be disabled
          onOff.setAttribute( "disabled", "" );
          expect( onOff.hasAttribute( "disabled" ) ).to.equal( true );
          expect( onOff.getAttribute( "disabled" ) )
            .to.be.a( "string" )
            .and.equal( "" );
        });

        test( "can be set via property \"disabled\"", function() {
          var onOff = document.createElement( "on-off" );

          onOff.disabled = true;
          expect( onOff )
            .to.have.property( "disabled" )
            .that.is.a( "boolean" )
            .and.equals( true );
        });

        test( "setting via attribute reflects to property", function() {
          var onOff = document.createElement( "on-off" );

          onOff.setAttribute( "disabled", "" );
          expect( onOff )
            .to.have.property( "disabled" )
            .that.is.a( "boolean" )
            .and.equals( true );
        });

        test( "setting via property reflects to attribute", function() {
          var onOff = document.createElement( "on-off" );

          onOff.disabled = true;
          expect( onOff.hasAttribute( "disabled" ) )
            .to.be.a( "boolean" )
            .and.to.equal( true );
        });

        // remove attribute sets to false
        test( "removing attribute reflects to property", function() {
          var onOff = document.createElement( "on-off" );

          onOff.setAttribute( "disabled", "" );
          expect( onOff.hasAttribute( "disabled" ) )
            .to.be.a( "boolean" )
            .and.to.equal( true );

          onOff.removeAttribute( "disabled" );
          expect( onOff )
            .to.have.property( "disabled" )
            .that.is.a( "boolean" )
            .and.equals( false );
        });

        test( "setting property to false removes attribute", function() {
          var onOff = document.createElement( "on-off" );

          onOff.disabled = true;
          expect( onOff )
            .to.have.property( "disabled" )
            .that.is.a( "boolean" )
            .and.equals( true );

          expect( onOff.hasAttribute( "disabled" ) )
            .to.be.a( "boolean" )
            .that.equals( true );

          onOff.disabled = false;
          expect( onOff )
            .to.have.property( "disabled" )
            .that.is.a( "boolean" )
            .and.equals( false );

          expect( onOff.hasAttribute( "disabled" ) )
            .to.be.a( "boolean" )
            .and.to.equal( false );
        });
      });
    });

    suite( "Events", function() {
      suite( "On Event", function() {
        test( "on event fires when clicked", function( done ) {
          var onOff = document.createElement( "on-off" );

          onOff.checked = false;
          onOff.disabled = false;

          onOff.addEventListener( "on", function( event ) {
            expect( event )
              .to.be.an.instanceof( CustomEvent )
              .and.to.have.property( "target", onOff );

            expect( event )
              .to.have.property( "type", "on" );

            expect( event )
              .to.have.deep.property( "detail.msg", "on" );

            done();
          });

          onOff.shadowRoot.getElementById( "checkbox" )
            .dispatchEvent( new MouseEvent( "click" ) );
        });

        test( "on event fires when checked property changed", function( done ) {
          var onOff = document.createElement( "on-off" );
          onOff.checked = false;

          onOff.addEventListener( "on", function( event ) {
            expect( event )
              .to.be.an.instanceof( CustomEvent )
              .and.to.have.property( "target", onOff );

            expect( event )
              .to.have.property( "type", "on" );

            expect( event )
              .to.have.deep.property( "detail.msg", "on" );

            done();
          });

          // should fire "on" and "toggle" event
          onOff.checked = true;
        });
      });

      suite( "Off Event", function() {
        test( "off event fires when clicked", function( done ) {
          var onOff = document.createElement( "on-off" );

          // set on
          onOff.checked = true;

          onOff.addEventListener( "off", function( event ) {
            expect( event )
              .to.be.an.instanceof( CustomEvent )
              .and.to.have.property( "target", onOff );

            expect( event )
              .to.have.property( "type", "off" );

            expect( event )
              .to.have.deep.property( "detail.msg", "off" );

            done();
          });

          onOff.shadowRoot.getElementById( "checkbox" )
            .dispatchEvent( new MouseEvent( "click" ) );
        });

        test( "off event fires when checked property changed", function( done ) {
          var onOff = document.createElement( "on-off" );

          onOff.checked = true;

          onOff.addEventListener( "off", function( event ) {
            expect( event )
              .to.be.an.instanceof( CustomEvent )
              .and.to.have.property( "target", onOff );

            expect( event )
              .to.have.property( "type", "off" );

            expect( event )
              .to.have.deep.property( "detail.msg", "off" );

            done();
          });

          onOff.checked = false;
        });
      });

      suite( "Toggle Event", function() {
        test( "toggle event fires when clicked on", function( done ) {
          var onOff = document.createElement( "on-off" );

          onOff.checked = false;

          onOff.addEventListener( "toggle", function( event ) {
            expect( event )
              .to.be.an.instanceof( CustomEvent )
              .and.to.have.property( "target", onOff );

            expect( event )
              .to.have.property( "type", "toggle" );

            expect( event.detail )
              .to.have.property( "msg", "toggle" );

            expect( event.detail )
              .to.have.property( "state", "on" );

            done();
          });

          onOff.shadowRoot.getElementById( "checkbox" )
            .dispatchEvent( new MouseEvent( "click" ) );
        });

        test( "toggle event fires when clicked off", function( done ) {
          var onOff = document.createElement( "on-off" );

          // set on
          onOff.checked = true;

          onOff.addEventListener( "toggle", function( event ) {
            expect( event )
              .to.be.an.instanceof( CustomEvent )
              .and.to.have.property( "target", onOff );

            expect( event )
              .to.have.property( "type", "toggle" );

            expect( event.detail )
              .to.have.property( "msg", "toggle" );

            expect( event.detail )
              .to.have.property( "state", "off" );

            done();
          });

          onOff.shadowRoot.getElementById( "checkbox" )
            .dispatchEvent( new MouseEvent( "click" ) );
        });

        test( "toggle event fires when checked property changed to true(on)", function( done ) {
          var onOff = document.createElement( "on-off" );

          onOff.checked = false;

          onOff.addEventListener( "toggle", function( event ) {
            expect( event )
              .to.be.an.instanceof( CustomEvent )
              .and.to.have.property( "target", onOff );

            expect( event )
              .to.have.property( "type", "toggle" );

            expect( event.detail )
              .to.have.property( "msg", "toggle" );

            expect( event.detail )
              .to.have.property( "state", "on" );

            done();
          });

          onOff.checked = true;
        });

        test( "toggle event fires when checked property changed to false(off)", function( done ) {
          var onOff = document.createElement( "on-off" );

          onOff.checked = true;

          onOff.addEventListener( "toggle", function( event ) {
            expect( event )
              .to.be.an.instanceof( CustomEvent )
              .and.to.have.property( "target", onOff );

            expect( event )
              .to.have.property( "type", "toggle" );

            expect( event.detail )
              .to.have.property( "msg", "toggle" );

            expect( event.detail )
              .to.have.property( "state", "off" );

            done();
          });

          onOff.checked = false;
        });
      });
    });
  });
})( window, document, window.chai );
