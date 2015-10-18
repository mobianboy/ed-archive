module.exports = function( $ ) {
  "use strict";

  /***
   * Proxy set up for OSX
   * System Prefs > Network > Advanced(Btn) > Proxies
   *  Check Automatic Proxy Configuration
   *  In URL Box: http://localhost:3546/spy-js-proxy.pac
   *  OK > Apply
   *
   * For Chrome
   *  check the page: chrome://net-internals/#proxy
   *  Make sure spy-js-proxy.pac shows up, click the button on the page to
   *  refresh the proxy list, anytime you restart the spy-js folder the
   *  proxy will need to be refreshed
   ***/

//  $.root = "http://localhost:5115";

  $.mapper = function( url ) {
    if ( url.indexOf( "vendor" ) >= 0 ) {
      return {
        instrument: false
      };
    }

    if ( url.indexOf( "coverage" ) >= 0 ) {
      return {
        instrument: false
      };
    }

    if ( url.indexOf( "livereload" ) >= 0 ) {
      return {
        instrument: false
      };
    }

    return {
      instrument: {
        prettify: false,
        objectDump: {
          depth: 1,
          propertyNumber: 3,
          arrayLength: 3,
          stringLength: 50
        }
      }
    };
  };

  $.eventFilter = {
    globalScope: false,
    timeout: true,
    interval: true,
    noEvents: []
  };
};
