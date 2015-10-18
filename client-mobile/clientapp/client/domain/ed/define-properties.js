/*eslint consistent-this: 0 */
var readOnly = function( value ) {
    value = typeof value === "undefined" ? null : value;

    return {
      configurable: false,
      enumerable: false,
      writeable: false,
      value
    };
  },
  readOnlyFreezeObjects = function( value ) {
    value = typeof value === "object" ? Object.freeze( value ) : value;
    value = typeof value === "undefined" ? null : value;
    return readOnly( value );
  },
  enumRO = function( value ) {
    value = typeof value === "undefined" ? null : value;

    return {
      configurable: false,
      enumerable: true,
      writeable: false,
      value
    };
  },
  enumROFreezeObjects = function( value ) {
    value = typeof value === "object" ? Object.freeze( value ) : value;
    value = typeof value === "undefined" ? null : value;
    return enumRO( value );
  },
  configRO = function( value ) {
    value = typeof value === "undefined" ? null : value;

    return {
      configurable: true,
      enumerable: false,
      writeable: false,
      value
    };
  },
  configEnumRO = function( value ) {
    value = typeof value === "undefined" ? null : value;

    return {
      configurable: true,
      enumerable: true,
      writeable: false,
      value
    };
  },
  defineViaReduceWithFunction = function( self, keys, valueMap, configCreator ) {
    Object.defineProperties( self, keys.reduce(( props, key ) => {
      props[ key ] = configCreator( valueMap[ key ] );
      return props;
    }, {}));
  };

export default {
  readOnly( self, keys, valueMap ) {
    defineViaReduceWithFunction( self, keys, valueMap, readOnly );
    /*
    Object.defineProperties( self, keys.reduce(( props, key ) => {
      props[ key ] = readOnly( valueMap[ key ] );
      return props;
    }, {}));
    */
  },
  readOnlyDeep( self, keys, valueMap ) {
    defineViaReduceWithFunction( self, keys, valueMap, readOnlyFreezeObjects );
  },
  enumReadOnly( self, keys, valueMap ) {
    defineViaReduceWithFunction( self, keys, valueMap, enumRO );
  },
  enumReadOnlyDeep( self, keys, valueMap ) {
    defineViaReduceWithFunction( self, keys, valueMap, enumROFreezeObjects );
  },
  configReadOnly( self, keys, valueMap ) {
    defineViaReduceWithFunction( self, keys, valueMap, configRO );
  },
  configEnumReadOnly( self, keys, valueMap ) {
    defineViaReduceWithFunction( self, keys, valueMap, configEnumRO );
  }
};
