<?php
/**
 * @author nicolaas [at] sunnysideup.co.nz
 * @package ecommerce
 */
class ProductWithVariations extends Product {

	static $hide_ancestor = "Product";

	public static $defaults = array(
		'AllowPurchase' => false
	);

	public static $casting = array();


	static $icon = 'ecommerce_extendedproductvariations/images/treeicons/ProductWithVariations';

	function getCMSFields() {
		$fields = parent::getCMSFields();

		// Standard product detail fields
		$fields->removeFieldFromTab('Root.Content.Main', "AllowPurchase");
		$fields->addFieldsToTab(
			'Root.Content.Variations',
			array(
				new HeaderField(_t('Product.VARIATIONSSET', 'This product has the following variations set')),
				new LiteralField('VariationsNote', '<p class="message good">If this product has active variations, the price of the product will be the price of the variation added by the member to the shopping cart.</p>'),
				$this->getVariationsTable()
			)
		);
		return $fields;
	}

	function getVariationsTable() {
		$singleton = singleton('ExtendedProductVariation');
		$query = $singleton->buildVersionSQL("`ProductID` = '{$this->ID}'");
		$variations = $singleton->buildDataObjectSet($query->execute());
		$filter = $variations ? "`ID` IN ('" . implode("','", $variations->column('RecordID')) . "')" : "`ID` < '0'";
		//$filter = "`ProductID` = '{$this->ID}'";

		$tableField = new HasManyComplexTableField(
			$this,
			'Variations',
			'ExtendedProductVariation',
			array(
				'Title' => 'Title',
				'Price' => 'Price'
			),
			'getCMSFields_forPopup',
			$filter
		);

		if(method_exists($tableField, 'setRelationAutoSetting')) {
			$tableField->setRelationAutoSetting(true);
		}

		return $tableField;
	}

	function AllowPurchase() {
		return false;
	}

	/**
	 * When the ecommerce module is first installed, and db/build
	 * is invoked, create some default records in the database.
	 */
	function requireDefaultRecords() {
		parent::requireDefaultRecords();
	}

}

class ProductWithVariations extends Product_Controller {

	function init() {
		parent::init();
	}

	function addVariation() {
		if($this->AllowPurchase && $this->urlParams['ID']) {
			$variation = DataObject::get_one(
				'ExtendedProductVariation',
				sprintf(
					"`ID` = %d AND `ProductID` = %d",
					(int)$this->urlParams['ID'],
					(int)$this->ID
				)
			);
			if($variation) {
				if($variation->AllowPurchase()) {
					ShoppingCart::add_new_item(new ProductVariation_OrderItem($variation));
					if(!$this->isAjax()) Director::redirectBack();
				}
			}
		}
	}

}

