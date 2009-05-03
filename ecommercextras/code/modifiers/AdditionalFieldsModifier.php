<?php

/**
 * Calculates the shipping cost of an order, by taking the products
 * and calculating the shipping weight, based on an array set in _config
 *
 * ASSUMPTION: The total order weight can be at maximum the last item
 * in the $shippingCosts array.
 *
 * @package ecommerce
 */
class AdditionalFieldsModifier extends OrderModifier {
	static $db = array(	);	static function show_form() {		return true;	}	static $requiredFieldsArray = Array(	);	static $paymentChoice = array();
	static $orderFormInstance = null;	static function addPaymentOption($name, $description) {		self::$paymentChoice[$name] = $description;	}	static function get_form($order) {		if(!isset(self::$orderFormInstance)) {			$CheckoutPage = DataObject::get_one("CheckoutPage");			$FieldSet = new FieldSet();			$FieldSet->push(new HeaderField("Delivery Details"));		}		return self::$orderFormInstance;	}	function ShowInTable() {		return false;	}	protected static $is_chargable = false;	function LiveAmount() {		return 0;	}	function TableTitle() {		return '';	}	public function onBeforeWrite() {		parent::onBeforeWrite();		foreach(self::$db as $fieldName=>$fieldType) {			if($fieldName == "DeliveryDate") {				$item = new Date("temp");				$item->setValue(Session::get("AdditionalFieldsModifier".$fieldName));				$this->$fieldName = $item->Format("Y-m-d");			}			else {				$this->$fieldName = Session::get("AdditionalFieldsModifier".$fieldName);			}			if(!$this->$fieldName && Session::get("AdditionalFieldsModifier".$fieldName)) {				$this->$fieldName = Session::get("AdditionalFieldsModifier".$fieldName);			}		}	}}

