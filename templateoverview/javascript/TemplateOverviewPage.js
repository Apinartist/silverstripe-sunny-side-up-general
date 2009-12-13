jQuery(document).ready(
	function () {
		if( jQuery("#classList").length > 0) {
			jQuery("#classList .typo-seemore").click(
				function() {
					var url = jQuery(this).attr("href");
					var id = jQuery(this).attr("rel");
					jQuery("#" + id).show();
					jQuery("#" + id).html("<li>loading pages ....</li>");
					jQuery("#" + id).load(url);
					return false;
				}
			);
		}
	}
);

