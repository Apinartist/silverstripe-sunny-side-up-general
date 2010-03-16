<?php


class StandingOrderForm extends Form {

	public function __construct($controller, $name, $orderID = null, $update = false) {

		if($update) {
			$standingOrder = DataObject::get_by_id('StandingOrder', $orderID);
			//we have to make sure that order items are loaded into memory...
			$items = $standingOrder->OrderItems();
			$order = $controller->BlankOrder();
			$standingOrderID = $standingOrder->ID;
		}
		else {
			$order = $orderID ? DataObject::get_by_id('Order', $orderID) : $controller->BlankOrder();
			$items = $order->Items();
			$standingOrder = null;
			$standingOrderID = 0;
		}
		$items = $order->Items();
		$fields = new FieldSet();
		$fields->push(new HeaderField('AlternativesHeader', 'Products'));

		$products = DataObject::get('FishProduct');
		$productsMap = $products->map('ID', 'Title', ' ');
		if($standingOrder) {
			//$fields->push($this->complexTableField($standingOrder));
		}
		if($items) {
			foreach($items as $item) {
				$fields->push(new DropdownField('Product[ID]['.$item->getProductID().']', "Preferred Product", $productsMap, $item->getProductID()));
				$fields->push(new NumericField('Product[Quantity]['.$item->getProductID().']', " ... quantity", $item->Quantity));
				$fields->push(new DropdownField('_Alternatives['.$item->getProductID().'][0]', " ... alternative 1", $productsMap));
				$fields->push(new DropdownField('_Alternatives['.$item->getProductID().'][1]', " ... alternative 2", $productsMap));
				$fields->push(new DropdownField('_Alternatives['.$item->getProductID().'][2]', " ... alternative 3", $productsMap));
				$fields->push(new DropdownField('_Alternatives['.$item->getProductID().'][3]', " ... alternative 4", $productsMap));
				$fields->push(new DropdownField('_Alternatives['.$item->getProductID().'][4]', " ... alternative 5", $productsMap));
			}
		}

		$fields->push(new HeaderField('DetailsHeader', 'Standing Order Details'));
		$fields->push(new ListboxField('PaymentMethod', 'Payment Method', StandingOrder::get_payment_methods(), null, count(StandingOrder::get_payment_methods())));
		$fields->push(new CalendarDateField('Start', 'Start'));
		$fields->push(new CalendarDateField('End', 'End'));
		$fields->push(new ListboxField('Period', 'Period', StandingOrder::get_period_fields(), null, count(StandingOrder::get_period_fields())));

		$fields->push(new ListboxField(
			'DeliveryDay',
			'Delivery day:',
			$source = array_combine(
				StandingOrder::get_delivery_days(),
				StandingOrder::get_delivery_days()
			),
			null,
			count(StandingOrder::get_delivery_days()),
			false
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

		$required["Start"] = 'Start';
		$required["End"] = 'End';
		$required["Period"] = 'Period';
		$required["DeliveryDay"] = 'DeliveryDay';

		$requiredFields = new RequiredFields($required);

		parent::__construct($controller, $name, $fields, $actions, $requiredFields);

		if($standingOrder) {
			$this->loadDataFrom(array(
				'Start' => $standingOrder->Start,
				'End' => $standingOrder->End,
				'Period' => $standingOrder->Period,
				'Notes' => $standingOrder->Notes,
				'DeliveryDay' => $standingOrder->DeliveryDay,
				'PaymentMethod' => $standingOrder->PaymentMethod,
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
			$params = $this->dataCheck($data);
			$standingOrder = StandingOrder::createFromOrder($order, $params);
			$orderItems = $standingOrder->OrderItems();
			if($orderItems) {
				foreach($orderItems as $orderItem) {
					if(isset($data["Product"]["ID"][$orderItem->ProductID])) {$newProductID = $data["Product"]["ID"][$orderItem->ProductID];}
					if(isset($data["Product"]["Quantity"][$orderItem->ProductID])) {$newQuantity = $data["Product"]["Quantity"][$orderItem->ProductID];}
					$change = false;
					if($newProductID != $orderItem->ProductID && $newProductID) {$orderItem->ProductID = $newProductID; $change = true;}
					if($newQuantity != $orderItem->ProductID && ($newQuantity || $newQuantity === 0)) {$orderItem->Quantity = $newQuantity; $change = true;}
					if($change) {
						$orderItem->write();
					}
				}
			}

			Director::redirect(StandingOrdersPage::get_standing_order_link('view', $standingOrder->ID));
		}
		else {
			Director::redirectBack();
		}

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
					$standingOrderItem->ProductID = $data["Product"]["ID"][$orderItem->ProductID];
					$standingOrderItem->Quantity = $data["Product"]["Quantity"][$orderItem->ProductID];
					$standingOrderItem->write();
				}
			}

			//start versioning again
			StandingOrder::$update_versions = true;

			$params = $this->dataCheck($data);

			$standingOrder->update($params);
			$standingOrder->Status = 'Pending';
			$standingOrder->write();

			//$standingOrder->sendUpdate();

			Session::set('StandingOrder', null);
		}

		Director::redirect(StandingOrdersPage::get_standing_order_link('view', $standingOrder->ID));

		return true;
	}

	protected function dataCheck($data) {
		if(isset($data['Start'])) {$params['Start'] = $data['Start'];} else {$params["Start"] = Date("Y-m-d");}
		if(isset($data['End'])) {$params['End'] = $data['End'];} else {$params["End"] = Date("Y-m-d", strtotime("+1 year"));}
		if(isset($data['Period'])) {$params['Period'] = $data['Period'];} else {$data['Period'] = StandingOrder::default_period_key();}
		if(isset($data['DeliveryDay'])) {$params['DeliveryDay'] = $data['DeliveryDay'];} else {$data['DeliveryDay'] = StandingOrder::default_delivery_day_key();}
		if(isset($data['PaymentMethod'])) {$params['PaymentMethod'] = $data['PaymentMethod'];} else {$data['PaymentMethod'] = StandingOrder::default_payment_method_key();}
		if(isset($data['Notes'])) $params['Notes'] = $data['Notes'];
		return Convert::raw2sql($params);
	}

	function complexTableField($controller) {
		$t = new HasManyComplexTableField(
			$controller,
			$name = "OrderItems",
			$sourceClass = "StandingOrder_OrderItem",
			$fieldList = array("Quantity" => "Quantity"),
			$detailFormFields = null,
			$sourceFilter = "StandingOrder_OrderItem.OrderID = ".$controller->ID,
			$sourceSort = "",
			$sourceJoin = ""
		);
		return $t;
	}

}