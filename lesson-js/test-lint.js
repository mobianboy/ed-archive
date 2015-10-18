/**
 * Created by rj on 7/9/15.
 */

'use strict';

var
  ready, fn,
  hello = 'world',
  pack = require( 'package.json' );

fn = function( are, you ) {
  return ready = are + you; // eslint-disable-line no-return-assign
};

// sup?

console.log( fn( hello, pack.name ));

console.log( ready );
