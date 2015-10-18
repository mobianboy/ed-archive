(function( ready, ownerDocument ) {
  "use strict";

  ready.then(function() {
    var
      elementName = "ed-splash",
      EDSplash,
      prototype = Object.create( HTMLElement.prototype ),
      template = ownerDocument.querySelector( `#tpl-${elementName}` ),
      transitionEnd = function( self ) {
        return function() {
          self.style.display = "none";
        };
      };

    prototype.createdCallback = function() {
      var
        clone = document.importNode( template.content, true );

      this.appendChild( clone );

      this.addEventListener( "transitionend", transitionEnd( this ));
      this.addEventListener( "webkitTransitionEnd", transitionEnd( this ));
    };
//    prototype.attachedCallback = function() {};
//    prototype.detachedCallback = function() {};
//    prototype.attributeChangedCallback = function() {};

    prototype.show = function() {
      this.classList.remove( "hidden" );
    };

    prototype.hide = function() {
      this.classList.add( "hidden" );
    };

    EDSplash = document.registerElement( elementName, { prototype });

//    console.log( "constructor %o", EDSplash );
//    console.log( "constructor.proto %o", EDSplash.prototype );
//    console.log( "instance %o", new EDSplash() );
  });
})( window.webComponentsReadyPromise, document._currentScript.ownerDocument || document.currentScript.ownerDocument );
