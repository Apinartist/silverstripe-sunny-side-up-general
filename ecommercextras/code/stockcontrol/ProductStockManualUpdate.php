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

	function onBeforeWrite() {
		if($m = Member::currentUser()) {
			$this->MemberID = $m->ID;
		}

		parent::onBeforeWrite();
	}

}
