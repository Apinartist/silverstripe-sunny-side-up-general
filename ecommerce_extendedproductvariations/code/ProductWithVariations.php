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

	static $icon = 'ecommerce_extendedproductvariations/images/treeicons/ProductWithVariations';

	static $hide_ancestor = "Product";

	static $add_action = 'Ecommerce Product With Variations';

	public static $defaults = array(
		'AllowPurchase' => true,
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
		$fields->addFieldsToTab("Root.Content.ProductVariations",
			new FieldSet(
				new HeaderField("CreateVariationGroupsHeader",'2. Create Variations', 3),
				new LiteralField("VariationsCreateAll",'<p><a href="'.$this->Link().'createallvariations/?stage=Stage" target="_blank">create all variations</a> - based on the variation lists selected above. Make sure page is SAVED and default PRICE has been entered!'),
				new HeaderField("VariationsExplanation","3. Review and edit actual product variations for sale (price must be higher than zero)", 3),
				$this->getVariationsTable(),
				new LiteralField("VariationsDeteleAll",'<p><a href="'.$this->Link().'deleteallvariations/?stage=Stage" target="_blank">delete all variations</a> - useful if the variations have gone pearshaped - PLEASE USE WITH CARE!</p>')
			)
		);
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
		$field->setPermissions(array("view", "edit", "delete"));
		$field->pageSize = 1000;
		return $field;
	}

	function IsInCart() {
		$variations = DataObject::get("ExtendedProductVariation", "Price > 0 AND ProductID = ".$this->ID);
		if($variations) {
			$v = false;
			$existingExtendedProductVariations = $this->Variations();
			foreach($existingExtendedProductVariations as $variation) {
				if($variation->IsInCart()) {
					$v = true;
				}
			}
		}
		else {
			$v = parent::IsInCart();
		}
		return $v;
	}

	function cleanupvariations() {
		if(!Permission::check("ADMIN")) {
			Security::permissionFailure($this, _t('Security.PERMFAILURE',' This page is secured and you need administrator rights to access it. Enter your credentials below and we will send you right along.'));
			return false;
		}
		else {
			DB::query("Delete FROM ProductVariation WHERE ProductID = 0 OR ProductID = ".$this->ID);
			DB::query("Delete FROM ProductVariation_versions WHERE ProductID = 0 OR ProductID = ".$this->ID);
			return true;
		}
	}

	public function deleteallvariations() {
		if($this->cleanupvariations()) {
			$count = 0;
			$v = $this->Variations();
			foreach($v as $variation) {
				$count++;
				$variation->delete();
			}
			return 'deleted  variations for '.$this->Title;
		}
	}


	function createallvariations() {
		if($this->cleanupvariations()) {
			if($this->Price) {
				$combinations = $this->getParentExtendedProductVariationGroups();
				if($combinations) {
					$groupsDataObject = new DataObjectSet();
					foreach($combinations as $combination) {
						//getting basic group data
						$group = DataObject::get_by_id("ExtendedProductVariationGroup", $combination->ExtendedProductVariationGroupID);
						$groupsDataObject->push($group);
						//making sole ones
						if($group->IncludeOptionsAsSoleProductVariations) {
							$options = $group->ExtendedProductVariationOptions();
							if($options) {
								foreach($options as $option) {
									$optionsDos = new DataObjectSet();
									$optionsDos->push($option);
									$this->createExtendedProductVariations($optionsDos);
								}
							}
						}
					}
					$obj = new ExtendedProductVariationOptionComboMaker();
					$obj->addGroups($groupsDataObject);
					$array = $obj->finalise();
					if(is_array($array) && count($array)) {
						//going through each ID list of option combos...
						foreach($array as $IDlist) {
							$optionsDos = DataObject::get("ExtendedProductVariationOption", '`ID` IN('.$IDlist.') ');
							$this->createExtendedProductVariations($optionsDos);
						}
					}
				}
			}
			else {
				die("you need to specify a price for the product first");
			}
		}
	}

	protected function createExtendedProductVariations($optionsDos) {
		//does it exist?
		$title = '';
		if($optionsDos->count() < 1) {
			return;
		}
		elseif(1 == $optionsDos->count()) {
			foreach($optionsDos as $option) {
				$title .= $option->ShorterName();
			}
		}
		else {
			foreach($optionsDos as $option) {
				$title .= $option->FullName();
			}
		}
		if($title) {
			$obj = ExtendedProductVariation::return_existing_or_create_new($optionsDos, $this->ID);
			if($obj) {
				if($obj instanceOf ExtendedProductVariation) {
					$obj->Title = $title;
					Database::alteration_message("Creating &quot;".$obj->Title."&quot; for &quot;".$this->Title."&quot;");
					$obj->Price = $this->Price;
					$obj->ProductID = $this->ID;
					$obj->write();
					//links Extend Product Variation to Options
					$variationDos = new DataObjectSet();
					$variationDos->push($obj);
					$this->addExtendedProductVariation($variationDos);
					foreach($optionsDos as $option) {
						$option->addExtendedProductVariations($variationDos);
					}
				}
			}
			else {
				user_error("Could not create ExtendedProductVariation because ExtendedProductVariation::return_existing_or_create_new did not return object");
			}
		}
		else {
			user_error("Could not create ExtendedProductVariation no title could be created");
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
		$pages = DataObject::get("Product", "`URLSegment` = 'example-product' OR `URLSegment` = 'example-product-2'");
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

	function onAfterWrite() {
		parent::onAfterWrite();
		//LeftAndMain::ForceReload();
	}

}

class ProductWithVariations_Controller extends Product_Controller {

	static $allowed_actions = array("showsimplecart", "ProductVariationsForm", "deleteallvariations", "createallvariations");

	protected $optionArray = null;

	function init() {
		parent::init();
	}

	function addVariation($data, $form) {
		$msg = '';
		$update = "Item could not be added, please try again.";
		if(isset($data["CurrentVariation"])) {
			if($data["CurrentVariation"] == -1) {
				$orderItem = new Product_OrderItem($this->dataRecord);
				ShoppingCart::add_new_item($orderItem);
				$update = "Added to Cart, ";
			}
			else {
				$variation = DataObject::get_one('ProductVariation','`ID` = '.(int)$data["CurrentVariation"].' AND `ProductID` = '.(int)$this->ID);
				if($variation) {
					if($variation->AllowPurchase()) {
						ShoppingCart::add_new_item(new ProductVariation_OrderItem($variation));
						$update = "Added to Cart, ";
					}
				}
				else {
					$msg = "Could not find product variation.";
				}
			}
		}
		else {
			$msg = "Could not be added to cart.";
		}
		if($checkoutPage = DataObject::get_one("CheckoutPage")) {
			$msg .= '<a href="'.$checkoutPage->Link().'">'.$update.' View Order</a>';
		}
		else {
			$msg .= $update;
		}
		if(!$this->isAjax()) {
			Session::set("ProductVariationsFormMessage", $msg);
			Director::redirectBack();
			return;
		}
		else {
			return $msg;
		}
	}

	public function ProductVariationsForm() {
		$buttonTitle = "Add To Cart";
		$fancyPrice = Payment::site_currency().$this->dbObject("Price")->Nice();
		if($variationsAvailable = $this->VariationsAvailable()) {
			$selectFields = new FieldSet();
			$groups = $this->ExtendedProductVariationGroups();
			if($groups) {
				foreach($groups as $group) {
					$options = DataObject::get("ExtendedProductVariationOption", "`ParentID` = ".$group->ID);
					//what options are actually available:
					if($options) {
						$selectFields->push(new DropdownField("ExtendedProductVariationGroup[".$group->ID."]", $group->DisplayName, $this->optionArray[$group->ID]));
					}
				}
			}

			$selectFieldsGp = new CompositeField($selectFields);
			$selectFieldsGp->setID("ExtendedProductVariationDropdowns");
			$fieldSet = new FieldSet($selectFieldsGp);
			$fieldSet->push(new DropdownField("CurrentVariation", "Final Selection", $variationsAvailable->toDropDownMap("ID", "Title", "--not selected--", "Title" )));
		}
		else {
			$fieldSet = new FieldSet();
			$fieldSet->push(new DropdownField("CurrentVariation", "Final Selection", array(-1 => $this->Title)));
			if($this->IsInCart()) {
				$buttonTitle = "Add Again";
				Requirements::customScript(
					"ProductWithVariations.AddProduct(-1); ProductWithVariations.PriceArray[0] = '".$fancyPrice."';",
					"ProductWithVariationsArray-1"
				);
			}
		}
		$fieldSet->push(new LiteralField('PriceField','<div id="ExtendedProductVariationPrice" class="toBeAdded">'.$fancyPrice.'</div>'));
		$msg = Session::get("ProductVariationsFormMessage");
		Session::set("ProductVariationsFormMessage", "");
		$fieldSet->push(new LiteralField('ExtendedProductVariationMessage','<div id="ExtendedProductVariationMessage">'.$msg.'</div>'));
		$action = new FormAction($action = "addVariation",$buttonTitle);
		$form = new Form(
			$controller = $this,
			$name = "ProductVariationsForm",
			$fields = $fieldSet,
			$actions = new FieldSet($action)
		);
		return $form;
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
					$this->optionArray[$option->ParentID][$option->ID] = $option->Name;
					$js .= " ProductWithVariations.ItemArray[$number][".$option->ParentID."] = ".$option->ID.";\r\n";
				}
				$fancyPrice = Payment::site_currency().$variation->dbObject("Price")->Nice();
				if($variation->IsInCart()) {
					Requirements::customScript("ProductWithVariations.AddProduct(".$variation->ID.");", "ProductWithVariationsArray".$variation->ID);
				}
				$js .= " ProductWithVariations.PriceArray[".$number."] = '".$fancyPrice."';\r\n";
				$js .= " ProductWithVariations.IDArray[".$number."] = ".$variation->ID.";\r\n";
			}
		}
		Requirements::javascript(THIRDPARTY_DIR."/jquery/plugins/form/jquery.form.js");
		Requirements::javascript("ecommerce_extendedproductvariations/javascript/ProductWithVariations.js");
		Requirements::customScript($js,'ProductWithVariationsArray');
		return $variations;
	}



}

