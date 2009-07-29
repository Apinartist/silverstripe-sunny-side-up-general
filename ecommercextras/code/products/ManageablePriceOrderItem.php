<?php

class ManageablePriceOrderItem extends Product_OrderItem {

	protected $_unitPriceAlternative;

	static $db = array(
		'UnitPriceAlternative' => 'Currency'
	);

	static $has_one = array(
		'Product' => 'Product'
	);

	public function __construct($product = null, $quantity = 1) {
 		parent::__construct($product, $quantity);
	}

	public function setAlternativeUnitPrice($price) {
		$this->_unitPriceAlternative = $price;
	}

	public function Product($current = false) {
		if($current) return DataObject::get_by_id('Product', $this->_productID);
		else return Versioned::get_version('Product', $this->_productID, $this->_productVersion);
	}

	function UnitPrice() {
		//check current record
		if($this->_unitPriceAlternative) {
			return $this->_unitPriceAlternative;
		}
		elseif($this->UnitPriceAlternative) {
			return $this->UnitPriceAlternative;
		}
		else {
			return $this->Product()->Price;
		}
	}

	function onBeforeWrite() {
		parent::onBeforeWrite();
		$this->UnitPrice = $this->UnitPrice();
		$this->UnitPriceAlternative = $this->_unitPriceAlternative;
	}

}