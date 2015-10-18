#!/usr/bin/env node

var servers = require( "./static.js" ),
  runDev = process.argv.some(function( v ) {
    "use strict";
    return v === "dev";
  });

if ( runDev ) {
  servers.startDev();
  console.log( "Dev Server Started" );
} else {
  servers.startProd();
  console.log( "Prod Server Started" );
}
