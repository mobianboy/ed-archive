/*eslint no-process-env:0*/

"use strict";
var gulp = require( "gulp" ),
  gutil = require( "gulp-util" ),
  plumber = require( "gulp-plumber" ),
  through = require( "through2" ),
  path = require( "path" ),
  defaults = require( "lodash" ).defaults,
  config = require( "../config.paths.js" ),
  traceur = require( "traceur" ),
  traceurOptions,
  compileES6,
  runCompileFromToWithOptions,
  isProduction = ( /^(PRODUCTION|QA)$/ ).test( process.env.GULP_ENVIRONMENT ),
  sourceMapRewriteLocation = process.env.ED_SOURCE_LOCATION;

// Compiler Options
traceurOptions = {
  modules: "instantiate",
  experimental: true
};

// Traceur compile in a stream!
/* Traceur node api is working! ...for now... */
compileES6 = function( options ) {
  return through.obj( function( file, enc, done ) {
    var es6, es5,
      opts = defaults( {}, options ),
      sourcePath = file.path,
      oldPath = file.path;

    if ( file.isNull() ) {
      this.push( file );
      return done();
    }

    gutil.log( "Source: " + gutil.colors.magenta( oldPath ) );

    // if in components folder, set to script mode
    // else set module name
    if ( config.traceur.compileAsScript.test( oldPath ) ) {
      gutil.log( "Compiling as " + gutil.colors.blue( "Script" ) );
      opts.script = true;
    } else {
      opts.moduleName = oldPath.replace( config.client + path.sep, "" ).replace( /\.js$/, "" );
      gutil.log( "Compiling as module with name: " + gutil.colors.magenta( opts.moduleName ) );
    }

    // Get ES6 File Content
    es6 = file.contents.toString( "utf8" );
    es5 = traceur.compile( es6, opts );

    // update source map url
    if ( typeof sourceMapRewriteLocation === "string" && sourcePath.indexOf( "vagrant" ) > -1 ) {
      sourcePath = sourcePath.replace(
        path.join( path.sep + "home", "vagrant" ),
        sourceMapRewriteLocation
      );
    }

    if ( isProduction ) {
      // remove source map
      es5 = es5.replace( /\/\/#.*[\n]?$/, "" );
    } else {
      es5 = es5.replace( "<compile-source>", "file://" + sourcePath );
    }

    // Update File Object
    file.contents = new Buffer( es5 );
    file.path = oldPath;

    this.push( file );
    done();
  });
};

runCompileFromToWithOptions = function( src, dest, opts ) {
  return gulp.src( src, {
    base: config.client
  })
    .pipe( plumber() )
    .pipe( compileES6( opts ) )
    .pipe( gulp.dest( dest ) );
};

/* dev task */
gulp.task( "traceur:dev", function() {
  gutil.log( "Traceur Compiling" );
  return runCompileFromToWithOptions( config.traceur.src, config.traceur.out.dev, traceurOptions );
});

/* watch task */
gulp.task( "traceur:watch:dev", function( done ) {
  gulp.watch( config.traceur.src )
    .on( "change", function( event ) {
      if ( event.type !== "deleted" ) {
        gutil.log( "Traceur Watch saw a change" );
        return runCompileFromToWithOptions( event.path, config.traceur.out.dev, traceurOptions );
      }
    });
  done();
});
gulp.task( "traceur:watch", gulp.series( "traceur:watch:dev" ) );

// TODO prod task
gulp.task( "traceur:prod", function() {
  return runCompileFromToWithOptions( config.traceur.src, config.traceur.out.prod, traceurOptions );
});
