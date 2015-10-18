/*jshint strict: false*/
// Base EDModel
import EDModel from "domain/ed/objects/EDModel";

// Parent Classes
import EDProfile from "domain/ed/objects/profile/EDProfile";
import EDMedia from "domain/ed/objects/media/EDMedia";
import EDUser from "domain/ed/objects/EDUser";
// Misc Types
import EDBadge from "domain/ed/objects/EDBadge";
import EDGenre from "domain/ed/objects/EDGenre";
import EDChart from "domain/ed/objects/EDChart";

// Profile Child Types
import EDArtist from "domain/ed/objects/profile/EDArtist";
import EDArtistGroup from "domain/ed/objects/profile/EDArtistGroup";
import EDFan from "domain/ed/objects/profile/EDFan";

// Media Child Types
import EDTrack from "domain/ed/objects/media/EDTrack";

var
  constructorMap = [
    EDModel,
    EDUser,
    EDProfile,
    EDArtist,
    EDArtistGroup,
    EDFan,
    EDMedia,
    EDTrack,
    EDBadge,
    EDGenre,
    EDChart
  ].reduce(function( map, currentModel, index ) {
//    console.log( "current("+index+"): %o", currentModel.MODEL_TYPE );
    map[ currentModel.MODEL_TYPE ] = currentModel;
    return map;
  }, {});

export default {
  get constructorMap() {
    return constructorMap;
  },
  hasValidType( object ) {
    if ( typeof object.modelType === "string" ) {
      return this.isValidType( object.modelType );
    }

    return false;
  },
  isValidType( modelType ) {
    return modelType in constructorMap;
  },
  checkForInstanceOfType( modelType, object ) {
    if ( !( "modelType" in object ) ) {
      throw new TypeError( "No model type found in object to check against" );
    }

    if ( modelType === object.modelType ) {
      return true;
    }

    if ( !( modelType in constructorMap ) ) {
      throw new TypeError( `Given model type is not a valid EDModel model type: ${modelType}` );
    }

    if ( !( object.modelType in constructorMap ) ) {
      throw new TypeError( `Given object with model type, ${object.modelType} is not a valid EDModel model type` );
    }

    return constructorMap[ modelType ].isPrototypeOf( constructorMap[ object.modelType ] );
  },
  fuzzyMatch( type, object ) {
    if ( object == null || typeof object.modelType !== "string" ) {
      throw new TypeError( "Object type is not a string" );
    }

    return object.modelType.indexOf( type ) > -1;
  },
  isProfileType( object ) {
    return this.checkForInstanceOfType( EDProfile.MODEL_TYPE, object );
  },
  isFan( object ) {
    return this.checkForInstanceOfType( EDFan.MODEL_TYPE, object );
  },
  isArtist( object ) {
    return this.checkForInstanceOfType( EDArtist.MODEL_TYPE, object );
  },
  isMediaType( object ) {
    return this.checkForInstanceOfType( EDMedia.MODEL_TYPE, object );
  },
  isGenreType( object ) {
    return this.checkForInstanceOfType( EDGenre.MODEL_TYPE, object );
  }
};
