<?php

class DefaultRecordsForEcommerce extends DataObject {

	function requireDefaultRecords() {
		parent::requireDefaultRecords();
		$array = $this->UpdateSQLArray();
		foreach($array as $innerArray) {
			if(isset($innerArray["W"]) && $innerArray["W"]) {
				$innerArray["W"] = " WHERE ".$innerArray["W"];
			}
			else {
				$innerArray["W"] = '';
			}
			$T = $innerArray["T"];
			$F = $innerArray["F"];
			$V = $innerArray["V"];
			$W = $innerArray["W"];
			DB::query("UPDATE \"$T\" SET \"$F\" = '$V' $W");
			DB::alteration_message(" SETTING $F TO $V IN $T $W ", "created");
		}
		DB::query("DELETE FROM SiteTree");
		DB::query("DELETE FROM SiteTree_Live");
		DB::query("DELETE FROM SiteTree_versions");
		//DB::query("DELETE FROM Page");
		//DB::query("DELETE FROM Page_Live");
		//DB::query("DELETE FROM Page_versions");
		//DB::query("DELETE FROM AccountPage");
		//DB::query("DELETE FROM AccountPage_Live");
		//DB::query("DELETE FROM AccountPage_versions");
		DB::query("DELETE FROM CheckoutPage");
		DB::query("DELETE FROM CheckoutPage_Live");
		DB::query("DELETE FROM CheckoutPage_versions");
		DB::query("DELETE FROM ProductGroup");
		DB::query("DELETE FROM ProductGroup_Live");
		DB::query("DELETE FROM ProductGroup_versions");
		DB::query("DELETE FROM ErrorPage");
		DB::query("DELETE FROM ErrorPage_Live");
		DB::query("DELETE FROM ErrorPage_versions");
		DB::query("DELETE FROM Product");
		DB::query("DELETE FROM Product_Live");
		DB::query("DELETE FROM Product_versions");
		DB::query("DELETE FROM RepeatOrdersPage");
		DB::query("DELETE FROM RepeatOrdersPage_Live");
		DB::query("DELETE FROM RepeatOrdersPage_versions");
		//DB::query("DELETE FROM TypographyTestPage");
		//DB::query("DELETE FROM TypographyTestPage_Live");
		//DB::query("DELETE FROM TypographyTestPage_versions");
		$pages = $this->Pages();
		foreach($pages as $fields) {
			$this->MakePage($fields);
		}
		$orders = DataObject::get("Order");
		if($orders) {
			foreach($orders as $order) {
				$order->delete();
			}
		}
		$this->AddVariations();
		$obj = new CartCleanupTask();
		$obj->cleanupUnlinkedOrderObjects();
	}

	private function MakePage($fields, $parentPage = null) {
		$page = DataObject::get_one("SiteTree", "\"URLSegment\" = '".$fields["URLSegment"]."'");
		if(!$page) {
			$className = "Page";
			if(isset($fields["ClassName"])) {
				$className = $fields["ClassName"];
			}
			$page = new $className();
		}
		$children = null;
		foreach($fields as $field => $value) {
			if($field == "Children") {
				$children = $value;
			}
			$page->$field = $value;
		}
		if($parentPage) {
			$page->ParentID = $parentPage->ID;
		}
		$page->writeToStage('Stage');
		$page->Publish('Stage', 'Live');
		$page->Status = "Published";
		$page->flushCache();
		DB::alteration_message("Creating / Updating ".$page->Title, "created");
		if($children) {
			foreach($children as $child) {
				$this->MakePage($child, $page);
			}
		}
	}

	private function UpdateSQLArray() {
		return array(
			array("T" => "SiteConfig", "F" => "Title", "V" => "Silverstripe Ecommerce Demo", "W" => ""),
			array("T" => "SiteConfig", "F" => "Tagline", "V" => "Built by Sunny Side Up", "W" => ""),
			array("T" => "SiteConfig", "F" => "Theme", "V" => "main", "W" => ""),
			array("T" => "SiteConfig", "F" => "PostalCodeURL", "V" => "http://tools.nzpost.co.nz/tools/address-postcode-finder/APLT2008.aspx", "W" => ""),
			array("T" => "SiteConfig", "F" => "PostalCodeLabel", "V" => "Check Code", "W" => ""),
			array("T" => "SiteConfig", "F" => "ReceiptEmail", "V" => "demo-orders@sunnysideup.co.nz", "W" => ""),
		);
	}

