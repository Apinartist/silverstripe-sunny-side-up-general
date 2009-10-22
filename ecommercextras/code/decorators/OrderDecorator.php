<?php

class OrderDecorator extends DataObjectDecorator {

 	private static $order_id_start_number = 0;
		static function set_order_id_start_number($number) {self::$order_id_start_number = $number;}

	function extraStatics() {
		return array(
			'casting' => array(
				'MemberSummary' => 'Text',
			)
		);
	}

	function getMemberSummary() {
		return "A".$this->_MemberSummary();
	}

	function MemberSummary() {
		return "B".$this->_MemberSummary();
	}

	function _MemberSummary() {
		$m = $this->owner->Member();
		if($m) {
			return $m->FirstName.' '.$m->Surname.' ('.$m->Email.')';
		}
	}

	function requireDefaultRecords() {
		parent::requireDefaultRecords();
		$number = intval(self::$order_id_start_number);

		if($number) {
			$count = DB::query("SELECT COUNT( ID ) FROM `Order` ")->value();
		 	if($count > 0) {
				$currentMax = DB::Query("SELECT MAX( ID ) FROM `Order`")->value();
				if($number > $currentMax) {
					DB::query("ALTER TABLE `Order`  AUTO_INCREMENT = $number ROW_FORMAT = DYNAMIC ");
			 		DataBase::alteration_message("Change OrderID start number to ".$number, "created");
				}
			}
		}
	}
}



