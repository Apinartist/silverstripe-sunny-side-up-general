<?php
/**
 * @author nicolaas [at] sunnysideup.co.nz
 * @package ecommerce
 * @sub-package ecommerce_productvariation
 * Extends Product
 * links to ProductVariationGroups to shwo what the product should have available
 * even if you do not sell the product without variation (i.e. you only sell red jersye and not jersey)
 * then still add a price as this will be the default price for the jersey variations
 * DEBUG RESET: TRUNCATE TABLE `extendedproductvariationoption_extendedproductvariations`  ;TRUNCATE `productvariation` ; TRUNCATE `productvariation_live` ;
   */
class ProductWithVariations extends Product {

	static $db = array(
		"DoNotAddVariationsAutomatically" => "Boolean"
	);

	static $icon = 'ecommerce_extendedproductvariations/images/treeicons/ProductWithVariations';

	static $hide_ancestor = "Product";

	static $add_action = 'a Product with variations';

	public static $defaults = array(
		'AllowPurchase' => true
	);

	public static $casting = array();

	protected static $hide_product_fields = array();
		static function set_hide_product_fields(array $array) {
			if(is_array($array)) {
				self::$hide_product_fields = $array;
			}
			else {
				user_error("ProductWithVariations::set_product_hide_fields() expects an array as first argument");
			}
		}

	function getCMSFields() {
		$fields = parent::getCMSFields();
		$fields->removeByName("Variations");
		foreach(self::$hide_product_fields as $fieldName) {
			$fields->removeByName($fieldName);
		}
		$fields->addFieldsToTab("Root.Content.ProductVariations", new CheckboxField("DoNotAddVariationsAutomatically", "Do not add variations automatically"));
		$fields->addFieldsToTab("Root.Content.ProductVariations",
			new HeaderField("VariationsExplanation","Actual product variations for sale (price must be higher than zero)", 3)
		);
		$fields->addFieldsToTab("Root.Content.ProductVariations",$this->getVariationsTable());
		return $fields;
	}



	function getVariationsTable() {
		$singleton = singleton('ExtendedProductVariation');
		$query = $singleton->buildVersionSQL("`ProductID` = ".$this->ID);
		$variations = $singleton->buildDataObjectSet($query->execute());
		$filter = $variations ? "`ID` IN ('" . implode("','", $variations->column('RecordID')) . "')" : "`ID` < '0'";
		//$filter = "`ProductID` = '{$this->ID}'";

		$field = new HasManyComplexTableField(
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
		if(method_exists($field, 'setRelationAutoSetting')) {
			$field->setRelationAutoSetting(true);
		}
		//$field->setPermissions(array("view", "edit"));
		$field->pageSize = 1000;
		return $field;
	}

	function IsInCart() {
		$v = false;
		$existingExtendedProductVariations = $this->Variations();
		foreach($existingExtendedProductVariations as $variation) {
			if($variation->IsInCart()) {
				$v = true;
			}
		}
		return $v;
	}


	function onBeforeWrite() {
		if($this->Price && !$this->DoNotAddVariationsAutomatically) {
			$combinations = $this->getParentExtendedProductVariationGroups();
			if($combinations) {
				$groupsDataObject = new DataObjectSet();
				foreach($combinations as $combination) {
					$group = DataObject::get_by_id("ExtendedProductVariationGroup", $combination->ExtendedProductVariationGroupID);
					$groupsDataObject->push($group);
					$options = $group->ExtendedProductVariationOptions();
					if($options && $group->IncludeOptionsAsSoleProductVariations) {
						foreach($options as $option) {
							$optionsDos = new DataObjectSet();
							$optionsDos->push($option);
							$this->createExtendedProductVariations($optionsDos);
						}
					}
				}
				$obj = new ExtendedProductVariationOptionComboMaker();
				$obj->addGroups($groupsDataObject);
				$array = $obj->finalise();
				if(is_array($array) && count($array)) {
					foreach($array as $IDlist) {
						$optionsDos = DataObject::get("ExtendedProductVariationOption", '`ID` IN('.$IDlist.') ');
						$this->createExtendedProductVariations($optionsDos);
					}
				}
			}
		}
		parent::onBeforeWrite();
	}


	protected function createExtendedProductVariations($optionsDos) {
		//does it exist?
		$title = '';
		if(1 == $optionsDos->count()) {
			foreach($optionsDos as $option) {
				$title = $option->ShorterName();
			}
		}
		elseif($optionsDos->count() > 1) {
			foreach($optionsDos as $option) {
				$title .= $option->FullName();
			}
		}
		if($title) {
			$obj = ExtendedProductVariation::return_existing_or_create_new($title, $optionsDos, $this->ID);
			if($obj instanceOf ExtendedProductVariation) {
				$obj->Title = $title;
			}
			elseif($obj) {
				//create title
				//create Extended Product Variation
				$obj = new ExtendedProductVariation();
				$obj->Title = $title;
			}
			if($obj) {
				$obj->Price = $this->Price;
				$obj->ProductID = $this->ID;
				$obj->write();
				//links Extend Product Variation to Options
				$variationDos = new DataObjectSet();
				$variationDos->push($obj);
				foreach($optionsDos as $option) {
					$option->addExtendedProductVariations($variationDos);
					$this->addExtendedProductVariation($variationDos);
				}
			}
		}
	}

	function addExtendedProductVariation($ExtendedProductVariations) {
    $existingExtendedProductVariations = $this->Variations();
    // method 1: Add many by iteration
    foreach($ExtendedProductVariations as $variations) {
      $existingExtendedProductVariations->add($variations);
    }
	}

	function requireDefaultRecords() {
		parent::requireDefaultRecords();
		//delete example product pages
		$pages = DataObject::get("Product", '`URLSegment` = "example-product" OR `URLSegment` = "example-product-2"');
		if($pages) {
			if(2 == $pages->count() ) {
				foreach($pages as $page) {
					$id = $page->ID;
					$stageRecord = Versioned::get_one_by_stage('SiteTree', 'Stage', "SiteTree.ID = $id");
					if ($stageRecord) $stageRecord->delete();
					$liveRecord = Versioned::get_one_by_stage('SiteTree', 'Live', "SiteTree_Live.ID = $id");
					if ($liveRecord) $liveRecord->delete();
				}
			}
		}
	}

}

class ProductWithVariations_Controller extends Product_Controller {

