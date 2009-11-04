<?php

/**
 *@author nicolaas [at] sunnysideup.co.nz
 *@package ecommerce
 *@subpackage ecommercextras
 *@description adds extra functionality to the Order Class
 **/

class OrderDecorator extends DataObjectDecorator {

 	private static $order_id_start_number = 0;
		static function set_order_id_start_number($number) {self::$order_id_start_number = $number;}

	public static function get_order_status_options() {
		$newArray = singleton('Order')->dbObject('Status')->enumValues(false);
		return $newArray;
	}

	public static function check_data_integrity_for_orders() {
		$list = self::get_order_status_options();
		$firstOption = current($list);
		$badOrders = DataObject::get("Order", 'Status = ""');
		foreach($badOrders as $order) {
			$order->Status = $firstOption;
			$order->write();
			Database::alteration_message("No order status for order number #".$order->ID." reverting to: $firstOption.","error");
		}
	}

	function extraStatics() {
		return array(
			'db' => array(
				"CustomerOrderNote" => "Text"
			),
			'has_many' => array(
				"OrderStatusLogInternal" => "OrderStatusLogInternal"
			),
			'casting' => array(
				'MemberSummary' => 'Text',
			)
		);
	}

	function MemberSummary() {
		$m = $this->owner->Member();
		if($m) {
			return $m->FirstName.' '.$m->Surname.' ('.$m->Email.')';
		}
	}

	function requireDefaultRecords() {
		parent::requireDefaultRecords();
		//set starting order number ID
		$number = intval(self::$order_id_start_number);
		$currentMax = 0;
		//TO DO: use proper DB language
		if($number) {
			$count = DB::query("SELECT COUNT( ID ) FROM `Order` ")->value();
		 	if($count > 0) {
				$currentMax = DB::Query("SELECT MAX( ID ) FROM `Order`")->value();
			}
			if($number > $currentMax) {
				DB::query("ALTER TABLE `Order`  AUTO_INCREMENT = $number ROW_FORMAT = DYNAMIC ");
				DataBase::alteration_message("Change OrderID start number to ".$number, "created");
			}
		}
	}

	// we need to
	static function get_receipt_email() {
		return self::$receipt_email;
	}

}



