/*jshint strict: false */
/*eslint vars-on-top: 0*/
import { default as LRUCache, symbols } from "domain/lib/storage/LRUCache";

var makeChangeObject, notifyOfChange,
  observers = Symbol( "observers" ),
  observeAll = Symbol( "observeAllObjects" ),
  { head, keyMap } = symbols;

/**
 * The observer change object follows the Object.observe change structure.
 * @typedef {object} Change
 * @property {string} name -- the key on which the change happened
 * @property {object} object -- the new value, in case of "remove" this will be null
 * @property {string} type -- the type of change, "set" or "remove"
 * @property {object} oldValue -- the old value that was stored at this key
 */

/**
 * @private
 * @param name -- the name of the property that changed, typically the key of the associated value
 * @param object {Object} -- the new value
 * @param type {string} -- the type of change
 * @param oldValue {Object} -- the old value
 * @returns {Object}
 */
makeChangeObject = function( name, object, type, oldValue ) {
  return {
    name,
    object,
    type,
    oldValue
  };
};

/**
 * @private
 * @param key -- the key on which the change happened
 * @param observers {Object} -- the observer key-->callback map
 * @param change {Object} -- the change object to pass to the observer callbacks
 */
notifyOfChange = function( key, observerMap, change ) {
  var toNotify = observerMap[ observeAll ];

  if ( key in observerMap ) {
    toNotify = toNotify.concat( observerMap[ key ] );
  }

  toNotify.forEach(function( cb ) {
    if ( cb.acceptList.some( value => value === change.type ) ) {
      cb([ change ]);
    }
  });
};

/*
  TODO remove observers?
  should observe fns get removed when item is dropped from cache?
  should there be a flag to set this option?
  default, remove observers with item
  also allow observers to live after their associated key has been removed?
*/

/**
 * @class ObservableLRUCache
 * @inheritdoc LRUCache
 */
export default class ObservableLRUCache extends LRUCache {
  /**
   * @constructor
   * @param limit { Number }
   */
  constructor( limit ) {
    super( limit );

    this[ observers ] = {
      [ observeAll ]: []
    };
  }

  static get OBSERVE_ALL() {
    return observeAll;
  }

  /**
   * @method observe
   * @param key -- the key to observe on
   * @param callback {observerCallback} -- observer function
   * @param acceptList { Array<string> } -- list of actions to observe, accepted: "set" and "remove"
   * @returns {undefined}
   *
   * @description
   *   Add observer callback for watching for changes to a specific key. Changes
   *   are only observed via set and remove operations, reordering an item in the
   *   internal linked list is not observable.
   */
  observe( key, callback, acceptList ) {
    callback.acceptList = Array.isArray( acceptList ) ? acceptList : [ "set", "remove" ];

    if ( key in this[ observers ] ) {
      this[ observers ][ key ].push( callback );
    } else {
      this[ observers ][ key ] = [ callback ];
    }
  }

  /**
   * @method unobserve
   * @param key -- the key the callback is attached to
   * @param callback {function} -- the observer function to remove
   * @returns {undefined}
   *
   * @description
   *   Removes a listener from the internal list of observers.
   */
  unobserve( key, callback ) {
    var tmpObserverList;

    if ( key in this[ observers ] ) {
      tmpObserverList = this[ observers ][ key ].filter( cb => cb !== callback );

      if ( tmpObserverList.length === 0 && key !== observeAll ) {
        delete this[ observers ][ key ];
      } else {
        this[ observers ][ key ] = tmpObserverList;
      }
    }
  }

  set( key, data ) {
    var returnValue,
      change = makeChangeObject( key, data, "set", this.peek( key ));

    returnValue = super.set( key, data );

    notifyOfChange( key, this[ observers ], change );

    return returnValue;
  }

  shift() {
    var change = makeChangeObject( this[ head ].key, null, "remove", super.shift());

    notifyOfChange( change.name, this[ observers ], change );

    return change.oldValue;
  }

  remove( key ) {
    var change = makeChangeObject( key, null, "remove", super.remove( key ));

    notifyOfChange( key, this[ observers ], change );

    return change.oldValue;
  }

  clear() {
    var oldKeyMap = this[ keyMap ];

    super.clear();

    for ( let key in oldKeyMap ) {
      if ( oldKeyMap.hasOwnProperty( key ) ) {
        notifyOfChange(
          key,
          this[ observers ],
          makeChangeObject(
            key,
            null,
            "remove",
            oldKeyMap[ key ].data
          )
        );
      }
    }
  }

  /*
  get [ Symbol.toStringTag ]() {
    return "ObservableLRUCache";
  }
  */
}

/**
 * @callback observerCallback
 * @param { Array<Change> } changes -- the list of changes that happened
 */
