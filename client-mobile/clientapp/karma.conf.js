/*eslint no-process-env:0, vars-on-top:0*/
// Karma configuration
// Generated on Thu Nov 06 2014 13:30:30 GMT-0800 (PST)
require( "dotenv" ).config();

var paths = require( "./config.paths.js" ),
  avaliableReporters = [ "mocha", "nyan" ],
  envReporter = process.env.KARMA_REPORTER,
  reporterToUse;

if ( envReporter && avaliableReporters.some( r => r === envReporter ) ) {
  reporterToUse = envReporter;
} else {
  reporterToUse = process.env.GULP_ENVIRONMENT === "DEVELOPMENT" ? "nyan" : "mocha";
}

module.exports = function( config ) {
  "use strict";
  config.set({

    // base path that will be used to resolve all patterns (eg. files, exclude)
    basePath: paths.karma.base,

    // web server port
    port: paths.karma.port,

    // frameworks to use
    // available frameworks: https://npmjs.org/browse/keyword/karma-adapter
    frameworks: [
      "mocha",
      "chai",
      "chai-as-promised",
      "chai-sinon"
    ],

    plugins: [
      // Frameworks
      "karma-mocha",
      "karma-chai",
      "karma-chai-plugins",
      "karma-chai-sinon",
      // Launchers
      "karma-chrome-launcher",
      // Reporters
      "karma-mocha-reporter",
      "karma-nyan-reporter",
      "karma-coverage"
    ],

    client: {
      mocha: {
        ui: "tdd"
      }
    },

    // list of files / patterns to load in the browser
    files: paths.karma.files,

    // list of files to exclude
    exclude: paths.karma.exclude,

    // preprocess matching files before serving them to the browser
    // available preprocessors: https://npmjs.org/browse/keyword/karma-preprocessor
    preprocessors: ( function() {
      var preprocessors = {};

      preprocessors[paths.karma.coverage.src] = [ "coverage" ];
      return preprocessors;
    })(),

    // test results reporter to use
    // possible values: "dots", "progress"
    // available reporters: https://npmjs.org/browse/keyword/karma-reporter
    reporters: [
      reporterToUse,
      "coverage"
    ],

    mochaReporter: {
      output: "noFailures",
      ignoreSkipped: true
    },

    nyanReporter: {
      suppressErrorReport: true
    },

    coverageReporter: {
      subdir: function( browser ) {
        return browser.toLowerCase().split( /[ /-]/ )[0];
      },
      reporters: [
        process.env.TRAVIS === "true" ? {
          type: "lcovonly",
          dir: paths.karma.coverage.out.lcov
        } : {
          type: "html",
          dir: paths.karma.coverage.out.html
        }
      ]
    },

//    proxies: {},

    // enable / disable colors in the output (reporters and logs)
    colors: true,

    // level of logging
    // possible values:
    //    config.LOG_DISABLE
    //    config.LOG_ERROR
    //    config.LOG_WARN
    //    config.LOG_INFO
    //    config.LOG_DEBUG
    logLevel: config.LOG_ERROR,

    // enable / disable watching file and executing tests whenever any file changes
    autoWatch: true,

    // Continuous Integration mode
    // if true, Karma captures browsers, runs the tests and exits
    singleRun: false,

    // start these browsers
    // available browser launchers: https://npmjs.org/browse/keyword/karma-launcher
    browsers: process.env.TRAVIS === "true" ? [ "ChromeTravis" ] : [ "Chrome" ],

    customLaunchers: {
      ChromeTravis: {
        base: "Chrome",
        flags: [ "--no-sandbox" ]
      }
    }
    /*
    browserNoActivityTimeout: 30000,
    browsers: [ "PhantomJS" ],
    phantomjsLauncher: {
      cmd: {
        linux: global.process.env.PHANTOMJS_BIN,
        darwin: global.process.env.PHANTOMJS_BIN,
        win32: global.process.env.PHANTOMJS_BIN
      }
    }
    */
  });
};
