( function( polymer, System ) {
  "use strict";

  Promise.all([
    System.import( "domain/ed/services/ed-discover-service" ),
    System.import( "domain/lib/event/create-event" )
  ]).then( function( imported ) {
    var
      intervalTime = 200,
      discoverService = imported[0].default,
      createEvent = imported[1].default,
      createUpdateEvent = function( name, detail ) {
        detail = detail || {};
        detail.name = name;

        return createEvent( "chartsUpdate", {
          detail: detail
        });
      },
      leftMoveHandler = function() {
        this.dispatchEvent( createUpdateEvent( "moveLeft" ));
      },
      rightMoveHandler = function() {
        this.dispatchEvent( createUpdateEvent( "moveRight" ));
      },
      requestArtists = function( trackList ) {
        console.log( "need to find artsits for: %o", trackList );
        var nextThen = function( nextIndex, currentTrack ) {
          return function( edArtist ) {
            currentTrack.artistName = edArtist.displayName;

            if ( nextIndex >= trackList.length ) {
              return edArtist;
            }

            return trackList[ nextIndex ].getArtist()
              .then( nextThen( nextIndex + 1, trackList[ nextIndex ] ) );
          };
        };

        if ( trackList.length === 0 ) {
          return;
        }

        trackList[ 0 ].getArtist()
          .then( nextThen( 1, trackList[ 0 ] ) );
      },
      updateCountdownHandler = function() {
        this.countdownLeft = this[ "chart-object" ].timeRemaining;
      };

    polymer( "ed-single-chart", {
      discoverService: discoverService,
      publish: {
        "chart-title": {
          reflect: true
        },
        "chart-subtitle": {
          reflect: true
        },
        "chart-badge": {
          reflect: true
        },
        "chart-name": {
          reflect: true
        },
        "default-copy": {
          reflect: true
        },
        "chart-object": {
          reflect: true
        }
      },
      get chartTitle() {
        return this[ "chart-title" ];
      },
      get chartSubtitle() {
        return this[ "chart-subtitle" ];
      },
      get chartBadge() {
        return this[ "chart-badge" ];
      },
      get chartIdentifier() {
        return this[ "chart-name" ];
      },
      get defaultCopy() {
        return this[ "default-copy" ];
      },
      get chartObject() {
        return this[ "chart-object" ];
      },
      set chartObject( value ) {
        if ( value.constructor.MODEL_TYPE !== "chart" ) {
          return this[ "chart-object" ];
        }

        this[ "chart-object" ] = value;
        this.noRankings = value.leaderboard.length;
        this.getLeaderBoard();

        this.intervalId = setInterval( this.handler.updateCountdown, intervalTime );

        return value;
      },
      getRankForId: function( id ) {
        if ( this.chartObject != null ) {
          return this.chartObject.getRankForId( id );
        }

        return -1;
      },
      ready: function() {
        this.moveLeft = this.$[ "move-left" ];
        this.moveRight = this.$[ "move-right" ];
      },
      attached: function() {
        if (( /fan$/ ).test( this.chartIdentifier )) {
          this.isFan = true;
          this.isTrack = false;
        } else if (( /track$/ ).test( this.chartIdentifier )) {
          this.isFan = false;
          this.isTrack = true;
        }

        this.handler = {
          leftMove: leftMoveHandler.bind( this ),
          rightMove: rightMoveHandler.bind( this ),
          updateCountdown: updateCountdownHandler.bind( this )
        };

        this.moveLeft.addEventListener( "touchstart", this.handler.leftMove );
        this.moveRight.addEventListener( "touchstart", this.handler.rightMove );

        // default values
        if ( this.noRankings == null ) {
          this.noRankings = true;
        }
      },
      detached: function() {
        this.moveLeft.removeEventListener( "touchstart", this.handler.leftMove );
        this.moveRight.removeEventListener( "touchstart", this.handler.rightMove );
      },
      getLeaderBoard: function() {
        console.log( this.chartObject.leaderboardCollection );

        if ( this.chartObject.leaderboard.length === 0 ) {
          console.log( "empty chart :(" );
          return;
        }

        this.chartObject
          .leaderboardCollection
            .getInSequence( 0, this.chartObject.leaderboard.length, true )
              .then(function( chartList ) {
                this.rankingsList = chartList;

                if ( this.isTrack ) {
                  requestArtists( chartList );
                }
              }.bind( this ))
              .catch(function( error ) {
                console.error( error.stack );
              });
      },
      attributeChanged: function( attrName, oldVal, newVal ) {}
    });
  });
})( window.Polymer, window.System );

// [ "completed-listens-fan", "most-tracks-rated-fan", "completed-listens-artist", "highest-rated-artist" ],
