/*eslint consistent-this: 0 */

// add routes to this map with a boolean flag for:
//  true --> needs auth
//  false --> "public" route
var routeMap = {
  // User Routes
  "user/create": false,
  "user/password/get": false,
  "user/password/set": false,
  "user/password/update": true,

  // Referral
  "referral/get": true,
  "referral/create": true,

  // Track
  "track/create": true,
  "track/delete": true,
  "track/get": true,
  "track/url/get": true,
  "track/list": true,
  "track/rate/set": true,
  "track/art/get": true,

  // Discover
  "discover/blend/get": true,
  "discover/list": true,

  // Profile
  "profile/get": true,
  "profile/update": true,
  "profile/set": true,
  "profile/create": true,
  "profile/art/create": true,
  "profile/art/delete": true,
  "profile/art/update": true,
  "profile/blend/get": true,
  "profile/blend/set": true,

  // Genre
  "genre/list": true,

  // Chart
  "chart/list": true
};

export default {
  needsAuth( route ) {
    if ( route in routeMap ) {
      return routeMap[ route ];
    }

    return false;
  }
};
