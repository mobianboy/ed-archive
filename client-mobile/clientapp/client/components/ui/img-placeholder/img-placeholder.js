( function( polymer ) {
  "use strict";

  var
    globalMap = {},
    updateMap = function() {
      Array.prototype.forEach.call(
        document.getElementsByTagName( "img-placeholder" ),
        function( element ) {
          element.rePrep();
          return true;
        }
      );
    };

  globalMap = (function() {
    var urlPrefix = "assets/placeholder-images/avatar-",
      urlPostfix = ".svg",
      map = {},
      imageNames = [
        "artist",
        "fan"
      ];

    imageNames.forEach(function( imageName ) {
      map[imageName] = urlPrefix + imageName + urlPostfix;
    });
    return map;
  })();

  polymer( "img-placeholder", {
    published: {
      image: {
        reflect: true
      }
    },
    get imageMap() {
      return globalMap;
    },
    set imageMap( value ) {
      globalMap = value;
      updateMap();
      return globalMap;
    },
    rePrep: function() {
      if ( !this.src ) {
        this.fallBack.src = this.src || globalMap[ this.image ];
      }
    },
    ready: function() {
      this.fallBack = this.shadowRoot.querySelector( ".fall-back" );
    },
    attached: function() {
      this.updateSrc();
    },
    imageChanged: function( oldVal, newVal ) {
      this.updateSrc().setAttribute( "image", newVal );
    },
    updateSrc: function() {
      if ( this.src ) {
        this.checkSrc( this.src ).then( function( result ) {
          this.fallBack.src = result ? this.src : globalMap[ this.image ];
        }.bind( this ) );
      }
      return this;
    },
    checkSrc: function( src ) {
      return new Promise( function( resolve ) {
        var tmpImage = new Image();
        tmpImage.onload = function( event ) {
          resolve( true );
        };
        tmpImage.onerror = function( event ) {
          resolve( false );
        };
        tmpImage.src = src;
      });
    }
  });
})( window.Polymer );
