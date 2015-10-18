/*jscs:disable requirePaddingNewLinesInObjects */
"use strict";
// Read ./tests.html
// Find all files matching "client/components/**/*.test.html"
// Add <link> tag for each component test file
// Write out to build/tests/index.html

var gulp = require( "gulp" ),
//  gutil = require( "gulp-util" ),
  through = require( "through2" ),
  config = require( "../config.paths.js" ),
  path = require( "path" ),
  separator = path.sep,
  commentsToReplace = {
    component: "<!-- INCLUDE COMPONENT TEST FILES -->",
    domain: "<!-- INCLUDE DOMAIN TEST FILES -->",
    wrappers: "<!-- INCLUDE TEST WRAPPERS -->"
  },
  getTestFileList,
  pathToElementName,
  transformFileListToImports,
  transformFileListToTestWrappers,
  transformFileListToScripts,
  buildIndexPage;

// Return promise that revolves to an array of absolute file names
getTestFileList = function( glob, toReplace, replaceWith ) {
  return new Promise( function( resolve, reject ) {
    var files = [];
    // Gulp me the file names! But don't read the files
    gulp.src( glob, { read: false })
      .pipe(
        through({ objectMode: true },
          function( file, enc, done ) {
            // clean path to be relative to build folder
            files.push( file.path.replace( toReplace, replaceWith ) );
            done( null );
          },
          function( done ) {
            // return promise with full list
            resolve( files );
            done();
          }
        )
      ).on( "error", function( err ) {
        // if there was an error reject that promise!
        reject( err );
      });
  });
};

// get "element-name" from path via basename
pathToElementName = function( filePath, extension ) {
  extension = extension || ".tests.html";
  return path.basename( filePath, extension );
};

// Create an array of <link rel="import" /> elements for files in array
transformFileListToImports = function( files ) {
  return files.map( function( thisPath ) {
    return "<link" +
      " id=\"tests-import-" + pathToElementName( thisPath ) + "\"" +
      " rel=\"import\"" +
      " href=\"" + thisPath + "\" />";
  });
};

// Create an array of <div id="element-name-test-wrapper" class="hidden"></div>
// elements for files in the array, these get added for testing purposes
transformFileListToTestWrappers = function( files ) {
  return files.map( function( thisPath ) {
    return "<div" +
      " id=\"" + pathToElementName( thisPath ) + "-test-wrapper\"" +
      " class=\"hidden\"></div>";
  });
};

// Transform list of files to script tags
transformFileListToScripts = function( files ) {
  return files.map( function( thisPath ) {
    return "<script src=\"" +
        thisPath + "\"></script>";
  });
};

// Gulp Plugin style testing index page builder
buildIndexPage = function( replaceString, htmlTags ) {
  return through.obj( function( file, enc, done ) {
    file.contents = new Buffer(
      file.contents.toString( "utf8" ).replace( replaceString, htmlTags.join( "\n  " ) )
    );
    done( null, file );
  });
};

// build the tests.html page
gulp.task( "build:tests:index", function() {
  return Promise.all([
    getTestFileList( config.testing.client.html, config.client + separator, "" ),
    getTestFileList( config.testing.tests, config.tests + separator, "" )
  ]).then( function( files ) {
    return gulp.src( config.testing.index )
      .pipe( buildIndexPage(
        commentsToReplace.component,
        transformFileListToImports( files[ 0 ] )
      ))
      .pipe( buildIndexPage(
        commentsToReplace.wrappers,
        transformFileListToTestWrappers( files[ 0 ] )
      ))
      .pipe( buildIndexPage(
        commentsToReplace.domain,
        transformFileListToScripts( files[ 1 ] )
      ))
      .pipe( gulp.dest( config.dev ) );
  });
});
