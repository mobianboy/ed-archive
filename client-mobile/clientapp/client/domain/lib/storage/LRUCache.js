/* jshint strict: false */
var size = Symbol( "size" ),
  tail = Symbol( "tail" ),
  head = Symbol( "head" ),
  keyMap = Symbol( "keyMap" );

export var symbols = {
  get size() {
    return size;
  },
  get tail() {
    return tail;
  },
  get head() {
    return head;
  },
  get keyMap() {
    return keyMap;
  }
};

export class LRUNode {
  constructor( key, data, newer, older ) {
    this.key = key;
    this.data = data;
    this.newer = newer;
    this.older = older;
  }
}

/**
 * @class LRUCache
 */
export default class LRUCache {
  constructor( limit ) {
    Object.defineProperty( this, "limit", {
      configurable: false,
      enumerable: false,
      writable: false,
      value: limit
    });

    this[ keyMap ] = {};
    this[ size ] = 0;
    this[ tail ] = null;
    this[ head ] = null;
  }

  get size() {
    return this[ size ];
  }

  set( key, data ) {
    var newNode = new LRUNode( key, data, null, null );

    if ( this.has( key ) ) {
      this[ keyMap ][ key ].data = data;
      this.get( key );
      return null;
    }

    this[ keyMap ][ key ] = newNode;

    if ( this[ tail ] ) {
      this[ tail ].newer = newNode;
      newNode.older = this[ tail ];
    } else {
      this[ head ] = newNode;
    }

    this[ tail ] = newNode;
    this[ size ] += 1;

    if ( this[ size ] > this.limit ) {
      return this.shift();
    }

    return null;
  }

  get( key ) {
    var node = this[ keyMap ][ key ];

    if ( node == null ) {
      return null;
    }

    if ( node.newer ) {
      if ( node === this[ head ] ) {
        this[ head ] = node.newer;
      }
      node.newer.older = node.older;
    }

    if ( node.older ) {
      node.older.newer = node.newer;
    }

    node.newer = null;
    node.older = this[ tail ];

    if ( this[ tail ] ) {
      this[ tail ].newer = node;
    }

    this[ tail ] = node;
    return node.data;
  }

  peek( key ) {
    return this[ keyMap ].hasOwnProperty( key ) ? this[ keyMap ][ key ].data : null;
  }

  has( key ) {
    return this[ keyMap ].hasOwnProperty( key );
  }

  shift() {
    var oldHead = this[ head ];

    if ( oldHead && oldHead.newer ) {
      this[ head ] = this[ head ].newer;
      this[ head ].older = null;
    } else {
      this[ head ] = null;
    }

    if ( oldHead != null ) {
      delete this[ keyMap ][ oldHead.key ];
      this[ size ] -= 1;
      return oldHead.data;
    }

    return null;
  }

  remove( key ) {
    var toRemove = this[ keyMap ][ key ];

    if ( toRemove == null ) {
      return null;
    }

    delete this[ keyMap ][ toRemove.key ];

    if ( toRemove.newer && toRemove.older ) {
      toRemove.older.newer = toRemove.newer;
      toRemove.newer.older = toRemove.older;
    } else if ( toRemove.newer ) {
      toRemove.newer.older = null;
      this[ head ] = toRemove.newer;
    } else if ( toRemove.older ) {
      toRemove.older.newer = null;
      this[ tail ] = toRemove.older;
    } else {
      this[ head ] = this[ tail ] = null;
    }

    this[ size ] -= 1;
    return toRemove.data;
  }

  clear() {
    this[ head ] = this[ tail ] = null;
    this[ size ] = 0;
    this[ keyMap ] = {};
  }

  keys() {
    var keys = [],
      key;

    if ( typeof Object.keys === "function" ) {
      return Object.keys( this[ keyMap ] );
    }

    for ( key in this[ keyMap ] ) {
      if ( this[ keyMap ].hasOwnProperty( key ) ) {
        keys.push( key );
      }
    }

    return keys;
  }

  forEach( fn, context=undefined, startAtTail=false ) {
    if ( typeof context === "boolean" ) {
      startAtTail = context;
      context = undefined;
    }

    for ( let [ key, data ] of startAtTail ? this.reverseOf : this ) {
      fn.call( context, key, data, this );
    }
  }

  toArray() {
    var s = [];

    for ( let [ key, data ] of this ) {
      s.push({
        key,
        data
      });
    }

    return s;
  }

  toString() {
    var str = "";

    for ( let [ key, data ] of this ) {
      str += `${key}:${data}` + ( key === this[ tail ].key ? "" : " < " );
    }

    return str;
  }

  * [ Symbol.iterator ]() {
    var currentNode = this[ head ];

    while ( currentNode ) {
      yield [ currentNode.key, currentNode.data ];
      currentNode = currentNode.newer;
    }
  }

  * reverse() {
    var currentNode = this[ tail ];

    while ( currentNode ) {
      yield [ currentNode.key, currentNode.data ];
      currentNode = currentNode.older;
    }
  }

  get reverseOf() {
    var rtn = {};
    rtn[ Symbol.iterator ] = this.reverse.bind( this );

    return rtn;
  }

  /*
  get [ Symbol.toStringTag ]() {
    return "LRUCache";
  }
  */
}
