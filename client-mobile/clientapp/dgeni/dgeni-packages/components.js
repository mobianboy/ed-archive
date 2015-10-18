"use strict";
var config = require( "../../config.paths.js" ),
    Package = require( "dgeni" ).Package;

module.exports = new Package( "components", [
  require( "dgeni-packages/jsdoc" ),
  require( "dgeni-packages/nunjucks" )
])
  .config( function( log, readFilesProcessor, templateFinder, writeFilesProcessor ) {
    log.level = "info";
    readFilesProcessor.basePath = config.dgeni.components.basePath;
    readFilesProcessor.sourceFiles = config.dgeni.components.src;
    templateFinder.templateFolders.unshift( config.dgeni.components.templateFolder );
    templateFinder.templatePatterns.unshift( config.dgeni.components.templatePattern );
    writeFilesProcessor.outputFolder = config.dgeni.components.outputFolder;
  });
