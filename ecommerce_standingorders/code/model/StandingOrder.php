<?php

/**
 * @author Michael Mitchell <michael@sunnysideup.co.nz>
 */

class StandingOrder extends DataObject {

	public static $db = array(
		'Status' => "Enum('Pending, Active, MemberCancelled, AdminCancelled, Finished', 'Pending')",

		'Start' => 'Date',
		'End' => 'Date',
		'Period' => 'Varchar',

		'LastCreated' => 'Date',

		'PaymentMethod' => 'Varchar',

		//Guytons.co.nz specific
		'DeliveryDay' => 'Text', //Serialized Array
		'Alternatives' => 'Text', //Serialized Array
		'Items' => 'Text', //FOR SEARCH PURPOSES ONLY!

		'Notes' => 'Text',
	);

	public static $has_one = array(
		'Member' => 'Member',
	);

	public static $has_many = array(
		'OrderItems' => 'StandingOrder_OrderItem' //products & quanitites
	);

	public static $casting = array(
		"OrderItemList" => "Text",
		"LastOrderDate" => "Date",
		"NextOrderDate" => "Date"
	);

	public static $searchable_fields = array(
		"Member.Email" => "PartialMatchFilter",
		"Member.Surname" => "PartialMatchFilter",
		"Items" => "PartialMatchFilter",
		"Status"
	);

	public static $summary_fields = array(
		'Member.Email' => 'First Name',
		'Member.Surname' => 'Surname',
		'OrderItemList' => 'Order Item List',
		'Start' => 'Start',
		'End' => 'End',
		'Period' => 'Period',
		'Status' => 'Status'
	);

	public static $default_sort = 'Created DESC';

	public static $versioning = array(
		'Stage'
	);

	public static $extensions = array(
		"Versioned('Stage')"
	);

	/**
	 * Dropdown options for Period
	 * @var array 'strtotime period' > 'nice name'
	 */
	public static $period_fields = array(
		'1 week' => 'Weekly',
		'2 weeks' => 'Fornightly',
		'1 month' => 'Monthly',
	);

	/**
	 * Should orderItems/orders call a write to update versions?
	 */
	public static $update_versions = true;

	protected static $status_nice = array(
		'Pending' => 'Pending',
		'Active' => 'Active',
		'MemberCancelled' => 'Pending Cancellation',
		'AdminCancelled' => 'Cancelled',
		'Finished' => 'Finished',
	);

	/**
	 * delivery days options for guytons.co.nz
	 * @var array
	 */
	protected static $delivery_days = array(
		'Monday',
		'Tuesday',
		'Wednesday',
		'Thursday',
		'Friday',
		'Saturday',
		'Sunday',
	);

	protected static $payment_methods = array(
		'DirectCreditPayment' => 'Direct Credit (payment into bank account)'
	);

	/**
	 * This is the from address that the receipt
	 * email contains. e.g. "info@shopname.com"
	 *
	 * @var string
	 */
	protected static $receipt_email;

	/**
	 * This is the subject that the receipt
	 * email will contain. e.g. "Joe's Shop Receipt".
	 *
	 * @var string
	 */
	protected static $receipt_subject;

	/**
	 * Guytons.co.nz specific, set the delivery days options
	 * @param $days array of days
	 * @return null
	 */
	public static function set_delivery_days($days = array()) {
		self::$delivery_days = $days;
	}

	public static function delivery_days() {
		$array = array();
		$page = DataObject::get_one("StandingOrdersPage");
		if($page) {
			$array = explode(",", $page->OrderDays);
		}
		if(count($array)) {
			return $array;
		}
		else {
			return self::$delivery_days;
		}
	}

	/**
	 * @param $days array of days
	 * @return null
	 */
	public static function set_payment_methods($payment_methods = array()) {
		self::$payment_methods = $payment_methods;
	}

	public static function payment_methods() {
		return self::$payment_methods;
	}

	public static function set_receipt_email($receipt_email) {
		self::$receipt_email = $receipt_email;
	}

	public static function receipt_email() {
		return self::$receipt_email;
	}

	public static function set_receipt_subject($receipt_subject) {
		self::$receipt_subject = $receipt_subject;
	}

	public static function receipt_subject() {
		return self::$receipt_subject;
	}

