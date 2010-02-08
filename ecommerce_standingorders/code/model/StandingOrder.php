<?php

/**
 * @author Michael Mitchell <michael@michaelmitchell.co.nz>
 */

class StandingOrder extends DataObject {
	
	public static $db = array(
		'Start' => 'Date',
		'End' => 'Date',
		'Period' => 'Varchar',
	
		//Guytons.co.nz specific
		'CardToken' => 'Varchar', //dps credit card token for this order
		'DeliveryDays' => 'Text', //Serialized Array
		
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
		'Period' => 'Period'
	);
	
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
	public static $period_dropdown_fields = array(
		'1 week' => 'Weekly',
		'2 weeks' => 'Fornightly',
		'1 month' => 'Monthly',
	);
	
	/**
	 * Should orderItems/orders call a write to update versions?
	 */
	public static $update_versions = true;
	
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
					new CalendarDateField('Start', 'Start'),
					new CalendarDateField('End', 'End (Optional)'),
					new DropdownField('Period', 'Period', self::$period_dropdown_fields),
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
					new ReadonlyField('Readonly[Member]', 'Member', $this->Member()->getTitle().' ('.$this->Member()->Email.')'),
					new CalendarDateField('Start', 'Start'),
					new CalendarDateField('End', 'End (Optional)'),
					new DropdownField('Period', 'Period', self::$period_dropdown_fields),
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
					new CheckboxField('CreateDraftOrder', 'Manually create a draft order from this standing order? (save to create draft order)')
				),
				new Tab('Products',
					$this->getCMSProductsTable()
				),
				new Tab('History',
					$this->getCMSHistoryTable()
				)
			)
		);
		
		
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
					new CalendarDateField('Start', 'Start'),
					new CalendarDateField('End', 'End (Optional)'),
					new DropdownField('Period', 'Period', self::$period_dropdown_fields),
					new TextField('DeliveryDays', 'Delivery Days'),
					new TextareaField('Notes', 'Notes')
				),
				new Tab('Products',
					$this->getCMSVersionedProductsTable()
				)
			)
		);
		
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

	public function createDraftOrder() {
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
		
		Director::redirect($order->Link());
	}
		
	public function onBeforeWrite() {
		parent::onBeforeWrite();

		if(isset($_REQUEST['_DeliveryDays'])) {
			$this->DeliveryDays = implode(',', $_REQUEST['_DeliveryDays']);
		}
		
		if($this->ID == false) {
			$this->changed['Version'] = 1;
			
			if(!isset($this->record['Version'])) {
				$this->record['Version'] = -1;
			}	
	
			$_SQL = Convert::raw2sql($_POST);

			$member = DataObject::get_one('Member', 'Email = \''.$_SQL['Email'].'\'');

			$data = array(); foreach($_POST as $k => $v) if($v) $data[$k] = $v;
			
			if($member) {
				$member->update($data);
			} else {
				$member = new Member();
				$member->update($data);
			}
			
			$member->write();

			$this->MemberID = $member->ID;
			$this->Notes = 'Manually Created by '.Member::currentMember()->Name;
			
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
}