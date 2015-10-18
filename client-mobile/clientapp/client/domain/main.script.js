(function( window, Promise ) {
  "use strict";

  var
    hidePlayerRoutes = [
      "/",
      "/login",
      "/register",
      "/registration"
    ],
    needToHidePlayerForRoute = function( route ) {
      return hidePlayerRoutes.some( hideRoute => {
        return hideRoute === route;
      });
    },
    hideSplashScreen = function() {
      window.setTimeout(function() {
        document.querySelector( "#splash-screen" ).hide();
      }, 750);
    };

  Promise.all([
    window.polymerReadyPromise,
    window.deviceReadyPromise
  ]).then(function( ready ) {
    console.log( "polymer and device ready!" );
    console.dir( ready );

    Promise.all([
      System.import( "domain/ed/services/ed-data-service" ),
      System.import( "domain/ed/services/ed-connection-service" ),
      System.import( "domain/ed/services/ed-user-service" ),
      System.import( "domain/ed/services/ed-player-service" ),
      System.import( "domain/ed/analytics/ed-analytics-service" ),
      System.import( "domain/ed/cordova/ed-admob" )
    ])
      .then( imports => {
        var
          // destructing the default imports to their associated name
          [
            dataService,
            connectionService,
            userService,
            playerService,
            analyticsService,
            edAdMob
          ] = imports.map( imported => imported.default ),
          songCard = document.getElementById( "song-card" ),
          router = document.getElementById( "root-app-router" ),
          resumePlayback;

        // Fires before the app router does anything regarding a route change
        // Check if open session, if not, route back to login page
        // This should solve all "in the app but no session" issues
        router.addEventListener( "state-change", function( event ) {
          console.log( "in state-change: %o", event );

          if ( !userService.isOpenSession && !( /\/(login|register|registration|forgot-pass)/i ).test( event.detail.path )) {
            console.log( "no open session, going to login page" );
            event.preventDefault();
            router.go( "/login", {
              replace: true
            });
          }
        });

        // Fires before the new route is imported or rendered, manages song card visibility
        router.addEventListener( "activate-route-start", function( event ) {
          if ( needToHidePlayerForRoute( event.detail.path )) {
            songCard.hide();
          } else {
            songCard.show();
          }

          if ( event.detail.path === "/charts" ) {
            edAdMob.show();
          } else {
            edAdMob.hide();
          }
        });

        // Fires at end of route load and render process, sends analytics "routeRequest" event
        router.addEventListener( "activate-route-end", function( event ) {
          // remove "router" from event model since is causes a circular reference
          // which throws an error during JSON serialization
          var
            params = Object.keys( event.detail.model )
              .reduce(function( accumulator, key ) {
                if ( key !== "router" ) {
                  accumulator[ key ] = event.detail.model[ key ];
                }

                return accumulator;
              }, {});

          analyticsService.send( "routeRequest", {
            route: event.detail.path,
            params
          });
        });

        // need to init manually to ensure event binding
//        router.init();

        window.edLoadScriptPromise.then(function() {
          if ( userService.isOpenSession ) {
            if ( userService.hasOnboarded ) {
              router.go( "/discover" );
            } else {
              router.go( "/onboarding/like" );
            }
          } else {
            router.go( "/login" );
          }

          // remove splash screen!
          hideSplashScreen();
        });

        // bind to cordova events to listen for app background/foreground states
        document.addEventListener( "pause", function() {
          resumePlayback = playerService.currentStats.state;
        }, false );

        document.addEventListener( "resume", function() {
          if ( resumePlayback && playerService.isPaused ) {
            playerService.play();
          }
        }, false );
      })
      .catch( error => {
        console.error( "Problem in main script: " + error.message );
        console.error( error.stack );

        // remove splash screen!
        hideSplashScreen();
      });
  });
})( window, window.Promise );