	/**
	 * Create due draft orders
	 */
	public static function createDraftOrders() {
		Versioned::reading_stage('Stage');

		set_time_limit(0); //might take a while with lots of orders


		//get all standing orders
		$standingOrders = DataObject::get('StandingOrder', 'Status = \'Active\'');

		if($standingOrders) {
			foreach($standingOrders as $standingOrder) {
				//current time + period is less than LastCreated and less then end
				$currentTime = (strtotime(date('Y-m-d'))-1);
				$startTime = strtotime($standingOrder->FirstOrderDate());
				$endTime = strtotime($standingOrder->End);
				$lastTime = strtotime($standingOrder->LastCreated);
				$periodTime = strtotime('+'.$standingOrder->Period, $lastTime);

				if($periodTime < $currentTime && $periodTime < $endTime) {
					StandingOrder::$update_versions = false;

					$standingOrder->createDraftOrder(false);
					$standingOrder->LastCreated = date('Y-m-d');
					$standingOrder->writeWithoutVersion();

					StandingOrder::$update_versions = true;
				}
				else if($periodTime > $endTime) {
					$standingOrder->Status = 'Finished';
					$standingOrder->write();
					$standingOrder->sendUpdate();
				}
			}
		}


	}

	/**
	 * Create a StandingOrder from a regular Order and its Order Items
	 * @param Order $Order
	 * @param $params params
	 * @return StandingOrder
	 */
	public static function createFromOrder(Order $Order, $params = array()) {
		Versioned::reading_stage('Stage');

		$standingOrder = new StandingOrder();
		$standingOrder->Status = 'Pending';
		$standingOrder->MemberID = $Order->MemberID;
		$standingOrder->update($params);
		$standingOrder->write();

		$orderItems = $Order->Items();

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

		$standingOrder->write();

		$standingOrder->sendReceipt();

		return $standingOrder;
	}

	function OrderItemList() {
		$a = array();
		if($list = $this->OrderItems()) {
			foreach($list as $item) {
				$a[] = $item->Quantity . " x " . $item->Title();
			}
		}
		return "Products: ".implode(", ", $a).".";
	}

	function getOrderItemList() {
		return $this->OrderItemList();
	}

	function FirstOrderDate() {
		$startTime = strtotime($this->Start);
		$firstTime = strtotime("Next ".$this->DeliveryDay, $startTime);
		return Date::create($className = "Date", $value = Date("Y-m-d", $firstTime));
	}

	function getFirstOrderDate() {
		return $this->FirstOrderDate();
	}

	function NextOrderDate() {
		$lastTime = strtotime($this->LastCreated);
		$nextTime = strtotime('+'.$this->Period, $lastTime);
		return Date::create($className = "Date", $value = Date("Y-m-d", $nextTime));
	}

	function getNextOrderDate() {
		return $this->NextOrderDate();
	}

	function LastOrderDate() {
		return Date::create($className = "Date", $value = $this->LastCreated);
	}

	function getLastOrderDate() {
		return $this->LastOrderDate();
	}

	/**
	 * CMS Fields for ModelAdmin, use different fields for adding/editing
	 * @see sapphire/core/model/DataObject#getCMSFields($params)
	 */
	public function getCMSFields() {
		if($this->ID) {
			return self::getCMSFields_edit();
		}
		else return self::getCMSFields_add();
	}

	/**
	 * CMS Fields to adding via ModelAdmin
	 * @return FieldSet
	 */
	public function getCMSFields_add() {
		$fields = new FieldSet(
			new TabSet('Root',
				new Tab('Main',
					new AutocompleteTextField('Email', 'Email (Auto complete)', 'admin/security/autocomplete/Email'),
					new DropdownField('PaymentMethod', 'Payment Method', self::$payment_methods),
					new CalendarDateField('Start', 'Start'),
					new CalendarDateField('End', 'End (Optional)'),
					new DropdownField('Period', 'Period', self::$period_fields),
					new ListboxField(
						$name = '_DeliveryDay',
						$title = 'Delivery day:',
						$source = array_combine(
							self::delivery_days(),
							self::delivery_days()
						),
						$value = explode(',', $this->DeliveryDay),
						$size = 7,
						$multiple = false
					),
					new TextareaField('Notes', 'Notes')
				)
			)
		);

		return $fields;
	}

