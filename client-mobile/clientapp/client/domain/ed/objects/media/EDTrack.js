/*jshint strict: false*/
import define from "domain/ed/define-properties";
import EDMedia from "domain/ed/objects/media/EDMedia";

//import edDataService from "domain/ed/services/ed-data-service";
//import edConnectionService from "domain/ed/services/ed-connection-service";
//import edUserService from "domain/ed/services/ed-user-service";

var
  edDataService,
  edConnectionService,
  edUserService,
  importDataService,
  importConnectionService,
  importUserService;

/**
 * The Imports here were causing a circular dependency that was screwing
 * up the creation of the model-type-checker module. To solve this issue
 * for now I'm lazy importing the required services for this model class.
 */
importDataService = function() {
  return System.import( "domain/ed/services/ed-data-service" )
    .then( imported => {
      edDataService = imported.default;
      return edDataService;
    });
};

importConnectionService = function() {
  return System.import( "domain/ed/services/ed-connection-service" )
    .then( imported => {
      edConnectionService = imported.default;
      return edConnectionService;
    });
};

importUserService = function() {
  return System.import( "domain/ed/services/ed-user-service" )
    .then( imported => {
      edUserService = imported.default;
      return edUserService;
    });
};

export default class EDTrack extends EDMedia {
  static get MODEL_TYPE() {
    return EDMedia.MODEL_TYPE + "-track";
  }

  constructor( args ) {
    super( args );

    define.enumReadOnly( this, [
      "length",
      "rating",
      "format",
      "bitrate",
      "encoding"
//      "averageRating",
//      "createdBy",
//      "playCount",
//      "waveformImage"
    ], args );
  }

  hasBeenRated() {
    return this.rating !== -1;
  }

  getArtist() {
    if ( edDataService == null ) {
      return importDataService()
        .then(() => {
          return this.getArtist();
        });
    }

    return edDataService.getArtistById( this.profileId );
  }

  // TODO figure this one out if needed
  getCreator() {
    if ( edDataService == null ) {
      return importDataService()
        .then(() => {
          return this.getCreator();
        });
    }

    return edDataService.getByTypeAndId( "profile", this.createdBy );
  }

  getUrl() {
    if ( edConnectionService == null ) {
      return importConnectionService()
        .then(() => {
          return this.getUrl();
        });
    }

    return edConnectionService.request( "track/url/get", 10, {
      data: {
        id: this.id,
        format: "original"
      }
    });
  }

  rate( rating ) {
    if ( edConnectionService == null ) {
      return importConnectionService()
        .then(() => {
          return this.rate( rating );
        });
    } else if ( edUserService == null ) {
      return importUserService()
        .then(() => {
          return this.rate( rating );
        });
    }

    return edConnectionService.request( "track/rate/set", 10, {
      data: {
        profileId: edUserService.currentProfile.id,
        trackId: this.id,
        rating
      }
    });
  }
}

