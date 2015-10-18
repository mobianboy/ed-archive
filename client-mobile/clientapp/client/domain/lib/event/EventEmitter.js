/* jshint strict:false */
/*eslint no-inline-comments: 0*/
var handlerMap = Symbol( "handlerMap" ); // jshint ignore:line

/** @class EventEmitter */
export default class EventEmitter {
  constructor( eventNames ) {
    this[ handlerMap ] = {};

    if ( Array.isArray( eventNames )) {
      eventNames.forEach( name => this[ handlerMap ][ name ] = [] );
    }
  }

  /**
   * @method on
   * @param eventName { string }
   * @param handler { function }
   * @returns {EventEmitter}
   */
  on( eventName, handler ) {
    if ( Array.isArray( this[ handlerMap ][ eventName ] )) {
      this[ handlerMap ][ eventName ].push( handler );
    } else {
      this[ handlerMap ][ eventName ] = [ handler ];
    }

    return this;
  }

  /**
   * @method off
   * @param eventName { string }
   * @param handler { function }
   * @returns {EventEmitter}
   */
  off( eventName, handler ) {
    var handlerArray = this[ handlerMap ][ eventName ];

    if ( handlerArray && handlerArray.length > 0 ) {
      this[ handlerMap ][ eventName ] = handlerArray.filter( h => h !== handler );
    }

    return this;
  }

  /**
   * @method once
   * @param eventName { string }
   * @param handler { function }
   * @returns {EventEmitter}
   */
  once( eventName, handler ) {
    var toRemove = ( event, ...extraArgs ) => {
      this.off( eventName, toRemove );
      handler.call( this, event, ...extraArgs );
    };

    this.on( eventName, toRemove );
    return this;
  }

  /**
   * @method clear
   * @description clears all event handlers for type
   * @param eventName { string }
   * @returns {EventEmitter}
   */
  clear( eventName ) {
    if ( eventName ) {
      this[ handlerMap ][ eventName ] = [];
    } else {
      this[ handlerMap ] = {};
    }

    return this;
  }

  /**
   * @method dispatch
   * @param event { Event }
   * @param extraArgs { ...Array }
   * @returns {EventEmitter}
   */
  dispatch( event, ...extraArgs ) {
    if ( event == null || event.type == null ) {
      throw new TypeError( "Dispatch was not called with proper Event object" );
    }

    extraArgs.unshift( event );

    if ( Array.isArray( this[ handlerMap ][ event.type ] )) {
      this[ handlerMap ][ event.type ].forEach( h => {
        h.apply( this, extraArgs );
      });
    }

    return this;
  }

  /**
   * @static
   * @method bindToEventTarget
   * @param emitterInstance { EventEmitter }
   * @param eventTarget { EventTarget }
   * @param eventNames { Array<string> }
   * @param extraArgs { ...Array }
   */
  static bindToEventTarget( emitterInstance, eventTarget, eventNames, ...extraArgs ) {
    var listeners = eventNames.map( function() {
      return function( event ) {
        emitterInstance.dispatch( event, ...extraArgs );
      };
    });

    eventNames.forEach( function( name, index ) {
      eventTarget.addEventListener( name, listeners[ index ] );
    });

    eventTarget.unbindEventEmitter = function() {
      eventNames.forEach( function( name, index ) {
        eventTarget.removeEventListener( name, listeners[ index ] );
      });

      delete eventTarget.unbindEventEmitter;
    };
  }
}
