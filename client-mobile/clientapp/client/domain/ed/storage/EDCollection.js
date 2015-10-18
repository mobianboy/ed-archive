/*jshint strict: false*/

import define from "domain/ed/define-properties";
import dataService from "domain/ed/services/ed-data-service";

var datalist = Symbol( "datalist" );

export default class EDCollection {
  constructor( type, ids ) {
    Object.defineProperties( this, {
      ids: {
        configurable: false,
        enumerable: false,
        writeable: false,
        value: ids
      },
      type: {
        configurable: false,
        enumerable: false,
        writeable: false,
        value: type
      }
    });

    this[ datalist ] = ids.slice( 0 );
  }

  get length() {
    return this.ids.length;
  }

  /**
   *
   * @method get
   * @param index { number }
   * @returns {*}
   */
  get( index ) {
    if ( index >= this.length ) {
      return Promise.reject( new RangeError( `Index: ${index} is out of bounds for EDCollection with length ${this.length}` ) );
    }

    if ( !( this[ datalist ][ index ] instanceof Promise ) ) {
      this[ datalist ][ index ] = dataService.getByTypeAndId( this.type, this.ids[ index ] );
    }

    return this[ datalist ][ index ];
  }

  /**
   *
   * @method getRange
   * @param indexFrom { number }
   * @param indexTo { number }
   * @param singlePromise { boolean } -- if true returns a single promise instead of an array of promises
   */
  getRange( indexFrom=0, indexTo=undefined, singlePromise=false ) {
    if ( singlePromise === true ) {
      return Promise.all( this.getRange( indexFrom, indexTo ) );
    }

    return this.ids.slice( indexFrom, indexTo ).map(( id, index ) => this.get( index ));
  }

  /**
   *
   * @method getAll
   * @param singlePromise { boolean } -- if true returns a single promise instead of an array of promises
   * @returns {*}
   */
  getAll( singlePromise=false ) {
    if ( singlePromise === true ) {
      return Promise.all( this.getRange() );
    }

    return this.getRange();
  }

  /**
   * @method getInSequence
   * @description
   *  Gets data from the data service sequentially. When one promise resolves
   *  a request for the next set of data is made, so on and so forth
   * @param indexFrom
   * @param indexTo
   * @param singlePromise {boolean} -- if true returns a single promise instead of an array of promises
   * @returns {*}
   */
  getInSequence( indexFrom=0, indexTo=undefined, singlePromise=false ) {
    var
      maxIndex = typeof indexTo === "number" ? indexTo : this.ids.length,
      nextThen = ( nextIndex ) => {
        // Returns a "then" function
        return ( data ) => {
          if ( nextIndex >= maxIndex ) {
            return data;
          }

          return this.get( nextIndex )
            .then( nextThen( nextIndex + 1 ));
        };
      };

    return this.get( indexFrom )
      .then( nextThen( indexFrom + 1 ) )
      .then( last => {
        // return the full array of promises
        return this.getRange( indexFrom, indexTo, singlePromise );
      });
  }

  /**
   *
   * @method [ Symbol.iterator ]
   * @param index { number }
   */
  * [ Symbol.iterator ]() {
    var index = 0,
      length = this.ids.length;

    while ( index < length ) {
      yield this.get( index );
      index += 1;
    }
  }
}
