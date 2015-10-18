( function( win, doc, System, sinon, expect ) {
  "use strict";

  suite( "edDiscoverService", function() {
    var edDiscoverService, EDGenre;

    suiteSetup( function( done ) {
      Promise.all( [
        System.import( "domain/ed/services/ed-discover-service" ),
        System.import( "domain/ed/objects/EDGenre" )
      ] )
        .then( function( imported ) {
          edDiscoverService = imported[0].default;
          EDGenre = imported[1].default;
          done();
        }, function( error ) {
          console.warn( "Could not import 'edDiscoverService' for testing: ", error.message );
          console.error( error.stack );
          done( error );
        } );
    } );

    suite( "Properties", function() {
      suite( "currentProfileBlend", function() {
        test( "edService has 'currentProfileBlend' property", function() {
          expect( edDiscoverService )
            .to.have.property( "currentProfileBlend" );
        });

        test( "is an array of EDGenre objects", function() {
          expect( edDiscoverService.currentProfileBlend )
            .to.be.an( "array" );
            // .that.deep.equals( "i have no idea what; is this even necessary" );
        });
      });
    });

    suite( "Methods", function() {
      suite( "getDiscoverTrackList", function() {
        test( "EDGenre object is a valid parameter", function() {
          var genreData =
            {
              id: 999
            },
            edGenre = new EDGenre( genreData ),
            getTrackSpy = sinon.spy( this, "getGenreTracks" );

          edDiscoverService.getDiscoverTrackList( edGenre.id );

          expect( getTrackSpy )
            .to.have.callCount( 1 );

          getTrackSpy.restore();
        });

        test( "'profileBlend' is a valid parameter", function() {
          var getTrackSpy = sinon.spy( this, "getBlendTracks" );

          edDiscoverService.getDiscoverTrackList( "profileBlend" );

          expect( getTrackSpy )
            .to.have.callCount( 1 );

          getTrackSpy.restore();
        });

        test( "when invalid parameter is used, an error is thrown", function() {
          var badParameter = "UNACCEPTABLE!",
            badParams = edDiscoverService.getDiscoverTrackList( badParameter );

          expect( badParams )
            .to.throw( Error );
        });
      });

      suite( "setCurrentProfileBlend", function() {
        // TODO need to indicate that promise was fulfilled or rejected
        test( "after successful response from server, should return array", function() {
          var country =
            {
              id: 999
            },
            bluegrass =
            {
              id: 414
            },
            punk =
            {
              id: 355
            },
            currentProfileBlend = [ country.id, bluegrass.id, punk.id ],
            setCurrentBlend = edDiscoverService.setCurrentProfileBlend( currentProfileBlend );

          expect( setCurrentBlend )
            .to.be.an( "array" );
        });

        test( "if response from server is unsuccessful, should throw error", function() {
          var country =
            {
              id: 999
            },
            bluegrass =
            {
              id: 414
            },
            punk =
            {
              id: 355
            },
            currentProfileBlend = [ country.id, bluegrass.id, punk.id ],
            setCurrentBlend = edDiscoverService.setCurrentProfileBlend( currentProfileBlend );

          expect( setCurrentBlend )
            .to.throw( Error );
        });
      });
    });

    suite( "helper functions", function() {
      // TODO need to indicate that promise was fulfilled or rejected
      suite( "getGenreTracks", function() {
        test( "after successful response from server, should return array", function() {
          var genreData =
            {
              id: 999
            },
            edGenre = new EDGenre( genreData ),
            trackReturn = edDiscoverService.getGenreTracks( edGenre.id );

          expect( trackReturn )
            .to.be.an( "array" );
        });

        test( "if response from server is unsuccessful, should throw error", function() {
          var genreData =
            {
              id: 999
            },
            edGenre = new EDGenre( genreData ),
            trackReturn = edDiscoverService.getGenreTracks( edGenre.id );

          expect( trackReturn )
            .to.throw( Error );
        });
      });

      suite( "getBlendTracks", function() {
        test( "after successful response from server, should return array", function() {
          var blendReturn = edDiscoverService.getBlendTracks();

          expect( blendReturn )
            .to.be.an( "array" );
        });

        test( "if response from server is unsuccessful, should throw error", function() {
          var blendReturn = edDiscoverService.getBlendTracks();

          expect( blendReturn )
            .to.throw( Error );
        });
      });
    });
  });
})( window, document, window.System, window.sinon, window.chai.expect );
