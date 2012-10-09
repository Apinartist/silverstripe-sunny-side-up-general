
;(function($) {
	jQuery(document).ready(
		function() {
			//must do first!
			windowResizer.init();
		}
	);

})(jQuery);

var SSUhoverMenu = {

	mobileBrowsing: true,
	set_mobileBrowsing: function(b) {this.mobileBrowsing = b;},
	animateIn: {opacity: "1"},
	animateOut: {opacity: "0.75"},

	init: function() {
		jQuery(".hasCSSHover").removeClass("hasCSSHover");
		if(this.mobileBrowsing) {
			jQuery("#Nav").unbind("mouseenter").unbind("mouseleave");
			jQuery("#Nav li.level1").unbind("mouseenter").unbind("mouseleave");
			jQuery("#Nav ul li").css('left', 'auto');
			jQuery("#Nav").animate(SSUhoverMenu.animateIn).addClass("menuIn").removeClass("menuOut");
		}
		else {
			jQuery("#Nav li.level1").hoverIntent(
				{
					over: SSUhoverMenu.menuIn,  // function = onMouseOver callback (required)
					timeout: 200,   // number = milliseconds delay before onMouseOut function call
					out: SSUhoverMenu.menuOut  // function = onMouseOut callback (required)
				}
			);
			jQuery("#Nav").hoverIntent(
				{
					over: function(){jQuery(this).animate(SSUhoverMenu.animateIn).addClass("menuIn").removeClass("menuOut");},  // function = onMouseOver callback (required)
					timeout: 1500,   // number = milliseconds delay before onMouseOut function call
					out: function(){jQuery(this).animate(SSUhoverMenu.animateOut).addClass("menuOut").removeClass("menuIn");}  // function = onMouseOut callback (required)
				}
			);
			jQuery("#Nav").animate(SSUhoverMenu.animateOut).addClass("menuOut").removeClass("menuIn");
			jQuery("#Nav").children("li").each(
				function(i, el) {
					var parentOffset = jQuery(el).offset();
					var left = parentOffset.left;
					var docWidth = jQuery(document).width();
					if(left > (docWidth - 270)) {
						left = docWidth - 270;
					}
					leftString = left + "px";
					jQuery(el).children("ul").animate({left: leftString});
				}
			);
		}
	},

	menuIn: function() {jQuery(this).children("ul").slideDown()},

	menuOut: function() {jQuery(this).children("ul").slideUp()}

}


/*
 *@author nicolaas[at]sunnysideup.co.nz
 *
 **/


var windowResizer = {

	windowWidth: 0,

	windowHeight: 0,

	minWrapperWidth: 530,

	minWrapperHeight: 400,
		set_min_wrapper_height: function(v) {this.minWrapperHeight = v;},

	maxWrapperWidth: 1920,

	maxWrapperHeight: 1449,

	smallScreenMaxSize: 960,

	imageWidth: "",

	imageHeight: "",

	imageSelector : '#RandomVisualThought',

	init : function() {
		windowResizer.manageSidebar();
		if(jQuery("#RandomVisualThought").length > 0) {
			jQuery("#RandomVisualThought").click(
				function(el) {
					var url = jQuery(this).attr("rel");
					jQuery("body").append('<div id="RandomImageLarge"><img src="'+url+'" alt="random image large" /></div>');
					jQuery("#RandomImageLarge")
						//.css('background-image', 'url('+url+')')
						.click(
							function(){
								jQuery(this).remove();
								jQuery(document).removeAttr("keydown");
							}
						);
					windowResizer.resizeImage();
					jQuery(document).keydown(
						function(e) {
							if(jQuery('#RandomImageLarge').length) {
								if (e.which == 27) {jQuery('#RandomImageLarge').click(); }  // esc   (does not work)
							}
						}
					);
				}
			);
		}
		//redo when window is resized
		jQuery(window).resize(
			function() {
				windowResizer.manageSidebar();
				if(jQuery("#RandomImageLarge").length) {
					windowResizer.resizeImage();
				}
			}
		);
	},

	resizeImage : function() {
		//get window height
		this.getWindowSizing();
		//width within boundaries
		if(1 == 2) {
			if(this.windowWidth < this.minWrapperWidth) {
				this.windowWidth = this.minWrapperWidth;
			}
			if(this.windowWidth > this.maxWrapperWidth) {
				this.windowWidth = this.maxWrapperWidth;
			}
			//height within boundaries
			if(this.windowHeight < this.minWrapperHeight) {
				this.windowHeight = this.minWrapperHeight;
			}
			if(this.windowHeight > this.maxWrapperHeight) {
				this.windowHeight = this.maxWrapperHeight;
			}
		}
		var image = this.getImage();

		// 2) Center image in the middle
		this.imageWidth = 'auto';
		this.imageHeight = this.windowHeight + 'px';
		image.width(windowResizer.imageWidth).height(windowResizer.imageHeight);
		if(jQuery(image).width() > jQuery(image).height()) {
			if(jQuery(image).width() < this.windowWidth) {
				this.imageWidth = this.windowWidth + 'px';
				this.imageHeight = 'auto';
				image.width(windowResizer.imageWidth).height(windowResizer.imageHeight);
			}
		}
		if(jQuery(image).height() > this.windowHeight) {
			var heightDifference = jQuery(image).height() - this.windowHeight;
			jQuery(image).css("margin-top", "-"+Math.round(heightDifference / 2)+"px" )
		}
	},


	getImage : function() {
		return jQuery('#RandomImageLarge img');
	},

	standardSidebarWidth: 200,

	manageSidebar: function(){
		this.getWindowSizing();
		if(this.windowWidth < this.smallScreenMaxSize) {
			jQuery("body").addClass("mobileBrowsing");
			jQuery("#Sidebar").width(jQuery("#LayoutHolder").width()+"px");
			SSUhoverMenu.set_mobileBrowsing(true);
			SSUhoverMenu.init();
		}
		else {
			jQuery("body").removeClass("mobileBrowsing");
			jQuery("#Sidebar").width(this.standardSidebarWidth+"px");
			SSUhoverMenu.set_mobileBrowsing(false);
			SSUhoverMenu.init();
		}
	},

	getWindowSizing : function(){
		this.windowWidth = jQuery(window).width();
		this.windowHeight = jQuery(window).height();
	}

}



