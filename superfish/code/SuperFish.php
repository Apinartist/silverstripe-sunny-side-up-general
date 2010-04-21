<?php

/**
 *@author nicolaas[at]sunnysideup.co.nz
 *
Superfish’s default options:

$.fn.superfish.defaults = {
    hoverClass:    'sfHover',          // the class applied to hovered list items
    pathClass:     'overideThisToUse', // the class you have applied to list items that lead to the current page
    pathLevels:    1,                  // the number of levels of submenus that remain open or are restored using pathClass
    delay:         800,                // the delay in milliseconds that the mouse can remain outside a submenu without it closing
    animation:     {opacity:'show'},   // an object equivalent to first parameter of jQuery’s .animate() method
    speed:         'normal',           // speed of the animation. Equivalent to second parameter of jQuery’s .animate() method
    autoArrows:    true,               // if true, arrow mark-up generated automatically = cleaner source code at expense of initialisation performance
    dropShadows:   true,               // completely disable drop shadows by setting this to false
    disableHI:     false,              // set to true to disable hoverIntent detection
    onInit:        function(){},       // callback function fires once Superfish is initialised – 'this' is the containing ul
    onBeforeShow:  function(){},       // callback function fires just before reveal animation begins – 'this' is the ul about to open
    onShow:        function(){},       // callback function fires once reveal animation completed – 'this' is the opened ul
    onHide:        function(){}        // callback function fires after a sub-menu has closed – 'this' is the ul that just closed
};

You can override any of these options by passing an object into the Superfish method upon initialisation. For example:

//link to the CSS files for this menu type
<link rel="stylesheet" type="text/css" media="screen" href="superfish.css" />

// link to the JavaScript files (hoverIntent is optional)
<script type="text/javascript" src="hoverIntent.js"></script>
<script type="text/javascript" src="superfish.js"></script>

// initialise Superfish
<script type="text/javascript">

    $(document).ready(function() {
        $('ul.sf-menu').superfish({
            delay:       1000,                            // one second delay on mouseout
            animation:   {opacity:'show',height:'show'},  // fade-in and slide-down animation
            speed:       'fast',                          // faster animation speed
            autoArrows:  false,                           // disable generation of arrow mark-up
            dropShadows: false                            // disable drop shadows
        });
    });

</script>

 *
 **/

class PrettyPhoto extends Object {


	protected static $config = "";
		static function set_config($v) {self::$config = $v;}
		static function get_config() {return self::$config;}


	static function include_code() {
		if(Director::is_ajax()) {
			self::block();
		}
		else {
			Requirements::javascript('superfish/javascript/hoverIntent.js');
			Requirements::javascript('superfish/javascript/superfish.js');
			Requirements::ThemedCSS('superfish');
			Requirements::customScript('superfishconfig', self::get_config());
		}
	}

	static function block() {
		Requirements::block('superfish/javascript/hoverIntent.js');
		Requirements::block('superfish/javascript/superfish.js');
		Requirements::block('superfish/css/superfish.css');
		Requirements::block('superfishconfig');

	}
}