<?php

class ManageablePriceOrderItem extends Product_OrderItem {

	protected $_unitPriceAlternative;
	protected $_usesAlternativePrice;

	static $db = array(
		'UsesAlternativePrice' => 'Boolean',
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
		$this->_usesAlternativePrice = true;
	}

	public function Product($current = false) {
		if($current) {
			$product = DataObject::get_by_id('Product', $this->_productID);
		}
		else  {
			return $product = Versioned::get_version('Product', $this->_productID, $this->_productVersion);
		}
		$product->Price = $this->UnitPrice();
		return $product;
	}

	function UnitPrice() {
		//check current record
		if($this->_usesAlternativePrice) {
			return $this->_unitPriceAlternative;
		}
		elseif($this->UsesAlternativePrice && $this->ID) {
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