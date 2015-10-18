/*jshint strict: false*/

var transactionSymbol = Symbol( "transaction" ); // jshint ignore:line

export default class PDBTransaction {
  constructor( idbTransaction ) {
    Object.defineProperty( this, transactionSymbol, {
      configurable: false,
      enumerable: false,
      writeable: false,
      value: idbTransaction
    });
  }

  get mode() {
    return this[ transactionSymbol ].mode;
  }

  objectStore( storeName ) {
    return this[ transactionSymbol ].objectStore( storeName );
  }

  abort() {
    return this[ transactionSymbol ].abort();
  }

  promise( request ) {
    return new Promise(( resolve, reject ) => {
      this[ transactionSymbol ].oncomplete = function() {
        resolve( request.result );
      };

      this[ transactionSymbol ].onerror = function( event ) {
        reject( event );
      };
      //    request.onsuccess = function() {};
      //    request.onerror = function() {};
    });
  }
}
