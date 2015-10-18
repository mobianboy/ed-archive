/*jshint strict: false*/

import EventEmitter from "domain/lib/event/EventEmitter";
import createEvent from "domain/lib/event/create-event";
import typeChecker from "domain/ed/objects/model-type-checker";
import edDataService, { updateModel } from "domain/ed/services/ed-data-service";
import edConnectionService from "domain/ed/services/ed-connection-service";
import EDUser from "domain/ed/objects/EDUser";
import EDFan from "domain/ed/objects/profile/EDFan";
import edAnalytics from "domain/ed/analytics/ed-analytics-service";
import url from "domain/ed/urls";

var
  edUserService = new EventEmitter([ "edLogin", "edLogout" ]),
  currentProfile = null,
  currentUserId = null,
  isOpenSession = false,
  hasOnboarded = false,
  sessionAuthJSON = null,
  loggedInDate = null,
  currentProfileObserver,
  setUpObserver,
  teardownObserver,
  referralsRemaining,
  saveCredentialsInLocalStorage,
  clearCredentialsFromLocalStorage;

Object.defineProperties( edUserService, {
  currentProfile: {
    configurable: false,
    enumerable: false,
    get: function() {
      return currentProfile;
    }
  },
  currentUserId: {
    configurable: false,
    enumerable: false,
    get: function() {
      return currentUserId;
    }
  },
  isOpenSession: {
    configurable: false,
    enumerable: false,
    get: function() {
      return isOpenSession;
    }
  },
  hasOnboarded: {
    configurable: false,
    enumerable: false,
    get: function() {
      return hasOnboarded;
    }
  },
  sessionAuthJSON: {
    configurable: false,
    enumerable: false,
    get: function() {
      return sessionAuthJSON;
    }
  },
  referralsRemaining: {
    configurable: false,
    enumberable: false,
    get: function() {
      return referralsRemaining;
    }
  },
  sessionDuration: {
    configurable: false,
    enumberable: false,
    get: function() {
      var now = new Date();

      if ( loggedInDate == null ) {
        return 0;
      }

      return now - loggedInDate;
    }
  }
});

// to watch for changes to current logged in profile
currentProfileObserver = function( changeList ) {
  changeList.forEach( change => {
    if ( change.type === "set" && ( currentProfile == null || currentProfile.id == change.object.id )) {
      currentProfile = change.object;
    }

    /* TODO Maybe?
    if ( change.type === "remove" ) {
      // force current profile back into the lru?
    }
    */
  });
};

setUpObserver = function( id ) {
  try {
    edDataService.observeProfile( id, currentProfileObserver, [ "set" ]);
  } catch ( error ) {
    console.warn( "Error setting up profile observer in user service" );
    console.error( error.stack );
  }
};

teardownObserver = function( id ) {
  try {
    edDataService.unobserveProfile( id, currentProfileObserver );
  } catch ( error ) {
    console.warn( "Error tearing down profile observer in user service" );
    console.error( error.stack );
  }
};


saveCredentialsInLocalStorage = function( credentialMap ) {
  if ( localStorage ) {
    localStorage.setItem( "edLoginInfo", JSON.stringify( credentialMap ));
  }
};

clearCredentialsFromLocalStorage = function() {
  if ( localStorage ) {
    localStorage.removeItem( "edLoginInfo" );
  }
};

// todo remove
window.edUserService = edUserService;

/**
 * @method getReferrals
 */
edUserService.getReferrals = function() {
  return edConnectionService.request( "referral/get", 10 )
    .then( edJson => {
      if ( edJson.isSuccessful && edJson.hasProperty( "data.referralsRemaining" )) {
        referralsRemaining = edJson.data.referralsRemaining;
      }

      return referralsRemaining;
    })
    .catch( error => {
      console.warn( "Error on referral get: " + error.message );
      // console.stack( error.stack );

      referralsRemaining = 0;
      return referralsRemaining;
    });
};

/**
 * @method login
 */
