/*jshint strict: false*/
var
  AdMob = window.AdMob,
  admobid = {},
  admobOptions,
  setAdMobIds = function() {
    if ( /(android)/i.test( navigator.userAgent )) {
      admobid = {
        banner: "ca-app-pub-6485453766683902/8331842279"
      };
    } else if ( /(ipod|iphone|ipad)/i.test( navigator.userAgent )) {
      admobid = {
        banner: "ca-app-pub-6485453766683902/8052640677"
      };
    }
  };

try {
  admobOptions = {
    // adSize: 'SMART_BANNER',
    position: AdMob.AD_POSITION.TOP_CENTER,
    offsetTopBar: true //iOS7+
    // bgColor: 'black',
    // x: integer,
    // y: integer,
    // overlap: true,
    // isTesting: true
  };
} catch ( error ) {
  console.warn( "AdMob isn't supported" );
}

setAdMobIds();

export default {
  show: function() {
    if ( AdMob && AdMob.createBanner ) {
      AdMob.setOptions( admobOptions );

      AdMob.createBanner({
        adId: admobid.banner,
        autoShow: true
      });
    }
  },
  hide: function() {
    if ( AdMob && AdMob.createBanner ) {
      AdMob.removeBanner();
    }
  }
}
