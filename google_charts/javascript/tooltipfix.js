function fixTooltip() {
	jQuery('iframe[id*=Drawing_Frame_]').each(function() {
		jQuery(jQuery(this).get(0).contentDocument).find('g').each(function() {
			var clipPath = jQuery(this).attr('clip-path');
			if(! clipPath) return;
			if(clipPath.indexOf('url(#') == -1) return;
			jQuery(this).attr('clip-path', 'url(' + document.location + clipPath.substring(4));
		});
	});
}