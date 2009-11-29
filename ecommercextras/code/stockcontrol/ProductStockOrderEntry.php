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

	//MODEL ADMIN STUFF
	public static $searchable_fields = array(
		"BaseQuantity",
		"NoProductPresent",
		"Parent"
	);

	public static $field_labels = array(
		"BaseQuantity" => "Calculated Quantity On Hand",
		"NoProductPresent" => "Product Not Present"
		"Parent" => "Product"
		"LastEdited" => "Last Calculated"
	);

	public static $summary_fields = array(
		"BaseQuantity",
		"Parent",
		"LastEdited"
	);

	public static $singular_name = "Product Stock Order Entry";

	public static $plural_name = "Product Stock  Order Entries";

	public function canCreate() {return false;}

	public function canEdit() {return false;}

	public function canDelete() {return false;}

	public function canView() {return $this->canDoAnything();}

	protected function canDoAnything() {
		if(!Permission::check("ADMIN")) {
			Security::permissionFailure($this, _t('Security.PERMFAILURE',' This page is secured and you need administrator rights to access it. Enter your credentials below and we will send you right along.'));
		}
		return true;
	}

	function onAfterWrite() {
		parent::onAfterWrite();
		//make sure no doubles are created
		while(DataObject::get_one("ProductStockOrderEntry", "OrderID = ".$this->OrderID." AND ID <> ".$this->ID)) {
			$toBeDeleted = DataObject::get_one("ProductStockOrderEntry", "OrderID = ".$this->OrderID, false, "LastEdited ASC");
			$toBeDeleted->delete();
		}
	}

}
