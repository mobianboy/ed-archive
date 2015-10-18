( function( polymer, System ) {
  "use strict";

  System.import( "domain/ed/services/ed-user-service" )
    .then( function( imported ) {
      var userService = imported.default,
        emailRegexPattern = /^\s*[\w\-\+_]+(\.[\w\-\+_]+)*\@[\w\-\+_]+\.[\w\-\+_]+(\.[\w\-\+_]+)*\s*$/,
        cleanupErrorHandler = function() {
          this.cleanupErrors();
        };

      polymer( "ed-referral-view", {
        /* LIFECYCLE */
        userService: userService,
        referrals: 0,
        ready: function() {
          this.emailInput = this.shadowRoot.querySelector( ".email" );
          this.submitButton = this.shadowRoot.querySelector( "#referral-submit" );
          this.emailError = this.shadowRoot.querySelector( "#errorEmail" );
          this.handlers = {
            cleanup: cleanupErrorHandler.bind( this )
          };
        },
        attached: function() {
          this.emailInput.addEventListener( "focus", this.handlers.cleanup, true );
          this.referrals = userService.referralsRemaining;
        },
        detached: function() {
          this.emailInput.removeEventListener( "focus", this.handlers.cleanup );
        },
        get validEmail() {
          return this.emailInput.validity.valid && emailRegexPattern.test( this.emailInput.value );
        },
        cleanupErrors: function() {
          this.emailError.classList.add( "hidden" );
          this.emailInput.classList.remove( "invalid" );
        },
        submitFriendEmail: function( event ) {
          event.preventDefault();

          if ( !this.validEmail ) {
            this.emailInput.classList.add( "invalid" );
            this.emailError.classList.remove( "hidden" );
            // todo .scrollIntoView();
            return;
          }

          return userService.referral( this.emailInput.value )
            .then( function( response ) {
              this.emailInput.value = "";
              this.emailError.classList.add( "hidden" );
              this.emailInput.classList.remove( "invalid" );

              if ( this.referrals !== response ) {
                this.referrals = response;
              }
            }.bind( this ))
            .catch( function() {
              console.log( "referral request did not go through" );
              // todo .scrollIntoView();
            }, this );
        },
        updateReferralCount: function() {
          return userService.getReferrals()
            .then(function( referrals ) {
              this.referrals = referrals;
            }.bind( this ));
        }
      });
    });
})( window.Polymer, window.System );
