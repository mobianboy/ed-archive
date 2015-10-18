/*jshint strict: false*/

import define from "domain/ed/define-properties";
import createEvent from "domain/lib/event/create-event";

var
  readTransaction = function( pdb, storeName, indexName, fnName, args ) {
    var t = pdb.read( storeName );
    return t.promise( t.objectStore( storeName ).index( indexName )[ fnName ]( ...args ) );
  };
/*
  createAccessEvent = function( objectStore, indexName, operation, input, result ) {
    var [ key ] = input,
      value = result;

    return createEvent( "access", {
      detail: {
        objectStoreName: objectStore.name,
        key,
        value,
        indexName,
        operation,
        result
      }
    });
  };
*/

export default class PDBIndex {
  constructor( objectStore, indexName ) {
    var
      storeName = objectStore.name,
      idbIndex = objectStore.pdb.read( storeName ).objectStore( storeName ).index( indexName );

    Object.defineProperties( this, {
      objectStore: {
        configurable: false,
        enumerable: true,
        writeable: false,
        value: objectStore
      }
    });

    define.enumReadOnly( this, [ "name", "keyPath", "multiEntry", "unique" ], idbIndex );
  }

  get( ...args ) {
    return readTransaction(
      this.objectStore.pdb,
      this.objectStore.name,
      this.name,
      "get", args
    );
  }

  count( ...args ) {
    return readTransaction(
      this.objectStore.pdb,
      this.objectStore.name,
      this.name,
      "count", args
    );
  }

  getKey( ...args ) {
    return readTransaction(
      this.objectStore.pdb,
      this.objectStore.name,
      this.name,
      "getKey", args
    );
  }

  // TODO fix cursor stuff
  openCursor( ...args ) {
    return readTransaction(
      this.objectStore.pdb,
      this.objectStore.name,
      this.name,
      "openCursor",
      args
    );
  }

  openKeyCursor( ...args ) {
    return readTransaction(
      this.objectStore.pdb,
      this.objectStore.name,
      this.name,
      "openKeyCursor",
      args
    );
  }

  /* maybe future methods (according to mdn)
  getAll() {}
  getAllKeys() {}
  */
}
