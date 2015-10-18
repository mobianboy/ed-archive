/*eslint-env mocha */
/*jscs:disable maximumLineLength*/
( function( window, document, polymer, sinon, chai ) {
  "use strict";
  var expect = chai.expect,
      // get wrapper from document or for karma, create a new div and append it to the DOM
      testingWrapper = document.getElementById( "img-placeholder-test-wrapper" ) ||
        ( function() {
          var wrapper = document.createElement( "div" );
          document.body.appendChild( wrapper );
          return wrapper;
        })(),
      imageMap = ( function() {
        var urlPrefix = "http://",
            urlPostfix = ".com/200/300",
            map = {},
            imageNames = [
              "fillmurray",
              "placecage"
            ];

        imageNames.forEach( function( imageName ) {
          map[imageName] = urlPrefix + imageName + urlPostfix;
        });
        return map;
      })(),
      // original state to test against
      originalWrapperOuterHTML = testingWrapper.outerHTML,
      // re-sets wrapper to blank
      resetWrapper = function() {
        testingWrapper.innerHTML = "";
      };

  suite( "<img-placeholder>", function() {
    suite( "Life Cycle", function() {
      teardown( function() {
        resetWrapper();
      });

      test( "ready: can create from document.createElement", function() {
        var createdSpy = sinon.spy(
          polymer.getRegisteredPrototype( "img-placeholder" ),
          "ready"
        );

        expect( document.createElement( "img-placeholder" ) )
          .to.have.property( "outerHTML" )
          .that.is.a( "string" )
          .and.equals( "<img-placeholder></img-placeholder>" );

        expect( createdSpy ).to.have.callCount( 1 );
        createdSpy.restore();
      });

      // skipped due to polymer not generating attached
      test.skip( "attached: can be added to another DOM Element", function() {
        var newElement = document.createElement( "img-placeholder" ),
            attachedSpy = sinon.spy( newElement, "attached" );

        testingWrapper.appendChild( newElement );

        expect( attachedSpy ).to.have.callCount( 1 );

        expect( testingWrapper )
          .to.have.property( "innerHTML" )
          .that.is.a( "string" )
          .and.equals( "<img-placeholder></img-placeholder>" );

        attachedSpy.restore();
      });

      // skipped due to polymer not generating detached
      test.skip( "detached: can be removed from another DOM element", function() {
        var newElement = document.createElement( "img-placeholder" ),
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
      suite( "Image", function() {
        test( "can be set via \"setAttribute\"", function() {
          var imgPlaceholder = document.createElement( "img-placeholder" ),
              setTo = "placecage";

          imgPlaceholder.setAttribute( "image", setTo );

          expect( imgPlaceholder.hasAttribute( "image" ) ).to.equal( true );

          expect( imgPlaceholder.getAttribute( "image" ) )
            .to.be.a( "string" )
            .that.equals( setTo );

          expect( imgPlaceholder )
            .to.have.property( "outerHTML" )
            .that.equals( "<img-placeholder image=\"" + setTo + "\"></img-placeholder>" );
        });

        test( "can be set via property \"image\"", function() {
          var imgPlaceholder = document.createElement( "img-placeholder" ),
              setTo = "placecage";

          imgPlaceholder.image = setTo;

          expect( imgPlaceholder )
            .to.have.property( "image" )
            .that.equals( setTo );
        });

        test( "setting via \"setAttribute\" reflects to property \"image\"", function() {
          var imgPlaceholder = document.createElement( "img-placeholder" ),
              setTo = "placecage";

          imgPlaceholder.setAttribute( "image", setTo );

          expect( imgPlaceholder )
            .to.have.property( "image" )
            .that.equals( setTo )
            .and.equals( imgPlaceholder.getAttribute( "image" ) );
        });

        test( "setting via property \"image\" reflects to attribute \"image\"", function() {
          var imgPlaceholder = document.createElement( "img-placeholder" ),
              setTo = "placecage",
              observeFn = function( changes ) {
                expect( imgPlaceholder.hasAttribute( "image" ) ).to.equal( true );
                expect( imgPlaceholder.getAttribute( "image" ) )
                  .to.be.a( "string" )
                  .that.equals( setTo )
                  .and.equal( imgPlaceholder.image );

                Object.unobserve( imgPlaceholder, observeFn );
              };

          imgPlaceholder.image = setTo;

          Object.observe( imgPlaceholder, observeFn );
        });

        test( "setting \"image\" via property uses the imageMap url", function() {
          var imgPlaceholder = document.createElement( "img-placeholder" ),
              observeFn = function( changes ) {
                expect( imgPlaceholder.shadowRoot.querySelector( "img" ) )
                  .to.have.property( "src" )
                  .that.is.a( "string" )
                  .and.equals( imageMap.placecage );

                Object.unobserve( imgPlaceholder, observeFn );
              };

          imgPlaceholder.image = "placecage";

          Object.observe( imgPlaceholder, observeFn );
        });

        test( "setting \"image\" via attribute uses the imageMap url", function() {
          var imgPlaceholder = document.createElement( "img-placeholder" ),
              observeFn = function( changes ) {
                expect( imgPlaceholder.shadowRoot.querySelector( "img" ) )
                  .to.have.property( "src" )
                  .that.is.a( "string" )
                  .and.equals( imageMap.placecage );

                Object.unobserve( imgPlaceholder, observeFn );
              };

          imgPlaceholder.setAttribute( "image", "placecage" );

          Object.observe( imgPlaceholder, observeFn );
        });

        suite( "Src", function() {
          test( "can be set via \"setAttribute\"", function() {
            var imgPlaceholder = document.createElement( "img-placeholder" ),
                setTo = "http://www.placecage.com/200/300";

            imgPlaceholder.setAttribute( "src", setTo );

            expect( imgPlaceholder.hasAttribute( "src" ) ).to.equal( true );

            expect( imgPlaceholder.getAttribute( "src" ) )
              .to.be.a( "string" )
              .that.equals( setTo );

            expect( imgPlaceholder )
              .to.have.property( "outerHTML" )
              .that.equals( "<img-placeholder src=\"" + setTo + "\"></img-placeholder>" );
          });

          test( "can be set via property \"src\"", function() {
            var imgPlaceholder = document.createElement( "img-placeholder" ),
                setTo = "http://www.placecage.com/200/300";

            imgPlaceholder.src = setTo;

            expect( imgPlaceholder )
              .to.have.property( "src" )
              .that.equals( setTo );
          });

          test( "setting via \"setAttribute\" reflects to property \"src\"", function() {
            var imgPlaceholder = document.createElement( "img-placeholder" ),
                setTo = "http://www.placecage.com/200/300";

            imgPlaceholder.setAttribute( "src", setTo );

            expect( imgPlaceholder )
              .to.have.property( "src" )
              .that.equals( setTo )
              .and.equals( imgPlaceholder.getAttribute( "src" ) );
          });

          test( "setting via property \"src\" reflects to attribute \"src\"", function() {
            var imgPlaceholder = document.createElement( "img-placeholder" ),
                setTo = "Set via Property",
                observeFn = function( changes ) {
                  expect( imgPlaceholder.hasAttribute( "src" ) ).to.equal( true );
                  expect( imgPlaceholder.getAttribute( "src" ) )
                    .to.be.a( "string" )
                    .that.equals( setTo )
                    .and.equal( imgPlaceholder.src );

                  Object.unobserve( imgPlaceholder, observeFn );
                };

            imgPlaceholder.src = setTo;

            Object.observe( imgPlaceholder, observeFn );
          });

          test( "using src will override image via attribute", function() {
            var imgPlaceholder = document.createElement( "img-placeholder" ),
                srcImg = "http://www.placecage.com/200/300",
                observeFn = function( changes ) {
                  expect( imgPlaceholder.hasAttribute( "src" ) ).to.equal( true );
                  expect( imgPlaceholder.shadowRoot.querySelector( "img" ) )
                    .to.have.property( "src" )
                    .that.is.a( "string" )
                    .and.equals( srcImg );

                  Object.unobserve( imgPlaceholder, observeFn );
                };

            imgPlaceholder.setAttribute( "image", "fillmurray" );
            imgPlaceholder.setAttribute( "src", srcImg );

            Object.observe( imgPlaceholder, observeFn );
          });

          test( "removing attribute \"src\" uses image fallback", function() {
            var imgPlaceholder = document.createElement( "img-placeholder" ),
                observeFn = function( changes ) {
                  expect( imgPlaceholder.hasAttribute( "src" ) ).to.equal( false );
                  expect( imgPlaceholder.shadowRoot.querySelector( "img" ) )
                    .to.have.property( "src" )
                    .that.is.a( "string" )
                    .and.equals( imageMap.fillmurray );

                  Object.unobserve( imgPlaceholder, observeFn );
                };

            imgPlaceholder.setAttribute( "image", "fillmurray" );
            imgPlaceholder.setAttribute( "src", "http://www.placecage.com/200/300" );

            expect( imgPlaceholder.hasAttribute( "image" ) ).to.equal( true );
            expect( imgPlaceholder.hasAttribute( "src" ) ).to.equal( true );

            imgPlaceholder.removeAttribute( "src" );

            Object.observe( imgPlaceholder, observeFn );
          });

          test( "setting \"src\" to null uses image fallback", function() {
            var imgPlaceholder = document.createElement( "img-placeholder" ),
                srcImg = "http://www.placecage.com/200/300",
                observeFn = function( changes ) {
                  expect( imgPlaceholder.shadowRoot.querySelector( "img" ) )
                    .to.have.property( "src" )
                    .that.is.a( "string" )
                    .and.equals( imageMap.fillmurray );

                  Object.unobserve( imgPlaceholder, observeFn );
                };

            imgPlaceholder.image = "fillmurray";
            imgPlaceholder.src = srcImg;
            expect( imgPlaceholder )
              .to.have.property( "src" )
              .that.is.a( "string" )
              .and.equals( srcImg );

            imgPlaceholder.src = null;

            Object.observe( imgPlaceholder, observeFn );
          });

          test( "setting \"src\" to undefined uses image fallback", function() {
            var imgPlaceholder = document.createElement( "img-placeholder" ),
                srcImg = "http://www.placecage.com/200/300",
                observeFn = function( changes ) {
                  expect( imgPlaceholder.shadowRoot.querySelector( "img" ) )
                    .to.have.property( "src" )
                    .that.is.a( "string" )
                    .and.equals( imageMap.fillmurray );

                  Object.unobserve( imgPlaceholder, observeFn );
                };

            imgPlaceholder.image = "fillmurray";
            imgPlaceholder.src = srcImg;
            expect( imgPlaceholder )
              .to.have.property( "src" )
              .that.is.a( "string" )
              .and.equals( srcImg );

            imgPlaceholder.src = undefined;

            Object.observe( imgPlaceholder, observeFn );
          });

          test( "if src isnt valid use fallback", function() {
            var imgPlaceholder = document.createElement( "img-placeholder" ),
                innerImg = imgPlaceholder.shadowRoot.querySelector( "img" ),
                srcImg = "http://www.placecage.com/200/300",
                observeFn = function( changes ) {
                  imgPlaceholder.image = "fillmurray";
                  imgPlaceholder.src = srcImg;

                  expect( innerImg )
                    .to.have.property( "src" )
                    .that.is.a( "string" )
                    .and.equals( srcImg );

                  imgPlaceholder.src = "WaitThisIsn'tARealImage";

                  expect( innerImg )
                    .to.have.property( "src" )
                    .that.is.a( "string" )
                    .and.equals( imageMap.fillmurray );

                  Object.unobserve( imgPlaceholder, observeFn );
                };
            Object.observe( imgPlaceholder, observeFn );
          });
        });

        suite( "ImageMap", function() {
          test( "can be set via \"property\"", function() {
            var imgPlaceholder = document.createElement( "img-placeholder" ),
                newMap = {
                  stevensegallery: "http://stevensegallery.com/200/300",
                  fillmurray: "http://fillmurray.com/200/300"
                };

            imgPlaceholder.imageMap = newMap;
            expect( imgPlaceholder )
              .to.have.property( "imageMap" )
              .that.is.a( "object" )
              .and.equals( newMap );
          });

          test( "can set new image with new imageMap", function() {
            var imgPlaceholder = document.createElement( "img-placeholder" ),
                newMap = {
                  stevensegallery: "http://stevensegallery.com/200/300",
                  fillmurray: "http://fillmurray.com/200/300"
                },
                observeFn = function( changes ) {
                  expect( imgPlaceholder.shadowRoot.querySelector( "img" ) )
                    .to.have.property( "src" )
                    .that.is.a( "string" )
                    .and.equals( newMap.stevensegallery );

                  Object.unobserve( imgPlaceholder, observeFn );
                };

            imgPlaceholder.imageMap = newMap;
            imgPlaceholder.image = "stevensegallery";

            Object.observe( imgPlaceholder, observeFn );
          });
        });
      });
    });
  });
})( window, document, window.Polymer, window.sinon, window.chai );
