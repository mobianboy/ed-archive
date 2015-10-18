// jscs:disable requirePaddingNewLinesInObjects
"use strict";
var gulp = require( "gulp" ),
  gutil = require( "gulp-util" ),
  path = require( "path" ),
  through = require( "through2" ),
  symlink = require( "gulp-symlink" ),
  config = require( "../config.paths.js" ),
  runSymlink,
  runCopy,
  join,
  rewriteExt;

// Joins folder name with path basename, will also change .min.js to .js
// This is meant to be used when you have access to the file
join = function( folder, file ) {
  var basename = path.basename( file.path );

  if ( ( /\.min\.js$/ ).test( basename ) ) {
    gutil.log( "rewrite min: " + basename );
    basename = basename.replace( /\.min\.js$/, ".js" );
  }

  return path.join(
    folder,
    basename
  );
};

// Rewrites part of a path name matching the pattern with replace string
// This is meant to be used like a gulp plugin: .pipe(rewriteExt(p,r))
rewriteExt = function( pattern, replace ) {
  if ( typeof pattern === "string" ) {
    pattern = new RegExp( pattern );
  }

  return through.obj( function( file, enc, done ) {
    if ( file.path && pattern.test( file.path ) ) {
      file.path = file.path.replace( pattern, replace );
    }

    this.push( file );
    done();
  });
};

// For dev and tests tasks
runSymlink = function( src, dest ) {
  return gulp.src( src, { read: false })
    .pipe( symlink( function( file ) {
      // rename file, fix extension
      return join( dest, file );
    }) );
};

// for prod task
runCopy = function( src, dest ) {
  return gulp.src( src )
    .pipe( rewriteExt( /\.min\.js$/, ".js" ) )
    .pipe( gulp.dest( dest ) );
};

gulp.task( "vendor:dev", function() {
  return runSymlink( config.vendor.src, config.vendor.out.dev );
});

gulp.task( "vendor:tests", function( ) {
  return runSymlink( config.vendor.tests, config.vendor.out.dev );
//  return runCopy( config.vendor.tests, config.vendor.out.dev );
});

gulp.task( "vendor:prod", function() {
  return runCopy( config.vendor.min, config.vendor.out.prod );
});
