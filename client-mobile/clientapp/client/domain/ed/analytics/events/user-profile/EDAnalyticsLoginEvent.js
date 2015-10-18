/*jshint strict:false*/

import define from "domain/ed/define-properties";
import EDAnalyticsEvent from "domain/ed/analytics/events/EDAnalyticsEvent";

var eventKeys = [
  "time"
];

export default class EDAnalyticsLoginEvent extends EDAnalyticsEvent {
  static get TYPE() {
    return "login";
  }

  constructor( args ) {
    super( args );

    // Define Keys on this object
    define.enumReadOnly( this, eventKeys, args );
  }

  get valueKeys() {
    // returns a "clone"
    return eventKeys.slice();
  }
}
