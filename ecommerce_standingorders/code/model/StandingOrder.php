<?php

/**
 * @author Michael Mitchell <michael@michaelmitchell.co.nz>
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
		'DeliveryDays' => 'Text', //Serialized Array
		'Alternatives' => 'Text', //Serialized Array
		
		'Notes' => 'Text',
	);
	
	public static $has_one = array(
		'Member' => 'Member',
	);
	
	public static $has_many = array(
		'OrderItems' => 'StandingOrder_OrderItem', //products & quanitites
	);
	
	public static $summary_fields = array(
		'Member.FirstName' => 'First Name',
		'Member.Surname' => 'Surname',
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
		return self::$delivery_days;	
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
				$startTime = strtotime($standingOrder->Start);
				$endTime = strtotime($standingOrder->End);
				$lastTime = strtotime($standingOrder->LastCreated);
				$periodTime = strtotime('+'.$standingOrder->Period, $lastTime);

				if($periodTime < $currentTime && $periodTime < $endTime) {
					StandingOrder::$update_versions == false;

					$standingOrder->createDraftOrder(false);
					$standingOrder->LastCreated = date('Y-m-d');
					$standingOrder->writeWithoutVersion();
					
					StandingOrder::$update_versions == true;
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
						'_DeliveryDays',
						'Deilvery days:',
						array_combine(
							self::$delivery_days,
							self::$delivery_days
						),
						explode(',', $this->DeliveryDays),
						7,
						true
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
					new DropdownField('Status', 'Status', singleton(__CLASS__)->dbObject('Status')->enumValues()),
					new DropdownField('PaymentMethod', 'Payment Method', self::$payment_methods),
					new CalendarDateField('Start', 'Start'),
					new CalendarDateField('End', 'End (Optional)'),
					new DropdownField('Period', 'Period', self::$period_fields),
					new ListboxField(
						'_DeliveryDays',
						'Deilvery days:',
						array_combine(
							self::$delivery_days,
							self::$delivery_days
						),
						explode(',', $this->DeliveryDays),
						7,
						true
					),
					new TextareaField('Notes', 'Notes'),
					new CheckboxField('SendReciept', 'Manually a reciept email for this standing order? (save to send reciept)'),
					new CheckboxField('SendUpdate', 'Manually an update email for this standing order? (save to send update)'),
					new CheckboxField('CreateDraftOrder', 'Manually create a draft order from this standing order? (save to create draft order)')
				),
				new Tab('Products',
					$this->getCMSProductsTable()
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
		
		$fields->addFieldToTab('Root', new Tab('History',$this->getCMSHistoryTable()));
		
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
					new DropdownField('PaymentMethod', 'Payment Method', self::$payment_methods),
					new CalendarDateField('Start', 'Start'),
					new CalendarDateField('End', 'End (Optional)'),
					new DropdownField('Period', 'Period', self::$period_fields),
					new TextField('DeliveryDays', 'Delivery Days'),
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
				'LastEdited' => 'Date',
				'Version' => 'Version',
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
		
		$order->sendReceipt();
		
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
		$subject = self::$receipt_subject ? self::$receipt_subject : "Standing ORder Information #$this->ID";

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
	
	public function TableDeliveryDays() {
		return $this->DeliveryDays;
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

		if(isset($_REQUEST['_DeliveryDays'])) {
			$this->DeliveryDays = implode(',', $_REQUEST['_DeliveryDays']);
		}
		
		if(isset($_REQUEST['_Alternatives'])) {
			$this->Alternatives = serialize($_REQUEST['_Alternatives']);
		}
		
		if($this->ID == false) {
			$this->changed['Version'] = 1;
			
			if(!isset($this->record['Version'])) {
				$this->record['Version'] = -1;
			}	
	
			$this->LastCreated = $this->Start;
			
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
		
		if(isset($_POST['CreateDraftOrder'])) $this->createDraftOrder();
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