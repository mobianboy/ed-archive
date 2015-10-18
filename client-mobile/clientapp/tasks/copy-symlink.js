// jscs:disable requirePaddingNewLinesInObjects
"use strict";
var gulp = require( "gulp" ),
  symlink = require( "gulp-symlink" ),
  config = require( "../config.paths.js" ),
  runSymlink,
  runCopy;

runCopy = function( src, dest ) {
  return gulp.src( src, { base: config.client })
    .pipe( gulp.dest( dest ) );
};

runSymlink = function( src, toReplace, replaceWith ) {
  return gulp.src( src, { read: false } )
    .pipe( symlink( function( file ) {
      return file.path.replace( toReplace, replaceWith );
    }));
};

// Symlink files that don't need to be compiled into the build/www folder
gulp.task( "symlink:dev", function() {
  return runSymlink( config.symlink.src, config.client, config.dev );
});

// Symlink component test files
gulp.task( "symlink:tests:components", function() {
  return runSymlink( config.symlink.tests.component, config.client, config.dev );
});

// Symlink domain test files
gulp.task( "symlink:tests:domain", function() {
  return runSymlink( config.symlink.tests.domain, config.tests, config.dev );
});

gulp.task( "symlink:tests", gulp.parallel(
  "symlink:tests:components",
  "symlink:tests:domain"
));

// Copy files that don't need to be compiled into the build/prod folder
gulp.task( "copy:prod", function() {
  return runCopy( config.symlink.prod, config.prod );
});
