//
//  Description:
//    Some eardish things
//
//  Dependencies:
//    None
//
//  Configuration:
//    None
//
//  Commands:
//    eardish - responds with eardish emoji
//    lance is watching - responds with lance-is-watching emoji
//
//  Notes:
//    This is just the beginning
//
//  Author
//

module.exports = function( robot ) {
  robot.hear( /eardish/i, function( res ) {
    res.send( ':eardish:' );
  });

  robot.hear( /lance is watching/i, function( res ) {
    res.send( ':lance-is-watching:' );
  });
};
