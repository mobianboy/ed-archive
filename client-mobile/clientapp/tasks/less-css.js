"use strict";
var gulp = require( "gulp" ),
  gutil = require( "gulp-util" ),
  plumber = require( "gulp-plumber" ),
  path = require( "path" ),
  through = require( "through2" ),
  config = require( "../config.paths.js" ),
  defaults = require( "lodash" ).defaults,
  less = require( "less" ),
  lessOptions,
  transformDirName,
  runCompile,
//  runWatchCompile, // TODO?
  formatLessError,
  myLessCompile;

// Options
lessOptions = {
  ieCompat: false,
  compress: false,
  strictMath: true,
//  silent: true,
  paths: config.less.includePaths
};

transformDirName = function( src, srcDir, destDir ) {
  return path.dirname( src.replace( srcDir, destDir ) );
};

formatLessError = function( error ) {
  return [
    gutil.colors.bgRed.black( " LESS " + error.type + " Error: " + error.message + " " ),
    "In file: " + gutil.colors.magenta( error.filename ),
    "At line: " + gutil.colors.cyan( error.line + ":" + error.column ),
    "What it didn't like: " + gutil.colors.red.bold( error.extract )
  ].join( "\n" );
};

/* jshint -W071 */
myLessCompile = function( options ) {
  return through.obj( function( file, enc, done ) {
    var str, opts;

    if ( file.isNull() ) {
      return done( null, file );
    }

    str = file.contents.toString( "utf8" );
    opts = defaults( {}, options );

    opts.filename = file.path;
    opts.sourceMap = file.sourceMap ? true : false;

    less.render( str, opts )
      .then( function( output ) {
        file.contents = new Buffer( output.css );
        file.path = gutil.replaceExtension( file.path, ".css" );

        if ( file.sourceMap ) {
          // TODO: add source map stuff?
          gutil.log( gutil.colors.blue.bgRed( "LESS Source mapping not implemented" ) );
        }

        done( null, file );
      })
      .catch( function( err ) {
        err.lineNumber = err.line;
        err.fileName = err.filename;

        gutil.log( formatLessError( err ) );
        done( new gutil.PluginError( "LESS Compiler", err ) );
      });
  });
};

runCompile = function( src, dest, opts ) {
  return gulp.src( src )
    .pipe( plumber() )
    .pipe( myLessCompile( opts ) )
    .pipe( gulp.dest( dest ) );
};

// runWatchCompile = function( src, dest, task, opts ) {
//  // TODO abstract watch task?
// };

// COMPILE DEV
gulp.task( "less:dev", function() {
  return runCompile( config.less.compile, config.less.out.dev, lessOptions );
});

// WATCH LESS COMPILE TO DEV
gulp.task( "less:watch:dev", function( done ) {
  // regular watch included less, recompile all
  gulp.watch( config.less.included, gulp.series( "less:dev" ));

  // watch all non-include and compile per file
  gulp.watch( config.less.compile )
    .on( "change", function( event ) {
      var destinationPath;

      if ( event.type !== "deleted" ) {
        destinationPath = transformDirName( event.path, config.client, config.dev );
        gutil.log( "LESS saw a change at: " + gutil.colors.magenta( event.path ) );
        return runCompile( event.path, destinationPath, lessOptions );
      }
    });
  done();
});
gulp.task( "less:watch", gulp.series( "less:watch:dev" ) );

// COMPILE FOR PRODUCTION
// TODO PRODUCTION OPTIMIZATIONS
gulp.task( "less:prod", function() {
  var opts = defaults( {}, lessOptions );
  opts.compress = true;
  return runCompile( config.less.compile, config.less.out.prod, opts );
});

gulp.task( "less", gulp.series( "less:dev" ) );
