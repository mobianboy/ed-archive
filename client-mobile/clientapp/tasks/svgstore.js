"use strict";
var gulp = require( "gulp" ),
  svgstore = require( "gulp-svgstore" ),
  config = require( "../config.paths.js" ),
  compileSVGs;

compileSVGs = function( src, dest ) {
  return gulp.src( src )
    .pipe( svgstore() )
    .pipe( gulp.dest( dest ) );
};

gulp.task( "svgstore:dev", function() {
  return compileSVGs( config.svgstore.src, config.svgstore.out.dev );
});

gulp.task( "svgstore:prod", function() {
  return compileSVGs( config.svgstore.src, config.svgstore.out.prod );
});
