/* Drag n drop Cart Functions */
var total = 0.0;

jQuery(document).ready(function() {
	dragToCart.init();
});


var dragToCart = {

	addedItems: new Array(),

	init: function() {

		//alert("setting up drop and drag");

		jQuery(".productItem").draggable({ helper: "clone", opacity: "0.5" });

		dragToCart.addDroppable();

	},

	latestID : 0,

	addDroppable: function() {
		dragToCart.makeEnoughRoomForDroppingItems();
		jQuery("#ShoppingCart").droppable({
			accept: ".productItem",
			hoverClass: "dropHover",
			drop: function(ev, ui) {
				jQuery("#ShoppingCart").addClass("beingUpdated");
				var clone = ui.draggable.clone();
				dragToCart.latestID = jQuery(clone).attr("rel");
				//alert("adding item - using: " + URL);
				var URL = dragToCart.AjaxStorageCartURL + dragToCart.latestID;
				var droppedItem = jQuery(".cart").load(
					URL,
					{},
					dragToCart.afterUpdateItem
				);
			}
		});
	},

	makeEnoughRoomForDroppingItems: function() {
		var height = 0;
		jQuery(".productItem").each(
			function() {
				if(jQuery(this).height() > height) {
					height = jQuery(this).height();
				}
			}
		);
		//alert("adjusting shoppingCart size height to " + height);
		height += 30;
		if(height > jQuery("#ShoppingCart").height()) {
			jQuery("#ShoppingCart").height(height+"px");
		}
		var width = 0;
		jQuery(".productItem").each(
			function() {
				if(jQuery(this).width() > width) {
					width = jQuery(this).width();
				}
			}
		);
		//alert("adjusting shoppingCart size width to " + width);
		width += 30;
		if(width > jQuery("#ShoppingCart").width()) {
			jQuery("#ShoppingCart").width(width+"px");
		}
	},

	afterUpdateItem: function(){
		//error
		jQuery(this).ajaxError(
			function(event, request, settings){
				$(this).append("<li>Error adding Storage item " + settings.html + "</li>");
			}
		);

		dragToCart.addDroppable();
		dragToCart.quantityFieldReset();
	},

	quantityFieldReset: function() { //copy from ecommerce
		jQuery('input.ajaxQuantityField').each(
			function() {
				jQuery(this).attr('disabled', false);
				jQuery(this).change(
					function() {
						var name = jQuery(this).attr('name')+ '_SetQuantityLink';
						var setQuantityLink = jQuery('[name=' + name + ']');
						if(jQuery(setQuantityLink).length > 0) {
							setQuantityLink = jQuery(setQuantityLink).get(0);
							if(! this.value) this.value = 0;
							else this.value = this.value.replace(/[^0-9]+/g, '');
							var url = jQuery('base').attr('href') + setQuantityLink.value + '?quantity=' + this.value;
							jQuery.getJSON(url, null, setChanges);
						}
					}
				);
			}
		);
	}

}