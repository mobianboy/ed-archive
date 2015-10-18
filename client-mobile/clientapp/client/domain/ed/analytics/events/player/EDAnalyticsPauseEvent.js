/*jshint strict:false*/

import define from "domain/ed/define-properties";
import EDAnalyticsEvent from "domain/ed/analytics/events/EDAnalyticsEvent";

var eventKeys = [
  "trackId",
  "timecode"
];

export default class EDAnalyticsPauseEvent extends EDAnalyticsEvent {
  static get TYPE() {
    return "pause";
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
