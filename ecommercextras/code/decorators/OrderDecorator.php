<?php

class OrderDecorator extends DataObjectDecorator {

	protected static $order_status_option_string = '';
	protected static $order_status_option_default = '';
	private static $has_alternative_status_options = false;
		public static function set_alternative_status_options($optionstring, $default) {
			self::$order_status_option_string = $optionstring;
			self::$order_status_option_default = $default;
			self::$has_alternative_status_options = true;
		}

 	private static $order_id_start_number = 0;
		static function set_order_id_start_number($number) {self::$order_id_start_number = $number;}

	static function check_data_integrity_for_orders() {
		$badOrders = DataObject::get("Order", 'Status = ""');
		$list = singleton('Order')->dbObject('Status')->enumValues(false);
		$firstOption = current($list);
		foreach($badOrders as $order) {
			$order->Status = $firstOption;
			$order->write();
			Database::alteration_message("No order status for order number #".$order->ID." reverting to $firstOption.","error");
		}
	}

	function extraStatics() {
		if(self::$has_alternative_status_options) {
			$db[] = array('Status' => 'Enum("'.self::$has_alternative_status_options.'","'.self::$order_status_option_default.'")"');
		}
		return array(
			$db,
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
}



