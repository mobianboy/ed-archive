# [CSS Premix Mixins]
The following is a list of less mixin for CSS shortcuts.


**TODOS**
- [ ] Add import information
- [ ] re-organize a little bit
- [ ] ensure proper examples
- [ ] double check for proper use cases


## Keyframes mixin
 * **.keyframes( @name:string, @rules:{ keyframe block } );**

 * **Description:** This will build a set of keyframe definitions with the given prefixes.

 Example:
 ```less
  .keyframes("my-fade-animation", {
    from {
      opacity: 0;
    }
    50% {
      opacity: 0.3;
    }
    to{
      opacity: 1;
    }
  });
 ```

## Animation property mixin
 * **.animation();**

 * **Description:** .animation mixin is shorthand for -webkit-animation and animation and use for css animations
 should be used like a native css animation property.

 * This mixin will take in the following parameters with its respective values in order
 ```sh
  @animation: <single-animation-name> || <time> || <timing-function> || <time> || <single-animation-iteration-count>
  || <single-animation-direction> || <single-animation-fill-mode> || <single-animation-play-state>
  ```

 Example:
 ```less
   .animation( my-fade-animation 3s, my-other-animation 2s 2s ; );
  ```

### Additional Animation property mixin
  Example:
  ```less
  .animation( my-fade-animation; 3s; @delay: 2s );
 ```

 * **Description:** This is an extended mixin for advance animation and -webkit-animation. This mixin requires a name and duration, but for every additional parameter there needs to be an @parameterName: value. The following is a list of usable parameters for this mixin are as follows:
 ```sh
 
  @name: a name of your custom animation
  @duration: <time> (Default is 0)
  @timingFn: linear || ease || ease-in || ease-out || ease-in-out || cubic-bezier(n,n,n,n)
  @delay: <time> (Default is 0)
  @iterationCount: <number> (Default is 1) || infinite
  @direction: normal || reverse || alternate || alternate-reverse
  @fillMode: none || forwards || backwards || both
  @playState: paused || running
  
 ```

## Column property mixin
* **.column();**

* **Description:** This mixin is used to separate content into multiple columns. This mixin needs to take in @count,
which is short for column-count, as its first parameter in order to work. Support across all browsers with the
exception of @fill which only has support for Moz, see below for details.

 Example:
 ```less
 .column( 3; @width: 200px; @ruleColor: red );
 ```

* This mixin takes in the following parameters:
```sh
 @count: <number> | auto *REQUIRED*
 @width: auto | <length>
 @ruleWidth: medium | thin | thick | <length>
 @ruleStyle: none | hidden | dotted | dashed | solid | double | groove | ridge | inset | outset
 @ruleColor:  <color>, currently set to transparent as it has no default color and takes in the current color of the
 element as default.
 @fill:  balance | auto
 @gap: <length> | normal
 @span: 1 | all
```


## Filter property mixin
 * **.filter();**

 * **Description:** .filter mixin is shorthand for -webkit-filter and filter. This mixin will take in the following
 parameters with its respective values the parameters do not have to be in order and may contain more than one
 parameter. Currently does not support for IE and Opera Mini as the native filter does not support.
 ```sh
  @filter: <none> || <blur> || <brightness> || <contrast> || <url> || <drop-shadow> || <grayscale> || <hue-rotate> ||
   <invert> || <opacity> || <sepia>
   ```

 Example:
 ```less
 .filter( grayscale(50%) contrast(20%) );
 ```

 * The following is a list of values for the parameters of .filter:
 ```sh
  blur: <number> || <percentage>
  brightness: <number> || <percentage>
  contrast: <number> || <percentage>
  url: url(svg-url#element-id)
  drop-shadow: <offset-x> <offset-y> (required) || <blur-radius> (optional) || <spread-radius> (optional) || <color>
  (optional) drop-shadow takes in numerical values with the exception of color *
  grayscale: <number> || <percentage>
  hue-rotate: <angle>
  invert: <number> || <percentage>
  opacity: <number> || <percentage>
  sepia: <number> || <percentage>
```

