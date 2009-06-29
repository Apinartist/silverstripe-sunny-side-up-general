(function($){
	$(document).ready(
		function() {
			$(".recommendedproducts").livequery(
				function() {
					alert("recommendedproducts");
					$(".recommendedproducts").hide();
				}
			);
		}
	);
})(jQuery);
