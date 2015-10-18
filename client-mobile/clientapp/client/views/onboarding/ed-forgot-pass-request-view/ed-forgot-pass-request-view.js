(function( polymer ) {
  "use strict";

  System.import( "domain/ed/services/ed-user-service" )
    .then(function( imported ) {
      var
        userService = imported.default,
        emailRegexPattern = /^\s*[\w\-\+_]+(\.[\w\-\+_]+)*\@[\w\-\+_]+\.[\w\-\+_]+(\.[\w\-\+_]+)*\s*$/,
        cleanupErrorHandler = function( event ) {
          this.cleanupErrors();
        },
        goSubmitHandler = function( event ) {
          if ( event.keyCode === 13 ) {
            this.submitButton.dispatchEvent( new MouseEvent( "click" ) );
          }
        };

      polymer( "ed-forgot-pass-request-view", {
        /* LIFECYCLE */
        ready: function() {
          this.logo = this.shadowRoot.querySelector( ".ed-logo-header" );
          this.submitButton = this.shadowRoot.querySelector( "#forgot-submit" );
          this.emailInput = this.shadowRoot.querySelector( ".email" );
          this.serverError = this.shadowRoot.querySelector( "#errorServer" );
          this.emailError = this.shadowRoot.querySelector( "#errorEmail" );
          this.handlers = {
            cleanup: cleanupErrorHandler.bind( this ),
            goSubmit: goSubmitHandler.bind( this )
          };
        },
        attached: function() {
          this.emailInput.addEventListener( "blur", this.handlers.cleanup, true );
          this.emailInput.addEventListener( "keyup", this.handlers.goSubmit );
        },
        detached: function() {
          this.emailInput.removeEventListener( "blur", this.handlers.cleanup );
          this.emailInput.addEventListener( "keyup", this.handlers.goSubmit );
        },
        get validEmail() {
          return this.emailInput.validity.valid && emailRegexPattern.test( this.emailInput.value );
        },
        cleanupErrors: function() {
          this.emailError.classList.add( "hidden" );
          this.serverError.classList.add( "hidden" );
          this.emailInput.classList.remove( "invalid" );
        },
        submitRequest: function( event ) {
          event.preventDefault();

          if ( !this.validEmail ) {
            this.emailInput.classList.add( "invalid" );
            this.emailError.classList.remove( "hidden" );
            this.logo.scrollIntoView();
            return;
          }

          userService.requestPasswordReset( this.emailInput.value )
            .then( function( response ) {
              if ( response != null && response.isSuccessful ) {
                this.router.go( "/forgot-pass/reset" );
              } else {
                this.emailInput.classList.add( "invalid" );
                this.emailError.classList.add( "hidden" );
                this.serverError.classList.remove( "hidden" );
              }
            }.bind( this ))
            .catch( function() {
              console.log( "password request did not go through" );
              this.logo.scrollIntoView();
            }, this );
        }
      });
    });
})( window.Polymer );
