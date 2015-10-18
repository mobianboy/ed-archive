( function( polymer, System ) {
  "use strict";

  System.import( "domain/ed/services/ed-user-service" )
    .then(function( imported ) {
      var
        userService = imported.default,
        observeProfileHandler = function( changeList ) {
          console.log( "changed %o", changeList );

          changeList.reverse().every(function( change ) {
            if ( change.name == this.edFan.id ) {
              this.edFan = change.object;
              return false;
            }

            return true;
          }, this);
        },
        triggerMenuHandler = function( event ) {
          if ( this.edMenu.classList.contains( "show-menu" ) ) {
            this.edMenu.classList.remove( "show-menu" );
          }

          this.setActive( event.detail.path );
        };

      polymer( "ed-side-menu", {
        /* LIFECYCLE */
        ready: function() {
          this.edMenu = document.getElementById( "side-menu" );
          this.router = document.getElementById( "root-app-router" );

          // handlers
          this.handlers = {
            triggerMenu: triggerMenuHandler.bind( this ),
            profileObserver: observeProfileHandler.bind( this )
          };

          this.observing = false;

          // todo? move to "attached" and remove on "detached"
          userService.on( "edLogin", function() {
            this.edFan = userService.currentProfile;

            if ( !this.observing ) {
              userService.observeCurrentProfile( this.handlers.profileObserver, [ "set" ]);
              this.observing = true;
            }
          }.bind( this ));

          userService.on( "edLogout", function() {
            if ( this.observing ) {
              userService.unobserveCurrentProfile( this.handlers.profileObserver );
              this.observing = false;
            }
          }.bind( this ));
        },
        attached: function() {
          this.router.addEventListener( "activate-route-end", this.handlers.triggerMenu );

          if ( userService.isOpenSession ) {
            this.edFan = userService.currentProfile;

            if ( !this.observing ) {
              userService.observeCurrentProfile( this.handlers.profileObserver, [ "set" ]);
              this.observing = true;
            }
          }
        },
        detached: function() {
          this.router.removeEventListener( "activate-route-end", this.handlers.triggerMenu );

          if ( this.observing ) {
            userService.unobserveCurrentProfile( this.handlers.profileObserver );
            this.observing = false;
          }
        },
//        attributeChanged: function( attrName ) {}
        setActive: function( path ) {
          if ( this.routeAnchors == null ) {
            this.routeAnchors = Array.prototype.slice.call( this.shadowRoot.querySelectorAll( "ar-anchor"), 0 );
          }

          this.routeAnchors.forEach(function( anchor ) {
            if ( anchor.getAttribute( "route" ) === path ) {
              anchor.classList.add( "active" );
              return;
            }

            anchor.classList.remove( "active" );
          });
        }
      });
    });
})( window.Polymer, window.System );
