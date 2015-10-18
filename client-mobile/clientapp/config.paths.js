/*global __dirname*/
/*eslint strict:0, key-spacing:0*/
// jscs:disable maximumLineLength
"use strict";
/***
 * This file contains paths for all the different files in the codebase.
 * Everything is absolute path'd starting from the __dirname of this file, which should
 * be at the root of the project directory
 ***/

var path = require( "path" ),
  join = path.join,
  paths = {
    root:             __dirname,
    client:           join( __dirname, "client" ),
    tests:            join( __dirname, "tests" ),
    build:            join( __dirname, "build" ),
    dgeni:            join( __dirname, "dgeni" ),
    docs:             join( __dirname, "docs" ),
    logs:             join( __dirname, "logs" ),
    tasks:            join( __dirname, "tasks" ),
    server:           join( __dirname, "server" ),
    bowerComponents:  join( __dirname, "bower_components" ),
    nodeModules:      join( __dirname, "node_modules" )
  };

paths.dev = join( paths.build, "www" );
paths.prod = join( paths.build, "prod" );
paths.coverage = join( paths.build, "coverage" );

/*** VENDOR SCRIPTS ***/
paths.vendor = {
  out: {
    dev: join( paths.dev, "vendor" ),
    prod: join( paths.prod, "vendor" )
  },
  tests: [
    // in node_modules
    join( "mocha", "mocha.css" ),
    join( "mocha", "mocha.js" ),
    join( "chai", "chai.js" ),
    join( "sinon", "pkg", "sinon.js" ),
    join( "chai-as-promised", "lib", "chai-as-promised.js" ),
    join( "sinon-chai", "lib", "sinon-chai.js" )
  ].map( p => join( paths.nodeModules, p ) ),
  src: [
    join( paths.bowerComponents, "webcomponentsjs", "webcomponents.js" ),
    join( paths.bowerComponents, "polymer", "polymer.js" ),
    join( paths.nodeModules, "traceur", "bin", "traceur-runtime.js" ),
    join( paths.bowerComponents, "es6-module-loader", "dist", "es6-module-loader-sans-promises.js" ),
//    join( paths.bowerComponents, "es6-module-loader", "dist", "es6-module-loader.src.js" ),
    join( paths.bowerComponents, "es6-module-loader", "dist", "es6-module-loader-sans-promises.js.map" ),
    join( paths.bowerComponents, "system.js", "dist", "system.js" ),
//    join( paths.bowerComponents, "system.js", "dist", "system.src.js" ),
    join( paths.bowerComponents, "system.js", "dist", "system.js.map" ),
//    join( paths.bowerComponents, "director", "build", "director.js" ),
    join( paths.bowerComponents, "app-router", "app-router.html" ),
    join( paths.bowerComponents, "aws-sdk-js", "dist", "aws-sdk.js" ),
    join( paths.bowerComponents, "IndexedDBShim", "dist", "indexeddbshim.js" )
  ],
  minMap: {
    "webcomponents.js": "webcomponents.min.js",
    "polymer.js": "polymer.min.js",
    "aws-sdk.js": "aws-sdk.min.js"
//    "indexeddbshim": "indexeddbshim.min.js"
//    "director.js": "director.min.js"
  }
};
// Create min source paths from minMap and src
paths.vendor.min = paths.vendor.src.map( source => {
  var base = path.basename( source );

  if ( paths.vendor.minMap.hasOwnProperty( base ) ) {
    return source.replace( base, paths.vendor.minMap[base] );
  }
  return source;
});

/*** TODO COPY (FOR PROD) ***/

/*** SYMLINK ***/
paths.symlink = {
  src: [
    // link these
    join( paths.client, "**", "*.*" ),

    // don't link these
    // less, markdown, es6 js, test html,
    join( "!**", "*.less" ),
    join( "!**", "*.md" ),
    join( "!**", "*.es6.js" ),
    join( "!" + paths.client, "domain", "**", "*.js" ),
    join( "!**", "*.tests.html" ),
    join( "!**", "*.tests.js" ),
    join( "!**", ".new" ),
    join( "!**", "assets", "icons", "svg", "**" )
  ],
  tests: {
    component: [
      join( paths.client, "**", "*.tests.html" ),
      join( paths.client, "**", "*.tests.js" )
    ],
    domain: [
      join( paths.tests, "domain", "**", "*.js" ),
      join( paths.tests, "karma-tweaks.js" )
    ]
  }
};
paths.symlink.prod = paths.symlink.src.concat( join( "!**", "coverage.html" ) );

/*** LESS ***/
paths.less = {
  src: join( paths.client, "**", "*.less" ),
  out: {
    dev: paths.dev,
    prod: paths.prod
  },
  // todo double check in windows if '/' gets changed to '\'
  includePaths: [ join( "client", "styles", "/" ) ],
  included: [
    join( "styles", "mixins", "**", "*.less" ),
    join( "**", "*.vars.less" ),
    join( "**", "*.mixin.less" ),
    join( "**", "*.include.less" ),
    join( "**", "fonts", "*.less" )
  ].map( s => join( paths.client, s ) )
};
paths.less.compile = paths.less.included
  .map( s => "!" + s )
  .concat( paths.less.src );

