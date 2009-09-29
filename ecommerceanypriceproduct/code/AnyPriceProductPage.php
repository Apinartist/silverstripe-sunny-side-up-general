<?php
/**
 *
 *@author nicolaas [at] sunnysideup.co.nz
 *@package ecommerce
 *@subpackage products
 *@requires ecommerce
 *
 *
 */


class AnyPriceProductPage extends Product {

	public static $db = array(
		"AmountFieldLabel" => "Varchar(255)",
		"ActionFieldLabel" => "Varchar(255)"
	)

	public static $defaults = array(
		'AllowPurchase' => true
		'Price' => 0
	);

	static $add_action = 'a Product With Adjustable Price';

	static $icon = 'ecommerceanypriceproduct/images/treeicons/AnyPriceProductPage';

	function canCreate() {
		return !DataObject::get("SiteTree", '`ClassName` = "AnyPriceProductPage"');
	}

	function getCMSFields() {
		$fields = parent::getCMSFields();

		$fields->addFieldsToTab(
			"Root.Content.AddAmountForm",
			array(
				new TextField("AmountFieldLabel", "Amount Field Label (what amount would you like to pay?)")
				new TextField("ActionFieldlabel", "Action Field Label (e.g. pay entered amount now)")
			)

		);
		// Standard product detail fields
		$fields->removeFieldsFromTab(
			'Root.Content.Main',
			array(
				'Weight',
				'Price',
				'Model'
			)
		);


		// Flags for this product which affect it's behaviour on the site
		$fields->removeFieldsToTab(
			'Root.Content.Main',
			array(
				'FeaturedProduct'
			)
		);

		return $fields;
	}


	/**
	 * Conditions for whether a product can be purchased.
	 *
	 * If it has the checkbox for 'Allow this product to be purchased',
	 * as well as having a price, it can be purchased. Otherwise a user
	 * can't buy it.
	 *
	 * @return boolean
	 */
	function AllowPurchase() {
		return $this->AllowPurchase ;
	}


}

class Product_Controller extends Page_Controller {

	function init() {
		parent::init();
	}

	function AddNewPriceForm() {
		$fields = new FieldSet(
			new CurrencyField("Amount", $this->AmountFieldLabel)
		);

		$actions = new FieldSet(
			new ActionField("doAddNewPriceForm", $this->ActionFieldLabel)
		);

		return new Form(
			$controller = $this
			$name = "AddNewPriceForm",
			$fields
			$actions
			$requiredFields
		);
	}

	function doAddNewPriceForm($data, $form) {
		print_r($form);
		print_r($data);
	}


}
