
import EDWebSocket from "domain/ed/connection/EDWebSocket";
import checkRoute from "domain/ed/connection/route-auth-check";
import EDJSONResponse from "domain/ed/connection/EDJSONResponse";

var
  edConnectionService,
  lastRequest = Promise.resolve( true ),
  edSocket = new EDWebSocket(),
  parseSocketMessage = function( response ) {
    if ( typeof response.data === "string" ) {
      return new EDJSONResponse( response.data );
    }

    console.warn( "Parse Socket Message bypassed creating an EDJSONResponse object! %o", response );
    return response.data;
  },
  joinData = function( dataObject, jsonObject ) {
    return Object.keys( dataObject ).reduce(( prevJson, currKey ) => {
      prevJson[ currKey ] = dataObject[ currKey ];
      return prevJson;
    }, jsonObject );
  };

export default edConnectionService = {
  authenticateConnection( email, password ) {
    return edSocket.authenticate( email, password )
      .then( parseSocketMessage )
      .then( parsedResponse => {
        // return dataBlock ( profileId, userId, onboarded )
        return parsedResponse.data;
      }).catch( error => {
        console.warn( "Issue authenticating in connection service: " + error.message );
        console.error( error.stack );
        throw error;
      });
  },

  deauthenticateSocket() {
    edSocket.close( 4000 );
  },

  send( route, priority=10, data={} ) {
    var
      json = joinData( data, {
        action: {
          route,
          priority
        }
      });

    if ( checkRoute.needsAuth( route ) && !edSocket.isAuthenticated ) {
      edSocket.once( "authenticated", () => {
        this.formattedSend( json );
      });
      return;
    }

    this.formattedSend( json );
  },

  request( route, priority=10, data={} ) {
    var json = joinData( data, {
      action: {
        route,
        priority
      }
    });

    return new Promise(( resolve, reject ) => {
      if ( checkRoute.needsAuth( route ) && !edSocket.isAuthenticated ) {
        edSocket.once( "authenticated", () => {
          resolve( this.formattedRequest( json ));
        });

        return;
      }

      resolve( this.formattedRequest( json ));
    });
  },

  // TODO Remove transactional request hack
  //    reset formattedSend
  //    remove lastRequest stuffs
  // these two functions mainly used by analytics send requests
  formattedSend( data ) {
    return this.formattedRequest( data );
  },

  formattedRequest( data ) {
    var performNextRequest = function() {
      return edSocket.request( data ).then( parseSocketMessage );
    };

    return lastRequest = lastRequest.then( performNextRequest, performNextRequest );
  }
};

// TODO remove debug
window.EDJSONResponse = EDJSONResponse;
window.edSocket = edSocket;
window.edConnectionService = edConnectionService;
