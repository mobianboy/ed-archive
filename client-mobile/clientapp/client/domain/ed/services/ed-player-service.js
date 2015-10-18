/*jshint strict: false*/

import EDTrack from "domain/ed/objects/media/EDTrack";
import EDCollection from "domain/ed/storage/EDCollection";

import edAnalyticsService from "domain/ed/analytics/ed-analytics-service";
import edDiscoverService from "domain/ed/services/ed-discover-service";
import edDataService from "domain/ed/services/ed-data-service";
import edUserService from "domain/ed/services/ed-user-service";

import EventEmitter from "domain/lib/event/EventEmitter";
import createEvent from "domain/lib/event/create-event";

var
  queue = [],
  currentArtist = null,
  currentTrack = null,
  currentIndex = 0,
  emitter = new EventEmitter([ "play", "pause", "stop", "skip" ]),
  audio = new Audio() || document.createElement( "audio" ),
  hasScrubbed = false,
  completedListens = null,
  ratedTracks = null,
  currentlyPlaying = false,
  edPlayerService,
  tracksCollection,
  hasScrubbedHandler,
  trackEndedHandler,
  rateCurrentlyPlaying,
  updateCurrentIndex,
  getTrackAndArtist,
  userService,
  importUserService,
  currentlyDiscovering;

// helpers
hasScrubbedHandler = function( event ) {
  if ( !hasScrubbed ) {
    hasScrubbed = true;
  }

  return hasScrubbed;
};

trackEndedHandler = function() {
  if ( !hasScrubbed ) {
    edAnalyticsService.send( "completedListen", {
      trackId: currentTrack.id
    });
  }

  hasScrubbed = false;
};

rateCurrentlyPlaying = function( number ) {
  if ( number != null && currentTrack ) {
    return currentTrack.rate( number )
      .then( response => {
        edAnalyticsService.send( "rate", {
          trackId: currentTrack.id,
          timecode: audio.currentTime,
          rating: number
        });

        return response;
      });
  }
};

updateCurrentIndex = function( newIndex ) {
  currentIndex = newIndex;
  return tracksCollection.getInSequence( newIndex, newIndex + 3 );
};

getTrackAndArtist = function( tracks, index ) {
  var trackObject;

  return tracks.get( index )
    .then( edTrack => {
      trackObject = edTrack;
      return edPlayerService.playTrack( edTrack );
    })
    .catch( error => {
      if ( error.name === "EDWebSocketTimeoutError" ) {
        console.log( "getting edTrack timed out!" );
      }
    })
    .then(() => {
      return edDataService.getArtistById( trackObject.profileId, 10 )
    })
    .then( edArtist => {
      currentArtist = edArtist;

      return edArtist;
    })
    .then(() => {
      return edPlayerService.emitter.dispatch( createEvent( "playerUpdate", {
        detail: {
          type: "artistUpdate"
        }
      }));
    })
    // TODO weird place to put this, but it wont work after the play method
    .then(() => {
      return edAnalyticsService.send( "play", {
        trackId: currentTrack.id,
        timecode: audio.currentTime
      });
    });
};

importUserService = function() {
  return System.import( "domain/ed/services/ed-user-service" )
    .then( imported => {
      userService = imported.default;

      userService.on( "edLogout", function() {
        if ( edPlayerService.isPlaying || edPlayerService.isPaused ) {
          edPlayerService.stop();
        }
      });

      return userService;
    });
};
// end helpers

// init audio element
audio.setAttribute( "id", "hiddenAudioPlayer" );
audio.setAttribute( "preload", "auto" );

audio.style.display = "none";
audio.style.visibility = "hidden";

audio.addEventListener( "seeked", hasScrubbedHandler );
audio.addEventListener( "ended", trackEndedHandler );
// end init audio element

importUserService();

