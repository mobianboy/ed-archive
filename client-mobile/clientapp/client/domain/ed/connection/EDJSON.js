
var json = Symbol( "rawJSON" );

export var symbols = {
  json: json
};

// Blocks
// Server -> Client (EDJSONResponse)
//    status
//    meta
//    data
//    messages
//    followUp

// Client -> Server (EDJSONRequest)
//    action
//    auth
//    data
//    meta
//    analytics
//      common
//      event

export default class EDJSON {
  constructor( jsonBody={} ) {
    if ( typeof jsonBody === "string" ) {
      try {
        jsonBody = JSON.parse( jsonBody );
      } catch ( error ) {
        console.warn( "Error parsing json in EDJSON constructor: " + error.message );
        console.error( error.stack );
        jsonBody = {};
      }
    }

    this[ json ] = jsonBody;
  }

  hasBlocks( ...blockNames ) {
    return blockNames.every( blockName => blockName in this[ json ] && this[ json ][ blockName ] != null );
  }

  hasProperty( lookup ) {
    var lookUpArray;

    if ( typeof lookup !== "string" && !( lookup instanceof String )) {
      throw new TypeError( `First argument to hasProperty is not of type string: ${lookup}` );
    }

    lookUpArray = lookup.split( "." ).filter( prop => prop !== "" );

    return lookUpArray.reduce(( previous, current, index, array ) => {
      // console.log( "starting check args: %o", [ previous, current, index, array ]);
      if ( typeof previous === "boolean" ) {
        return previous;
      }

      if ( current in previous ) {
        // console.log( `${current} was in %o`, previous );
        if ( index + 1 === array.length ) {
          // console.log( "index: " + index + " array.length: " + array.length );
          return true;
        }

        // console.log( "returning previous[current]: %o", previous[ current ] );
        return previous[ current ];
      }

      return false;
    }, this[ json ] );
  }

  // possibly combine this functionality into "hasProperty"
  hasProperties( ...lookup ) {
    return lookup.map( toLookup => this.hasProperty( toLookup ) ).every( bool => bool === true );
  }

  get hasData() {
    return "data" in this[ json ] && this[ json ].data != null;
  }

  get data() {
    return this[ json ].data;
  }

  get hasMeta() {
    return "meta" in this[ json ] && this[ json ].meta != null;
  }

  get meta() {
    return this[ json ].meta;
  }

  toJSON() {
    return this[ json ];
  }
}
