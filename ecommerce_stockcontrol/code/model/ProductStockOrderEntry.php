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
		"IncludeInCurrentCalculation" => "Boolean"
	);

	static $has_one = array(
		"Parent" => "ProductStockCalculatedQuantity",
		"Order" => "Order",
	);

	static $defaults = array(
		"IncludeInCurrentCalculation" => 1
	);

	//MODEL ADMIN STUFF
	public static $searchable_fields = array(
		"Quantity",
		"IncludeInCurrentCalculation",
		"ParentID",
		"OrderID",
	);

	public static $field_labels = array(
		"Quantity" => "Calculated Quantity On Hand",
		"IncludeInCurrentCalculation" => "Include in Calculation",
		"ParentID" => "Product",
		"OrderID" => "Order"
	);

	public static $summary_fields = array(
		"OrderID",
		"ParentID",
		"Quantity"
	);

	public static $default_sort = "LastEdited DESC, ParentID ASC";

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
		if($this->ID) {
			//basic checks
			if(!$this->ParentID) {
				$this->delete();
				user_error("Can not create record without associated product.", E_USER_ERROR);
			}
			if(!$this->OrderID) {
				$this->delete();
				user_error("Can not create record without order.", E_USER_ERROR);
			}
			//make sure no doubles are created
			while($tobeDeleted = DataObject::get_one("ProductStockOrderEntry", "`OrderID` = ".$this->OrderID." AND `ID` <> ".$this->ID, false, "LastEdited ASC")) {
				$toBeDeleted = DataObject::get_one("ProductStockOrderEntry", "OrderID = ".$this->OrderID, false, "LastEdited ASC");
				$toBeDeleted->delete();
			}
		}
	}

}