edUserService.login = function( email, password ) {
  var
    json = {
      auth: {
        email,
        password
      }
    };

  // conn service auth method returns only the "data" block of the response
  return edConnectionService.authenticateConnection( email, password )
    .then( raw => {
      currentUserId = raw.userId;
      hasOnboarded = raw.onboarded;
      return edDataService.getProfileById( raw.profileId );
    })
    .then( edProfile => {
      currentProfile = edProfile;
      isOpenSession = true;
      sessionAuthJSON = json.auth;
      loggedInDate = new Date();

      edUserService.dispatch(
        createEvent( "edLogin", {
          detail: {
            userId: currentUserId,
            profile: currentProfile,
            onboarded: hasOnboarded
          }
        })
      );

      // save for login on app restart
      saveCredentialsInLocalStorage( json.auth );

      // observe for changes
      setUpObserver( edProfile.id );

      return edUserService.getReferrals()
        .then(function() {
          // analytics
          edAnalytics.send( "login", {
            time: ( new Date() ).toISOString()
          });

          return currentProfile;
        })
        .catch( error => {
          console.warn( "Error with referral or analytics:login during user-service login: " + error.message );
          console.error( error.stack );
          return currentProfile;
        });
    })
    .catch( error => {
      if ( currentProfile != null && "id" in currentProfile ) {
        teardownObserver( currentProfile.id );
      }

      currentProfile = null;
      currentUserId = null;
      isOpenSession = false;
      hasOnboarded = false;
      referralsRemaining = 0;

      console.warn( "this person was unable to login: " + error.message );
      console.error( error.stack );
    });
};

/**
 * @method restoreSession
 */
edUserService.restoreSession = function() {
  var
    localStorage = window.localStorage,
    loginData;

  if ( localStorage ) {
    loginData = localStorage.getItem( "edLoginInfo" );

    if ( loginData ) {
      loginData = JSON.parse( loginData );

      if ( loginData.password && loginData.email ) {
        return edUserService.login( loginData.email, loginData.password )
          .catch(function( error ) {
            clearCredentialsFromLocalStorage();
            throw error;
          });
      }
    }
  }

  return Promise.reject( new Error( "Could not restore Session" ));
};

/**
 * @method logout
 */
edUserService.logout = function() {
  // Don't allow auto login on app restart
  clearCredentialsFromLocalStorage();

  edUserService.dispatch( createEvent( "edLogout", {
    detail: {
      userId: currentUserId,
      profile: currentProfile
    }
  }));

  edAnalytics.send( "logout", {
    time: ( new Date() ).toISOString()
  });

  if ( currentProfile != null && "id" in currentProfile ) {
    teardownObserver( currentProfile.id );
  }

  currentProfile = null;
  currentUserId = null;
  isOpenSession = false;
  sessionAuthJSON = null;
  loggedInDate = null;
  referralsRemaining = 0;

  edConnectionService.deauthenticateSocket();

  return true;
};

/**
 * @method changeProfileImage
 */
edUserService.changeProfileImage = function( image, fileData ) {
  var imageData = fileData != null ? fileData : image;

  return edConnectionService.request( "aws/token/get", 10 )
    .then( edJson => {
      var
        json,
        s3 = new AWS.S3({
          accessKeyId: edJson.data.id,
          secretAccessKey: edJson.data.key,
          region: edJson.data.region
        });

      return new Promise(( resolve, reject ) => {
        s3.upload({
          Key: "public/temp/" + currentProfile.id + "/" + imageData.name,
          ContentType: imageData.type,
          Body: image,
          Bucket: edJson.data.bucketName,
          CopySource: edJson.data.bucketName + "/" + imageData.name
        }, function( error, data ) {
          if ( error != null ) {
            console.warn( "Issue uploading image to AWS" );
            reject( error );
            return;
          }

          json = {
            data: {
              profileId: currentProfile.id,
              title: imageData.name,
              url: data.Location,
              type: "avatar"
            }
          };

          return resolve(
            edConnectionService.request( "profile/art/create", 10, json )
              .then(() => {
                return edDataService.getProfileById( currentProfile.id, 10, true );
              })
          );
        }); // end S3 callback
      })
      .catch(function( error ) {
        console.warn( "image upload was not successfully completed: " + error.message );
        console.error( error.stack );
        throw error;
      });
    });
};

/**
 * @method referral
 */
edUserService.referral = function( email ) {
  var json = {
    data: {
      // todo
      //userId: currentUserId,
      userId: parseInt( currentUserId, 10 ),
      email
    }
  };

  if ( referralsRemaining === 0 ) {
    return Promise.reject( new Error( "This user has used up all of their referrals" ) );
  }

  return edConnectionService.request( "referral/create", 10, json )
    .then( edJson => {
      if ( edJson.isSuccessful && edJson.hasData ) {
        edAnalytics.send( "invite", {
          // todo doesn't return invite code that is created by server
          code: edJson.data.inviteCode,
          recipient: edJson.data.email
        });

        referralsRemaining = edJson.data.referralsRemaining;
      }

      return referralsRemaining;
    })
    .catch( error => {
      console.warn( "referral email was not successfully sent: " + error.message );
      console.error( error.stack );
      throw error;
    });
};