export default edPlayerService = {
  get emitter() {
    return emitter;
  },

  get currentStats() {
    return {
      playing: currentTrack,
      isPlaying: currentlyPlaying,
      time: this.formattedTime,
      hours: this.currentHours,
      minutes: this.currentMinutes,
      seconds: this.currentSeconds,
      length: this.trackLength,
      currentArtist
    };
  },

  get userStats() {
    return {
      completedListens,
      ratedTracks
    };
  },

  get queue() {
    return queue;
  },

  get isPlaying() {
    return !audio.paused;
  },

  get isPaused() {
    return audio.paused && !!audio.src;
  },

  get isStopped() {
    return audio.paused && !audio.src;
  },

  get currentTime() {
    if ( this.isPlaying || this.isPaused ) {
      return audio.currentTime;
    }

    return 0;
  },

  get currentSeconds() {
    return Math.floor( this.currentTime % 60 );
  },

  get currentMinutes() {
    return Math.floor( this.currentTime / 60 );
  },

  get currentHours() {
    return Math.floor( 60 / this.currentTime );
  },

  get trackLength() {
    if ( currentTrack != null ) {
      return audio.duration;
    }

    return 0;
  },

  get formattedCurrentTime() {
    var ss = this.currentSeconds,
      mm = this.currentMinutes,
      hh = this.currentHours;
    ss = ss < 10 ? `0${ ss }` : ss;
    mm = mm < 10 ? `0${ mm }` : mm;
    hh = hh < 10 ? `0${ hh }` : hh;

    if ( this.isPlaying || this.isPaused ) {
      //if ( hh !== "00" ) {
      //  return `${ hh }:${ mm }:${ ss }`;
      //}
      return `${ mm }:${ ss }`;
    }

    return "00:00";
  },

  get formattedLength() {
    if ( !isNaN( this.trackLength ) && ( this.isPlaying || this.isPaused ) ) {
      return this.formatTime( this.trackLength );
    }

    return "00:00";
  },

  get formattedDisplayTime() {
    return this.formattedCurrentTime + " / " + this.formattedLength;
  },

  formatTime: function( time ) {
    var ss = Math.floor( time % 60 ),
      mm = Math.floor( time / 60 );
    ss = ss < 10 ? `0${ ss }` : ss;
    mm = mm < 10 ? `0${ mm }` : mm;

    return `${ mm }:${ ss }`;
  },

  playTrack: function( edTrack ) {
    if ( !edTrack instanceof EDTrack ) {
      throw new TypeError( "Track is not an EDTrack object" );
    }

    // playing same track toggles the play pause events
    if ( currentTrack != null && edTrack === currentTrack ) {
      if ( this.isPlaying ) {
        return this.pause();
      }

      if ( this.isPaused ) {
        return this.play();
      }
    }

    currentTrack = edTrack;

    return edTrack.getUrl()
      .then( response => {
        audio.src = response.data.url;
        audio.play();

        currentlyPlaying = true;

        return edTrack;
      })
      .catch( error => {
        if ( error.name === "EDWebSocketTimeoutError" ) {
          console.log( "getting track url timed out!" );
        }
      })
      .then(() => {
        return this.getCurrentUserStats();
      })
      .then(() => {
        return this.emitter.dispatch( createEvent( "playerUpdate", {
          detail: {
            type: "resetSongCard"
          }
        }));
      })
      .then(() => {
        return this.emitter.dispatch( createEvent( "playerUpdate", {
          detail: {
            type: "play"
          }
        }));
      });
  },

  play: function( content ) {
    if ( content == null && this.isPaused && !!audio.src ) {
      if ( this.isPaused ) {
        audio.play();

        this.emitter.dispatch( createEvent( "playerUpdate", {
          detail: {
            type: "play"
          }
        }));

        currentlyPlaying = true;
      } else {
        return this.pause();
      }
      return true;
    }

    // Do content type check, call specific playTrack method
    if ( content instanceof EDTrack ) {
      return this.playTrack( content );
    }
  },

  pause: function() {
    if ( this.isPlaying ) {
      audio.pause();

      this.emitter.dispatch( createEvent( "playerUpdate", {
        detail: {
          type: "pause"
        }
      }));

      currentlyPlaying = false;

      edAnalyticsService.send( "pause", {
        trackId: currentTrack.id,
        timecode: audio.currentTime
      });
    }

    return this.isPaused;
  },

  stop: function() {
    var trackId = currentTrack.id,
      timecode = audio.currentTime;

    if ( this.isPlaying || this.isPaused ) {
      audio.pause();
      audio.removeAttribute( "src" );
      currentTrack = null;
    }

    currentlyPlaying = false;

    edAnalyticsService.send( "quit", {
      trackId,
      timecode,
      action: "stop"
    });

    return true;
  },

  scrubTo: function( value ) {
    hasScrubbed = true;
    audio.currentTime = value;
    return audio.currentTime;
  },

  scrubEnd: function( start, end ) {
    if ( currentTrack ) {
      return edAnalyticsService.send( "scrub", {
        trackId: currentTrack.id,
        timeStart: start,
        timeEnd: end
      });
    }
  },

  enqueue: function( ids ) {
    tracksCollection = new EDCollection( EDTrack.MODEL_TYPE, tracksCollection.ids.concat( ids ));
  },

  enqueueNext: function( edTrack ) {
    return this.queue.unshift( edTrack );
  },

  dequeue: function() {
    return this.queue.shift();
  },

  skip: function() {
    var oldTrack = currentTrack,
      oldTrackCurrentTime = audio.currentTime;

    if ( this.isPlaying || this.isPaused ) {
      audio.pause();
    }

    currentIndex += 1;
    hasScrubbed = false;

    if ( currentIndex === tracksCollection.length - 2 ) {
      edDiscoverService.getDiscoverTrackList( currentlyDiscovering ).then( trackIds => {
        this.enqueue( trackIds );
      });
    }

    return getTrackAndArtist( tracksCollection, currentIndex )
      .then(() => {
        return updateCurrentIndex( currentIndex );
      })
      .then(() => {
        return edAnalyticsService.send( "quit", {
          trackId: oldTrack.id,
          timecode: oldTrackCurrentTime,
          action: "skip"
        });
      });
  },

  skipTo: function( index ) {
    var i, len = this.queue.length;

    if ( this.queue.length ) {
      for ( i = 0; i < len; i++ ) {
        if ( i === index ) {
          return this.play( this.queue[ i ] );
        }
      }
    }
  },

  rateTrack: function( number ) {
    return rateCurrentlyPlaying( number );
  },

  queueTracksAndPlay: function( tracks, show ) {
    this.enqueue( tracks );

    return getTrackAndArtist( tracksCollection, currentIndex )
      .then(() => {
        return updateCurrentIndex( currentIndex );
      })
      .then(() => {
        if ( show ) {
          return this.emitter.dispatch( createEvent( "playerUpdate", {
            detail: {
              type: "showMainPlayer"
            }
          }));
        }
      });
  },

  startMusicDiscovery: function( type ) {
    currentlyDiscovering = type;

    return edDiscoverService.getDiscoverTrackList( type )
      .then( trackIds => {
        tracksCollection = new EDCollection( EDTrack.MODEL_TYPE, trackIds );

        return this.queueTracksAndPlay( trackIds, true );
      })
      .catch( error => {
        console.warn( "Error getting tracks in player service" );
        console.error( error );
        throw error;
      });
  },

  getCurrentUserStats: function() {
    return edUserService.getStats()
      .then( stats => {
        completedListens = stats.completedListens;
        ratedTracks = stats.ratedTracks;

        return stats;
      });
  }
};

// TODO remove debug
window.playerService = edPlayerService;
window.edDiscoverService = edDiscoverService;