/**
* hoverIntent is similar to jQuery's built-in "hover" function except that
* instead of firing the onMouseOver event immediately, hoverIntent checks
* to see if the user's mouse has slowed down (beneath the sensitivity
* threshold) before firing the onMouseOver event.
*
* hoverIntent r6 // 2011.02.26 // jQuery 1.5.1+
* <http://cherne.net/brian/resources/jquery.hoverIntent.html>
*
* hoverIntent is currently available for use in all personal or commercial
* projects under both MIT and GPL licenses. This means that you can choose
* the license that best suits your project, and use it accordingly.
*
* // basic usage (just like .hover) receives onMouseOver and onMouseOut functions
* $("ul li").hoverIntent( showNav , hideNav );
*
* // advanced usage receives configuration object only
* $("ul li").hoverIntent({
*	sensitivity: 7, // number = sensitivity threshold (must be 1 or higher)
*	interval: 100,   // number = milliseconds of polling interval
*	over: showNav,  // function = onMouseOver callback (required)
*	timeout: 0,   // number = milliseconds delay before onMouseOut function call
*	out: hideNav    // function = onMouseOut callback (required)
* });
*
* @param  f  onMouseOver function || An object with configuration options
* @param  g  onMouseOut function  || Nothing (use configuration options object)
* @author    Brian Cherne brian(at)cherne(dot)net
*/
;(function($) {
	$.fn.hoverIntent = function(f,g) {
		// default configuration options
		var cfg = {
			sensitivity: 7,
			interval: 100,
			timeout: 0
		};
		// override configuration options with user supplied object
		cfg = $.extend(cfg, g ? { over: f, out: g } : f );

		// instantiate variables
		// cX, cY = current X and Y position of mouse, updated by mousemove event
		// pX, pY = previous X and Y position of mouse, set by mouseover and polling interval
		var cX, cY, pX, pY;

		// A private function for getting mouse position
		var track = function(ev) {
			cX = ev.pageX;
			cY = ev.pageY;
		};

		// A private function for comparing current and previous mouse position
		var compare = function(ev,ob) {
			ob.hoverIntent_t = clearTimeout(ob.hoverIntent_t);
			// compare mouse positions to see if they've crossed the threshold
			if ( ( Math.abs(pX-cX) + Math.abs(pY-cY) ) < cfg.sensitivity ) {
				$(ob).unbind("mousemove",track);
				// set hoverIntent state to true (so mouseOut can be called)
				ob.hoverIntent_s = 1;
				return cfg.over.apply(ob,[ev]);
			} else {
				// set previous coordinates for next time
				pX = cX; pY = cY;
				// use self-calling timeout, guarantees intervals are spaced out properly (avoids JavaScript timer bugs)
				ob.hoverIntent_t = setTimeout( function(){compare(ev, ob);} , cfg.interval );
			}
		};

		// A private function for delaying the mouseOut function
		var delay = function(ev,ob) {
			ob.hoverIntent_t = clearTimeout(ob.hoverIntent_t);
			ob.hoverIntent_s = 0;
			return cfg.out.apply(ob,[ev]);
		};

		// A private function for handling mouse 'hovering'
		var handleHover = function(e) {
			// copy objects to be passed into t (required for event object to be passed in IE)
			var ev = jQuery.extend({},e);
			var ob = this;

			// cancel hoverIntent timer if it exists
			if (ob.hoverIntent_t) { ob.hoverIntent_t = clearTimeout(ob.hoverIntent_t); }

			// if e.type == "mouseenter"
			if (e.type == "mouseenter") {
				// set "previous" X and Y position based on initial entry point
				pX = ev.pageX; pY = ev.pageY;
				// update "current" X and Y position based on mousemove
				$(ob).bind("mousemove",track);
				// start polling interval (self-calling timeout) to compare mouse coordinates over time
				if (ob.hoverIntent_s != 1) { ob.hoverIntent_t = setTimeout( function(){compare(ev,ob);} , cfg.interval );}

			// else e.type == "mouseleave"
			} else {
				// unbind expensive mousemove event
				$(ob).unbind("mousemove",track);
				// if hoverIntent state is true, then call the mouseOut function after the specified delay
				if (ob.hoverIntent_s == 1) { ob.hoverIntent_t = setTimeout( function(){delay(ev,ob);} , cfg.timeout );}
			}
		};

		// bind the function to the two event listeners
		return this.bind('mouseenter',handleHover).bind('mouseleave',handleHover);
	};
})(jQuery);
