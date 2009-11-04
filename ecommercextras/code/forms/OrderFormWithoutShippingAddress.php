<?php
 /**
	* @author Nicolaas [at] sunnysideup.co.nz
  *
  * @see CheckoutPage
  *
  * @package ecommercextras
  */
class OrderFormWithoutShippingAddress extends OrderForm {

	protected static $fixed_country_code;

	protected static $extra_fields = array();

	protected static $postal_code_url = "http://www.nzpost.co.nz/Cultures/en-NZ/OnlineTools/PostCodeFinder";
	protected static $postal_code_label = "find postcode";

	static function add_extra_field($tabName, $field) {
		self::$extra_fields[] = array("TabName" =>$tabName, "FieldObject" => $field);
	}

	static function set_fixed_country_code($v) {
		self::$fixed_country_code = $v;
	}

	static function set_postal_code_url($v) {
		self::$postal_code_url = $v;
	}

	static function set_postal_code_label($v) {
		self::$postal_code_label = $v;
	}

	function __construct($controller, $name) {

		Requirements::javascript('ecommercextras/javascript/OrderFormWithoutShippingAddress.js');

		parent::__construct($controller, $name);

		//stop people adding different shipping address
		$this->unsetActionByName("action_useDifferentShippingAddress");
		$this->unsetActionByName("useDifferentShippingAddress");
		$member = Member::currentMember();
		if(!$member || !$member->ID || $member->Password == '') {
			$this->fields->addFieldToTab("", new LiteralField('MemberInfoAlso', '<p class="message good LoginCallToAction">Please <a href="Security/login?BackURL=' . CheckoutPage::find_link(true) . '/">log-in now</a> to retrieve your account details or create an account below.</p>'), "FirstName");
			//improve password field TEMPORARY HACK!
			//$passwordField = new OptionalConfirmedPasswordField('Password', 'Password', '', null, true);
			//$passwordField->minLength = 6;
			//$passwordField->showOnClickTitle = "add password now";
			//$this->fields->replaceField("Password", $passwordField);
			Requirements::javascript('ecommercextras/javascript/OptionalConfirmedPasswordField.js');
			Requirements::block(SAPPHIRE_DIR . '/javascript/ConfirmedPasswordField.js');
		}

		$this->fields->removeFieldFromTab("RightOrder", "Membership Details");
		$this->fields->removeFieldFromTab("RightOrder", "MemberInfo");
		//add extra fields
		foreach(self::$extra_fields as $fieldCombo) {
			$this->fields->addFieldToTab($fieldCombo["TabName"],$fieldCombo["FieldObject"]);
		}
		//replace field for address

		foreach($this->fields->dataFields() as $i => $child) {
			if(is_object($child)){
				$name = $child->Name();
				switch ($name) {
					case "Address":
						$child->setTitle('Delivery Address (no Postal Box)');
						break;
					case "AddressLine2":
						$child->setRightTitle('<a href="'.self::$postal_code_url.'" id="OrderFormWithoutShippingAddressPostalCodeLink">'.self::$postal_code_label.'</a>');
						$child->setTitle('Postal Code');
						break;
					default:
						break;
				}
			}
		}
		//$this->fields->replaceField("Address", new TextField("Address", "Delivery Address (no Postal Box)"));
		//$this->fields->replaceField("AddressLine2", $PostalCodeField);

		//$this->resetField("Country", "NZ");

		if(self::$fixed_country_code) {
			$this->resetField("Country", self::$fixed_country_code);
		}

		$this->fields->addFieldToTab("", new TextareaField('CustomerOrderNote', 'Note / Question'));

		//$orderForm = new OrderForm($this, "OrderForm"); ************ NOT SURE WHY WE NEED THIS!
		$data = $this->getData();
		$this->loadDataFrom($data);
	}

}

