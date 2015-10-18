
import define from "domain/ed/define-properties";
import EventEmitter from "domain/lib/event/EventEmitter";

export default class EDModel extends EventEmitter {
  static get MODEL_TYPE() {
    return "base";
  }

  constructor( args ) {
    if ( !( "id" in args ) ) {
      throw new TypeError( "EDModel id argument not found" );
    }

    if ( !( "type" in args ) ) {
      // TODO figure out if this should really always be required
      console.warn( "EDModel created without 'type' Property: %o", args );
      // throw new TypeError( "EDModel type argument not found" );
    }

    // call super to initialize EventEmitter properties
    super();

    define.enumReadOnly( this, [
      "id",
      "type"
    ], args );

//    define.enumReadOnlyDeep( this, [ "art" ], args );

    // TODO REMOVE WHEN STANDARDIZED
    // This is for catching any data that might slip through the cracks
    define.readOnlyDeep( this, [ "raw" ], {
      raw: args
    });
  }

  get modelType() {
    return this.constructor.MODEL_TYPE;
  }

/*
  // Not Currently Supported on any system :(
  get [ Symbol.toStringTag ]() {
    return "EDModel";
  }
*/
}
