<?php

class ManageablePriceOrderItem extends Product_OrderItem {

	protected $_usesAlternativePrice;

	protected $_unitPriceAlternative;

	static $db = array(
		'UsesAlternativePrice' => 'Boolean',
		'UnitPriceAlternative' => 'Currency'
	);

	static $has_one = array(
		'Product' => 'Product'
	);

	public function __construct($object = null, $quantity = 1) {
		// Case 1: Constructed by getting OrderItem from DB
		if(is_array($object)) {
			$this->_usesAlternativePrice = $object['UsesAlternativePrice'];
			$this->_unitPriceAlternative = $object['UnitPriceAlternative'];
		}
		// Case 2: Constructed in memory
		if(is_object($object)) {
			$this->_usesAlternativePrice = $object->UsesAlternativePrice;
			$this->_unitPriceAlternative = $object->UnitPriceAlternative;
		}
 		parent::__construct($object, $quantity);
	}

	public function setAlternativeUnitPrice($price) {
		$this->_unitPriceAlternative = $price;
		$this->_usesAlternativePrice = true;
	}

	public function Product($current = false) {
		if($current) {
			$product = DataObject::get_by_id('Product', $this->_productID);
		}
		else  {
			return $product = Versioned::get_version('Product', $this->_productID, $this->_productVersion);
		}
		return $product;
	}

	function UnitPrice() {
		//check current record
		if($this->_usesAlternativePrice) {
			return $this->_unitPriceAlternative;
		}
		elseif($this->UsesAlternativePrice) {
			return $this->UnitPriceAlternative;
		}
		else {
			return $this->Product()->Price;
		}
	}

	function onBeforeWrite() {
		parent::onBeforeWrite();
		$this->UsesAlternativePrice = $this->_usesAlternativePrice;
		$this->UnitPriceAlternative = $this->_unitPriceAlternative;
	}

}