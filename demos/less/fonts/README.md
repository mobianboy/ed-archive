edFont is where all of the font files reside for the app. To change the default font, save the files in the /client/assets/fonts/*newFontName* folder. The folder should be created and the font files saved as lowercase lettering, kebab case if necessary. To update the app's font, change the following lines and replace the "ubuntu" references:
```
   @font-family: Ubuntu;
   @root-url: './../assets/fonts/ubuntu/ubuntu';
```

Double check that the new font has all of the same weights as the previous font in the @font-face references; make adjustments as necessary.

***

edTypeStyles is where the typography design style guide for the look and feel of the site is translated. Currently there are several type styles used in the style guide, references headlines or paragraph formatting. The parameter for edType is the type of style with a hyphen and then the sizing. For example, heading style at XX-Large is:
```.edType( heading-XXL );
```
