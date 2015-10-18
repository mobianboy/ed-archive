/*eslint strict: [2, "global"]*/
"use strict";
var config = require( "./config.paths.js" );

module.exports = {
  root: config.dev,
  suites: [ "tests.html" ],
  verbose: true,
  expanded: true,
  remote: false,
  persistent: true,
  browsers: [ "chrome" ]
};
