<?php
/**
 * @author nicolaas [at] sunnysideup.co.nz
 *
 * @package ecommerce
 * @package ecommerce
 */
class ExtendedProductVariation extends ProductVariation {

	static $has_one = array(
		'ExtendedProductVariation1' => 'ExtendedProductVariation1',
		'ExtendedProductVariation2' => 'ExtendedProductVariation2',
		'ExtendedProductVariation3' => 'ExtendedProductVariation3',
		'ExtendedProductVariation4' => 'ExtendedProductVariation4',
		'ExtendedProductVariation5' => 'ExtendedProductVariation5'
	);

	function getCMSFields_forPopup() {
		$fields = array();
		$fields[] = new TextField('Title');
		$fields[] = new TextField('Price');
		$fields[] = new DropdownField('ExtendedProductVariation1', "List 1","ExtendedProductVariation1");
		return new FieldSet($fields);
	}



}
