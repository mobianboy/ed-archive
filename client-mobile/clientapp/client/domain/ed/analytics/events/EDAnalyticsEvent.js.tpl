/*jshint strict:false*/

import define from "domain/ed/define-properties";
import EDAnalyticsEvent from "domain/ed/analytics/events/EDAnalyticsEvent";

var eventKeys = [
  // TODO add this event's key values
];

// TODO UPDATE NAME
export default class EDAnalyticsEventName extends EDAnalyticsEvent {
  static get TYPE() {
    // TODO UPDATE NAME/TYPE
    return "event-name";
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
