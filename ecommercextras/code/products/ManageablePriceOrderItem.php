<?php

class ManageablePriceOrderItem extends Product_OrderItem {

	protected $_unitPriceAlternative;

	static $db = array(
		'UnitPrice' => 'Currency',
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
		if($this->_unitPriceAlternative) {
			debug::show(1);
			return $this->_unitPriceAlternative;
		}
		elseif($this->UnitPriceAlternative) {
			debug::show(2);
			return $this->UnitPriceAlternative;
		}
		else {
			debug::show(3);
			return $this->Product()->Price;
		}
	}

	function onBeforeWrite() {
		parent::onBeforeWrite();
		$this->UnitPrice = $this->UnitPrice();
		$this->UnitPriceAlternative = $this->_unitPriceAlternative;
	}

}