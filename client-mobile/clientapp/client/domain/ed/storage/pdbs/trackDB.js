/*jshint strict: false*/
// jscs:disable requirePaddingNewLinesInObjects

import promisedDB from "domain/lib/storage/PromisedDB/promisedDB";

var
  openTrackDB,
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
          userId: [ "userId", { unique: false }],
          profileId: [ "profileId", { unique: false }]
        }
      }
    }
  ];

openTrackDB = function() {
  return promisedDB.open( "track", 1, dbConfig )
    .catch( onError );
};

onError = function( error ) {
  console.warn( "Error opening Track DB, removing and trying again" );
  console.error( error.stack );

  window.indexedDB.deleteDatabase( "track" );
  return openTrackDB();
};

export default openTrackDB();
