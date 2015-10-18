/*jshint strict: false */

import EventEmitter from "domain/lib/event/EventEmitter";
import define from "domain/ed/define-properties";
import PDBObjectStore from "domain/lib/storage/PromisedDB/PDBObjectStore";
import PDBTransaction from "domain/lib/storage/PromisedDB/PDBTransaction";

var IDBKeyRange = window.IDBKeyRange || window.webkitIDBKeyRange || window.msIDBKeyRange,
  objectStoresFromConfigArray;

objectStoresFromConfigArray = function( configArray ) {
  var storeMap = {};

  if ( !Array.isArray( configArray ) ) {
    throw new TypeError( "Invalid argument to PDBDatabase constructor" );
  }

  configArray.forEach(function( versionConfig ) {
    Object.keys( versionConfig ).forEach(function( storeName ) {
      var indexes;

      if ( !( storeName in storeMap ) ) {
        storeMap[ storeName ] = Object.keys( versionConfig[ storeName ].indexes );
        return;
      }

      indexes = storeMap[ storeName ];

      Object.keys( versionConfig[ storeName ].indexes ).forEach(function( indexName ) {
        if ( indexes.some( existingIndexName => existingIndexName === indexName ) ) {
          // already exists
          console.log( "index: %s should already exist", indexName );
          return;
        }

        indexes.push( indexName );
      });

      storeMap[ storeName ] = indexes;
    });
  });

  return storeMap;
};

/**
 * @class PDBDatabase
 * @inherits EventEmitter
 * @property name {string}
 * @property version {number}
 * @property objectStoreNames {DOMStringList}
 * @property variable {property key} -- the name of each object store will be saved as a PDBObjectStore
 */
export default class PDBDatabase extends EventEmitter {
  /**
   * @private
   * @constructor PDBDatabase
   * @param idb {IDBDatabase}
   */
  constructor( idb, configArray ) {
    var objectStores = objectStoresFromConfigArray( configArray );

    super([ "change" ]);

    Object.defineProperties( this, {
      idb: {
        configurable: false,
        enumberable: false,
        writeable: false,
        value: idb
      },
      close: {
        configurable: false,
        enumberable: true,
        writeable: false,
        value: function() {
          return idb.close();
        }
      }
    });

    define.enumReadOnly( this, [ "name", "version" ], idb );
    define.enumReadOnlyDeep( this, [ "objectStoreNames" ], idb );

    Object.keys( objectStores ).forEach( storeName => {
      Object.defineProperty( this, storeName, {
        configurable: false,
        enumberable: true,
        writeable: false,
        value: new PDBObjectStore( this, storeName, objectStores[ storeName ] )
      });
    });
  }

  transaction( storeNames, accessMode ) {
    return new PDBTransaction( this.idb.transaction( storeNames, accessMode ) );
  }

  read( storeNames ) {
    return this.transaction( storeNames, "readonly" );
  }

  write( storeNames ) {
    return this.transaction( storeNames, "readwrite" );
  }

  bound( lower, upper, excludeLower=false, excludeUpper=false ) {
    return IDBKeyRange.bound( lower, upper, excludeLower, excludeUpper );
  }

  only( value ) {
    return IDBKeyRange.only( value );
  }

  lowerBound( bound, exclude=false ) {
    return IDBKeyRange.lowerBound( bound, exclude );
  }

  upperBound( bound, exclude=false ) {
    return IDBKeyRange.upperBound( bound, exclude );
  }
}
