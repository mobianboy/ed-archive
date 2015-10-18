/*jshint strict: false*/

import define from "domain/ed/define-properties";
import EDModel from "domain/ed/objects/EDModel";

/*
Current possible badges
Fan
  1 Iron Ears --  competed-listens-fan
  2 Star Lord --  most-tracks-rated-fan

Artist
  3 Most Completed Plays  --  completed-listens-track
  4 Highest Rated Song    --  highest-rated-track
 */

var
  idToName = function( id ) {
    switch( id ) {
      case 1:
        return "Iron Ears";
      case 2:
        return "Star Lord";
      case 3:
        return "Most Completed Plays";
      case 4:
        return "Highest Rated Song";
      default:
        return "";
    }
  },
  idToIconName = function( id ) {
    var iconName = "badge-";

    switch( id ) {
      case 1:
        iconName += "iron-ears";
        break;
      case 2:
        iconName += "star-lord";
        break;
      case 3:
        iconName += "artist-mcl";
        break;
      case 4:
        iconName += "hrs";
        break;
      default:
        iconName += "empty";
        break;
    }

    return iconName;
  };

export default class EDBadge extends EDModel {
  static get MODEL_TYPE() {
    return "badge";
  }

  constructor( args ) {

    args.type = args.type || EDBadge.MODEL_TYPE;
    args.name = args.name || idToName( args.id );
    args.iconName = args.iconName || idToIconName( args.id );

    super( args );

    // TODO where does "dateAquired" go?

    define.enumReadOnly( this, [
      "name",
      "iconName",
      "count",
      "badgeType"
    ], args );
  }
}
