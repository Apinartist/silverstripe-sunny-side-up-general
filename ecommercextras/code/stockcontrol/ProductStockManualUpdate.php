<?php
/**
 *@author: Nicolaas [at] Sunny Side Up . Co . Nz
 *@description: works out the quantity available for each product
 *
 *
 **/

class ProductStockManualUpdate extends DataObject {

	static $db = array(
		"Quantity" => "Int",
	);

	static $has_one = array(
		"Parent" => "ProductStockCalculatedQuantity",
		"Member" => "Member"
	);

	//MODEL ADMIN STUFF
	public static $searchable_fields = array(
		"Quantity",
		"Parent",
		"Member"
	);

	public static $field_labels = array(
		"Quantity",
		"Parent"  => "Product",
		"Member"  => "Updated By"
	);

	public static $summary_fields = array(
		"Quantity",
		"Parent"
	);

	public static $singular_name = "Product Stock Manual Update Entry";

	public static $plural_name = "Product Stock Manual Update Entries";

	public function canView() {return $this->canDoAnything();}

	public function canCreate() {return $this->canDoAnything();}

	public function canEdit() {return false;}

	public function canDelete() {return false;}

	protected function canDoAnything() {
		if(!Permission::check("ADMIN")) {
			Security::permissionFailure($this, _t('Security.PERMFAILURE',' This page is secured and you need administrator rights to access it. Enter your credentials below and we will send you right along.'));
		}
		return true;
	}


	function onBeforeWrite() {
		if($m = Member::currentUser()) {
			$this->MemberID = $m->ID;
		}

		parent::onBeforeWrite();
	}

}
