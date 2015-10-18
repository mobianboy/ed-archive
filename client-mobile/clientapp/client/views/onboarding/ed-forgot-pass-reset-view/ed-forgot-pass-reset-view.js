( function( polymer ) {
  "use strict";

  Promise.all([
    System.import( "domain/ed/services/ed-user-service" )
  ]).then(function( imported ) {
    var
      userService = imported[ 0 ].default,
      cleanUpHandler = function( event ) {
        if ( event.target.tagName === "ED-FORM-INPUT" ) {
          this.cleanUpErrors();
        }

        if ( event.target.tagName === "ED-PAIRED-INPUT" ) {
          this.cleanUpPasswordErrors();
        }
      },
      submitCheckHandler = function( event ) {
        event.preventDefault();

        if ( !this.validPassword || !this.validResetCode ) {
          this.postEarlyErrors();
          this.postPasswordEarlyErrors();
          this.logo.scrollIntoView();
          return;
        }

        userService.resetPassword( this.resetCode.value, this.pairedInput.val )
          .then(function( response ) {
            this.router.go( "/discover" );
            return response;
          }.bind( this ))
          .catch(function( error ) {
            // TODO go go error stuff
            this.resetCode.classList.add( "invalid" );
            this.errorDivs.resetCodeError.classList.remove( "hidden" );
            this.logo.scrollIntoView();
            return error;
          }.bind( this ));
      },
      goSubmitHandler = function( event ) {
        if ( event.keyCode === 13 ) {
          this.submitButton.dispatchEvent( new MouseEvent( "click" ) );
        }
      };

    polymer( "ed-forgot-pass-reset-view", {
      /* LIFECYCLE */
      get validPassword() {
        return this.pairedInput.isValid;
      },
      get validResetCode() {
        return this.resetCode.value !== "";
      },
      ready: function() {
        this.logo = this.shadowRoot.querySelector( ".ed-logo-header" );
        this.pairedInput = this.shadowRoot.querySelector( "ed-paired-input" );
        this.resetCode = this.shadowRoot.querySelector( ".reset-code" );
        this.formContainer = this.shadowRoot.querySelector( "#forgot-form" );
        this.submitButton = this.shadowRoot.querySelector( "#forgot-submit" );

        this.handlers = {
          submitCheck: submitCheckHandler.bind( this ),
          cleanUp: cleanUpHandler.bind( this ),
          goSubmit: goSubmitHandler.bind( this )
        };

        this.errorDivs = {
          passwordShort: this.shadowRoot.querySelector( "#errorPasswordShort" ),
          passwordMismatch: this.shadowRoot.querySelector( "#errorPasswordMismatch" ),
          // server side checks
          resetCodeError: this.shadowRoot.querySelector( "#errorResetCode" )
        };
      },
      attached: function() {
        this.formContainer.addEventListener( "blur", this.handlers.cleanUp, true );
        this.submitButton.addEventListener( "click", this.handlers.submitCheck );
        this.pairedInput.addEventListener( "keyup", this.handlers.goSubmit );
      },
      detached: function() {
        this.formContainer.removeEventListener( "blur", this.handlers.cleanUp );
        this.submitButton.removeEventListener( "click", this.handlers.submitCheck );
        this.pairedInput.removeEventListener( "keyup", this.handlers.goSubmit );
      },
      postEarlyErrors: function() {
        if ( !this.validResetCode ) {
          this.resetCode.classList.add( "invalid" );
          this.errorDivs.resetCodeError.classList.remove( "hidden" );
        }
      },
      postPasswordEarlyErrors: function() {
        if ( this.pairedInput.val == null || this.pairedInput.val.length < 8 ) {
          // post password to short
          this.errorDivs.passwordShort.classList.remove( "hidden" );
          this.pairedInput.setAttribute( "invalid-primary", "" );
          this.pairedInput.setAttribute( "invalid-confirm", "" );
        }

        if ( !this.pairedInput.inputMatchConfirm ) {
          // passwords don't match
          this.errorDivs.passwordMismatch.classList.remove( "hidden" );
          this.pairedInput.setAttribute( "invalid-primary", "" );
          this.pairedInput.setAttribute( "invalid-confirm", "" );
        }
      },
      cleanUpErrors: function() {
        if ( this.validResetCode ) {
          this.resetCode.classList.remove( "invalid" );
          this.errorDivs.resetCodeError.classList.add( "hidden" );
        }
      },
      cleanUpPasswordErrors: function() {
        if ( this.pairedInput.val !== null || this.pairedInput.val.length > 7 ) {
          this.errorDivs.passwordShort.classList.add( "hidden" );
          this.pairedInput.removeAttribute( "invalid-primary" );
          this.pairedInput.removeAttribute( "invalid-confirm" );
        }

        if ( this.pairedInput.inputMatchConfirm ) {
          this.errorDivs.passwordMismatch.classList.add( "hidden" );
          this.pairedInput.removeAttribute( "invalid-primary" );
          this.pairedInput.removeAttribute( "invalid-confirm" );
        }
      }
    });
  });
})( window.Polymer );
