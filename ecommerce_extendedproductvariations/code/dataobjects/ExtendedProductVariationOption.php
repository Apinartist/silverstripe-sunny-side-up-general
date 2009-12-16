<?php
/**
 * @author Nicolaas [at] sunnysideup.co.nz
 * @package ecommerce
 * @sub-package ecommerce_productvariation
 * the options available for product variations
 * each one belong to a group
 * and links to many Variations (so we know how they relate to the products)
 */

class ExtendedProductVariationOption extends DataObject {

	public static $db = array(
		"Code" => "Varchar(30)",
		"Name" => "Varchar(255)",
		"Sort" => "Int"
	);
	public static $has_one = array(
		"Parent" => "ExtendedProductVariationGroup"
	);

	public static $has_many = array();

	public static $many_many = array(
		"ExtendedProductVariations" => "ExtendedProductVariation"
	);

	public static $defaults = array(
		"Sort" => 100
	);

	public static $casting = array(
		"FullName" => "Text"
	);

	public static $default_sort = "ParentID ASC, Sort ASC, Name ASC";

	public static $searchable_fields = array(
		"Code",
		"Name"
	);

	public static $summary_fields = array(
		"Code" => "Short Code",
		"Name" => "Long Name",
		"Sort" => "Sorting Value",
		"Parent.Name" => "Group"
	);

	public static $field_labels = array(
		"Code" => "Short Code",
		"Name" => "Long Name",
		"Sort" => "Number for Sorting, lower numbers show first, equal numbers are sorted alpabetically",
		"Parent" => "Group"
	);



	public static $singular_name = "Product Variation Option";
		static function set_singular_name($string) {self::$singular_name = $string; self::$variation_in_use = true;}

	public static $plural_name = "Product Variation Options";
		static function set_plural_name($string) {self::$plural_name = $string;}

	public function canCreate() {
		return DataObject::get_one("ExtendedProductVariationGroup") ? true: false;
	}

	function getCMSFields() {
		$fields = parent::getCMSFields();
		if(class_exists("DataObjectSorterController")) {
			$fields->addFieldToTab("Root.SortList", new LiteralField("InvitationToSort", $this->dataObjectSorterPopupLink($this->ParentID)));
		}
		return $fields;
	}

	function addExtendedProductVariations($ExtendedProductVariations) {
    $existingExtendedProductVariations = $this->ExtendedProductVariations();
    // method 1: Add many by iteration
    foreach($ExtendedProductVariations as $variations) {
      $existingExtendedProductVariations->add($variations);
    }
	}

	public function FullName () {
		$group = DataObject::get_by_id("ExtendedProductVariationGroup", $this->ParentID);
		$v = $group->Prefix;
		if($group->IncludeGroupTitleInVariationTitle) {
			$v .= $group->Title.': ';
		}
		$v .= $this->Name.$group->Postfix;
		return $v;
	}

	function ShorterName() {
		$group = DataObject::get_by_id("ExtendedProductVariationGroup", $this->ParentID);
		$v = '';
		if($group->IncludeGroupTitleInVariationTitle) {
			$v .= $group->Title.': ';
		}
		$v .= $this->Name;
		return $v;
	}

	public function onBeforeWrite() {
		if(!$this->ParentID) {
			$this->ParentID = DataObject::get_one("ExtendedProductVariationGroup")->ID;
		}
		parent::onBeforeWrite();
	}

	public function onBeforeDelete() {
		if($data = $this->ExtendedProductVariations()) {
			foreach($data as $combination) {
				$obj = DataObject::get_by_id("ExtendedProductVariation", $combination->ID);
				if($obj) {
					$obj->delete();
				}
			}
		}
		parent::onBeforeDelete();
	}

	function requireDefaultRecords() {
		DB::query("Update ExtendedProductVariationOption set Sort = 100 WHERE Sort IS NULL or Sort = 0");
		parent::requireDefaultRecords();
		//here is where the magic happens of auto adding the groups
		$options = DataObject::get("ExtendedProductVariationOption");
		if(!$options) {
			$o = new ExtendedProductVariationGroup();
			$o->Name = "Default Option";
			$o->write();
		}
		parent::requireDefaultRecords();
	}

	function ExtendedProductVariation() {
		return $this->ExtendedProductVariations();
	}

}