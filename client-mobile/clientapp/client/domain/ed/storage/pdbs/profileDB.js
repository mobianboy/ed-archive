/*jshint strict: false*/
// jscs:disable requirePaddingNewLinesInObjects

import promisedDB from "domain/lib/storage/PromisedDB/promisedDB";

var
  openProfileDB,
  onError,
  dbConfig = [
    // Version 1
    {
      objects: {
        options: {
          keyPath: "id"
        },
        indexes: {
          type: [ "type", { unique: false }],
          userId: [ "userId", { unique: false }]
        }
      }
    }
  ];

openProfileDB = function() {
  return promisedDB.open( "profile", 1, dbConfig )
    .catch( onError );
};

onError = function( error ) {
  console.warn( "Error opening Profile DB: " + error.message );
  console.error( error.stack );

  window.indexedDB.deleteDatabase( "profile" );
  return openProfileDB();
};

export default openProfileDB();
