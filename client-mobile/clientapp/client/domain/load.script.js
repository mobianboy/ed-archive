(function() {
  window.edLoadScriptPromise =
    System.import( "domain/ed/services/ed-user-service" )
      .then(function( imported ) {
        return imported.default.restoreSession();
      })
      .then(function( edProfile ) {
        console.log( "Session Restored!" );
        return edProfile;
      })
      .catch(function( error ) {
        console.warn( "Issue trying to restore session: " + error.message );

        if ( !(/could not restore session/gi).test( error.message )) {
          console.error( error.stack );
        }
      });
})();
