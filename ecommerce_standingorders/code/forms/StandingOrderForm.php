<?php


class StandingOrderForm extends Form {
	
	public function __construct($controller, $name, $orderID = null, $update = false) {
		
		if($update) {
			$standingOrder = DataObject::get_by_id('StandingOrder', $orderID);
			$order = $controller->BlankOrder();
		}
		else {
			$order = $orderID ? DataObject::get_by_id('Order', $orderID) : $controller->BlankOrder();
			$standingOrder = null;	
		}
		
		$fields = new FieldSet();
		
		$fields->push(new HeaderField('AlternativesHeader', 'Alternative Products'));
		
		$items = $order->Items();

		$products = DataObject::get('FishProduct');
		$productsMap = $products->map('ID', 'Title', ' ');
		
		if($items) {
			foreach($items as $item) {
				$fields->push(new DropdownField('_Alternatives['.$item->getProductID().'][0]', $item->ProductTitle(), $productsMap));
				$fields->push(new DropdownField('_Alternatives['.$item->getProductID().'][1]', '', $productsMap));
				$fields->push(new DropdownField('_Alternatives['.$item->getProductID().'][2]', '', $productsMap));
				$fields->push(new DropdownField('_Alternatives['.$item->getProductID().'][3]', '', $productsMap));
				$fields->push(new DropdownField('_Alternatives['.$item->getProductID().'][4]', '', $productsMap));
			}
		}
		
		$fields->push(new HeaderField('DetailsHeader', 'Standing Order Details'));
		//$fields->push(new DropdownField('PaymentMethod', 'Payment Method', StandingOrder::payment_methods()));
		$fields->push(new CalendarDateField('Start', 'Start'));
		$fields->push(new CalendarDateField('End', 'End (Optional)'));
		$fields->push(new DropdownField('Period', 'Period', StandingOrder::$period_fields));
		
		$fields->push(new ListboxField(
			'_DeliveryDays',
			'Deilvery days:',
			array_combine(
				StandingOrder::delivery_days(),
				StandingOrder::delivery_days()
			),
			null,
			4,
			true
		));
		
		$fields->push(new TextareaField('Notes', 'Notes'));
		if($order->ID) $fields->push(new HiddenField('OrderID', 'OrderID', $order->ID));
		if($standingOrder) $fields->push(new HiddenField('StandingOrderID', 'StandingOrderID', $standingOrder->ID));

		$actions = new FieldSet();

		if($standingOrder) {
			$actions->push(new FormAction('doSave', 'Save'));
		}
		else {
			$actions->push(new FormAction('doCreate', 'Create'));
		}
		
		$required[] = 'Start';
		$required[] = 'Period';
		$required[] = 'DeliveryDays';
		
		$requiredFields = new RequiredFields($required);
		
		parent::__construct($controller, $name, $fields, $actions, $requiredFields);
		
		if($standingOrder) { 
			$this->loadDataFrom(array(
				'Start' => $standingOrder->Start,
				'End' => $standingOrder->End,
				'Period' => $standingOrder->Period,
				'Notes' => $standingOrder->Notes,
				'_DeliveryDays' => explode(',', $standingOrder->DeliveryDays),
				'_Alternatives' => unserialize($standingOrder->Alternatives),
			));
		}
	}
	
	/**
	 * Create a new stadning
	 */
	public function doCreate($data, $form, $request) {
		$memberID = Member::currentUserID();

		if(isset($data['OrderID'])) {
			$order = DataObject::get_one('Order', 'Order.ID = \''.$data['OrderID'].'\' AND MemberID = \''.$memberID.'\'');
		}
		else {
			$order = $form->Controller()->BlankOrder();
		}
		
		if($order) {
			$params = array();
			
			if(isset($data['Start'])) $params['Start'] = $data['Start'];
			if(isset($data['End'])) $params['End'] = $data['End'];
			if(isset($data['Period'])) $params['Period'] = $data['Period'];
			if(isset($data['Notes'])) $params['Notes'] = $data['Notes'];
	
			$standingOrder = StandingOrder::createFromOrder($order, $params);
	
			Director::redirect(StandingOrdersPage::get_standing_order_link('view', $standingOrder->ID));
		}
		else Director::redirectBack();

		return true;
	}
	
	/**
	 * Save the changes
	 */
	public function doSave($data, $form, $request) {
		Versioned::reading_stage('Stage');
		
		$memberID = Member::currentUserID();
		
		$standingOrder = DataObject::get_one('StandingOrder', 'StandingOrder.ID = \''.$data['StandingOrderID'].'\' AND MemberID = \''.$memberID.'\'');

		if($standingOrder) {
			$params = array();
			
			//stop versioning while we make alterations
			StandingOrder::$update_versions = false;
			
			$orderItems = $standingOrder->OrderItems();
			
			if($orderItems) {
				foreach($orderItems as $orderItem) {
					$orderItem->delete();
				}
			}
			
			$orderItems = $form->Controller()->BlankOrder()->Items();
		
			if($orderItems) {
				foreach($orderItems as $orderItem) {
					$standingOrderItem = new StandingOrder_OrderItem();
					$standingOrderItem->OrderID = $standingOrder->ID;
					$standingOrderItem->OrderVersion = $standingOrder->Version;
					$standingOrderItem->ProductID = $orderItem->ProductID;
					$standingOrderItem->Quantity = $orderItem->Quantity;
					$standingOrderItem->write();
				}
			}
			
			//start versioning again
			StandingOrder::$update_versions = true;
			
			if(isset($data['Start'])) $params['Start'] = $data['Start'];
			if(isset($data['End'])) $params['End'] = $data['End'];
			if(isset($data['Period'])) $params['Period'] = $data['Period'];
			if(isset($data['Notes'])) $params['Notes'] = $data['Notes'];
	
			$standingOrder->update($params);
			$standingOrder->Status = 'Pending';
			$standingOrder->write();
			
			$standingOrder->sendUpdate();
			
			Session::set('StandingOrder', null);
		}
		
		Director::redirect(StandingOrdersPage::get_standing_order_link('view', $standingOrder->ID));
		
		return true;
	}
	
}