/*** Paths to JavaScript Files ***/
paths.scripts = {
  all:            [
    join( paths.root, "**", "*.js" ),
    join( "!", paths.bowerComponents ),
    join( "!", paths.nodeModules )
  ],
  client: join( paths.client, "**", "*.js" ),
  tasks: join( paths.tasks, "**", "*.js" ),
  tests: join( paths.tests, "**", "*.js" ),
  clientTests: [
    join( paths.client, "**", "*.tests.js" ),
    join( paths.client, "**", "*.tests.html" )
  ],
  server: join( paths.server, "**", "*.js" ),
  es6: {
    all: join( paths.root, "**", "*.es6.js" ),
    client: join( paths.client, "**", "*.es6.js" )
  }
};

/*** Traceur ES6 --> ES5 ***/
paths.traceur = {
  bin: join( paths.nodeModules, ".bin", "traceur" ),
  src: [
    join( paths.client, "**", "*.es6.js" ),
    join( paths.client, "domain", "**", "*.js" )
  ],
  compileAsScript: new RegExp( "^" + join( paths.client, "components" ) + "|\\.script\\.js$" ),
  out: {
    dev: paths.dev,
    prod: paths.prod
  }
};

/*** TESTING ***/
paths.testing = {
  index: join( paths.tests, "tests.html" ),
  client: {
    js: join( paths.client, "**", "*.tests.js" ),
    html: join( paths.client, "**", "*.tests.html" )
  },
  tests: [
    join( paths.tests, "**", "*.js" ),
    join( "!", paths.tests, "karma-tweaks.js" )
  ]
};
paths.testing.client.all = Object.keys( paths.testing.client )
  .map( prop => paths.testing.client[ prop ] );

/*** KARMA ***/
paths.karma = {
  rc: join( paths.root, "karma.conf.js" ),
  base: paths.dev,
  port: 9876,
  coverage: {
    src: "**/!(vendor)/!(*.tests).js",
    out: {
      html: join( paths.dev, "coverage" ),
      lcov: join( paths.build, "coverage" )
    }
  },
  exclude: [
    "index.html",
    "tests.html",
    "**/.new/*.*",
    "coverage/**"
  ],
  files: ( function() {
    var files = [];

    // load vendor scripts in correct order
    [
      "webcomponents.js",
      "polymer.js",
      "traceur-runtime.js",
      "es6-module-loader.js",
      "system.js"
    ].forEach( function( vendor ) {
        files.push({
          pattern: join( "vendor", vendor ),
          watched: false,
          included: true,
          served: true
        });
      });

    files.push({
      pattern: "karma-tweaks.js",
      watched: true,
      included: true,
      served: true
    });

    // Load Domain Tests Files
    files.push({
      pattern: "domain/**/*.tests.js",
      watched: true,
      included: true,
      served: true
    });

    // load html tests files
    files.push({
      pattern: "**/*.tests.html",
      watched: true,
      included: true,
      served: true
    });

    // Watch JS Files
    files.push({
      pattern: "**/*.js",
      watched: true,
      included: false,
      served: true
    });

    // Watch HTML Files
    files.push({
      pattern: "**/*.html",
      watched: true,
      included: false,
      served: true
    });

    // make sure everything else is served
    files.push({
      pattern: "**/*.*",
      watched: false,
      included: false,
      served: true
    });

    return files;
  })()
};

/*** LOGGING ***/
paths.logging = {
  gulp: join( paths.logs, "gulp" ),
  createDatedFilename: function( name, date ) {
    date = date || new Date();
    return date.getFullYear() + "-" +
      ( 1 + date.getMonth() ) + "-" +
      date.getDate() + "_" +
//      date.toTimeString().split( " " )[0].replace( /:/g, "-" ) + "_" +
      name + ".log";
  },
  createDatedLogFile: function( folder, name, date ) {
    return join( folder, paths.logging.createDatedFilename( name, date ) );
  }
};

/*** SERVER PATHS ***/
paths.server = {
  ports: {
    dev: 5115,
    prod: 5116
  },
  fallback: {
    dev: join( paths.dev, "index.html" ),
    prod: join( paths.prod, "index.html" )
  },
  watch: paths.dev
};

/*** DGENI PATHS ***/
paths.dgeni = {
  components: {
    basePath: paths.dgeni,
    src: [
      {
        include: join( paths.client, "components", "**", "*.js" ),
        exclude: [
          join( paths.client, "components", ".new", "*.*" ),
          join( paths.client, "components", "**", "*.tests.js" ),
          join( paths.client, "components", "**", "*.es6.js" )
        ],
        basePath: "components"
      }
    ],
    templateFolder: join( paths.dgeni, "templates" ),
    templatePattern: "components.template.html",
    outputFolder: join( paths.docs, "components" )
  }
};

/*** SVGSTORE PATHS ***/
paths.svgstore = {
  src: join( paths.client, "assets", "icons", "svg", "*.svg" ),
  out: {
    dev: join( paths.dev, "assets", "icons" ),
    prod: join( paths.prod, "assets", "icons" )
  }
};

/*** ENVBUILD PATHS ***/
/*** ask about qa paths ***/
paths.envBuild = {
  src: {
    dev:  join( "domain", "ed", "urls", "dev.js" ),
    prod: join( "domain", "ed", "urls", "prod.js" ),
    qa:   join( "domain", "ed", "urls", "qa.js" )
  },
  remove: {
    dev:  join( "domain", "ed", "urls" ),
    prod: join( "domain", "ed", "urls" ),
    qa:   join( "domain", "ed", "urls" )
  }
};

// Export!
module.exports = paths;
