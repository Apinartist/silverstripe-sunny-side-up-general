jQuery(document).ready(
	function(){
		AncestryField.init();
	}
);
//TODO: complete height system...
var AncestryField = {

	height: 200,

	extraHeightPerNode: 200,

	init: function(){
		jQuery(".ancestorNode").hide();
		jQuery(".ancestorNode.level1").show();
		jQuery(".ancestorNode input").keyup(
			function(){
				var rel = jQuery(this).parent("div.ancestorNode").attr("rel");
				if(jQuery(this).val().length > 1) {
					jQuery(rel).show();
				}
				else {
					jQuery(rel).hide();
				}
			}
		);

	}

}
