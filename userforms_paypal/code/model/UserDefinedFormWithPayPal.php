<?php

/**
 */

class UserDefinedFormWithPayPal extends UserDefinedForm {

	/**
	 * map Paypal Fields ($key) to fields used in the userdefined form
	 * For example, if the userdefinedforms contains a fields 'LastName'
	 * then Paypal will be presented with its value as Surname.
	 * @var Array
	 */
	public static $mapped_fields = array(
		'Surname' => array("Name", "Surname", "LastName"),
		'FirstName' => array("FirstName", "Firstname", "Name"),
		'Address1' => array("Address", "Street"),
		'Address2' => array("Address", "Address2", "Suburb"),
		'City' => array("City", "Town"),
		'State' => array("State", "Province"),
		'Zip' => array("Zip", "PostalCode", "Zipcode", "Postcode"),
		'Email' => array("Email", "E-Mail"),
	);
		static function set_mapped_fields($a) {self::$mapped_fields = $a;}
		static function get_mapped_fields() {return self::$mapped_fields;}
		static function add_mapped_fields($key, $arrayValue) {self::$mapped_fields[$key] = $arrayValue;}
		static function remove_mapped_fields($key) {unset(self::$mapped_fields[$key]);}


	/**
	 * @var Array Fields on the user defined form page.
	 */
	static $db = array(
		"Amount" => "Double",
		"ProductName" => "Varchar(100)",
		"Currency" => "Varchar(5)",
		"BusinessEmail" => "Varchar(100)",
		"PaypalButtonLabel" => "Varchar(100)"
	);

	/**
	 * @var Array Default values of variables when this page is created
	 */
	static $defaults = array(
		'PaypalButtonLabel' => 'pay now'
	);

	/**
	 * Setup the CMS Fields for the User Defined Form
	 *
	 * @return FieldSet
	 */
	public function getCMSFields() {
		$fields = parent::getCMSFields();

		// define tabs
		$fields->findOrMakeTab('Root.Content.Paypal', _t('UserDefinedFormWithPayPal.PAYPAL', 'Pay Pal'));
		// field editor
		$fields->addFieldToTab("Root.Content.Paypal", new NumericField("Amount", "Amount"));
		$fields->addFieldToTab("Root.Content.Paypal", new TextField("Currency", "Currency Code"));
		$fields->addFieldToTab("Root.Content.Paypal", new TextField("PaypalButtonLabel", "Pay now button Label (e.g. Pay Now)"));
		return $fields;
	}


}

/**
 * Controller for the {@link UserDefinedFormWithPayPal} page type.
 *
 * @package userform
 * @subpackage pagetypes
 */

class UserDefinedFormWithPayPal_Controller extends UserDefinedForm_Controller {


	/**
	 *
	 * @var SubmittedForm Object
	 */
	protected $mostLikeLySubmission = null;
	/**
	 * Process the form that is submitted through the site
	 *
	 * @param Array Data
	 * @param Form Form
	 * @return Redirection
	 */
	function process($data, $form) {
		//pre process?
		parent::process($data, $form);
		//post process
		$this->mostLikeLySubmission = DataObject::get_one("SubmittedForm", "\"Created\" DESC");
		Session::set("UserDefinedFormWithPayPalID", $this->mostLikeLySubmission->ID);
		$paypalIdentifier1 = new SubmittedFormField();
		$paypalIdentifier1->Name = "Paypal Identifier";
		$paypalIdentifier1->Title = "Paypal Identifier";
		$paypalIdentifier1->Value = $this->mostLikeLySubmission->ID;
		$paypalIdentifier1->ParentID = $this->mostLikeLySubmission->ID;
		$paypalIdentifier1->write();
		//add a double-check
		$paypalIdentifier2 = new SubmittedFormField();
		$paypalIdentifier2->Name = "Paypal DoubleCheck";
		$paypalIdentifier2->Title = "Paypal DoubleCheck";
		$paypalIdentifier2->Value = print_r($data, 1);
		$paypalIdentifier2->ParentID = $this->mostLikeLySubmission->ID;
		$paypalIdentifier2->write();
		return Director::redirect($this->Link() . 'finished' . $referrer);
	}

	/**
	 * This action handles rendering the "finished" message,
	 * which is customisable by editing the ReceivedFormSubmission.ss
	 * template.
	 *
	 * @return ViewableData
	 */
	function finished() {
		$referrer = isset($_GET['referrer']) ? urldecode($_GET['referrer']) : null;
		$this->mostLikeLySubmission = DataObject::get_by_id("SubmittedForm", intval(Session::get("UserDefinedFormWithPayPalID"))-0);
		$customisedArray = array(
			'Link' => $referrer,
			'MerchantID' => $this->BusinessEmail,
			'ProductName' => $this->ProductName,
			'SubmittedFormID' => $this->mostLikeLySubmission->ID,
			'Amount' => $this->Amount,
			'PaypalButtonLabel' => $this->PaypalButtonLabel
		);
		foreach(UserDefinedFormWithPayPal::get_mapped_fields() as $paypalField => $forFieldsArray){
			$customisedArray[$paypalField] = $this->getSubmittedFormValue($forFieldsArray);
		}
		if($this->mostLikeLySubmission) {
			return $this->customise(
				'Content' => $this->customise($customisedArray)->renderWith('ReceivedFormSubmissionWithPayPal'),
				'Form' => '',
			));
		}
		else {
			return parent::finished();
		}
	}

	/**
	 *
	 * @param Array - array of fields to check for
	 */
	protected function getSubmittedFormValue($nameArray) {
		if($this->mostLikeLySubmission) {
			foreach($nameArray as $name) {
				$name = Convert::raw2sql($name);
				$field = DataObject::get_one(
					"SubmittedFormField",
					" (\"Name\" = '$name' OR \"Title\" = '$name') AND \"ParentID\" = ".$this->mostLikeLySubmission->ID."
				");
				if($field) {
					return Convert::raw2att($field->Value);
				}
			}
		}
		return "";
	}

}
