/*jshint strict:false*/
/*jscs:disable maximumLineLength*/

var eventMap = {};

/* TODO ADD EVENTS AS CREATED
import EDAnalyticsNameEvent from "domain/ed/analytics/events/EDAnalyticsEventName";
*/

// Player Events
import EDAnalyticsCompletedListenEvent from "domain/ed/analytics/events/player/EDAnalyticsCompletedListenEvent";
import EDAnalyticsPlayEvent from "domain/ed/analytics/events/player/EDAnalyticsPlayEvent";
import EDAnalyticsPauseEvent from "domain/ed/analytics/events/player/EDAnalyticsPauseEvent";
import EDAnalyticsQuitEvent from "domain/ed/analytics/events/player/EDAnalyticsQuitEvent";
import EDAnalyticsRateEvent from "domain/ed/analytics/events/player/EDAnalyticsRateEvent";
import EDAnalyticsScrubEvent from "domain/ed/analytics/events/player/EDAnalyticsScrubEvent";

// User and Profile Events
import EDAnalyticsInviteEvent from "domain/ed/analytics/events/user-profile/EDAnalyticsInviteEvent";
import EDAnalyticsRegisterEvent from "domain/ed/analytics/events/user-profile/EDAnalyticsRegisterEvent";
import EDAnalyticsLoginEvent from "domain/ed/analytics/events/user-profile/EDAnalyticsLoginEvent";
import EDAnalyticsLogoutEvent from "domain/ed/analytics/events/user-profile/EDAnalyticsLogoutEvent";
import EDAnalyticsEditProfileEvent from "domain/ed/analytics/events/user-profile/EDAnalyticsEditProfileEvent";
import EDAnalyticsEditDiscoverBlend from "domain/ed/analytics/events/user-profile/EDAnalyticsEditDiscoverBlend";

// Application Events
import EDAnalyticsRouteRequestEvent from "domain/ed/analytics/events/application/EDAnalyticsRouteRequestEvent";

// The event constructor TYPE property should be synonymous with the event "name"
eventMap = {
  /* TODO ADD EVENT TO MAP AS NEEDED/CREATED
  [ EDAnalyticsNameEvent.TYPE ]: EDAnalyticsNameEvent,
  */

  // Player Events
  [ EDAnalyticsCompletedListenEvent.TYPE ]: EDAnalyticsCompletedListenEvent,
  [ EDAnalyticsPlayEvent.TYPE ]: EDAnalyticsPlayEvent,
  [ EDAnalyticsPauseEvent.TYPE ]: EDAnalyticsPauseEvent,
  [ EDAnalyticsQuitEvent.TYPE ]: EDAnalyticsQuitEvent,
  [ EDAnalyticsRateEvent.TYPE ]: EDAnalyticsRateEvent,
  [ EDAnalyticsScrubEvent.TYPE ]: EDAnalyticsScrubEvent,

  // User and Profile Events
  [ EDAnalyticsInviteEvent.TYPE ]: EDAnalyticsInviteEvent,
  [ EDAnalyticsRegisterEvent.TYPE ]: EDAnalyticsRegisterEvent,
  [ EDAnalyticsLoginEvent.TYPE ]: EDAnalyticsLoginEvent,
  [ EDAnalyticsLogoutEvent.TYPE ]: EDAnalyticsLogoutEvent,
  [ EDAnalyticsEditProfileEvent.TYPE ]: EDAnalyticsEditProfileEvent,
  [ EDAnalyticsEditDiscoverBlend.TYPE ]: EDAnalyticsEditDiscoverBlend,

  // Application Events
  [ EDAnalyticsRouteRequestEvent.TYPE ]: EDAnalyticsRouteRequestEvent
};

// An array of all event "types" aka "names", excluding "names"
Object.defineProperty( eventMap, "names", {
  configurable: false,
  eunumerable: false,
  writeable: false,
  get() {
    return Object.keys( eventMap );
  }
});

export default eventMap;
