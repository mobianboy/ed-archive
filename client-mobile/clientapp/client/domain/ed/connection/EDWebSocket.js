/*jshint strict: false*/

// 5 min -- 300000
// 2.5 min -- 150000
// 1.5 min -- 90000

var
  EDWebSocketTimeoutError,
  requestTimeout = 90000,
  isAuthenticated = Symbol( "isAuthenticated" ),
  token = 0,
  generateToken = function() {
    return ++token;
  },
  deAuthOnClose = function( self ) {
    return function( event ) {
      console.log( "socket was closed: %o, %o", event, self );
      self[ isAuthenticated ] = false;
    };
  };

import HealingWebSocket, { symbols } from "domain/lib/connection/HealingWebSocket";
import url from "domain/ed/urls";
import createEvent from "domain/lib/event/create-event";
import edUserService from "domain/ed/services/ed-user-service";
import EDJSONResponse from "domain/ed/connection/EDJSONResponse";

// Create a custom error class
// Can't extend builtins so we have to do it old school
EDWebSocketTimeoutError = function( message ) {
  this.message = message || "EDWebSocket request timed out";

  if ( "captureStackTrace" in Error ) {
    Error.captureStackTrace( this, EDWebSocketTimeoutError );
  } else {
    this.stack = ( new Error() ).stack;
  }
};

EDWebSocketTimeoutError.prototype = Object.create( Error.prototype );
EDWebSocketTimeoutError.prototype.name = "EDWebSocketTimeoutError";
EDWebSocketTimeoutError.prototype.constructor = EDWebSocketTimeoutError;

/**
 * @class EDWebSocket
 * @extends HealingWebSocket
 */
export default class EDWebSocket extends HealingWebSocket {
  constructor() {
    super( url.path );
    this[ isAuthenticated ] = false;

    this.on( "close", deAuthOnClose( this ));
  }

  /** @property isAuthenticated {boolean} */
  get isAuthenticated() {
    return this[ isAuthenticated ];
  }

  /**
   * @method authenticate
   * @param email {string} - user's email
   * @param password {string} - user's password
   * @returns {Promise} - resolves to the websocket message event
   */
  authenticate( email, password ) {
    var authBlock = {
      auth: {
        email,
        password
      }
    };

    // can now use this.request
    return this.request( authBlock )
      .then( event => {
        var response;

        try {
          // response = JSON.parse( event.data );
          response = new EDJSONResponse( event.data );
        } catch ( error ) {
          console.warn( "Error parsing auth json: " + error.message );
          throw error;
        }

        if ( response.hasStatus && response.hasStatusCode( 1 )) {
          this[ isAuthenticated ] = true;

          // authenticated event contains data block as event detail
          this.dispatch(
            createEvent( "authenticated", {
              detail: response.data
            })
          );
        } else {
          this[ isAuthenticated ] = false;
        }

        return event;
      });
  }

  /**
   * @method request
   * @param data {object} - the fully formed eardish formatted JSON object
   * @returns {Promise} - resolves to the parsed JSON response
   */
  request( data ) {
    var newToken;

    if (
      data instanceof ArrayBuffer ||
      data instanceof Blob ||
      data instanceof String ||
      typeof data === "string"
    ) {
      throw new TypeError( "EDWebSocket request function only accepts simple objects" );
    }

//    console.log( "request called with data: %o", data );

    if ( !data.hasOwnProperty( "action" ) ) {
      data.action = {};
    }

    newToken = generateToken();
    data.action.responseToken = newToken;

    return new Promise(( resolve, reject ) => {
      var
        timeoutId,
        handler = ( event ) => {
          var responseData;

          try {
            responseData = JSON.parse( event.data );
          } catch ( error ) {
            console.warn( "error in socket request handler " + error.message );
            console.error( error.stack );
            responseData = event;
          }

          if ( "meta" in responseData && responseData.meta.responseToken === newToken ) {
            resolve( event );
            window.clearTimeout( timeoutId );
            this.off( "message", handler );
          }
        }; // end handler function

      this.on( "message", handler );
      this.send( data );

      // Set a timeout to reject promise if request takes too long
      timeoutId = window.setTimeout(() => {
        console.warn( "timeout! %o", data );
        reject( new EDWebSocketTimeoutError( `Request to ${data.action.route} with response token ${ data.action.responseToken } timed out` ) );
        this.off( "message", handler );
      }, requestTimeout );
    });
  }

  /**
   * @private
   * @method Symbol(heal)
   * @param [data] {object} -- optional formatted eardish JSON request to send after healing
   */
  [ symbols.heal ]( data ) {
    console.log( "edSocket is being healed" );

    // Call authenticate before super so that re-auth request goes before
    // anything else does
    if ( edUserService.isOpenSession && edUserService.sessionAuthJSON != null ) {
      this.authenticate(
        edUserService.sessionAuthJSON.email,
        edUserService.sessionAuthJSON.password
      );
    }

    super[ symbols.heal ]( data );
  }

  /**
   * Overload EventEmitter.prototype.clear
   *   This reattaches the listener that de-auths the socket after
   *   All listeners are cleared
   * @param args
   * @returns {EventEmitter}
   */
  clear( ...args ) {
    var toReturn = super.clear( ...args );

    if ( args.length === 0 || args.some( eventName => "close" === eventName ) ) {
      this.on( "close", deAuthOnClose( this ));
    }

    return toReturn;
  }
}
