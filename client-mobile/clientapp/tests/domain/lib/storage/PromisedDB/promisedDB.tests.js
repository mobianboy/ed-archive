/*eslint-env mocha*/
/*jscs:disable requirePaddingNewLinesInObjects*/
/*global suite, test, console*/
(function( win, doc, System, sinon, expect ) {
  "use strict";

  /* TODOS
      Add versioned config
      Get Cursors working as generators
   */

  /***
   * PLEASE NOTE:
   *  This test suite is dynamically built based on the configurations
   *  for promisedDB. Tests are also written against dummy data found below,
   *  all tests should clean up after themselves if they complete successfully.
   */

  var
    fullPDBConfig,
    minimalPDBConfig,
    dummyProfiles,
    dummyTracks,
    insertProfile,
    insertTrack;

  // Fully formed config object for testing
  fullPDBConfig = [
    // Version 1
    {
      profile: {
        options: {
          keyPath: "id",
          autoIncrement: false
        },
        indexes: {
          type: [ "type", { unique: false, multiEntry: false }],
          userId: [ "userId", { unique: false, multiEntry: false }]
        }
      },
      track: {
        options: {
          keyPath: "id",
          autoIncrement: false
        },
        indexes: {
          type: [ "type", { unique: false, multiEntry: false }],
          profileId: [ "profileId", { unique: false, multiEntry: false }]
        }
      }
    },
    // Version 2
    {
      user: {
        options: {
          keyPath: "id",
          autoIncrement: false
        },
        indexes: {
          type: [ "type", { unique: false, multiEntry: false }]
        }
      }
    }
  ];

  // Minimally formed config object for testing
  minimalPDBConfig = [
    // Version 1
    {
      profile: {
        options: { keyPath: "id" },
        indexes: {
          type: [ "type" ],
          userId: [ "userId" ]
        }
      },
      track: {
        options: { keyPath: "id" },
        indexes: {
          type: [ "type" ],
          profileId: [ "profileId" ]
        }
      }
    },
    // Version 2
    {
      user: {
        options: { keyPath: "id" },
        indexes: {
          type: [ "type" ]
        }
      }
    }
  ];
  /*jscs:enable*/

  // Dummy profile data
  dummyProfiles = [
    {
      id: 0,
      name: "Hello World",
      type: "artist",
      userId: 0,
      genre: "Rock"
    },
    {
      id: 1,
      name: "Hello John",
      type: "artist",
      userId: 1,
      genre: "EDM"
    },
    {
      id: 2,
      name: "Bobby Boggs",
      type: "fan",
      userId: 2,
      genre: "Alt"
    },
    {
      id: 3,
      name: "Jimmy Johns",
      type: "fan",
      userId: 1,
      genre: "Pop"
    }
  ];

  insertProfile = {
    id: 4,
    name: "Jamie Smits",
    type: "artist",
    userId: 3,
    genre: "Dance"
  };

  // dummy track data
  dummyTracks = [
    {
      id: 0,
      name: "Hello Song",
      type: "track",
      profileId: 0,
      genre: "Rock"
    },
    {
      id: 1,
      name: "Hello Electro",
      type: "mix",
      profileId: 1,
      genre: "EDM"
    },
    {
      id: 2,
      name: "Hello Boggs",
      type: "track",
      profileId: 0,
      genre: "Alt"
    },
    {
      id: 3,
      name: "Goodbye",
      type: "demo",
      profileId: 1,
      genre: "Pop"
    }
  ];

  insertTrack = {
    id: 4,
    name: "Hey-oh",
    type: "demo",
    profileId: 4,
    genre: "Dance"
  };

  // Start Test Suites
  suite( "PromisedDB", function() {
    var promisedDB,
      PDBTransaction,
      PDBObjectStore,
      PDBIndex,
      configs = {
        fullPDBConfig: fullPDBConfig,
        minimalPDBConfig: minimalPDBConfig
      };

    this.timeout( 5000 );

    suiteSetup(function( done ) {
      indexedDB.deleteDatabase( "testing-db" );

      Promise.all([
        System.import( "domain/lib/storage/PromisedDB/promisedDB" ),
        System.import( "domain/lib/storage/PromisedDB/PDBDatabase" ),
        System.import( "domain/lib/storage/PromisedDB/PDBTransaction" ),
        System.import( "domain/lib/storage/PromisedDB/PDBObjectStore" ),
        System.import( "domain/lib/storage/PromisedDB/PDBIndex" )
      ])
        .then(function( imported ) {
          promisedDB = imported[ 0 ].default;
          PDBTransaction = imported[ 2 ].default;
          PDBObjectStore = imported[ 3 ].default;
          PDBIndex = imported[ 4 ].default;

          done();
        }, function( error ) {
          console.warn( "Could not import 'PromisedDB' for testing: ", error.message );
          console.error( error.stack );
          done( error );
        });
    });

    // Start Construction Tests
    Object.keys( configs ).forEach(function( configName ) {
      test( "construction with config object: " + configName, function( done ) {
        var dbOpenRequest,
          versionNumber = configs[ configName ].length;

        promisedDB.open( "testing-db", versionNumber, configs[ configName ] )
          .then(function( pdb ) {
            console.log( "testing-db constructed: %o", pdb );
            pdb.close();

            dbOpenRequest = indexedDB.open( "testing-db", versionNumber );
            dbOpenRequest.onsuccess = function( event ) {
              var idb = event.target.result,
                transaction = idb.transaction( [ "profile", "track" ], "readonly" ),
                profileStore,
                trackStore;

              expect( idb )
                .to.have.property( "objectStoreNames" );

              expect( idb.objectStoreNames )
                .to.have.property( "0" )
                .that.equals( "profile" );

              expect( idb.objectStoreNames )
                .to.have.property( "1" )
                .that.equals( "track" );

              profileStore = transaction.objectStore( "profile" );
              trackStore = transaction.objectStore( "track" );

              expect( profileStore )
                .to.be.an.instanceOf( IDBObjectStore );

              expect( trackStore )
                .to.be.an.instanceOf( IDBObjectStore );

              expect( profileStore )
                .to.have.property( "name", "profile" );

              expect( trackStore )
                .to.have.property( "name", "track" );

              expect( profileStore )
                .to.have.property( "autoIncrement", false );

              expect( trackStore )
                .to.have.property( "autoIncrement", false );

              expect( profileStore )
                .to.have.property( "keyPath", "id" );

              expect( trackStore )
                .to.have.property( "keyPath", "id" );

              expect( profileStore )
                .to.have.property( "indexNames" );

              expect( trackStore )
                .to.have.property( "indexNames" );

              expect( profileStore.indexNames )
                .to.have.property( "0", "type" );

              expect( profileStore.indexNames )
                .to.have.property( "1", "userId" );

              expect( trackStore.indexNames )
                .to.have.property( "0", "profileId" );

              expect( trackStore.indexNames )
                .to.have.property( "1", "type" );

//              console.dir( profileStore );
//              console.dir( trackStore );

              transaction.oncomplete = function() {
                idb.close();
                indexedDB.deleteDatabase( "testing-db" );
                done();
              };
            };
          });
      });
    });
    // END Construction Tests

    suite( "Own Properties & Symbols", function() {
      var pdb,
        config = fullPDBConfig,
        versionNumber = config.length,
        dbName = "testing-db";

      // Populate DB
      suiteSetup(function( done ) {
        var doneHelper = (function() {
          var count = 0;
          return function() {
            if ( ++count === 8 ) {
              done();
            }
          };
        })();

        promisedDB.open( dbName, versionNumber, config )
          .then(function( createdPDB ) {
            pdb = createdPDB;

            // Seed Profile Data
            dummyProfiles.forEach(function( entry ) {
              pdb.profile.add( entry ).then( doneHelper );
            });

            // Seed Track Data
            dummyTracks.forEach(function( entry ) {
              pdb.track.add( entry ).then( doneHelper );
            });
          });
      });

      // Destroy DB
      suiteTeardown(function() {
        pdb.close();
        promisedDB.deleteDatabase( pdb.name );
      });

      suite( "IndexedDB Style Properties", function() {
        test( "name", function() {
          expect( pdb )
            .to.have.property( "name" )
            .that.is.a( "string" )
            .and.equals( dbName );
        });

        test( "version", function() {
          expect( pdb )
            .to.have.property( "version" )
            .that.is.a( "number" )
            .and.equals( versionNumber );
        });

        test( "objectStoreNames", function() {
          expect( pdb )
            .to.have.property( "objectStoreNames" )
            .that.is.an.instanceOf( DOMStringList );

          expect( pdb.objectStoreNames )
            .to.have.property( 0 )
            .that.equals( "profile" );

          expect( pdb.objectStoreNames )
            .to.have.property( 1 )
            .that.equals( "track" );

          expect( pdb.objectStoreNames )
            .to.have.property( "length" )
            .that.equals( 3 );
        });
      });

      suite( "PromisedDB Methods", function() {
        test( "transaction", function() {
          var transaction;

          expect( pdb )
            .to.respondTo( "transaction" );

          transaction = pdb.transaction( "profile", "readonly" );

          expect( transaction )
            .to.be.an.instanceOf( PDBTransaction );

          expect( transaction )
            .to.have.property( "mode", "readonly" );
        });

        test( "read", function() {
          var transaction;

          expect( pdb )
            .to.respondTo( "read" );

          transaction = pdb.read( "profile" );

          expect( transaction )
            .to.have.property( "mode" )
            .that.equals( "readonly" );

          expect( transaction )
            .to.respondTo( "objectStore" );

          expect( transaction.objectStore( "profile" ))
            .to.have.property( "name" )
            .that.equals( "profile" );
        });

        test( "write", function() {
          var transaction;

          expect( pdb )
            .to.respondTo( "write" );

          transaction = pdb.write( "profile" );

          expect( transaction )
            .to.have.property( "mode" )
            .that.equals( "readwrite" );

          expect( transaction )
            .to.respondTo( "objectStore" );

          expect( transaction.objectStore( "profile" ))
            .to.have.property( "name" )
            .that.equals( "profile" );
        });

        test( "bound", function() {
          var keyRange = pdb.bound( 0, 2 );

          expect( keyRange )
            .to.be.instanceOf( IDBKeyRange );

          expect( keyRange )
            .to.have.property( "lower" )
            .that.equals( 0 );

          expect( keyRange )
            .to.have.property( "upper" )
            .that.equals( 2 );
        });

        test( "only", function() {
          var keyRange = pdb.only( 0 );

          expect( keyRange )
            .to.be.instanceOf( IDBKeyRange );
        });

        test( "lowerBound", function() {
          var keyRange = pdb.lowerBound( 0 );

          expect( keyRange )
            .to.be.instanceOf( IDBKeyRange );

          expect( keyRange )
            .to.have.property( "lower" )
            .that.equals( 0 );
        });

        test( "upperBound", function() {
          var keyRange = pdb.upperBound( 2 );

          expect( keyRange )
            .to.be.instanceOf( IDBKeyRange );

          expect( keyRange )
            .to.have.property( "upper" )
            .that.equals( 2 );
        });
      });

      suite( "PDBTransaction", function() {
        suite( "Properties", function() {
          test( "mode", function() {
            var transaction = pdb.transaction( "profile", "readonly" );

            expect( transaction )
              .to.have.property( "mode" )
              .that.equals( "readonly" );
          });

          test( "objectStore", function() {
            var transaction = pdb.transaction( "profile", "readonly" );

            expect( transaction )
              .to.respondTo( "objectStore" );

            expect( transaction.objectStore( "profile" ) )
              .to.be.an.instanceOf( IDBObjectStore );
          });

          test( "promise", function( done ) {
            var transaction = pdb.transaction( "profile", "readonly" ),
              request = transaction.objectStore( "profile" ).get( dummyProfiles[ 0 ].id );

            expect( transaction )
              .to.respondTo( "promise" );

            transaction.promise( request )
              .then(function( data ) {
                expect( data )
                  .to.deep.equal( dummyProfiles[ 0 ]);

                done();
              }).catch( done );
          });

          test( "abort", function() {
            expect( pdb.transaction( "profile", "readonly" ))
              .to.respondTo( "abort" );
          });
        });
      });

      config.forEach(function( versionConfig, versionNumber ) {
        versionNumber += 1;

        suite( "version number: " + versionNumber, function() {
          Object.keys( versionConfig ).forEach(function( storeName ) {
            suite( "objectStore: " + storeName, function() {
              test( "pdb has property objectStore: " + storeName, function() {
                expect( pdb )
                  .to.have.property( storeName )
                  .that.is.an.instanceof( PDBObjectStore );
              });

              suite( storeName + " has objectStore properties", function() {
                test( "indexNames", function() {
                  expect( pdb[ storeName ] )
                    .to.have.property( "indexNames" )
                    .that.is.an.instanceof( DOMStringList );
                });

                test( "keyPath", function() {
                  expect( pdb[ storeName ] )
                    .to.have.property( "keyPath" )
                    .that.is.a( "string" )
                    .and.equals( versionConfig[ storeName ].options.keyPath || "" );
                });

                test( "name", function() {
                  expect( pdb[ storeName ] )
                    .to.have.property( "name" )
                    .that.is.a( "string" )
                    .and.equals( storeName );
                });

                test( "autoIncrement", function() {
                  expect( pdb[ storeName ] )
                    .to.have.property( "autoIncrement" )
                    .that.equals( versionConfig[ storeName ].options.autoIncrement );
                });
              });

              suite( storeName + " has objectStore methods", function() {
                test( "add", function( done ) {
                  var toInsert = storeName === "profile" ? insertProfile : insertTrack;

                  expect( pdb[ storeName ] )
                    .to.respondTo( "add" );

                  pdb[ storeName ].add( toInsert )
                    .then(function( result ) {
                      expect( result )
                        .to.equal( toInsert.id );

                      done();
                    })
                    .catch( done );
                });

                test( "delete", function( done ) {
                  var toDelete = storeName === "profile" ? insertProfile : insertTrack;

                  expect( pdb[ storeName ] )
                    .to.respondTo( "delete" );

                  pdb[ storeName ].delete( toDelete.id )
                    .then(function( result ) {
                      expect( result )
                        .to.equal( undefined );

                      done();
                    })
                    .catch( done );
                });

                test( "clear", function( done ) {
                  var restoreData = storeName === "profile" ? dummyProfiles : dummyTracks;

                  expect( pdb[ storeName ] )
                    .to.respondTo( "clear" );

                  pdb[ storeName ].clear()
                    .then(function( data ) {
                      expect( data )
                        .to.equal( undefined );

                      // restore data
                      return Promise.all(
                        restoreData.map(function( toRestore ) {
                          return pdb[ storeName ].put( toRestore );
                        })
                      );
                    })
                    .then(function( ids ) {
                      expect( ids )
                        .to.deep.equal( restoreData.map(function( data ) { return data.id; }) );

                      // deep check for restored data
                      return Promise.all(
                        ids.map(function( id ) {
                          return pdb[ storeName ].get( id );
                        })
                      );
                    })
                    .then(function( dataList ) {
                      expect( dataList )
                        .to.deep.equal( restoreData );

                      done();
                    })
                    .catch( done );
                });

                test( "get", function( done ) {
                  var toGet = storeName === "profile" ? dummyProfiles[ 0 ] : dummyTracks[ 0 ];

                  expect( pdb[ storeName ] )
                    .to.respondTo( "get" );

                  pdb[ storeName ].get( toGet.id )
                    .then(function( data ) {
                      expect( data )
                        .to.deep.equal( toGet );

                      done();
                    }).catch( done );
                });

                test( "put", function( done ) {
                  var toPut,
                    original = storeName === "profile" ? dummyProfiles[ 0 ] : dummyTracks[ 0 ];

                  toPut = Object.assign({}, original );
                  toPut.genre = "New Genre";

                  expect( pdb[ storeName ] )
                    .to.respondTo( "put" );

                  // put new version with augmented genre
                  pdb[ storeName ].put( toPut )
                    .then(function( newId ) {
                      expect( newId )
                        .to.equal( toPut.id );

                      // only returns id so do get to see if data was commited
                      return pdb[ storeName ].get( newId );
                    })
                    .then(function( result ) {
                      expect( result )
                        .to.deep.equal( toPut );

                      expect( result )
                        .to.have.property( "genre" )
                        .that.equals( "New Genre" );

                      // reset data back to original
                      return pdb[ storeName ].put( original );
                    })
                    .then(function() {
                      done();
                    })
                    .catch( done );
                });

                // TODO Fix when returns generator
                test.skip( "openCursor", function( done ) {
                  expect( pdb[ storeName ] )
                    .to.respondTo( "openCursor" );

                  pdb[ storeName ].openCursor()
                    .then(function( cursor ) {
                      expect( cursor )
                        .to.be.instanceof( IDBCursor );

                      done();
                    }).catch( done );
                });

                test( "count", function( done ) {
                  var dataCount = storeName === "profile" ? dummyProfiles.length : dummyTracks.length;

                  expect( pdb[ storeName ] )
                    .to.respondTo( "count" );

                  pdb[ storeName ].count()
                    .then(function( count ) {
                      expect( count )
                        .to.equal( dataCount );

                      done();
                    }).catch( done );
                });

                test( "count with arguments", function( done ) {
                  var keyRange = pdb.bound( 0, 2 );

                  expect( pdb[ storeName ] )
                    .to.respondTo( "count" );

                  pdb[ storeName ].count( keyRange )
                    .then(function( count ) {
                      expect( count )
                        .to.equal( 3 );

                      done();
                    }).catch( done );
                });
              });

              suite( storeName + " has proper indexes", function() {
                Object.keys( versionConfig[ storeName ].indexes ).forEach(function( indexName ) {
                  suite( storeName + " index: " + indexName, function() {
                    test( "object store: " + storeName + " has index " + indexName, function() {
                      expect( pdb[ storeName ] )
                        .to.have.property( indexName )
                        .that.is.an.instanceOf( PDBIndex );
                    });

                    suite( indexName + " has index properties", function() {
                      test( "name", function() {
                        expect( pdb[ storeName ][ indexName ] )
                          .to.have.property( "name" )
                          .that.equals( indexName );
                      });

                      test( "keyPath", function() {
                        expect( pdb[ storeName ][ indexName ] )
                          .to.have.property( "keyPath" )
                          .that.equals( versionConfig[ storeName ].indexes[ indexName ][ 0 ] );
                      });

                      test( "multiEntry", function() {
                        expect( pdb[ storeName ][ indexName ] )
                          .to.have.property( "multiEntry" )
                          .that.equals(
                            versionConfig[ storeName ].indexes[ indexName ][ 1 ].multiEntry
                          );
                      });

                      test( "unique", function() {
                        expect( pdb[ storeName ][ indexName ] )
                          .to.have.property( "unique" )
                          .that.equals(
                            versionConfig[ storeName ].indexes[ indexName ][ 1 ].unique
                          );
                      });
                    });

                    // TODO CURSORS
                    suite( indexName + " has index methods", function() {
                      test( "count", function( done ) {
                        var indexCount =
                              storeName === "profile" ?
                                dummyProfiles.length :
                                dummyTracks.length;

                        expect( pdb[ storeName ][ indexName ] )
                          .to.respondTo( "count" );

                        pdb[ storeName ][ indexName ].count()
                          .then(function( count ) {
                            expect( count )
                              .to.be.a( "number" )
                              .and.equal( indexCount );

                            done();
                          })
                          .catch( done );
                      });

                      test( "count with arguments", function( done ) {
                        var
                          indexPath = versionConfig[ storeName ].indexes[ indexName ][ 0 ],
                          indexValue =
                            storeName === "profile" ?
                              dummyProfiles[ 0 ][ indexPath ] :
                              dummyTracks[ 0 ][ indexPath ],
                          indexCount =
                              storeName === "profile" ?
                                dummyProfiles.filter(function( data ) { return data[ indexPath ] === indexValue; }).length :
                                dummyTracks.filter(function( data ) { return data[ indexPath ] === indexValue; }).length;

                        expect( pdb[ storeName ][ indexName ] )
                          .to.respondTo( "count" );

                        pdb[ storeName ][ indexName ].count( indexValue )
                          .then(function( count ) {
                            expect( count )
                              .to.be.a( "number" )
                              .and.equal( indexCount );

                            done();
                          })
                          .catch( done );
                      });

                      test( "get", function( done ) {
                        var toGet = storeName === "profile" ? dummyProfiles[ 0 ] : dummyTracks[ 0 ],
                          indexPath = versionConfig[ storeName ].indexes[ indexName ][ 0 ];

                        expect( pdb[ storeName ][ indexName ] )
                          .to.respondTo( "get" );

                        pdb[ storeName ][ indexName ]
                          .get( toGet[ indexPath ] )
                            .then(function( data ) {
                              expect( data )
                                .to.deep.equal( toGet );

                              done();
                            })
                            .catch( done );
                      });

                      test( "getKey", function( done ) {
                        var toGet = storeName === "profile" ? dummyProfiles[ 0 ] : dummyTracks[ 0 ],
                          indexPath = versionConfig[ storeName ].indexes[ indexName ][ 0 ];

                        expect( pdb[ storeName ][ indexName ] )
                          .to.respondTo( "getKey" );

                        pdb[ storeName ][ indexName ]
                          .getKey( toGet[ indexPath ] )
                            .then(function( id ) {
                              expect( id )
                                .to.equal( toGet.id );

                              done();
                            })
                            .catch( done );
                      });

                      test.skip( "openCursor", function( done ) {
                        expect( pdb[ storeName ][ indexName ] )
                          .to.respondTo( "openCursor" );

                        // TODO ?
                        pdb[ storeName ][ indexName ].openCursor()
                          .then(function( data ) {
                            expect( data )
                              .to.be.instanceof( IDBCursorWithValue );

                            done();
                          })
                          .catch( done );
                      });

                      test.skip( "openKeyCursor", function( done ) {
                        expect( pdb[ storeName ][ indexName ] )
                          .to.respondTo( "openKeyCursor" );

                        // TODO ?
                        pdb[ storeName ][ indexName ].openKeyCursor()
                          .then(function( data ) {
                            expect( data )
                              .to.be.instanceof( IDBCursor );

                            done();
                          })
                          .catch( done );
                      });
                    }); // end index methods suite
                  }); // end storeName + indexName suite
                }); // end Object.keys( indexes )
              }); // end storeName has indexes suite
            }); // end object store suite
          }); // end versionConfig.keys.forEach
        }); // end version suite
      }); // end config.forEach
    });

    suite( "Methods", function() {});
  });
})( window, document, window.System, window.sinon, window.chai.expect );
