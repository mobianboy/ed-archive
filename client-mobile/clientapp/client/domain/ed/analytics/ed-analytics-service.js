/*jshint strict:false*/
var
  edAnalyticsService,
  simData = {},
  lastKnownLocation = {
    lat: null,
    long: null
  },
  updateLocation = function() {
    if ( navigator.geolocation ) {
      navigator.geolocation.getCurrentPosition(function( pos ) {
        lastKnownLocation.lat = pos.coords.latitude;
        lastKnownLocation.long = pos.coords.longitude;
      });
    }
  },
  getSimCardInfo = function() {
    if ( window.plugins != null && window.plugins.sim ) {
      return window.plugins.sim.getSimInfo(
        ( sim ) => {
          simData.carrier = sim.carrierName;
        },
        ( error ) => {
          console.warn( "Error getting sim card info: " + error.message );
          console.error( error.stack );
        }
      );
    }
  };

// Services
import edConnectionService from "domain/ed/services/ed-connection-service";
import edUserService from "domain/ed/services/ed-user-service";
import edPlayerService from "domain/ed/services/ed-player-service";

// Analytics Stuff
import eventMap from "domain/ed/analytics/event-map";
import EDAnalyticsEvent from "domain/ed/analytics/events/EDAnalyticsEvent";

// try to get geo location data
updateLocation();

// get sim card data
getSimCardInfo();

// Route request event attached below...

/**
 * @module ed-analytics-service
 */
export default edAnalyticsService = {
  get commonBlock() {
    return {
      device: this.deviceBlock,
      clientVersion: this.version,
      location: this.locationBlock,
      time: this.time,
      user: edUserService.currentUserId,
      profile: edUserService.isOpenSession ? edUserService.currentProfile.id : null,
      viewRoute: window.location.pathname + window.location.hash,
      viewState: this.viewStateBlock,
      session: this.sessionBlock
    };
  },

  get deviceBlock() {
    var
      device = window.device,
      block = {
        type: window.navigator.userAgent,
        make: "",
        model: "",
        carrier: simData.carrier || "",
        OS: "",
        UUID: ""
      };

    if ( device ) {
      block.make = device.manufacturer;
      block.model = device.model;
      block.OS = device.platform + " " + device.version;
      block.UUID = device.uuid;
    }

    return block;
  },

  get viewStateBlock() {
    var currentPlayerStats = edPlayerService.currentStats;

    return {
      playerState: {
        trackId: currentPlayerStats.playing == null ? null : currentPlayerStats.playing.id,
        playing: edPlayerService.isPlaying,
        timecode: currentPlayerStats.time,
        queueLength: edPlayerService.queue.length
      }
    };
  },

  get sessionBlock() {
    // TODO double check the math for this
    return {
      duration: edUserService.sessionDuration
    };
  },

  get locationBlock() {
    updateLocation();
    return lastKnownLocation;
  },

  get version() {
    return "0.0.1";
  },

  get time() {
    return ( new Date() ).toISOString();
  },

  /**
   * @method send
   * @description
   *  This method as two signatures:
   *    If you send it an EDAnalyticsEvent instance that instance will be used
   *      to construct the "eventBlock" of this analytics server call.
   *
   *    If you send it a string and argument map the function will call
   *      createEvent and pass along the event creation arguments.
   */
  send( ...args ) {
    var event;

    // Catch errors to make analytic calls "safe"
    try {
      if ( args.length === 1 && args[0] instanceof EDAnalyticsEvent ) {
        event = args[ 0 ];
      } else {
        event = this.createEvent( ...args );
      }

      if ( edUserService.isOpenSession ) {

        edConnectionService.formattedSend({
          analytics: {
            common: this.commonBlock,
            event: event.eventBlock
          }
        });

      }
    } catch ( error ) {
      console.warn( "Suppressing exception on analytics call" );
      console.warn( "Original Message: %s", error.message );
      console.error( error.stack );
    }
  },

  /**
   * @method createEvent
   * @param { string } eventName -- the event name of the event to create
   * @param { object } eventArgs -- an "arg ball" of the properties for this event
   *
   * @returns { EDAnalyticsEvent }
   */
  createEvent( eventName, eventArgs ) {
    if ( !( eventName in eventMap ) ) {
      throw new TypeError(
        `No analytics event with name ${eventName} found in analytics event map.`
      );
    }

    if ( eventArgs == null ) {
      throw new TypeError(
        `Missing argument map for construction of ${eventName} analytics event`
      );
    }

    return new eventMap[ eventName ]( eventArgs );
  }
};
