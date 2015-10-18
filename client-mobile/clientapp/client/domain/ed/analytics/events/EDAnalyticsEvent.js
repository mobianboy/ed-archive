import define from "domain/ed/define-properties";

export default class EDAnalyticsEvent {
  constructor( args ) {
    // Any top level props could be added here...
    // define.enumReadOnly( this, [], args );
  }

  get type() {
    return this.constructor.TYPE;
  }

  get values() {
    return this.valueKeys.reduce(( json, currKey ) => {
      json[ currKey ] = this[ currKey ];
      return json;
    }, {});
  }

  get eventBlock() {
    return {
      type: this.type,
      values: this.values
    };
  }
}
