/*eslint strict: [ 2, "global"], no-sync: 0*/
"use strict";
var koa = require( "koa" ),
  send = require( "koa-send" ),
  fs = require( "fs" ),
  config = require( "../config.paths.js" ),
  createServer,
  serve;

/* jshint -W071 */
serve = function( root, fallback ) {
  return function* ( next ) {
    var stat;

    try {
      stat = fs.statSync( root + this.path );

      if ( stat.isDirectory() ) {
        throw new Error( "Won't serve directory" );
      }
      yield send( this, root + this.path );
    } catch ( e ) {
      console.log( "falling back for: " + this.path );
      yield send( this, fallback );
    }
  };
};

createServer = function( rootDir, fallbackFile, port ) {
  var server = koa();
  server.use( serve( rootDir, fallbackFile ) );
  return server.listen( port );
};

module.exports = {
  startDev: function() {
    return createServer( config.dev, config.server.fallback.dev, config.server.ports.dev );
  },
  startProd: function() {
    return createServer( config.prod, config.server.fallback.prod, config.server.ports.prod );
  }
};