	/**
	 * CMS Fields to adding via ModelAdmin
	 * @return FieldSet
	 */
	public function getCMSFields_edit() {
		$countries = Geoip::getCountryDropDown();
		$lastCreatedObj = $this->LastOrderDate();
		$lastCreated = $lastCreatedObj->Long();
		$firstCreatedObj = $this->FirstOrderDate();
		$firstCreated = $firstCreatedObj->Long();
		if($this->Status == "Active") {
			$nextCreatedObj = $this->NextOrderDate();
			$nextCreated = $nextCreatedObj->Long();
		}
		else {
			$lastCreated = "Standing Order Not Active";
			$nextCreated = "Standing Order Not Active";
		}
		$fields = new FieldSet(
			new TabSet('Root',
				new Tab('Main',
					new LiteralField('Readonly[Member]',
<<<HTML
	<div class="field readonly " id="Readonly[Member]">
		<label for="Form_EditForm_Readonly-Member" class="left">Member</label>
		<div class="middleColumn">
			<span class="readonly" id="Form_EditForm_Readonly-Member">{$this->Member()->getTitle()} ({$this->Member()->Email}) <a target="_blank" href="admin/security/EditForm/field/Members/item/{$this->Member()->ID}/show">(view)</a></span>
			<input type="hidden" value="{$this->Member()->getTitle()} ({$this->Member()->Email})" name="Readonly[Member]"/>
		</div>
	</div>
HTML
					),
					new DropdownField('Status', 'Status', self::$status_nice),
					//new DropdownField('PaymentMethod', 'Payment Method', self::$payment_methods),
					new CalendarDateField('Start', 'Start'),
					new CalendarDateField('End', 'End (Optional)'),
					new DropdownField('Period', 'Period', self::$period_fields),
					new ListboxField(
						$name = '_DeliveryDay',
						$title = 'Delivery day:',
						$source = array_combine(
							self::$delivery_days,
							self::$delivery_days
						),
						$value = explode(',', $this->DeliveryDay),
						$size = 7,
						$multiple = false
					),
					new TextareaField('Notes', 'Notes'),
					new CheckboxField('SendReciept', 'Send a receipt email for this standing order (executed when you click Save) '),
					new CheckboxField('SendUpdate', 'Send an update email for this standing order (executed when you click Save) '),
					new CheckboxField('CreateDraftOrder', 'Create a draft order from this standing order (executed when you click Save) ')
				),
				new Tab('Products',
					$this->getCMSProductsTable()
				),
				new Tab('Previous Orders',
					new ReadOnlyField("FirstCreatedFormatted", "First Order", $firstCreated),
					new ReadOnlyField("LastCreatedFormatted", "Last Order", $lastCreated),
					new ReadOnlyField("NextCreatedFormatted", "Next Order", $nextCreated),
					new HeaderField("DraftOrders", "Orders Placed Through this Standing Order"),
					$this->getCMSPreviousOrders()
				)
			)
		);

		$alternatives = unserialize($this->Alternatives);
		$orderItems = $this->OrderItems();

		$products = DataObject::get('FishProduct');
		$productsMap = array('');
		$productsMap = $products->map('ID', 'Title', ' ');

		if($orderItems) {
			foreach($orderItems as $orderItem) {
				$value = isset($alternatives[$orderItem->ProductID]) ? $alternatives[$orderItem->ProductID] : null;
				$fields->addFieldToTab('Root.Alternatives', new DropdownField('_Alternatives['.$orderItem->ProductID.'][0]', $orderItem->ProductTitle(), $productsMap, $value[0]));
				$fields->addFieldToTab('Root.Alternatives', new DropdownField('_Alternatives['.$orderItem->ProductID.'][1]', '', $productsMap, $value[1]));
				$fields->addFieldToTab('Root.Alternatives', new DropdownField('_Alternatives['.$orderItem->ProductID.'][2]', '', $productsMap, $value[2]));
				$fields->addFieldToTab('Root.Alternatives', new DropdownField('_Alternatives['.$orderItem->ProductID.'][3]', '', $productsMap, $value[3]));
				$fields->addFieldToTab('Root.Alternatives', new DropdownField('_Alternatives['.$orderItem->ProductID.'][4]', '', $productsMap, $value[4]));
			}
		}

		$fields->addFieldToTab('Root', new Tab(
			'HistoryOfChanges',
			new HeaderField("HistoryTableHeader", "List of updates to the standing order with date of update and version"),
			$this->getCMSHistoryTable()
		));

		return $fields;
	}

