/*jshint strict:false */
/**
 * Created by rj on 11/24/14.
 */
var
  hello = ( name ) => {
    console.log( "Hello " + name );
    return name;
  },
  goodbye = ( name ) => {
    console.log( "Goodbye " + name );
  };

System.map[ "hello-goodbye" ] = "domain/hello-goodbye";

export { hello, goodbye };

export default {
  get hello() { return hello; },
  get goodbye() { return goodbye; }
};
