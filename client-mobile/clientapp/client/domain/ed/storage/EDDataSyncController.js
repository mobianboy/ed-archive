/*jshint strict: false*/

/*
Observe Changes on the db then persist them to the lru
Change on db --> pass through transformer --> put into lru
 */

export default class EDDataSyncController {
  constructor( pdb, lru, transformer ) {
    pdb.on( "change", function( event ) {
      var data = event.detail.value;

      // TODO Debug remove
//      console.log( "pushing to lru %o, data %o", lru, event );
      lru.set( transformer( data ) );
    });
  }
}