	/**
	 * CMS Fields for Popup
	 * @return FieldSet
	 */
	public function getCMSFields_forPopup() {
		$fields = new FieldSet(
			new TabSet('Root',
				new Tab('Main',
					new ReadonlyField('Readonly[Member]', 'Member', $this->Member()->getTitle().' ('.$this->Member()->Email.')'),
					new DropdownField('Status', 'Status', self::$status_nice),
					//new DropdownField('PaymentMethod', 'Payment Method', self::$payment_methods),
					new CalendarDateField('Start', 'Start'),
					new CalendarDateField('End', 'End (Optional)'),
					new DropdownField('Period', 'Period', self::$period_fields),
					new TextField('DeliveryDay', 'Delivery Day'),
					new TextareaField('Notes', 'Notes')
				),
				new Tab('Products',
					$this->getCMSVersionedProductsTable()
				)
			)
		);

		$alternatives = unserialize($this->Alternatives);
		$orderItems = $this->getVersionedComponents('OrderItems');

		$products = DataObject::get('FishProduct');
		$productsMap = array('');
		$productsMap = $products->map('ID', 'Title', ' ');

		if($orderItems) {
			foreach($orderItems as $orderItem) {
				$value = isset($alternatives[$orderItem->ProductID]) ? $alternatives[$orderItem->ProductID] : null;
				$fields->addFieldToTab('Root.Alternatives', new DropdownField('_Alternatives['.$orderItem->ProductID.'][0]', $orderItem->ProductTitle(), $productsMap, $value[0]));
				$fields->addFieldToTab('Root.Alternatives', new DropdownField('_Alternatives['.$orderItem->ProductID.'][1]', '', $productsMap, $value[1]));
				$fields->addFieldToTab('Root.Alternatives', new DropdownField('_Alternatives['.$orderItem->ProductID.'][2]', '', $productsMap, $value[2]));
				$fields->addFieldToTab('Root.Alternatives', new DropdownField('_Alternatives['.$orderItem->ProductID.'][3]', '', $productsMap, $value[3]));
				$fields->addFieldToTab('Root.Alternatives', new DropdownField('_Alternatives['.$orderItem->ProductID.'][4]', '', $productsMap, $value[4]));
			}
		}

		return $fields;
	}

	/**
	 * Get products table
	 * @return ComplexTableField
	 */
	public function getCMSProductsTable() {
		$products = DataObject::get('Product');

		$fields = new TabSet('Root',
			new Tab('Main',
				new DropdownField('ProductID', 'Choose a product:', $products->map()),
				new TextField('Quantity', 'Quantity')
			)
		);

		$table = new ComplexTableField(
			$this,
			'OrderItems',
			'StandingOrder_OrderItem',
			array(
				'Product.Title' => 'Title',
				'Quantity' => 'Qty',
			),
			new FieldSet($fields)
		);

		$table->setShowPagination(false);
		$table->setAddTitle('Product');
		$table->setPermissions(array('add', 'edit', 'delete'));

		return $table;
	}


	/**
	 * Get previous actual order table
	 * @return ComplexTableField
	 */
	public function getCMSPreviousOrders() {
		$table = new ComplexTableField(
			$controller = $this,
			$name = 'PreviousOrders',
			$sourceClass = 'DraftOrder',
			$fieldList = array(
				"ID" => "Order ID",
				"Total" => "Value",
				"Created" => "Created",
				"Status" => "Status",
				"ViewLink" => "Link",
				"LoadLink" => "Load"
			),
			$detailFormFields = new FieldSet(),
			$sourceFilter = "StandingOrderID = ".$this->ID,
			$sourceSort = "Created DESC",
			$sourceJoin = ""
		);
		$table->setFieldCasting(array(
			'Created' => 'Date->Long',
			'Total' => 'Currency->Nice'
		));
		$table->setShowPagination(false);
		$table->setAddTitle('Previous Orders');
		$table->setPermissions(array("export"));
		return $table;
	}



	/**
	 * Get products table for versioned popup
	 * @return ComplexTableField
	 */
	public function getCMSVersionedProductsTable() {
		$table = new ComplexTableField(
			$this,
			'OrderItems',
			'StandingOrder_OrderItem',
			array(
				'Product.Title' => 'Title',
				'Quantity' => 'Qty',
			),
			'getCMSFields_forPopup'
		);

		$table->setCustomSourceItems($this->getVersionedComponents('OrderItems'));

		$table->setShowPagination(false);
		$table->setPermissions(array());

		return $table;
	}

	/**
	 * Repeating Order History
	 * @return unknown_type
	 */
	public function getCMSHistoryTable() {
		$table = new VersionedComplexTableField(
			$this,
			'StandingOrders',
			'StandingOrder',
			array(
				'LastEdited' => 'Date'
			),
			'getCMSFields_forPopup'
		);

		$table->setCustomSourceItems($this->allVersions());
		$table->setPermissions(array('show'));

		return $table;
	}

