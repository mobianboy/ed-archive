"use strict";
var gulp = require( "gulp" ),
  del = require( "del" ),
  config = require( "../config.paths.js" ),
  clean;

// rm -rf path
clean = function( path ) {
  return function( done ) {
    del( path, done );
  };
};

gulp.task( "clean:dev", clean( config.dev ) );
gulp.task( "clean:prod", clean( config.prod ) );
gulp.task( "clean:coverage", clean( config.coverage ) );

gulp.task( "clean:docs:components", clean( config.dgeni.components.outputFolder ) );

gulp.task( "clean", gulp.parallel( "clean:dev", "clean:prod", "clean:coverage" ) );

gulp.task( "clean:docs", gulp.series( "clean:docs:components" ) );