## Flex display property mixin
* **.flexContainer();**

* **Description:** This mixin controls the flow on content as a container and works with the .flexItem mixin. This
mixin has a default display property and needs to take in a flex-direction followed by a flex-wrap in order to work
. No support for Opera Mini

Example:
```less
.flexContainer( row nowrap; @flexDirection: column; );
```

* This mixin takes in the following parameters:
```sh
  @flexFlow: which is the equivalent of flex-direction and flex-wrap  *REQUIRED*
  @flex-direction:  row || row-reverse || column || column-reverse
  @flex-wrap: nowrap || wrap || wrap-reverse
  @justifyContent: flex-start || flex-end || center || space-between || space-around
  @alignItems: flex-start || flex-end || center || baseline || stretch
  @alignContent has no effect when there is only one line of content
  @alignContent: flex-start || flex-end || center || space-between || space-around || stretch
```


## Flex property mixin
 * **.flexItem();**

 * **Description:** This mixin controls the flow of the items inside of the .flexContainer mixin. Currently no
 support for Opera Mini due to lack of support for native flex. These are mixins that apply to items inside a flex-box container

 Example:
 ```less
  .flexItem( 1 2 auto );
 ```
 * flexItem is a mixin for flex which is shorthand for flex-grow, flex-shrink, and flex-basis in order
   ```sh
   
   @grow: <number> (default is 0)
   @shrink: <number> (default is 1)
   @basis: <number> || auto
   @alignSelf: auto || stretch || center || flex-start || flex-end || baseline
   
 ```

## Flex order property mixin
 * **.flexOrder();**

 * **Description:** This mixin controls the order of the contents inside the .flex-mixin container

 Example:
  ```sh
  .flexOrder( 1 );
  ```
 * Usage: controls the order in which items appear in the flex container
  ```sh
  
  @order: <number>
  
 ```

## Font-feature-settings Mixin
* **.fontFeature();**

* **Description:** This mixin is shorthand for fontFeature-settings which applies advance typographic and font
features to OpenType fonts. No support for Safari and Opera Mini.

Example:
 ```less
 
.fontFeature( "kern" 2 );
.fontFeature( "smcp" );

```

* This mixin takes in the following parameters:
```sh

 @feature: <string> [ <integer> | on | off ]
 
```


## Hyphen mixin
* **.hyphen();**

* **Description:** This mixin hyphenates words that are too long when it reaches a margin instead of moving the
entire word down to the next row. The word should also contain "&shy;" in order to hyphenate the word. NO SUPPORT FOR
 CHROME, OPERA, OR MOBILE BROWSERS

Example:
 ```less
 
 .hyphens( auto );
 
 ```

* This mixin takes in the following parameters:
```sh

 @hyphens: none | manual | auto
 
```


## Masks property mixin
 * **.masks();**
Example:
 ```less
  .masks( url('images/mask.png'); @repeat: no-repeat; @attach: scroll );
 ```
 * **Description:** This mixin is used in order to crop over images with another image. Currently this mixin only
 supports Webkit due to low support for other browsers will be improved upon for other browsers moving forward.
 .masks is a mixin for -webkit-mask which is shorthand for
 ```sh
 
 <mask-image> [<mask-repeat> || <mask-attachment> || <mask-position> || <mask-origin> || <mask-clip>]
 
 ```
 * This mixin will take in the following parameters with its respective values