	public function getVersionedComponents($component) {
		$baseTable = ClassInfo::baseDataClass(self::$has_many[$component]);
		$query = singleton(self::$has_many[$component])->buildVersionSQL("`{$baseTable}`.OrderID = {$this->ID} AND `{$baseTable}`.OrderVersion = {$this->Version}");
		$result = singleton(self::$has_many[$component])->buildDataObjectSet($query->execute());

		return $result;
	}

	/**
	 * Create a new DraftOrder from the StandingOrder
	 * @return null
	 */
	public function createDraftOrder($redirect = true) {
		//create draft order
		$order = new DraftOrder();
		$order->write();

		// Set the items from the cart into the order
		if($this->OrderItems()) {
			foreach($this->OrderItems() as $orderItem) {
				$product = DataObject::get_by_id('Product', $orderItem->ProductID);

				if($product) {
					$productOrderItem = new Product_OrderItem(
						array(
							'ProductID' => $product->ID,
							'ProductVersion' => $product->Version,
							'Quantity' => $orderItem->Quantity,
						),
						$orderItem->Quantity
					);

					$productOrderItem->OrderID = $order->ID;
					$productOrderItem->write();
				}
			}
		}

		$order->StandingOrderID = $this->ID;
		$order->MemberID = $this->MemberID;
		$order->write();

		//$order->sendReceipt();

		if($redirect) Director::redirect($order->Link());
	}

	public function sendReceipt() {
		$this->sendEmail('StandingOrder_ReceiptEmail');
	}

	public function sendUpdate() {
		$this->sendEmail('StandingOrder_UpdateEmail');
	}

	protected function sendEmail($emailClass, $copyToAdmin = true) {
 		$from = self::$receipt_email ? self::$receipt_email : Email::getAdminEmail();
 		$to = $this->Member()->Email;
		$subject = self::$receipt_subject ? self::$receipt_subject : "Standing Order Confirmation (#$this->ID)";

 		$email = new $emailClass();
 		$email->setFrom($from);
 		$email->setTo($to);
 		$email->setSubject($subject);
		if($copyToAdmin) $email->setBcc(Email::getAdminEmail());

		$email->populateTemplate(
			array(
				'StandingOrder' => $this
			)
		);

		$email->send();
	}

	public function CanModify() {
		if(in_array($this->Status, array('Pending', 'Active'))) {
			return true;
		}
		else return false;
	}

	public function Link() {
		return StandingOrdersPage::get_standing_order_link('view', $this->ID);
	}

	public function Period() {
		if(isset(self::$period_fields[$this->Period])) return self::$period_fields[$this->Period];
	}

	public function ModifyLink() {
		return StandingOrdersPage::get_standing_order_link('modify', $this->ID);
	}

	public function CancelLink() {
		return StandingOrdersPage::get_standing_order_link('cancel', $this->ID);
	}

	public function DoneLink() {
		$page = DataObject::get_one("StandingOrdersPage");
		if($page) {
			return $page->Link();
		}
		$page = DataObject::get_one("Page", "URLSegment 'home'");
		return $page->Link();
	}

	public function TableAlternatives() {
		$alternatives = unserialize($this->Alternatives);

		$products = new DataObjectSet();

		if(is_array($alternatives)) {
			foreach($alternatives as $id => $alternative) {
				$product = DataObject::get_by_id('Product', $id);

				$product->Alternatives = new DataObjectSet();

				if(is_array($alternative)) {
					foreach($alternative as $id) {
						if($id) {
							$alternativeProduct = DataObject::get_by_id('Product', $id);

							if($alternativeProduct) $product->Alternatives->push($alternativeProduct);
						}
					}
				}

				$products->push($product);
			}
		}

		return $products;
	}

	public function TableDeliveryDay() {
		return $this->DeliveryDay;
	}

	public function TablePaymentMethod() {
		if(isset(self::$payment_methods[$this->PaymentMethod])) {
			return self::$payment_methods[$this->PaymentMethod];
		}
	}

	public function TableStatus() {
		return self::$status_nice[$this->Status];
	}


