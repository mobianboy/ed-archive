/*jshint strict: false */
/*eslint vars-on-top: 0*/
import EDModel from "domain/ed/objects/EDModel";
import ObservableLRUCache from "domain/lib/storage/ObservableLRUCache";

var checkKeyType;

// helpers
checkKeyType = function( tmpKey ) {
  return tmpKey instanceof EDModel ? tmpKey.id : tmpKey;
};

export default class EDLRUCache extends ObservableLRUCache {
  constructor( limit ) {
    super( limit );
  }

  /**
   *
   * @param key -- the key the callback is attached to
   * @param data
   * @returns {*}
   *
   * @description
   *   Checks if key passed is an instance of EDModel and if so,
   *   assigns key to the id of that instance and data to that key obj. It then
   *   calls set on the super class.
   */
  set( key, data ) {
    // TODO THROW/TYPE CHECK?
    if ( data == null && key instanceof EDModel ) {
      data = key;
      key = key.id;
    } else if ( key.id != null ) {
      data = key;
      key = key.id;
    }

    return super.set( key, data );
  }

  /**
   *
   * @param key
   * @returns {*}
   *
   * @description
   *    Checks to see if key is an instance of EDModel. It then
   *    removes that key from cache.
   */
  remove( key ) {
    key = checkKeyType( key );

    return super.remove( key );
  }
}