/**
 * @method register
 */
edUserService.register = function( args ) {
  var
    json = {
      data: args
    };

  return edConnectionService.request( "user/create", 10, json )
    .then( edJson => {
      // validate response
      if ( edJson.isSuccessful && edJson.hasProperties( "data.id", "data.inviteCode" )) {
        console.log( "response validated %o", edJson );

        edAnalytics.send( "register", {
          code: edJson.data.inviteCode
        });

        return edJson;
      }

      if ( edJson.hasStatusCode( 10 )) {
        console.log( "response on error", edJson );
        let tError = new TypeError( "Problem with Registration" );

        if ( edJson.hasProperty( "meta.invalidFields" )) {
          tError.invalidFields = edJson.meta.invalidFields;
        }
        throw tError;
      }

      return edJson;
    })
    .catch( error => {
      console.warn( "Error registering new user in User Service: " + error.message );
      console.error( error.stack );
      throw error;
    })
    .then(() => {
      return edUserService.login( args.email, args.password );
    });
};

/**
 * @method requestPasswordReset
 */
edUserService.requestPasswordReset = function( email ) {
  var json = {
    data: {
      email
    }
  };

  return edConnectionService.request( "user/password/get", 10, json )
    .then( edJson => {
      if ( !edJson.isSuccessful ) {
        console.warn( "non-successful status code in user/password/get response: %o", edJson );
      }

      return edJson;
    })
    .catch( error => {
      console.warn( "Forgot password email was not successfully sent: " + error.message );
      console.error( error.stack );
      throw error;
    });
};

/**
 * @method resetPassword
 */
edUserService.resetPassword = function( resetCode, password ) {
  var json = {
    data: {
      resetCode,
      password
    }
  };

  return edConnectionService.request( "user/password/set", 10, json )
    .then( edJson => {
      if ( edJson.isSuccessful ) {
        console.log( "successful password/set: %o", edJson );
      }

      // TODO the code for this will be 11
      if ( edJson.hasStatusCode( 11 ) || edJson.hasStatusCode( 10 ) ) {
        let resetError = new Error( "Problem with Resetting the Password" );
        resetError.response = edJson.toJSON();
        throw resetError;
      }

      return edJson;
    })
    .catch( error => {
      console.warn( "new password was not successfully set: " + error.message );
      console.error( error.stack );
      throw error;
    })
    .then( edJson => {
      return edUserService.login( edJson.data.email, json.data.password );
    });
};

/**
 * @method editProfile
 */
edUserService.editProfile = function( args ) {
  var json = {};

  json.data = Object.assign({
    id: currentProfile == null ? null : currentProfile.id
  }, args );

  return edConnectionService.request( "profile/set", 10, json )
    .then( edJson => {
      return updateModel( edJson );
    })
    .catch( error => {
      console.warn( "profile update was not successfully sent" );
      console.error( error.stack );
      throw error;
    });
};

/**
 * @method observeCurrentProfile
 */
edUserService.observeCurrentProfile = function( callback, acceptList=null ) {
  if ( currentProfile == null || !( "id" in currentProfile ) ) {
    throw new TypeError( "Current Profile is not set, can not observe something that doesn't exist" );
  }

  edDataService.observeProfile( currentProfile.id, callback, acceptList );
};

/**
 * @method unobserveCurrentProfile
 * @param callback
 */
edUserService.unobserveCurrentProfile = function( callback ) {
  if ( currentProfile == null || !( "id" in currentProfile ) ) {
    throw new TypeError( "Current Profile is not set, can not unobserve something that doesn't exist" );
  }

  edDataService.unobserveProfile( currentProfile.id, callback );
};

/**
 * @method getStats
 */
edUserService.getStats = function() {
  return edConnectionService.request( "user/stats/get", 10 )
    .then( edJson => {
      return edJson.data.stats;
    })
    .catch( error => {
      console.warn( "error retrieving user stats in user service: " + error.message );
      console.error( error.stack );
      throw error;
    });
};

export default edUserService;
