/* Drag n drop Cart Functions */
var total = 0.0;

jQuery(document).ready(function() {
	dragToCart.init();
});


var dragToCart = {

	addedItems: new Array(),

	init: function() {


		alert("setting up drop and drag");
		dragToCart.makeEnoughRoomForDroppingItems();

		jQuery(".productItem").draggable({ helper: "clone", opacity: "0.5" });

		jQuery("#ShoppingCart").droppable(
			{
				accept: ".productItem",

				hoverClass: "dropHover",

				drop: function(ev, ui) {
					var URL = ui.draggable.clone().children(".quantityBox a").attr("href");
					alert("adding item - using: " + URL);
					var droppedItem = ui.draggable.clone().load(URL, {}, function(){

						//error
						jQuery(this).ajaxError(
							function(event, request, settings){
								$(this).append("<li>Error adding Storage item " + settings.html + "</li>");
							}
						);

						reloadCart();
					});
				}
			}
		);
	},

	itemAddedMoreThanOneTime: function (id) {
		var droppedItem = getItemFromShoppingCartUsingId(id);
		var currentQuantity = 0;
		currentQuantity = ((jQuery(droppedItem).children(".NoOfItems").text()));
		//currentQuantity = ((jQuery(droppedItem).children(".NoOfItems").html()));

		if(parseInt(currentQuantity) > 1) {
			currentQuantity = parseInt(currentQuantity) - parseInt(1);
			jQuery(droppedItem).children(".NoOfItems").html(currentQuantity);
			return true;
		}

		if(parseInt(currentQuantity) == 2) {
			currentQuantity = parseInt(currentQuantity) - parseInt(1);
			jQuery(droppedItem).children(".NoOfItems").html("");
			return true;
		}
		return false;
	},

	updateQuantity: function (id) {
		var droppedItem = getItemFromShoppingCartUsingId(id);
		var currentQuantity = 0;
		currentQuantity = ((jQuery(droppedItem).children(".NoOfItems").text()));
		if(currentQuantity == '') {
			currentQuantity = 2;
		}
		else {
			currentQuantity = parseInt(currentQuantity) + parseInt(1);
		}
		jQuery(droppedItem).children(".NoOfItems").html(currentQuantity);
	},

	isItemPresentInShoppingCart: function(id) {
		var droppedItem = getItemFromShoppingCartUsingId(id);
		return droppedItem != null;
	},

	getItemFromShoppingCartUsingId: function (id){
		var itemsInShoppingCart = jQuery(".dropZone div");
		var droppedItem = null;

		for(i=0;i <= itemsInShoppingCart.length-1;i++) {
			if(id == itemsInShoppingCart[i].id) {
				droppedItem = itemsInShoppingCart[i];
				break;
			}
		}
		return droppedItem;
	},

	updateTotal: function (price) {
		total += parseFloat(price);
		jQuery("#Cart_Order_Total").html(total.toFixed(2));
		jQuery(".shoppingCartTotal");
	},

	listSelectedItems: function () {
		var itemsInShoppingCart = jQuery(".dropZone div");

		for(i=0;i <= itemsInShoppingCart.length-1;i++) {
			alert(itemsInShoppingCart[i].attributes["code"].nodeValue);
			alert(itemsInShoppingCart[i].attributes["size"].nodeValue);
		}
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
		alert(height);
		height += 10;
		jQuery("#ShoppingCart").height(height+"px");
	}
}