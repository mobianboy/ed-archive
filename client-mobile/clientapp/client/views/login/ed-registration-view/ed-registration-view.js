( function( polymer, System ) {
  "use strict";

  System.import( "domain/ed/services/ed-user-service" )
    .then(function( imported ) {
      var
        userService = imported.default,
        emailRegexPattern = /^\s*[\w\-\+_]+(\.[\w\-\+_]+)*\@[\w\-\+_]+\.[\w\-\+_]+(\.[\w\-\+_]+)*\s*$/,
        zipcodeRegexPattern = /^\d{5}(?:[-\s]\d{4})?$/,
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

      polymer( "ed-registration-view", {
        /* LIFECYCLE */
        ready: function() {
          this.logo = this.shadowRoot.querySelector( ".ed-logo-header" );
          this.submitButton = this.shadowRoot.querySelector( "#registration-submit" );
          this.registrationBody = this.shadowRoot.querySelector( ".ed-registration-body" );

          // ed-form-inputs
          this.formInputs = {
            firstName: this.shadowRoot.querySelector( ".name-first" ),
            lastName: this.shadowRoot.querySelector( ".name-last" ),
            email: this.shadowRoot.querySelector( ".email" ),
            inviteCode: this.shadowRoot.querySelector( ".invite-code" ),
            yearOfBirth: this.shadowRoot.querySelector( ".birthday" ),
            zipcode: this.shadowRoot.querySelector( ".zipcode" ),
            password: this.shadowRoot.querySelector( "ed-paired-input" )
          };

          this.errorDivs = {
            firstName: this.shadowRoot.querySelector( ".errorFirstName" ),
            lastName: this.shadowRoot.querySelector( ".errorLastName" ),
            email: this.shadowRoot.querySelector( "#errorEmail" ),
            yearOfBirth: this.shadowRoot.querySelector( "#errorYearOfBirth" ),
            zipcode: this.shadowRoot.querySelector( "#errorZipcode" ),
            passwordShort: this.shadowRoot.querySelector( "#errorPasswordShort" ),
            passwordMismatch: this.shadowRoot.querySelector( "#errorPasswordMismatch" ),
            // server side checks
            inviteCode: this.shadowRoot.querySelector( "#errorInviteCode" ),
            emailTaken: this.shadowRoot.querySelector( "#errorEmailTaken" )
          };

          // handlers
          this.handlers = {
            cleanup: cleanupErrorHandler.bind( this ),
            goSubmit: goSubmitHandler.bind( this )
          };
        },
        attached: function() {
          this.registrationBody.addEventListener( "blur", this.handlers.cleanup, true );
          this.formInputs.inviteCode.addEventListener( "keyup", this.handlers.goSubmit );
        },
        detached: function() {
          this.registrationBody.removeEventListener( "blur", this.handlers.cleanup );
          this.formInputs.inviteCode.removeEventListener( "keyup", this.handlers.goSubmit );
        },

        // Pre Server Call Validity Check Getters
        get validFirstName() {
          return this.formInputs.firstName.value !== "";
        },
        get validLastName() {
          return this.formInputs.lastName.value !== "";
        },
        get validEmail() {
          return this.formInputs.email.validity.valid && emailRegexPattern.test( this.formInputs.email.value );
        },
        get validYearOfBirth() {
          return this.formInputs.yearOfBirth.validity.valid;
        },
        get validZipcode() {
          return zipcodeRegexPattern.test( this.formInputs.zipcode.value );
        },
        get validPassword() {
          return this.formInputs.password.isValid;
        },
        get validInviteCode() {
          return this.formInputs.inviteCode.value !== "";
        },

        // is form "submittable"
        get canSubmit() {
          return Object.keys( this.formInputs ).every(function( current ) {
            return this[ inputPropertyNameToValidGetter( current ) ];
          }, this );
        },

        postPasswordEarlyErrors: function() {
          if ( this.formInputs.password.val == null || this.formInputs.password.val.length < 8 ) {
            // post password to short
            this.errorDivs.passwordShort.classList.remove( "hidden" );
            this.formInputs.password.setAttribute( "invalid-primary", "" );
            this.formInputs.password.setAttribute( "invalid-confirm", "" );
          }

          if ( !this.formInputs.password.inputMatchConfirm ) {
            // passwords don't match
            this.errorDivs.passwordMismatch.classList.remove( "hidden" );
            this.formInputs.password.setAttribute( "invalid-primary", "" );
            this.formInputs.password.setAttribute( "invalid-confirm", "" );
          }
        },

        cleanupPasswordErrors: function() {
          if ( this.formInputs.password.val != null || this.formInputs.password.val.length > 7 ) {
            this.errorDivs.passwordShort.classList.add( "hidden" );
            this.formInputs.password.removeAttribute( "invalid-primary" );
            this.formInputs.password.removeAttribute( "invalid-confirm" );
          }

          if ( this.formInputs.password.inputMatchConfirm ) {
            this.errorDivs.passwordMismatch.classList.add( "hidden" );
            this.formInputs.password.removeAttribute( "invalid-primary" );
            this.formInputs.password.removeAttribute( "invalid-confirm" );
          }
        },

        postEarlyErrors: function() {
          Object.keys( this.formInputs ).forEach(function( current ) {
            if ( !this[ inputPropertyNameToValidGetter( current ) ] ) {
              if ( current === "password" ) {
                this.postPasswordEarlyErrors();
              } else {
                this.errorDivs[ current ].classList.remove( "hidden" );
                this.formInputs[ current ].classList.add( "invalid" );
              }
            }
          }, this );
        },

        cleanupErrors: function() {
          Object.keys( this.formInputs ).forEach(function( current ) {
            if ( this[ inputPropertyNameToValidGetter( current ) ] ) {
              if ( current === "password" ) {
                this.cleanupPasswordErrors();
              } else {
                this.errorDivs[ current ].classList.add( "hidden" );
                this.formInputs[ current ].classList.remove( "invalid" );
              }
            }
          }, this );
        },

        submitForm: function( event ) {
          event.preventDefault();

          if ( !this.canSubmit ) {
            this.postEarlyErrors();
            this.logo.scrollIntoView();
            return;
          }

          userService.register({
            type: "fan",
            email: this.formInputs.email.value,
            password: this.formInputs.password.val,
            name: {
              first: this.formInputs.firstName.value,
              last: this.formInputs.lastName.value
            },
            inviteCode: this.formInputs.inviteCode.value,
            yearOfBirth: this.formInputs.yearOfBirth.value,
            zipcode: this.formInputs.zipcode.value
          })
            .then(function( response ) {
              this.router.go( "/onboarding/like" );
              return response;
            }.bind( this ))
            .catch(function( error ) {
              error.invalidFields.forEach(function( invalidField ) {
                if ( invalidField.name === "inviteCode" ) {
                  this.errorDivs.inviteCode.classList.remove( "hidden" );
                  this.formInputs.inviteCode.classList.add( "invalid" );
                } else if ( invalidField.name === "email" ) {
                  this.errorDivs.emailTaken.classList.remove( "hidden" );
                  this.formInputs.email.classList.add( "invalid" );
                }
              }, this );
              this.logo.scrollIntoView();
              return error;
            }.bind( this ));
        }
      });
    });
})( window.Polymer, window.System );
