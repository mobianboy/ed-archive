/*jshint strict: false*/

import define from "domain/ed/define-properties";

import EDModel from "domain/ed/objects/EDModel";

import EDProfile from "domain/ed/objects/profile/EDProfile";
import EDFan from "domain/ed/objects/profile/EDFan";
import EDArtist from "domain/ed/objects/profile/EDArtist";
import EDTrack from "domain/ed/objects/media/EDTrack";

import EDCollection from "domain/ed/storage/EDCollection";

var
  profileTypeForChartName = function( chartName ) {
    if ( ( /fan$/ ).test( chartName ) ) {
      return EDFan.MODEL_TYPE;
    } else if ( ( /artist$/ ).test( chartName ) ) {
      return EDArtist.MODEL_TYPE;
    } else if ( ( /track$/ ).test( chartName ) ) {
      return EDTrack.MODEL_TYPE;
    }
    return EDProfile.MODEL_TYPE;
  },
  minutes = 1000 * 60,
  hours = 1000 * 60 * 60,
  days = 1000 * 60 * 60 * 24;

export default class EDChart extends EDModel {
  static get MODEL_TYPE() {
    return "chart";
  }

  constructor( args ) {
    args.id = null;
    args.type = null;

    if ( args.dateEnds ) {
      args.dateEnds = new Date( args.dateEnds );
    }

    super( args );

    define.enumReadOnly( this, [
      "chartName",
      "dateEnds"
    ], args );

    define.enumReadOnlyDeep( this, [ "leaderboard" ], args );

    this.leaderboardCollection = new EDCollection(
      profileTypeForChartName( args.chartName ),
      args.leaderboard.map( value => value.id )
    );
  }

  get timeRemaining() {
    var
      now = Date.now(),
      timeLeft = this.dateEnds - now,
      daysLeft = Math.floor( timeLeft / days ),
      hoursLeft = Math.floor( timeLeft % days / hours ),
      minutesLeft = Math.floor( timeLeft % hours / minutes );

    return `${daysLeft}d ${hoursLeft}h ${minutesLeft}m`;
  }

  getRankForId( id ) {
    for ( let i = 0 ; i < this.leaderboard.length ; i++ ) {
      if ( this.leaderboard[ i ].id === id ) {
        return this.leaderboard[ i ].value;
      }
    }

    return -1;
  }
}
