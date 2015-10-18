/*eslint-env mocha*/
/*global suite, test, console*/
( function( win, doc, System, sinon, expect ) {
  "use strict";

  suite( "EDTrack", function() {
    var EDTrack;

    suiteSetup( function( done ) {
      System.import( "domain/ed/objects/media/EDTrack" )
        .then( function( imported ) {
          EDTrack = imported.default;
          done();
        }, function( error ) {
          console.warn( "Could not import 'EDTrack' for testing: ", error.message );
          console.error( error.stack );
          done( error );
        });
    });

    suite.skip( "EDTrack creation", function() {
      test( "constructor has expected properties", function() {
        var trackObj = {
            artistId: "001",
            createdBy: "Joe",
            name: "Piper",
            playCount: 0,
            waveformImage: "https://static.gearslutz.com/board/imgext.php?u=http%3A%2F%2Fdaveshort.org%2Fmusic%2Floudness%2Fmetallica_big.png&h=067a1a9b3aa501c5f0ac43f4851235bb",
            createdDate: Date.now()
          },
          track = new EDTrack( trackObj );

        expect( track )
          .to.have.property( "artistId" );

        expect( track )
          .to.have.property( "createdBy" );

        expect( track )
          .to.have.property( "name" );

        expect( track )
          .to.have.property( "playCount" );

        expect( track )
          .to.have.property( "waveformImage" );

        expect( track )
          .to.have.property( "createdDate" );
      });
    });

    suite.skip( "getArtist Method", function() {
      test( "returns a promise", function() {
        var trackObj = {
            artistId: "001",
            createdBy: "Joe",
            name: "Piper",
            playCount: 0,
            waveformImage: "https://static.gearslutz.com/board/imgext.php?u=http%3A%2F%2Fdaveshort.org%2Fmusic%2Floudness%2Fmetallica_big.png&h=067a1a9b3aa501c5f0ac43f4851235bb",
            createdDate: Date.now()
          },
          track = new EDTrack( trackObj );
      });
    });

    suite.skip( "getCreator", function() {
      test( "returns a promise", function() {
        var trackObj = {
            artistId: "001",
            createdBy: "Joe",
            name: "Piper",
            playCount: 0,
            waveformImage: "https://static.gearslutz.com/board/imgext.php?u=http%3A%2F%2Fdaveshort.org%2Fmusic%2Floudness%2Fmetallica_big.png&h=067a1a9b3aa501c5f0ac43f4851235bb",
            createdDate: Date.now()
          },
          track = new EDTrack( trackObj );
      });
    });
  });
})( window, document, window.System, window.sinon, window.chai.expect );
