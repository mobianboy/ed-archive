/*jshint strict:false */
/**
 *  This file will be for creating a set of functions that are useful for
 *  knowing which breakpoint section the application is in via JS
 */

// TODO bring in the quires that we use

export default {
  get isPhone() {
    return false;
  },
  get isPortrait() {
    return false;
  },
  get isLandscape() {
    return false;
  },
  get isDesktop() {
    return false;
  },
  isOriented( orientation ) {
    orientation = orientation || "portrait";
    return window.matchMedia( `(orientation: ${orientation})` ).matches;
  }
  // TODO listenFor?
};
