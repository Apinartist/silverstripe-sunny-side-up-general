<?php
/**
 *@author: Nicolaas [at] Sunny Side Up . Co . Nz
 *@description: works out the quantity available for each product
 *
 *
 **/

class ProductStockOrderEntry extends DataObject {

	static $db = array(
		"Quantity" => "Int",
		"NoLongerRelevant" => "Boolean",
	);

	static $has_one = array(
		"Parent" => "ProductStockCalculatedQuantity",
		"Order" => "Order",
	);

	function onAfterWrite() {
		parent::onAfterWrite();
		//make sure no doubles are created
		while(DataObject::get_one("ProductStockOrderEntry", "OrderID = ".$this->OrderID." AND ID <> ".$this->ID)) {
			$toBeDeleted = DataObject::get_one("ProductStockOrderEntry", "OrderID = ".$this->OrderID, "LastEdited ASC");
			$toBeDeleted->delete();
		}
	}

}
