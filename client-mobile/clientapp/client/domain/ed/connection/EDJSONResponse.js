
import EDJSON, { symbols } from "domain/ed/connection/EDJSON";

var json = symbols.json;

// Server -> Client (EDJSONResponse)
//    status
//    meta
//    data
//    messages
//    followUp

export default class EDJSONResponse extends EDJSON {
  constructor( jsonBody={} ) {
    super( jsonBody );
  }

  // inherits
  //  hasData
  //  hasMeta
  //  data getter
  //  meta getter

  get hasStatus() {
    return "status" in this[ json ] && this[ json ].status != null;
  }

  get hasMessages() {
    return "messages" in this[ json ] && this[ json ].messages != null;
  }

  get hasFollowUp() {
    return "followUp" in this[ json ] && this[ json ].followUp != null;
  }

  hasStatusCode( code ) {
    return this.hasStatus && this[ json ].status.code === code;
  }

  get status() {
    return this[ json ].status;
  }

  get messages() {
    return this[ json ].messages;
  }

  get followUp() {
    return this[ json ].followUp;
  }

  get isSuccessful() {
    if ( !this.hasStatus ) {
      return false;
    }

    if ( this.status.code == null ) {
      return false;
    }

    return 0 < this.status.code && this.status.code < 10;
  }
}
