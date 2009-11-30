<?php
/**
 * @author Nicolaas [at] sunnysideup.co.nz
 * @package ecommerce
 * @sub-package ecommerce_productvariation
 * groups are things like: sex, size, colour (not brown but colour)
 * each group can link to many options (e.g. brown, yellow, white)
 * each group can link to many products that use its groups for the product variations
 * @MAGIC:
 * - automatically add ExtendedProductVariation to ProductWithVariation where....
 * -- it has not been added yet
 * -- the option group (ExtendedProductVariationGroup) links to the Product
 * or Product Group (to which the product belongs).
 * --
 *
 *
 */

class ExtendedProductVariationGroup extends DataObject {

	public static $db = array(
		"Name" => "Varchar(255)",
		"DisplayName" => "Varchar(255)",
		"Prefix" => "Varchar(50)",
		"Postfix" => "Varchar(50)",
		"IncludeOptionsAsSoleProductVariations" => "Boolean",
		"IncludeOptionsAsCombinedProductVariations" => "Boolean",
		"IncludeGroupTitleInVariationTitle" => "Boolean"
	);

	public static $has_one = array(
	);

	public static $has_many = array(
		"ExtendedProductVariationOptions" => "ExtendedProductVariationOption",
	);

	public static $belongs_many_many = array(
		"ProductWithVariations" => "SiteTree"
	);

	public static $defaults = array(
		"IncludeOptionsAsSoleProductVariations" => 0,
		"IncludeOptionsAsCombinedProductVariations" => 1,
		"IncludeGroupTitleInVariationTitle" => 0
	);

	public static $default_sort = "`Name` ASC";

	public static $searchable_fields = array(
		"Name" => "PartialMatchFilter"
	);

	public static $summary_fields = array(
		"Name"
	);

	public static $field_labels = array(
		"Name" => "Internal Name",
		"DisplayName" => "Display Name",
		"Prefix" => "When joining variations into a combo (e.g. - XL / Male / Red), what should go before option name (e.g. - [hyphen]), make sure to include a space where needed. If unsure leave blank.",
		"Postfix" => "When joining variations into a combo(e.g. - XL / Male / Red), what should go after the option name (e.g. /), make sure to include a space where needed. If unsure leave blank.",
		"IncludeOptionsAsSoleProductVariations" => "Include options from this group as sole product variations (e.g. variation will read &quot;XL&quot; OR &quot;Kid Size 5&quot; and never &quot;Kid Size 5, XL&quot;) for selected products - i.e. does not work in combination with other variations. If unsure, please do NOT tick.",
		"IncludeOptionsAsCombinedProductVariations" => "Include options from this group as combined variations (e.g. variation will read: &quot;XL, Male, Red&quot;) for selected products - i.e. works in combination with other variations. If unsure, please DO tick ",
		"IncludeGroupTitleInVariationTitle" => "Include List Name (see above) in Title - e.g. variation will read: &quot;colour: Red, size: XL&quot; rather than &quot;Red, XL&quot;"
	);

	public static $singular_name = "Option Group";
		static function set_singular_name($string) {self::$singular_name = $string; self::$variation_in_use = true;}

	public static $plural_name = "Option Groups";
		static function set_plural_name($string) {self::$plural_name = $string;}

	function getCMSFields() {
		$fields = parent::getCMSFields();
		if($this->ID) {
			$fields->dataFieldByName("ProductWithVariations")->setPermissions(array());
			$fields->dataFieldByName("ProductWithVariations")->pageSize=1000;
		}
		return $fields;
	}

  function addProductGroups($productGroups) {
    $existingCategories = $this->ProductGroups();
    // method 1: Add many by iteration
    foreach($additionalCategories as $category) {
      $existingCategories->add($category);
    }
  }

  function addProductsWithVariations($ProductsWithVariation) {
    $existingProductWithVariations = $this->ProductWithVariations();
    // method 1: Add many by iteration
    foreach($ProductsWithVariation as $product) {
      $existingProductWithVariations->add($products);
    }
  }

	function addExtendedProductVariationOptions($ExtendedProductVariationOptions) {
    $existingExtendedProductVariationOptions = $this->ExtendedProductVariationOptions();
    // method 1: Add many by iteration
    foreach($ExtendedProductVariationOptions as $option) {
      $existingExtendedProductVariationOptions->add($option);
    }
  }


	public function onBeforeWrite() {
		parent::onBeforeWrite();
	}

	public function onBeforeDelete() {
		if($children = DataObject::get("ExtendedProductVariationOption", '`ParentID` = "'.$this->ID.'"')) {
			foreach($children as $child) {
				$child->delete();
			}
		}
		parent::onBeforeDelete();
	}

	function requireDefaultRecords() {
		parent::requireDefaultRecords();
		$groups = DataObject::get("ExtendedProductVariationGroup");
		if($groups) {
			if(!$groups->count()) {
				$g = new ExtendedProductVariationGroup();
				$g->Name = "Default Group";
				$g->write();
			}
		}
	}



}