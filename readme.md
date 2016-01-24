# TwentyTwentyProton

Before/After-Image-Gallery Plugin for Wordpress using Proton

## Requirements
1. Wordpress (tested with 4.4.1)
2. jQuery: [http://jquery.com/](http://jquery.com/)
3. TwentyTwenty: [https://github.com/zurb/twentytwenty](https://github.com/zurb/twentytwenty)
4. Proton: [https://github.com/a-jie/Proton](https://github.com/a-jie/Proton)

## Installation
Just type "bower install" in the plugin directory to update the requirements using bower or include them yourself.

**ATTENTION:** Bower-Users! jQuery is not included because it is present anyway most of the time - so you are responsible for including jquery yourself !

## TODO
### Localization
Since there are only two Words used in the Frontend you can just override the used CSS Attributes:
```css
.twentytwenty-proton .twentytwenty-before-label:before
{
	content:"Before";
}
.twentytwenty-proton .twentytwenty-after-label:before
{
	content:"After";
}
```

## Usage
Once installed and activaded the Plugin modifies the Wordpress Media Gallery and adds a new "Link"-Option while creating a Image Gallery for the "gallery"-Shortcode. You just need to select exactly two images, bring them in the right order (before,after) and select the new "Link"-Option: "TwentyTwentyProton".