
import EDJSON, { symbols } from "domain/ed/connection/EDJSON";

var json = symbols.json;

// Client -> Server (EDJSONRequest)
//    action
//    auth
//    data
//    meta
//    analytics
//      common
//      event

// TODO: This class is somewhat incomplete
// needs methods for adding data and other various properties
export default class EDJSONRequest extends EDJSON {
  constructor( jsonBody={} ) {
    super( jsonBody );
  }

  get action() {
    return this[ json ].action;
  }

  get route() {
    if ( this[ json ].action == null ) {
      return null;
    }

    return this[ json ].action.route;
  }

  set route( value ) {
    if ( this[ json ].action == null ) {
      this[ json ].action = {};
    }

    this[ json ].action.route = value;
    return value;
  }

  get priority() {
    if ( this[ json ].action == null ) {
      return null;
    }

    return this[ json ].action.priority;
  }

  set priority( value ) {
    if ( this[ json ].action == null ) {
      this[ json ].action = {};
    }

    this[ json ].action.priority = value;
    return value;
  }

  get hasAction() {
    return "action" in this[ json ] && this[ json ].action != null;
  }
}
