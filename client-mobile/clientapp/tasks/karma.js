"use strict";
var gulp = require( "gulp" ),
  gutil = require( "gulp-util" ),
  karma = require( "karma" ).server,
  spawn = require( "child_process" ).spawn,
  paths = require( "../config.paths.js" );

gulp.task( "karma:once", function( done ) {
  karma.start({
    configFile: paths.karma.rc,
    singleRun: true
  }, function( exitCode ) {
    gutil.log( "Karma exited with code: " + exitCode );
    done( exitCode );
//    global.process.exit( exitCode );
  });
});

gulp.task( "tdd", function( done ) {
  karma.start({
    configFile: paths.karma.rc
  }, function( exitCode ) {
    gutil.log( "Karma exited with code: " + exitCode );
    done( exitCode );
    global.process.exit( exitCode );
  });
});

gulp.task( "tdd:alone", function( done ) {
  spawn( "npm", [ "run-script", "tdd" ], {
    cwd: paths.root,
    stdio: "inherit",
    detached: false
  }).on( "close", function( code ) {
    gutil.log( "Karma exited with code: " + code );
    done( code );
  });
});
