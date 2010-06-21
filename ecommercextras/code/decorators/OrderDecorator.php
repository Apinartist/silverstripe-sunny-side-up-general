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
				"CustomerOrderNote" => "Text",
				"POBox" => "Text",
				'ShippingPhone' => 'Varchar(30)',
				'ShippingPostalCode' => 'Varchar(30)'
			),
			'has_many' => array(
				"OrderStatusLogWithDetails" => "OrderStatusLogWithDetails"
			),
			'casting' => array(
				'MemberSummary' => 'Text',
			),
			'searchable_fields' => array(
				'Member.FirstName' => array('title' => 'Customer Name', 'filter' => 'PartialMatchFilter'),
				'Member.Email' => array('title' => 'Customer Email', 'filter' => 'PartialMatchFilter'),
				'Status'/*,
				'From' => array(
					'field' => 'CalendarDateField',
					'filter' => 'OrderDecorator_EqualOrGreaterFilter'
				),
				'To' => array(
					'field' => 'CalendarDateField',
					'filter' => 'OrderDecorator_EqualOrLessFilter'
				)*/
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
		$bt = defined('DB::USE_ANSI_SQL') ? "\"" : "`";
//set starting order number ID
		$number = intval(self::$order_id_start_number);
		$currentMax = 0;
		//TO DO: use proper DB language
		if($number) {
			$count = DB::query("SELECT COUNT( ID ) FROM {$bt}Order{$bt} ")->value();
		 	if($count > 0) {
				$currentMax = DB::Query("SELECT MAX( ID ) FROM {$bt}Order{$bt}")->value();
			}
			if($number > $currentMax) {
				DB::query("ALTER TABLE {$bt}Order{$bt}  AUTO_INCREMENT = $number ROW_FORMAT = DYNAMIC ");
				//Database::alteration_message("Change OrderID start number to ".$number, "created");
			}
		}
	}

	// we need to
	function getReceiptEmail() {
		//return self::$receipt_email;
		return $this->owner->stat("receipt_email");
	}

	function getReceiptSubject() {
		//return self::$receipt_subject;
		return $this->owner->stat("receipt_subject");
	}

	/*
	function onAfterWrite() {
		if($this->owner->ID) {
			//just to make sure that order arrives!
			$subject = "new order on webshop #".$this->owner->ID;
			$body = "go to ".Director::absoluteURL("/OrderReport_Popup/invoice/").$this->owner->ID;
			@mail(Email::getAdminEmail(),$subject ,$body );
		}
		parent::onAfterWrite();
	}
	*/

	function onAfterWrite() {
		parent::onAfterWrite();
		HourlyEcommerceGroupUpdate::add_members_to_customer_group();
	}

	function updateSummaryFields(&$fields){
		$fields['Created'] = 'Date';
		$fields['Member.FirstName'] = 'First Name';
		$fields['Member.Surname'] = 'Surname';
		$fields['Status'] = 'Status';
		$fields['Total'] = 'Total';
	}

	function updateCMSFields(FieldSet &$fields) {
		$member = $this->owner->Member();
		$fields->addFieldToTab('Root.Export', $this->getExportTable());
		$fields->addFieldToTab('Root.Main', new HeaderField('MainDetails', 'Main Details'), 'Status');
		$fields->addFieldToTab('Root.Main', new ReadonlyField('OrderNo', 'Order No', "#{$this->owner->ID}"), 'Status');
		$fields->addFieldToTab('Root.Main', new ReadonlyField('Date', 'Date', date('l jS F Y h:i:s A', strtotime($this->owner->Created))), 'Status');
		$fields->addFieldToTab('Root.Main', new ReadonlyField('Name', 'Name', "$member->FirstName $member->Surname ($member->Email)"), 'Status');
		$fields->removeByName('Status');
		$fields->addFieldToTab('Root.Main', new ReadonlyField('TheStatus', 'Status', $this->owner->Status), 'Country');
		//$fields->replaceField('Status', new ReadonlyField('Status', 'Status', $this->owner->Status));
		$attributes = $fields->findOrMakeTab('Root.Attributes')->fieldByName('Attributes');
		$attributes->setFieldList(array('TableTitle' => 'Title'));
		$attributesReadonly = $attributes->performReadonlyTransformation();
		$attributesReadonly->setPermissions(array());
		$removeTabs = array('Attributes', 'Order Status Logs', 'Payments', 'Order Status Log With Details', 'Country', 'UseShippingAddress', 'ShippingName', 'ShippingAddress', 'ShippingAddress2', 'ShippingCity', 'ShippingCountry', 'Printed', 'Member', 'CustomerOrderNote');
		foreach($removeTabs as $tab) $fields->removeByName($tab);
		$fields->addFieldsToTab('Root.Main', array(
			new HeaderField('PriceDetails', 'Price Details'),
			$attributesReadonly,
			new ReadonlyField('TheTotal', 'Total', "{$this->owner->Total()} NZ$"),
			new HeaderField('ShippingDetails', 'Shipping Details'),
			new ReadonlyField('DeliveryName', 'Name', $this->owner->UseShippingAddress ? $this->owner->ShippingName : "$member->FirstName $member->Surname"),
			new ReadonlyField('DeliveryAddress', 'Address', $this->owner->UseShippingAddress ? "{$this->owner->ShippingAddress}\n{$this->owner->ShippingAddress2}\n{$this->owner->ShippingCity}\nNew Zealand" : "$member->Address\n$member->AddressLine2\n$member->City\nNew Zealand"),
		));
	}

	// Extra Functions

	function Summary($limit = 40) {
		$items = $this->owner->Items();
		foreach($items as $item) $summary[] = $item->TableTitle();
		$text = DBField::create('Text', implode(', ', $summary));
		return $text->LimitCharacters($limit);
	}

	function PlaintextProductSummary() {
		return "Summary of products goes here";
	}

	function order() {
		return "hello";
	}

	//hack for the sales report
	function Payment() {
		return DataObject::get("Payment", "OrderID = ".$this->owner->ID);
	}

}



class OrderDecorator_EqualOrGreaterFilter extends ExactMatchFilter {

	public function apply(SQLQuery $query) {
		$bt = defined('DB::USE_ANSI_SQL') ? "\"" : "`";
		$query = $this->applyRelation($query);
		$value = $this->getValue();
		$date = explode('/', $value);
		$value = "$date[2]-$date[1]-$date[0]";
		return $query->where("{$bt}Order{$bt}.{$bt}Created{$bt} >= '$value'");
	}
}

class OrderDecorator_EqualOrLessFilter extends ExactMatchFilter {

	public function apply(SQLQuery $query) {
		$bt = defined('DB::USE_ANSI_SQL') ? "\"" : "`";
		$query = $this->applyRelation($query);
		$value = $this->getValue();
		$date = explode('/', $value);
		$value = "$date[2]-$date[1]-$date[0]";
		return $query->where("{$bt}Order{$bt}.{$bt}Created{$bt} <= '$value'");
	}
}



