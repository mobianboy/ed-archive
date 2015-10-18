/*eslint no-inline-comments: 0, consistent-this: 0*/
var
  socket = Symbol( "socket" ), // jshint ignore:line
  heal = Symbol( "heal" ), // jshint ignore:line
  socketEvents = [ "open", "close", "message", "error" ],
  createSocket;

import EventEmitter from "domain/lib/event/EventEmitter";
import createEvent from "domain/lib/event/create-event";

/**
 *
 * @param self { HealingWebSocket }
 * @param url { String }
 * @param protocols { Array<String> }
 * @returns { WebSocket }
 */
createSocket = function( self, url, protocols ) {
  var oldSocket = self[ socket ];

  self[ socket ] = protocols == null ?
    new WebSocket( url ) :
    new WebSocket( url, protocols );

  EventEmitter.bindToEventTarget( self, self[ socket ], socketEvents );

  return oldSocket;
};

export var symbols = {
  get heal() {
    return heal;
  },
  get socket() {
    return socket;
  }
};

export default class HealingWebSocket extends EventEmitter {
  constructor( url, protocols ) {
    super( [ "heal", ...socketEvents ] );
    createSocket( this, url, protocols );
  }

  get isOpen() {
    return this[ socket ].readyState === WebSocket.OPEN;
  }

  get readyState() {
    return this[ socket ].readyState;
  }

  get binaryType() {
    return this[ socket ].binaryType;
  }

  set binaryType( value ) {
    this[ socket ].binaryType = value;
    return value;
  }

  get bufferedAmount() {
    return this[ socket ].bufferedAmount;
  }

  get extensions() {
    return this[ socket ].extensions;
  }

  get protocol() {
    return this[ socket ].protocol;
  }

  get url() {
    return this[ socket ].url;
  }

  close( code=1000, reason="" ) {
    return this[ socket ].close( code, reason );
  }

  send( data ) {
    if ( !( data instanceof ArrayBuffer || data instanceof Blob || typeof data === "string" || data instanceof String ) ) {
      data = JSON.stringify( data );
    }

    if ( this.isOpen ) {
      this[ socket ].send( data );
    } else if ( this.readyState === WebSocket.CONNECTING ) {
      this.once( "open", ( event ) => {
        this.send( data );
      });
    } else {
      this[ heal ]( data );
    }
  }

  [ heal ]( data ) {
    var oldSocket = createSocket( this, this.url, this.protocol !== "" ? [ this.protocol ] : null ),
      healEvent = createEvent( "heal", {
        detail: {
          oldSocket
        }
      });

    this.dispatch( healEvent );

    if ( data ) {
      this.once( "open", event => {
        this.send( data );
      });
    }
  }
}