```sh
  @image: url('somekindofimage.png')| -webkit-gradient | none
  @repeat: repeat | repeat-x | repeat-y | no-repeat | inherit
  @attach: scroll | fixed
  @position: [ <percentage>| <length>| left | center | right  ]  [ <percentage> | <length> | top  | center | bottom ]
  @origin: [padding | border | content]
  @clip: border | padding | content | text
```
## Region property mixin
 * **.flowTo(); .flowFrom(); **

 * **Description:** Using Regions allows you to flow text across different areas of the page into containers using
 the .flowTo and .flowFrom CSS properties. ONLY SUPPORTS SAFARI AND IE.

 Example:
 ```less
 
  .starting-variable {
   .flowTo( flow-name );
  }
  .ending-variable {
   .flowFrom( flow-name );
  }
  
  ```
 * This mixin takes in the following parameter
```sh
  @flowTo: <name>
  @flowFrom: <name>
```
## Transform property mixin
* **.transform();**

* **Description:** This mixin is shorthand for transform, -webkit-transform, and -ms-transform. .transform should be used like a native css transform property

Example:
 ```less
 
  .transform( skewX(25deg) rotate(25deg) translateX(17px) );
  
 ```
* This mixin will take in the following parameters with its respective values. Only one of these may be used at a time
 ```sh
 
  <none>
  matrix(n,n,n,n,n,n): <number> (6 values)
  matrix3d(n,n,n,n, ...): <number> (16 values)
  translate( x, y): one or two <translation-value> values
  translate3d(x,y,z): three <translation-value> values
  translateX(x): <translation-value>
  translateY(y): <translation-value>
  translateZ(z): <translation-value>
  scale(x,y): one or two unitless <number>s, e.g., scale(2.1,4)
  scale3d(x,y,z): three unitless <number>s
  scaleX(x): a unitless <number>, e.g., scaleX(2.7)
  scaleY(y): a unitless <number>, e.g., scaleY(0.3)
  scaleZ(z):  a unitless <number>, e.g., scaleZ(0.3)
  rotate(angle):  <angle>, e.g., rotate(30deg)
  rotate3d(x,y,z,angle): three <number>s and an <angle>
  rotateX(angle): <angle>, e.g., rotate(30deg)
  rotateY(angle): <angle>, e.g., rotate(30deg)
  rotateZ(angle): <angle>, e.g., rotate(30deg)
  skew(x-angle,y-angle): one or two <angle>s, e.g., skew(30deg,-10deg)
  skewX(angle): an <angle>, e.g., skewX(-30deg)
  skewY(angle): an <angle>, e.g., skewY(4deg)
  perspective(n): <length>
```
## BackfaceVisibility property mixin
* **.backfaceVisibility();**

Example:
```less
.backfaceVisibility( hidden );
```
* **Description:** The backfaceVisibility property defines whether or not an element should be visible when not
facing the screen. Currently this mixin only has no support for Opera Mini

*This mixin will take in the following parameters with its respective values
```sh
@visibility: visible | hidden
```

## Perspective property mixin
* **.perspective();**

* **Description:** The perspective property defines how many pixels a 3D element is placed from the view. This property
allows you to change the perspective on how 3D elements are viewed.

Example:
 ```less
 .perspective( 200px );
 ```

* This mixin will take in the following parameters
```sh
 @perspective: <length> | none
```

## TransformStyle property mixin
* **.transformStyle();**

* **Description:** The transformStyle property specifies how nested elements are rendered in 3D space. To be used
with transform

Example:
```less
.transformStyle( preserve-3d );
```

* This mixin will take in the following parameters
```sh
 @perspective: flat | preserve-3d
```

## Transform-origin property mixin
* **.transformOrigin();**

* **Description:** The transform-origin property allows you to change the position on transformed elements.

Example:
```less
.transformOrigin( center bottom );
```

* This mixin will take in the following parameters
```sh
 @origin: x-axis y-axis z-axis
  x-axis: left | center | right | <length> | <percent>
  y-axis: top | center | bottom | <length> | <percent>
  z-axis: <length>
```

## User-select property mixin
* **.userSelect();**

* **Description:** Method of preventing text/element selection using CSS.

Example:
```less
.userSelect( none );
```
* This mixin takes in the following parameter
```sh
 @select: <none> | <text> | <all> | <element>
```
