/*eslint-env mocha*/
/*global suite, test, console*/
( function( win, doc, System, sinon, expect ) {
  "use strict";

  suite( "EDMedia", function() {
    var EDMedia;

    suiteSetup( function( done ) {
      System.import( "domain/ed/objects/media/EDMedia" )
        .then( function( imported ) {
          EDMedia = imported.default;
          done();
        }, function( error ) {
          console.warn( "Could not import 'EDMedia' for testing: ", error.message );
          console.error( error.stack );
          done( error );
        });
    });

    suite( "EDMedia creation", function() {
      test( "constructor has expected properties", function() {
        var artObj = {
            original: null,
            phone: {
              small: null,
              large: null
            },
            tablet: {
              small: null,
              large: null
            },
            thumbnail: {
              small: null,
              large: null
            }
          },
          media = new EDMedia( artObj );

        expect( media )
          .to.have.property( "original" );

        expect( media )
          .to.have.property( "phone" );

        expect( media )
          .to.have.property( "tablet" );

        expect( media )
          .to.have.property( "thumbnail" );
      });

      test( "original property to be an image", function() {
        var artObj = {
            original: "",
            phone: {
              small: "",
              large: ""
            },
            tablet: {
              small: "",
              large: ""
            },
            thumbnail: {
              small: "",
              large: ""
            }
          },
          media = new EDMedia( artObj );

        expect( media.original )
          .to.be.a( "string" );

      });

      test( "phone property to be an object", function() {
        var artObj = {
            original: "",
            phone: {
              small: "",
              large: ""
            },
            tablet: {
              small: "",
              large: ""
            },
            thumbnail: {
              small: "",
              large: ""
            }
          },
          media = new EDMedia( artObj );

        expect( media.phone )
          .to.be.an( "object" );

      });

      test( "small property in phone block to be an image", function() {
        var artObj = {
            original: "",
            phone: {
              small: "",
              large: ""
            },
            tablet: {
              small: "",
              large: ""
            },
            thumbnail: {
              small: "",
              large: ""
            }
          },
          media = new EDMedia( artObj );

        expect( media.phone.small )
          .to.be.a( "string" );

      });

      test( "large property in phone block to be an image", function() {
        var artObj = {
            original: "",
            phone: {
              small: "",
              large: ""
            },
            tablet: {
              small: "",
              large: ""
            },
            thumbnail: {
              small: "",
              large: ""
            }
          },
          media = new EDMedia( artObj );

        expect( media.phone.large )
          .to.be.a( "string" );

      });

      test( "tablet property to be an object", function() {
        var artObj = {
            original: "",
            phone: {
              small: "",
              large: ""
            },
            tablet: {
              small: "",
              large: ""
            },
            thumbnail: {
              small: "",
              large: ""
            }
          },
          media = new EDMedia( artObj );

        expect( media.tablet )
          .to.be.an( "object" );

      });

      test( "small property in table block to be an image", function() {
        var artObj = {
            original: "",
            phone: {
              small: "",
              large: ""
            },
            tablet: {
              small: "",
              large: ""
            },
            thumbnail: {
              small: "",
              large: ""
            }
          },
          media = new EDMedia( artObj );

        expect( media.tablet.small )
          .to.be.a( "string" );

      });

      test( "thumbnail property to be an object", function() {
        var artObj = {
            original: "",
            phone: {
              small: "",
              large: ""
            },
            tablet: {
              small: "",
              large: ""
            },
            thumbnail: {
              small: "",
              large: ""
            }
          },
          media = new EDMedia( artObj );

        expect( media.thumbnail )
          .to.be.a( "object" );

      });

      test( "small property in thumbnail block to be an image", function() {
        var artObj = {
            original: "",
            phone: {
              small: "",
              large: ""
            },
            tablet: {
              small: "",
              large: ""
            },
            thumbnail: {
              small: "",
              large: ""
            }
          },
          media = new EDMedia( artObj );

        expect( media.thumbnail.small )
          .to.be.a( "string" );

      });

      test( "large property in thumbnail block to be an image", function() {
        var artObj = {
            original: "",
            phone: {
              small: "",
              large: ""
            },
            tablet: {
              small: "",
              large: ""
            },
            thumbnail: {
              small: "",
              large: ""
            }
          },
          media = new EDMedia( artObj );

        expect( media.thumbnail.large )
          .to.be.a( "string" );

      });
    });
  });
})( window, document, window.System, window.sinon, window.chai.expect );
