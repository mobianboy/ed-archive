var tmpEvent, createEvent,
  createWithConstructor = function( name, descriptor ) {
    return new CustomEvent( name, descriptor );
  },
  createWithInit = function( name, descriptor={} ) {
    var event = document.createEvent( "CustomEvent" ),
      { bubbles, cancelable, detail } = descriptor;

    event.initCustomEvent(
      name,
      typeof bubbles === "boolean" ? bubbles : true,
      typeof cancelable === "boolean" ? cancelable : true,
      detail || {}
    );

    return event;
  };

if ( window ) {
  try {
    tmpEvent = new CustomEvent( "test" );
    createEvent = createWithConstructor;
  } catch ( error ) {
    // use fallback method
    createEvent = createWithInit;
  }
}

// check if supported via try/catch
export default createEvent;