	function init() {
		parent::init();
	}

	function addVariation($data, $form) {
		if(isset($data["CurrentVariation"])) {
			$variation = DataObject::get_one('ProductVariation','`ID` = '.(int)$data["CurrentVariation"].' AND `ProductID` = '.(int)$this->ID);
			if($variation) {
				if($variation->AllowPurchase()) {
					ShoppingCart::add_new_item(new ProductVariation_OrderItem($variation));
					if(!$this->isAjax()) {
						Session::set("ProductVariationsFormMessage", "Added to cart.");
						Director::redirectBack();
						return;
					}
				}
			}
		}
		Session::set("ProductVariationsFormMessage", "Could not be added to cart.");
	}

	public function ProductVariationsForm() {
		if($variationsAvailable = $this->VariationsAvailable()) {
			$selectFields = new FieldSet();
			$groups = $this->ExtendedProductVariationGroups();
			if($groups) {
				foreach($groups as $group) {
					$options = DataObject::get("ExtendedProductVariationOption", "`ParentID` = ".$group->ID);
					if($options) {
						$selectFields->push(new DropdownField("ExtendedProductVariationGroup[".$group->ID."]", $group->Title, $options->toDropDownMap("ID", "Name", null, "Name")));
					}
				}
			}

			$selectFieldsGp = new CompositeField($selectFields);
			$selectFieldsGp->setID("ExtendedProductVariationDropdowns");
			$fieldSet = new FieldSet($selectFieldsGp);
			$fieldSet->push(new DropdownField("CurrentVariation", "Final Selection", $variationsAvailable->toDropDownMap("ID", "Title", "--not selected--", "Title" )));
			$fieldSet->push(new LiteralField('PriceField','<div id="ExtendedProductVariationPrice">'.$this->Price.'</div>'));
			if($msg = Session::get("ProductVariationsFormMessage")) {
				$fieldSet->push(new LiteralField('ExtendedProductVariationMessage','<div id="ExtendedProductVariationMessage">'.$msg.'</div>'));
				Session::set("ProductVariationsFormMessage", "");
			}
			$action = new FormAction($action = "addVariation",$title = "Add To Cart");
			return new Form(
				$controller = $this,
				$name = "ProductVariationsForm",
				$fields = $fieldSet,
				$actions = new FieldSet($action)
			);
		}
	}

	public function VariationsAvailable() {
		$items = array();
		$variations = DataObject::get("ExtendedProductVariation", "Price > 0 AND ProductID = ".$this->ID);
		$js = '';
		if($variations) {
			foreach($variations as $number => $variation) {
				$options = $variation->ExtendedProductVariationOptions();
				$js .= "ProductWithVariations.ItemArray[$number] = new Array();\r\n";
				foreach($options as $option) {
					$optionArray[$option->ParentID] = $option->ID;
					$js .= " ProductWithVariations.ItemArray[$number][".$option->ParentID."] = ".$option->ID.";\r\n";
				}
				$js .= " ProductWithVariations.PriceArray[".$number."] = '".$variation->Price."';\r\n";
				$js .= " ProductWithVariations.IDArray[".$number."] = ".$variation->ID.";\r\n";
			}
		}
		Requirements::javascript("ecommerce_extendedproductvariations/javascript/ProductWithVariations.js");
		Requirements::customScript($js,'ProductWithVariationsArray');
		return $variations;
	}

	public function deleteallvariations() {
		if(!Permission::check("ADMIN")) {
			Security::permissionFailure($this, _t('Security.PERMFAILURE',' This page is secured and you need administrator rights to access it. Enter your credentials below and we will send you right along.'));
		}
		else {
			$existingExtendedProductVariations = $this->Variations();
			// method 1: Add many by iteration
			foreach($ExtendedProductVariations as $variations) {
				$variations->delete();
			}
			return "deleted all variations for this product";
		}

	}

}