	private function AddVariations() {
		$colourObject = DataObject::get_one("ProductAttributeType", "\"Name\" = 'Colour'");
		if(!$colourObject) {
			$colourObject = new ProductAttributeType();
			$colourObject->Name = "Colour";
			$colourObject->Label = "Colour";
			$colourObject->IsColour = true;
			$colourObject->Sort = 100;
			$colourObject->write();
		}
		if($colourObject) {
			$redObject = DataObject::get_one("ProductAttributeValue", "\"Value\" = 'red'");
			if(!$redObject) {		
				$redObject = new ProductAttributeValue();
				$redObject->Value = "red";
				$redObject->TypeID = $colourObject->ID;
				$redObject->Sort = 100;
				$redObject->write();
			}			
			$blueObject = DataObject::get_one("ProductAttributeValue", "\"Value\" = 'blue'");
			if(!$blueObject) {		
				$blueObject = new ProductAttributeValue();
				$blueObject->Value = "blue";
				$blueObject->TypeID = $colourObject->ID;
				$blueObject->Sort = 110;
				$blueObject->write();
			}			
		}
		else {
			die("COULD NOT CREATE COLOUR OBJECT");
		}
		$sizeObject = DataObject::get_one("ProductAttributeType", "\"Name\" = 'Size'");
		if(!$sizeObject) {		
			$sizeObject = new ProductAttributeType();
			$sizeObject->Name = "Size";
			$sizeObject->Label = "Size";
			$sizeObject->Sort = 110;
			$sizeObject->write();
		}
		if($sizeObject) {
			$smallObject = DataObject::get_one("ProductAttributeValue", "\"Value\" = 'S'");
			if(!$smallObject) {		
				$smallObject = new ProductAttributeValue();
				$smallObject->Value = "S";
				$smallObject->TypeID = $sizeObject->ID;
				$smallObject->Sort = 100;
				$smallObject->write();
			}			
			$xtraLargeObject = DataObject::get_one("ProductAttributeValue", "\"Value\" = 'XL'");
			if(!$xtraLargeObject) {		
				$xtraLargeObject = new ProductAttributeValue();
				$xtraLargeObject->Value = "XL";
				$xtraLargeObject->TypeID = $sizeObject->ID;
				$xtraLargeObject->Sort = 110;
				$xtraLargeObject->write();
			}			
		}
		else {
			die("COULD NOT CREATE SIZE OBJECT");
		}
		
		$products = DataObject::get("Product", "", "", "", "0, 5");
		if($products && $colourObject && $sizeObject) {
			$variationCombos = array(
				array("Size" => $xtraLargeObject, "Colour" => $redObject),
				array("Size" => $xtraLargeObject, "Colour" => $blueObject),
				array("Size" => $smallObject, "Colour" => $redObject),
				array("Size" => $smallObject, "Colour" => $blueObject)
			);			
			foreach($products as $product) {
				$existingAttributeTypes = $product->VariationAttributes();
				$existingAttributeTypes->add($sizeObject);
				$existingAttributeTypes->add($colourObject);
				$existingAttributeTypes->write();
				$product->writeToStage('Stage');
				$product->Publish('Stage', 'Live');
				$product->Status = "Published";
				$product->flushCache();
				if(!DataObject::get("ProductVariation", "ProductID  = ".$product->ID)) {
					foreach($variationCombos as $variationCombo) {
						$productVariation = new ProductVariation();
						$productVariation->ProductID = $product->ID;
						$productVariation->Price = $product->Price * 2;
						$productVariation->write();
						$existingAttributeValues = $productVariation->AttributeValues();
						$existingAttributeValues->add($variationCombo["Size"]);
						$existingAttributeValues->add($variationCombo["Colour"]);
						$existingAttributeValues->write();
						DB::alteration_message(" Creating variation for ".$product->Title . " // COLOUR ".$variationCombo["Colour"]->Value. " SIZE ".$variationCombo["Size"]->Value, "created");					
					}
				}
			}
		}
	}

	/**
	 *
	 *Children = child pages....
	 **/

	function Pages() {
		return array(
			array(
				"URLSegment" => "home",
				"Title" => "Sunny Side Up Silverstripe Demo",
				"MenuTitle" => "Home",
				"Content" => "<p>This is a demo site for the Silverstripe E-commerce, developed by Sunny Side Up.  You can checkout the e-commerce module here: <a href=\"http://sunny.svnrepository.com/svn/sunny-side-up-general/ecommerce_test/\">http://sunny.svnrepository.com/svn/sunny-side-up-general/ecommerce_test/</a>.</p>"
			),
			array(
				"ClassName" => "TypographyTestPage",
				"URLSegment" => "typo",
				"Title" => "Typography Test page",
				"MenuTitle" => "Typo Page",
				"ShowInMenus" => 0,
				"ShowInSearch" => 0,
			),
			array(
				"ClassName" => "ProductGroup",
				"URLSegment" => "ProductGroup",
				"Title" => "Products",
				"MenuTitle" => "Products",
				"Content" => "<p>Please review our products below.</p>",
				"Children" => $this->getProducts()
			),
			array(
				"ClassName" => "CheckoutPage",
				"URLSegment" => "checkout",
				"Title" => "Checkout",
				"MenuTitle" => "Checkout",
				"Content" => "<p>Enter your details below to complete your order.</p>",
			)
		);
	}

	private function getProducts() {
		$array = array();
		for($i = 1; $i < 20; $i++) {
			$array[$i] = array(
				"ClassName" => "Product",
				"URLSegment" => "product$i",
				"Title" => "Product $i",
				"MenuTitle" => "Product $i",
				"Content" => "<p>Description for Product $i ...</p>",
				"Price" => 10 + $i + ($i / 100),
				"Featured" => (round($i / 15) == $i / 15) ? 1 : 0,
				"InternalItemID" => "AAA".$i
			);
		}
		return $array;
	}

}
