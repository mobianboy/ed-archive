"use strict";
var gulp = require( "gulp" ),
  path = require( "path" ),
  del = require( "del" ),
  through2 = require( "through2" ),
  config = require( "../config.paths.js" ),
  thisEnv,
  rewriteInPipe,
  runCopy,
  cleanUp;

switch ( process.env.GULP_ENVIRONMENT ) {
  case "DEVELOPMENT":
    thisEnv = "dev";
    break;
  case "PRODUCTION":
    thisEnv = "prod";
    break;
  case "QA":
    thisEnv = "qa";
    break;
  default:
    thisEnv = "dev";
    break;
}

// Rewrites the url
rewriteInPipe = function( toReplace ) {
  return through2.obj(function( file, enc, done ) {
    var pathObj = path.parse( file.path );

    // Rework them file paths!
    pathObj.dir = pathObj.dir.replace( path.sep + "urls", "" );
    pathObj.base = pathObj.base.replace( toReplace, "urls" );
    pathObj.name = "urls";

    file.path = path.relative( config.client, path.format( pathObj ) );

    this.push( file );
    done();
  });
};

// Copies the specific url file with the above rewrite function
runCopy = function( src, dest, env ) {
  return gulp.src( src, { base: config.client })
    .pipe( rewriteInPipe( env ) )
    .pipe( gulp.dest( dest ));
};

// rm -rf path
cleanUp = function( path ) {
  return function( done ) {
    del( path, done );
  };
};

// DEV TASKS
gulp.task( "envBuild:dev:copy", function() {
  var src = path.join( config.dev, config.envBuild.src[ thisEnv ] );
  return runCopy( src, config.dev, thisEnv );
});
gulp.task( "envBuild:dev:cleanup", cleanUp(
  path.join( config.dev, config.envBuild.remove[ thisEnv ] )
));

gulp.task( "envBuild:dev", gulp.series(
  "envBuild:dev:copy",
  "envBuild:dev:cleanup"
));
// TODO WATCH TASK

// PROD TASKS
gulp.task( "envBuild:prod:copy", function() {
  var src = path.join( config.prod, config.envBuild.src[ thisEnv ] );
  return runCopy( src, config.prod, thisEnv );
});
gulp.task( "envBuild:prod:cleanup", cleanUp(
  path.join( config.prod, config.envBuild.remove[ thisEnv ] )
));

gulp.task( "envBuild:prod", gulp.series(
  "envBuild:prod:copy",
  "envBuild:prod:cleanup"
));
