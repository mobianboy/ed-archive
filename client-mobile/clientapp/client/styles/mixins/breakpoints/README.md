## LESS Breakpoints

**Description:**
This mixin is a library of mixin that leverages string interpolation in order to simplify the nesting needed for LESS
media queries. The user will need to create the use case mixin in addition to the custom mixin required for this
library of mixins to work. The syntax for the mixin is similar to JQuery's way of chaining, see usage for example
syntax.

**Rules:**

* Query Mixins will always have/return the same variable names as the mixins themselves with the exception or the "@"
at the front.

* With the exception of all the Base Media Types( .all();, .allOnly();, print(); .printOnly();, screen();, screenOnly()
;, speech();, speechOnly(); ) all other queries should take in the mixins called before them

* The present .and(); and .or() mixin can only be used once. If additional and's and or's are needed please use .and1
();, and2(); and etc.

* Once the user is done with their mixin they should end the mixin with a .create( @previousVariable; @rules ); to
interpolate the mixin

**Usage:**
What the developers need to input:
```less
// Use case
#phone {
  .phone({
    & {
      display: block;
      background-color: red;
    }
  });
}

// Custom Breakpoint Mixin
.phone( @rules ) {
  .screenOnly();
  .and( @screenOnly );
  .maxWidth( @phoneMaxWidth; @and );
  .create( @maxWidth; @rules );
}
```

Preset of mixins provided by the mixin library:
```less
// Queries
.screenOnly(){
  @screenOnly: "only screen";
}

.and( @query ){
  @and: "@{query} and";
}

.maxWidth( @width; @query ){
  @maxWidth: "@{query} (max-width: @{width})";
}

// Variables
@phoneMaxWidth: 31em;

// Mixins
.create( @escape; @rules ) {
  @escaped: ~"@{escape}";
  @media @escaped {
    @rules();
  }
}
```

* Note: Variables can be changed in a variables.less file

Output:
```css
@media only screen and (max-width: 31em) {
  #phone {
    display: block;
    background-color: red;
  }
}
```

# List of Avaiable Mixins:

The following mixins were named to match the native css media queries, please see for further documentation on usage
of media queries https://developer.mozilla.org/en-US/docs/Web/Guide/CSS/Media_queries

* **Mixins required for Initialization**
```less
.all(); .allOnly(); print(); .printOnly(); .screen(); .screenOnly(); .speech(); .speechOnly();
```

* **Aspect Ratio**
*requires a ratio variable*
```less
.aspectRatio( @ratio; @query );
.minAspectRatio( @ratio; @query );
.maxAspectRatio( @ratio; @query );
```

* **Device Aspect Ratio**
*requires a ratio variable*
```less
.deviceAspectRatio( @ratio; @query );
.minDeviceAspectRatio( @ratio; @query );
.maxDeviceAspectRatio( @ratio; @query );
```

* **Device Height**
*requires a height variable*
```less
.deviceHeight( @height; @query );
.minDeviceHeight( @height; @query );
.maxDeviceHeight( @height; @query );
```

* **Device Width**
*requires a width variable*
```less
.deviceWidth( @width; @query );
.minDeviceWidth( @width; @query );
.maxDeviceWidth( @width; @query );
```

* **Query Height**
*requires a height variable*
```less
.queryHeight( @height; @query );
.minQueryHeight( @height; @query );
.maxQueryHeight( @height; @query );
```

* **Query Width**
*requires a width variable*
```less
.queryWidth( @width; @query );
.minQueryWidth( @width; @query );
.maxQueryWidth( @width; @query );
```

* **Monochrome**
*requires a bitsPerPixel variable*
```less
.monochrome( @bitsPerPixel; @query );
.minMonochrome( @bitsPerPixel; @query );
.maxMonochrome( @bitsPerPixel; @query );
```

* **Orientation**
```less
.landscapeOrientation( @query );
.portraitOrientation( @query );
```

* **Resolution**
*requires a density variable*
```less
.resolution( @density; @query );
.minResolution( @density; @query );
.maxResolution( @density; @query );
```

* **And / Or**
```less
.and( @query );
.and1( @query );
.and2( @query );
.and3( @query );
.and4( @query );
.and5( @query );

.or( @query );
.or1( @query );
.or2( @query );
.or3( @query );
.or4( @query );
.or5( @query );
```

* **Create**
*required on ALL custom mixins*
```less
.create( @escape; @rules );
```