	public function onBeforeWrite() {
		parent::onBeforeWrite();
		$this->Items = $this->OrderItemList();
		if(isset($_REQUEST['_DeliveryDay'])) {
			$this->DeliveryDay = implode(',', $_REQUEST['_DeliveryDay']);
		}

		if(isset($_REQUEST['_Alternatives'])) {
			foreach($_REQUEST['_Alternatives'] as $key => $array) {
				for($i = 0; $i < 5; $i++) {
					$item = $_REQUEST["_Alternatives"][$key][$i];
					if($item > 0 && $i > 0) {
						for($j = ($i - 1); $j >= 0; $j--) {
							$previousItem = $_REQUEST["_Alternatives"][$key][$j];
							if($item == $previousItem) {
								$_REQUEST["_Alternatives"][$key][$i]  = 0;
							}
						}
					}
				}
			}
			$this->Alternatives = serialize($_REQUEST['_Alternatives']);
		}

		if($this->ID == false) {
			$this->changed['Version'] = 1;

			if(!isset($this->record['Version'])) {
				$this->record['Version'] = -1;
			}

			$this->LastCreated = $this->FirstOrderDate()->format("Y-m-d");

			$_SQL = Convert::raw2sql($_POST);

			if($this->MemberID) {
				$member = DataObject::get_by_id('Member', $this->MemberID);
			}
			else {
				$member = DataObject::get_one('Member', 'Email = \''.$_SQL['Email'].'\'');
			}

			$data = array(); foreach($_POST as $k => $v) if($v) $data[$k] = $v;

			if($member) {
				$member->update($data);
			} else {
				$member = new Member();
				$member->update($data);
			}

			$member->write();

			if($this->MemberID == false) {
				$this->Notes = 'Manually Created by '.Member::currentMember()->Name;
				$this->MemberID = $member->ID;
			}

			self::$update_versions = false;
		}
		else {
			$lastTime = strtotime($this->LastCreated);
			$firstTime = strtotime($this->FirstOrderDate()->format("Y-m-d"));
			if($lastTime < $firstTime) {
				$this->LastCreated = $this->FirstOrderDate()->format("Y-m-d");
			}
		}
	}

	/**
	 * Complex Versioning accross more than one DataObject
	 */
	public function onAfterWrite() {
		parent::onAfterWrite();

		if(self::$update_versions) {
			self::$update_versions = false;

			$OrderItems = DataObject::get('StandingOrder_OrderItem', 'OrderID = '.$this->ID);

			if($OrderItems) foreach($OrderItems as $OrderItem) {
				if(
					$OrderItem->ID != $this->ignoreID &&
					$OrderItem->ClassName != $this->ignoreClass
				) {
					$OrderItem->OrderVersion = $this->Version;
					$OrderItem->write();
				}
			}

			self::$update_versions = true;
		}

		$this->createDraftOrders();
	}


}

class StandingOrder_OrderItem extends DataObject {

	public static $db = array(
		'Quantity' => 'Int',
		'OrderVersion' => 'Int',
	);

	public static $has_one = array(
		'Product' => 'Product',
		'Order' => 'StandingOrder',
	);

	public static $versioning = array(
		'Stage'
	);

	public static $extensions = array(
		"Versioned('Stage')"
	);

	public function Title() {
		return $this->Product()->Title;
	}

	public function ProductTitle() {
		return $this->Product()->Title;
	}

	public function Link() {
		return $this->Product()->Link();
	}

	public function ProductVersion() {
		return $this->Product()->Version;
	}

	/**
	 * Complex Versioning accross more than one DataObject
	 */
	public function onBeforeWrite() {
		parent::onBeforeWrite();

		if($this->ID == false) {
			$this->changed['Version'] = 1;
			if(!isset($this->record['Version'])) {
				$this->record['Version'] = -1;
			}
		}

		if(StandingOrder::$update_versions == true) {
			if($this->ID) {
				$this->Order()->ignoreID = $this->ID;
				$this->Order()->ignoreClassName = $this->ClassName;
				$this->Order()->write();
				$this->OrderVersion = $this->Order()->Version;
			}
		}
	}

	public function onAfterWrite() {
		parent::onAfterWrite();

		if($this->SendRecipt) $standingOrder->sendReceipt();
		if($this->SendUpdate) $standingOrder->sendUpdate();
	}

	public function onBeforeDelete() {
		parent::onBeforeDelete();

		if(StandingOrder::$update_versions == true) {
			if($this->ID) {
				$this->Order()->ignoreID = $this->ID;
				$this->Order()->ignoreClassName = $this->ClassName;
				$this->Order()->write();
				$this->OrderVersion = $this->Order()->Version;
			}
		}
	}
}

class StandingOrder_ReceiptEmail extends Email {

	protected $ss_template = 'StandingOrder_ReceiptEmail';

}

class StandingOrder_UpdateEmail extends Email {

	protected $ss_template = 'StandingOrder_UpdateEmail';

}