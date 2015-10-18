"use strict";
var gulp = require( "gulp" ),
  gutil = require( "gulp-util" ),
  config = require( "../config.paths.js" ),
  livereload = require( "livereload" ),
  servers = require( "../server/static.js" ),
  livereloadOptions;

// Options
livereloadOptions = {
  // default live reload port is 35729
  port: 35729,
  applyJSLive: false,
  applyCSSLive: false
//  exts: [ "html", "js", "css" ]
};

// Start dev server and live-reload server
gulp.task( "server:dev", function( done ) {
  var lrserver = livereload.createServer( livereloadOptions );

  // starts koa server
  servers.startDev();
  lrserver.watch( config.server.watch );

  gutil.log( "dev server started on " + config.server.ports.dev );
  done();
});

// Start prod server
gulp.task( "server:prod", function( done ) {
  // starts koa server
  servers.startProd();
  gutil.log( "prod server started on " + config.server.ports.prod );
  done();
});
