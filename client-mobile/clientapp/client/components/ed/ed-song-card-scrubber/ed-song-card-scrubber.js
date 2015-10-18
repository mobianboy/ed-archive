( function( polymer ) {
  "use strict";

  Promise.all([
    System.import( "domain/ed/services/ed-player-service" ),
    System.import( "domain/lib/event/create-event" )
  ]).then(function( imported ) {
    var
      playerService = imported[ 0 ].default,
      createEvent = imported[ 1 ].default,
      scrubStartValue,
      createUpdateEvent = function( name, detail ) {
        detail = detail || {};
        detail.name = name;

        return createEvent( "scrubberUpdate", {
          detail: detail
        });
      },
      updateCenterHandler = function() {
        var top = window.pageYOffset;
        this.mouseDown = true;

        // determines center of svg, using top to compensate for scrolling
        this.svgBox = this.svg.getBoundingClientRect();
        this.scrubCenter = [
          this.svgBox.left + this.svgBox.width / 2,
          this.svgBox.top + top + this.svgBox.height / 2
        ];
      },
      onScrubStartHandler = function() {
        this.currentVal = this.value;
        scrubStartValue = this.value;
      },
      onScrubMoveHandler = function() {
        var angle,
          radians;

        if ( this.mouseDown ) {
          if ( event.touches ) {
            radians = Math.atan2( event.touches[0].pageX - this.scrubCenter[0], event.touches[0].pageY - this.scrubCenter[1] );
          } else {
            radians = Math.atan2( event.pageX - this.scrubCenter[0], event.pageY - this.scrubCenter[1] );
          }

          angle = radians * ( 180 / Math.PI ) * -1 + 90;
          this.currentVal = ( angle + 90 ) * this.max / 360;

          this.scrubber.style.webkitTransform = "rotate(" + angle + "deg)";
          this.scrubber.style.transform = "rotate(" + angle + "deg)";

          this.shadowScrubber.style.webkitTransform = "rotate(" + angle + "deg)";
          this.shadowScrubber.style.transform = "rotate(" + angle + "deg)";

          this.front.style["stroke-dashoffset"] = -1 * angle * this.circFront / 360 - this.circFront * 1.25 + "%";
          this.mid.style["stroke-dashoffset"] = -1 * angle * this.circMid / 360 - this.circMid * 1.25 + "%";

          this.dispatchEvent( createUpdateEvent( "scrubMove", { currentVal: this.currentVal }));
          this.handler.updateScrub();
        }
      },
      onScrubEndHandler = function() {
        this.mouseDown = false;

        if ( scrubStartValue && this.currentVal && scrubStartValue !== this.currentVal ) {
          playerService.scrubEnd( scrubStartValue, this.currentVal );
        }

        this.dispatchEvent( createUpdateEvent( "scrubEnd", { currentVal: this.currentVal }));
      },
      updateScrubHandler = function() {
        var degPercent = parseInt( this.value / this.max * 360, 10 ),
          rotation = "rotate(" + ( degPercent - 90 ) + "deg)";

        this.scrubber.style.webkitTransform = rotation;
        this.scrubber.style.transform = rotation;

        this.shadowScrubber.style.webkitTransform = rotation;
        this.shadowScrubber.style.transform = rotation;

        this.front.style[ "stroke-dashoffset" ] = -1 * ( degPercent - 90 ) * this.circFront / 360 - this.circFront * 1.25  + "%";
        this.mid.style[ "stroke-dashoffset" ] = -1 * ( degPercent - 90 ) * this.circMid / 360 - this.circMid * 1.25 + "%";

        this.timer.innerText = playerService.formattedDisplayTime;
      },
      skipSongHandler = function() {
        this.dispatchEvent( createUpdateEvent( "skip" ));
        this.isSkipping = true;
        this.handler.resetScrubber();
      },
      playerServiceEventHandler = function( event ) {
        var eventName = event.detail.type != null ? event.detail.type : this.playIcon.getAttribute( "name" );

        switch ( eventName ) {
          case "play":
            if ( event.type === "playerUpdate" && playerService.isPlaying ) {
              this.isSkipping = false;
            }
            this.playIcon.setAttribute( "name", "pause" );
            break;
          case "pause":
            this.playIcon.setAttribute( "name", "play" );
            break;
          default:
            break;
        }

        this.dispatchEvent( createUpdateEvent( eventName ));
      },
      initScrubberHandler = function() {
        // calculates the circumference of circles
        this.circFront = 2.01 * Math.PI * parseInt( this.front.getAttribute( "r" ), 10 );
        this.circMid = 2.01 * Math.PI * parseInt( this.mid.getAttribute( "r" ), 10 );

        this.front.style[ "stroke-dasharray" ] = this.circFront + "%";
        this.front.style[ "stroke-dashoffset" ] = this.circFront + "%";
        this.mid.style[ "stroke-dasharray" ] = this.circMid + "%";
        this.mid.style[ "stroke-dashoffset" ] = this.circMid + "%";
      },
      resetScrubberHandler = function() {
        this.value = 0;
        this.timer.innerText = "00:00/00:00";
        this.handler.updateScrub();
      },
      enableScrubberHandler = function() {
        this.complete = false;
        this.disabled = false;
        this.removeAttribute( "complete" );
        this.scrubber.style.opacity = 1;
        this.shadowScrubber.style.opacity = .4;
        this.playBtn.disabled = false;
        this.playIcon.style.opacity = 1;
      },
      mouseOutHandler = function() {
        this.mouseDown = false;
      };

    polymer( "ed-song-card-scrubber", {
      complete: false,
      disabled: false,
      isSkipping: false,
      ready: function() {
        // selectors
        this.svg = this.shadowRoot.getElementById( "svg-circle" );
        this.mid = this.shadowRoot.getElementById( "mid-circle" );
        this.front = this.shadowRoot.getElementById( "front-circle" );
        this.scrubber = this.shadowRoot.getElementById( "circle-scrubber" );
        this.shadowScrubber = this.shadowRoot.getElementById( "shadow-scrubber" );
        this.playBtn = this.shadowRoot.getElementById( "play-btn" );
        this.playIcon = this.shadowRoot.getElementById( "play-icon" );
        this.skipBtn = this.shadowRoot.getElementById( "skip-btn" );
        this.timer = this.$[ "song-timer" ];

        // event handler
        this.handler = {
          initScrubber: initScrubberHandler.bind( this ),
          resetScrubber: resetScrubberHandler.bind( this ),
          updateCenter: updateCenterHandler.bind( this ),
          enableScrubber: enableScrubberHandler.bind( this ),
          scrubStart: onScrubStartHandler.bind( this ),
          scrubMove: onScrubMoveHandler.bind( this ),
          scrubEnd: onScrubEndHandler.bind( this ),
          updateScrub: updateScrubHandler.bind( this ),
          skipSong: skipSongHandler.bind( this ),
          playerServiceEvent: playerServiceEventHandler.bind( this ),
          mouseOut: mouseOutHandler.bind( this )
        };

        // init
        this.mouseDown = false;
      },
      attached: function() {
        // mouse events
        this.scrubber.addEventListener( "mousedown", this.handler.updateCenter );
        this.shadowScrubber.addEventListener( "mousedown", this.handler.updateCenter );
        this.skipBtn.addEventListener( "touchstart", this.handler.skipSong );
        this.addEventListener( "mouseup", this.handler.scrubStart );
        this.addEventListener( "mousemove", this.handler.scrubMove );
        this.addEventListener( "mouseout", this.handler.mouseOut );

        // touch events
        this.playBtn.addEventListener( "touchstart", this.handler.playerServiceEvent );
        this.scrubber.addEventListener( "touchstart", this.handler.updateCenter );
        this.shadowScrubber.addEventListener( "touchstart", this.handler.updateCenter );
        this.addEventListener( "touchstart", this.handler.scrubStart );
        this.addEventListener( "touchmove", this.handler.scrubMove );
        this.addEventListener( "touchend", this.handler.scrubEnd );

        // bind player updates through the service
        playerService.emitter.on( "playerUpdate", this.handler.playerServiceEvent );

        // init
        this.handler.initScrubber();
      },
      detached: function() {
        this.scrubber.removeEventListener( "mousedown", this.handler.updateCenter );
        this.shadowScrubber.removeEventListener( "mousedown", this.handler.updateCenter );
        this.skipBtn.removeEventListener( "touchstart", this.handler.skipSong );
        this.removeEventListener( "mouseup", this.handler.scrubStart );
        this.removeEventListener( "mousemove", this.handler.scrubMove );
        this.removeEventListener( "mouseout", this.handler.mouseOut );

        // touch events
        this.playBtn.removeEventListener( "touchstart", this.handler.playerServiceEvent );
        this.scrubber.removeEventListener( "touchstart", this.handler.updateCenter );
        this.shadowScrubber.removeEventListener( "touchstart", this.handler.updateCenter );
        this.removeEventListener( "touchstart", this.handler.scrubStart );
        this.removeEventListener( "touchmove", this.handler.scrubMove );
        this.removeEventListener( "touchend", this.handler.scrubEnd );

        playerService.emitter.off( "playerUpdate", this.handler.playerServiceEvent );
      },
      attributeChanged: function( attrName, oldVal, newVal ) {
        if ( attrName === "value" && !this.isSkipping ) {
          this.handler.updateScrub();
        }

        if ( this.value === this.max ) {
          if ( !this.complete && this.value > 0 ) {
            this.complete = true;
            this.dispatchEvent( createUpdateEvent( "songComplete" ));
          }
        } else {
          this.handler.enableScrubber();
        }

        if ( Math.floor( this.value ) > 29 ) {
          this.dispatchEvent( createUpdateEvent( "showRatings" ));
        }
      },
      disableScrubber: function() {
        this.disabled = true;
        this.setAttribute( "complete", "" );
        this.scrubber.style.opacity = 0;
        this.shadowScrubber.style.opacity = 0;
        this.playBtn.disabled = true;
        this.playIcon.style.opacity = 0.02;
      }
    });
  });
})( window.Polymer );
