( function( polymer, System ) {
  "use strict";

  System.import( "domain/ed/services/ed-user-service" )
    .then(function( imported ) {
      var
        userService = imported.default,
        emailRegexPattern = /^\s*[\w\-\+_]+(\.[\w\-\+_]+)*\@[\w\-\+_]+\.[\w\-\+_]+(\.[\w\-\+_]+)*\s*$/,
        passwordRegexPattern = /^.{8,}/,
        inputPropertyNameToValidGetter = function( inputName ) {
          var name = inputName[ 0 ].toUpperCase() + inputName.slice( 1 );
          return "valid" + name;
        },
        cleanupErrorHandler = function( event ) {
          this.cleanupErrors();
        },
        goSubmitHandler = function( event ) {
          if ( event.keyCode === 13 ) {
            this.submitButton.dispatchEvent( new MouseEvent( "click" ) );
          }
        };

      polymer( "ed-login-view", {
        /* LIFECYCLE */
        ready: function() {
          this.logo = this.shadowRoot.querySelector( ".ed-logo-header" );
          this.submitButton = this.shadowRoot.querySelector( "#login-submit" );
          this.loginBody = this.shadowRoot.querySelector( ".ed-login-body" );
          this.errorServer = this.shadowRoot.querySelector( "#errorServer" );
          this.lastInput = this.shadowRoot.getElementById( "form-password" );

          this.formInputs = {
            email: this.shadowRoot.querySelector( ".email" ),
            password: this.shadowRoot.querySelector( ".password" )
          };

          this.errorDivs = {
            email: this.shadowRoot.querySelector( "#errorEmail" ),
            password: this.shadowRoot.querySelector( "#errorPassword" )
          };

          this.handlers = {
            cleanup: cleanupErrorHandler.bind( this ),
            goSubmit: goSubmitHandler.bind( this )
          };
        },
        attached: function() {
          this.loginBody.addEventListener( "blur", this.handlers.cleanup, true );
          this.lastInput.addEventListener( "keyup", this.handlers.goSubmit );

          // go to discover if already logged in
          if ( userService.isOpenSession ) {
            this.router.go( "/discover" );
          }
        },
        detached: function() {
          this.loginBody.removeEventListener( "blur", this.handlers.cleanup, true );
          this.lastInput.removeEventListener( "keyup", this.handlers.goSubmit );
        },
        get validEmail() {
          return this.formInputs.email.validity.valid && emailRegexPattern.test( this.formInputs.email.value );
        },
        get validPassword() {
          return passwordRegexPattern.test( this.formInputs.password.value );
        },
        get canSubmit() {
          return Object.keys( this.formInputs ).every(function( current ) {
            return this[ inputPropertyNameToValidGetter( current ) ];
          }, this );
        },
        postEarlyErrors: function() {
          Object.keys( this.formInputs ).forEach(function( current ) {
            if ( !this[ inputPropertyNameToValidGetter( current ) ] ) {
              this.errorDivs[ current ].classList.remove( "hidden" );
              this.formInputs[ current ].classList.add( "invalid" );
            }
          }, this );
        },
        cleanupErrors: function() {
          Object.keys( this.formInputs ).forEach(function( current ) {
            if ( this[ inputPropertyNameToValidGetter( current ) ] ) {
              this.errorDivs[ current ].classList.add( "hidden" );
              this.formInputs[ current ].classList.remove( "invalid" );
            }
          }, this );
          this.errorServer.classList.add( "hidden" );
          this.errorServer.classList.remove( "invalid" );
        },
        submitForm: function( event ) {
          var
            email = this.formInputs.email.value,
            password = this.formInputs.password.value,
            errorServer = this.errorServer,
            routerOptions = {
              replace: true
            };

          event.preventDefault();

          if ( !this.canSubmit ) {
            this.postEarlyErrors();
            this.logo.scrollIntoView();
            return;
          }

          userService.login( email, password )
            .then(function( edFan ) {
              if ( edFan != null && userService.hasOnboarded ) {
                this.router.go( "/discover", routerOptions );
              } else if ( edFan != null ) {
                this.router.go( "/onboarding/like", routerOptions );
              } else {
                errorServer.classList.remove( "hidden" );
              }

              return edFan;
            }.bind( this ))
            .catch( function( error ) {
              errorServer.classList.remove( "hidden" );
              this.logo.scrollIntoView();
              return error;
            }, this );
        }
      });
    });
})( window.Polymer, window.System );
