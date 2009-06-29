<?php

/** * TO BE COMPLETED
 */
class NewsletterSignupModifier extends OrderModifier {
	static $db = array(		"NewsletterSignup" => "Boolean"	);	static function show_form() {		return true;	}	static $requiredFieldsArray = Array(	);
	static $orderFormInstance = null;	protected static $is_chargable = false;	protected static $newsletter_tickbox_label = "I would like to receive your newsletter";	protected static $newsletter_action_label = "Add to newsletter now";	static function set_newsletter_tickbox_label($v) {		self::$newsletter_tickbox_label = $v;	}	static function set_newsletter_action_label($v) {		self::$newsletter_action_label = $v;	}	static function get_form($controller) {		if(!isset(self::$orderFormInstance)) {			$fields = new FieldSet();			$fields->push($field = new CheckboxField('NewsletterSignup',self::$newsletter_tickbox_label, Session::get("NewsletterSignupModifier")));			$validator = null;			$actions = new FieldSet(				new FormAction_WithoutLabel('processOrderModifier', self::$newsletter_action_label)			);			self::$orderFormInstance = new NewsletterSignupModifier_Form($controller, 'ModifierForm', $fields, $actions, $validator);		}		return self::$orderFormInstance;	}	function ShowInTable() {		return false;	}	function LiveAmount() {		return 0;	}	function TableTitle() {		return '';	}	public function onBeforeWrite() {		parent::onBeforeWrite();		foreach(self::$db as $fieldName=>$fieldType) {			$this->$fieldName = Session::get("NewsletterSignupModifier".$fieldName);			if(!$this->$fieldName && Session::get("NewsletterSignupModifier".$fieldName)) {				$this->$fieldName = Session::get("NewsletterSignupModifier".$fieldName);			}		}	}}

class NewsletterSignupModifier_Form extends form {	public function processOrderModifier($data, $form) {		$order = ShoppingCart::current_order();		$modifiers = $order->Modifiers();		foreach($modifiers as $modifier) {			if (get_class($modifier) == 'NewsletterSignupModifier') {				Session::set("NewsletterSignupModifier", $data['PickupOrDeliveryType']);				$modifier->NewsletterSignupModifier = $data['NewsletterSignupModifier'];			}		}		Order::save_current_order();		if(Director::is_ajax()) {			return $this->controller->renderWith("AjaxCheckoutCart");		}		else {			Director::redirect(CheckoutPage::find_link());		}		return;	}}