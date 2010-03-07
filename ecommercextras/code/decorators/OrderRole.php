<?php

class OrderRole extends DataObjectDecorator {
	
	function alternatePermissionCheck() {return false;}
	
	function extraDBFields() {
		return array(
			'db' => array(
				'POBox' => 'Text'
			),
			'searchable_fields' => array(
				'Member.FirstName' => array('title' => 'Client Name'),
				'Member.Email' => array('title' => 'Client Email'),
				'Status'/*,
				'From' => array(
					'field' => 'CalendarDateField',
					'filter' => 'OrderRole_EqualOrGreaterFilter'
				),
				'To' => array(
					'field' => 'CalendarDateField',
					'filter' => 'OrderRole_EqualOrLessFilter'
				)*/
			)
		);
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
}

class OrderRole_EqualOrGreaterFilter extends ExactMatchFilter {
	
	public function apply(SQLQuery $query) {
		$query = $this->applyRelation($query);
		$value = $this->getValue();
		$date = explode('/', $value);
		$value = "$date[2]-$date[1]-$date[0]";
		return $query->where("`Order`.`Created` >= '$value'");
	}
}

class OrderRole_EqualOrLessFilter extends ExactMatchFilter {
	
	public function apply(SQLQuery $query) {
		$query = $this->applyRelation($query);
		$value = $this->getValue();
		$date = explode('/', $value);
		$value = "$date[2]-$date[1]-$date[0]";
		return $query->where("`Order`.`Created` <= '$value'");
	}
}

?>