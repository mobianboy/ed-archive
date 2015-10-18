/*eslint-env mocha*/
/*global suite, test, console*/
( function( win, doc, System, sinon, expect ) {
  "use strict";

  suite( "edPlayerService", function() {
    var edPlayerService, EDTrack;

    suiteSetup(function( done ) {
      System.import( "domain/ed/services/ed-player-service" )
        .then( function( imported ) {
          edPlayerService = imported.default;
          done();
        }, function( error ) {
          console.warn( "Could not import 'edPlayerService' for testing: ", error.message );
          console.error( error.stack );
          done( error );
        }
      );

      System.import( "domain/ed/objects/EDTrack" )
        .then(function( imported ) {
          EDTrack = imported.default;
          done();
        }, function( error ) {
          console.warn( "Could not import 'edPlayerService' for testing: ", error.message );
          console.error( error.stack );
          done( error );
        }
      );
    });

    // Start Testing
    suite( "edPlayerService creation", function() {
      test( "currentStats have appropriate properties", function() {
        expect( edPlayerService.currentStats )
          .to.be.an( "object" )
          .to.contain.all.keys([ "playing", "time", "hours", "minutes", "seconds", "length" ]);
      });

      test( "queue property is an array", function() {
        expect( edPlayerService.queue )
          .to.be.an( "array" );
      });

      test( "isPlaying is a boolean", function() {
        expect( edPlayerService.isPlaying )
          .to.be.a( "boolean" );
      });

      test( "isPaused is a boolean", function() {
        expect( edPlayerService.isPaused )
          .to.be.a( "boolean" );
      });

      test( "isStopped is a boolean", function() {
        expect( edPlayerService.isStopped )
          .to.be.a( "boolean" );
      });

      test( "currentTime is a number", function() {
        expect( edPlayerService.currentTime )
          .to.be.a( "number" );
      });

      test( "currentSeconds is a number", function() {
        expect( edPlayerService.currentSeconds )
          .to.be.a( "number" );
      });

      test( "currentMinutes is a number", function() {
        expect( edPlayerService.currentMinutes )
          .to.be.a( "number" );
      });

      test( "currentHours is a number", function() {
        expect( edPlayerService.currentHours )
          .to.be.a( "number" );
      });

      test( "formattedTime is a string", function() {
        expect( edPlayerService.formattedTime )
          .to.be.a( "string" );
      });

      test( "trackLength is a number", function() {
        expect( edPlayerService.trackLength )
          .to.be.a( "number" );
      });
    });

    suite( "play method", function() {
      test( "if a track instance is passed, then should return true", function() {
        var trackData = {
            name: "baby got back"
          },
          track = new EDTrack( trackData ),
          playSpy = sinon.spy( edPlayerService, "play" );

        expect( edPlayerService.play( track ) )
          .to.equal( true );

        expect( playSpy )
          .to.have.callCount( 1 );

        playSpy.restore();
      });

      test( "if a non track instance is passed, then should throw typeError", function() {
        var trackData = {
            name: "baby got back"
          };

        expect(function() {
          edPlayerService.play( trackData );
        }).to.throw( "Track is not an EDTrack object" );
      });
    });

    suite( "pause method", function() {
      test( "if a track instance is passed, then should return true", function() {
        var trackData = {
            name: "baby got back"
          },
          track = new EDTrack( trackData ),
          pauseSpy = sinon.spy( edPlayerService, "pause" ),
          isPaused;

        isPaused = edPlayerService.pause( track );

        expect( pauseSpy )
          .to.have.callCount( 1 );

        expect( isPaused )
          .to.equal( false );

        pauseSpy.restore();
      });
    });

    suite( "stop method", function() {
      test( "if a track instance is passed, then should return true", function() {
        var trackData = {
            name: "baby got back"
          },
          track = new EDTrack( trackData ),
          stopSpy = sinon.spy( edPlayerService, "stop" );

        edPlayerService.stop( track );

        expect( stopSpy )
          .to.have.callCount( 1 );

        expect( edPlayerService.stop( track ) )
          .to.equal( true );

        stopSpy.restore();
      });
    });

    suite( "enqueue method", function() {
      test( "enqueue should add track to the queue", function() {
        var trackData = {
            name: "baby got back"
          },
          oldLength = edPlayerService.queue.length,
          newLength,
          track = new EDTrack( trackData ),
          track2 = new EDTrack( trackData ),
          enqueueSpy = sinon.spy( edPlayerService, "enqueue" );

        edPlayerService.enqueue( track );

        newLength = edPlayerService.queue.length;

        expect( enqueueSpy )
          .to.have.callCount( 1 );

        expect( newLength )
          .to.be.above( oldLength );

        edPlayerService.dequeue();

        enqueueSpy.restore();
      });
    });

    suite( "enqueueNext method", function() {
      test( "enqueue should add track to the queue", function() {
        var trackData = {
            name: "baby got back"
          },
          track = new EDTrack( trackData ),
          track2 = new EDTrack( trackData ),
          enqueueNextSpy = sinon.spy( edPlayerService, "enqueueNext" );

        edPlayerService.enqueueNext( track );
        edPlayerService.enqueueNext( track2 );

        expect( enqueueNextSpy )
          .to.have.callCount( 2 );

        expect( edPlayerService.queue[ 0 ] )
          .to.equal( track2 );

        edPlayerService.dequeue();
        edPlayerService.dequeue();

        enqueueNextSpy.restore();
      });
    });

    suite( "dequeue method", function() {
      test( "remove next track to the queue", function() {
        var trackData = {
            name: "baby got back"
          },
          track = new EDTrack( trackData ),
          track2 = new EDTrack( trackData ),
          dequeueSpy = sinon.spy( edPlayerService, "dequeue" );

        edPlayerService.enqueue( track );
        edPlayerService.enqueue( track2 );

        edPlayerService.dequeue();

        expect( dequeueSpy )
          .to.have.callCount( 1 );

        expect( edPlayerService.queue.length )
          .to.equal( 1 );

        edPlayerService.dequeue();
        edPlayerService.dequeue();

        dequeueSpy.restore();
      });
    });

    suite( "next method", function() {
      test( "plays next track in the queue", function() {
        var trackData = {
            name: "baby got back"
          },
          track = new EDTrack( trackData ),
          track2 = new EDTrack( trackData ),
          nextSpy = sinon.spy( edPlayerService, "next" );

        edPlayerService.enqueue( track );
        edPlayerService.enqueue( track2 );

        edPlayerService.next();

        expect( nextSpy )
          .to.have.callCount( 1 );

        expect( edPlayerService.currentStats.playing )
          .to.equal( track );

        edPlayerService.dequeue();
        edPlayerService.dequeue();

        nextSpy.restore();
      });
    });

    suite( "skipTo method", function() {
      test( "plays targeted track in the queue", function() {
        var trackData = {
            name: "baby got back"
          },
          track = new EDTrack( trackData ),
          track2 = new EDTrack( trackData ),
          skipToSpy = sinon.spy( edPlayerService, "skipTo" );

        edPlayerService.enqueue( track );
        edPlayerService.enqueue( track2 );

        edPlayerService.skipTo( 1 );

        expect( skipToSpy )
          .to.have.callCount( 1 );

        expect( edPlayerService.currentStats.playing )
          .to.equal( track2 );

        edPlayerService.dequeue();
        edPlayerService.dequeue();

        skipToSpy.restore();
      });
    });
  });
})( window, document, window.System, window.sinon, window.chai.expect );
