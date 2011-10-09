<?php

class DefaultRecordsForEcommerce extends DataObject {

	protected $examplePages = array();

	function requireDefaultRecords() {
		parent::requireDefaultRecords();

		$this->checkreset();

		$this->CreatePages();

		$this->AddVariations();

		$this->AddMyModifiers();

		$this->UpdateMyRecords();

		$this->createTags();

		$this->createRecommendedProducts();

		$this->addStock();

		$this->addSpecialPrice();

		$this->productsInManyGroups();

		$this->createShopAdmin();

		$this->collateExamplePages();

	}

	function checkreset(){
		if(DataObject::get_one("Product")) {
			echo "<script type=\"text/javascript\">window.location = \"/build-ecommerce/reset/?flush=1\";</script>";
			die("data has not been reset yet... <a href=\"/build-ecommerce/reset/\">reset data now....</a>");
		}
	}


	private function CreatePages()  {
		$pages = $this->Pages();
		foreach($pages as $fields) {
			$this->MakePage($fields);
		}
		$termsPage = DataObject::get_one("Page", "URLSegment = 'terms-and-conditions'");
		$checkoutPage = DataObject::get_one("CheckoutPage");
		$checkoutPage->TermsPageID = $termsPage->ID;
		$checkoutPage->writeToStage('Stage');
		$checkoutPage->Publish('Stage', 'Live');
		$checkoutPage->Status = "Published";
		$checkoutPage->flushCache();
		DB::alteration_message("adding terms page to checkout page");
	}

	/**
	 *
	 *Children = child pages....
	 **/

	private function Pages() {
		return array(
			array(
				"URLSegment" => "home",
				"Title" => "Sunny Side Up Silverstripe E-commerce Demo",
				"MenuTitle" => "Home",
				"Content" => "
				<p>
					This is a demo site for the Silverstripe E-commerce, developed by Sunny Side Up.
					You can install an identical copy of this site (including test data) on your own development server by checking out this SVN repository: <br />
					<a href=\"http://sunny.svnrepository.com/svn/sunny-side-up-general/ecommerce_test/\">http://sunny.svnrepository.com/svn/sunny-side-up-general/ecommerce_test/</a>.
				</p><p>
					Thank you <a href=\"http://www.silverstripe.org\">Silverstripe Community</a> for the Silverstripe foundation.
					A big <i>kia ora</i> also to all the developers who contributed to <a href=\"http://code.google.com/p/silverstripe-ecommerce/\">the Silverstripe Ecommerce Project</a>, especially <a href=\"http://www.burnbright.co.nz/\">Jeremy</a>.
				</p>
				<p>
					If you have some feedback then please <a href=\"/about-us/\">contact us</a>.
					<a href=\"http://www.sunnysideup.co.nz\">Sunny Side Up</a> is also available for <a href=\"/about-us/\">paid support</a>.
				</p>
				<p>
					This demo is based on the <a href=\"https://silverstripe-ecommerce.googlecode.com/svn/branches/ssu/\">Sunny Side Up Branch</a> of e-commerce, as well as a buch of complementary modules.
					You can log-in as follows: shop@silverstripe-eommerce.com / test123.
				</p>
				",
				"Children" => array(
					array(
						"URLSegment" => "tag-explanation",
						"Title" => "Tag Explanations",
						"MenuTitle" => "Tags",
						"ShowInMenus" => false,
						"ShowInSearch" => false,
						"Content" => "<p>This page can explain the tags shown for various products. </p>",
					)
				)
			),
			array(
				"ClassName" => "ProductGroup",
				"URLSegment" => "shop",
				"Title" => "Shop",
				"MenuTitle" => "Shop",
				"Content" => "<p>Please review our products below.</p>",
				"Children" => $this->getProductGroups()
			),
			array(
				"ClassName" => "CheckoutPage",
				"URLSegment" => "checkout",
				"Title" => "Checkout",
				"MenuTitle" => "Checkout",
				"Content" => "
					<p>
						For further information on our terms of trade, please visit ....
						NOTE: a checkout page can also be broken down into several steps (pages) using a setting in the CMS. These steps are:
					</p>
					<ul>
						<li><a href=\"/orderstep/orderitems/#OrderItemsOuter\">Order Items</a></li>
						<li><a href=\"/orderstep/ordermodifiers/#OrderModifiersOuter\">Modifiers (tax / delivery / etc...)</a></li>
						<li><a href=\"/orderstep/orderconfirmation/#OrderConfirmationOuter\">Confirm Order</a></li>
						<li><a href=\"/orderstep/orderformandpayment/#OrderFormAndPaymentOuter\">Client Details + Payment (payment will be separated at some stage)</a></li>
					</ul>
					<p>To test the tax, set your country to New Zealand (GST inclusive) or Australia (exclusive tax)</p>
				",
				"InvitationToCompleteOrder" => "<p>Please complete your details below to finalise your order.</p>",
				"AlreadyCompletedMessage" => "<p>Sorry, but this order has already been completed and can no longer be edited.</p>",
				"FinalizedOrderLinkLabel" => "View completed order",
				"CurrentOrderLinkLabel" => "View current order",
				"StartNewOrderLinkLabel" => "Start new order",
				"NoItemsInOrderMessage" => "<p>There are no items in your current order</p>",
				"NonExistingOrderMessage" => "<p>We are sorry, but we can not find this order.</p>",
				"MustLoginToCheckoutMessage" => "<p>You must log in first before you can check out this order.</p>",
				"LoginToOrderLinkLabel" => "Log in now to checkout order",
				"Children" => array(
					array(
						"ClassName" => "OrderConfirmationPage",
						"URLSegment" => "confirmorder",
						"Title" => "Order Confirmation",
						"MenuTitle" => "Order Confirmation",
						"ShowInMenus" => 0,
						"ShowInSearch" => 0,
						"Content" => "<p>Please review your order below.</p>"
					),
					array(
						"ClassName" => "AccountPage",
						"URLSegment" => "account page",
						"Title" => "Account Page",
						"MenuTitle" => "Account Page",
						"ShowInMenus" => 0,
						"ShowInSearch" => 0,
						"Content" => "<p>Update your details below.</p>"
					),
					array(
						"ClassName" => "Page",
						"URLSegment" => "terms-and-conditions",
						"Title" => "Terms and Conditions",
						"MenuTitle" => "Terms",
						"ShowInMenus" => 1,
						"ShowInSearch" => 1,
						"Content" => "<p>All terms and conditions go here...</p>"
					)
				)
			),
			array(
				"ClassName" => "CartPage",
				"URLSegment" => "cart",
				"Title" => "Cart",
				"MenuTitle" => "Cart",
				"ShowInMenus" => 0,
				"ShowInSearch" => 0,
				"Content" => "<p>Please review your order below. A Cart Page is like a checkout page but without the checkout form.</p>"
			),
			array(
				"ClassName" => "AddToCartPage",
				"URLSegment" => "quick-add",
				"Title" => "Quick Add",
				"MenuTitle" => "Quick Add",
				"ShowInMenus" => 1,
				"ShowInSearch" => 1,
				"Content" => "<p>Choose your products below and continue through to the checkout...</p>",
				"Children" => array(
					array(
						"ClassName" => "AddUpProductsToOrderPage",
						"URLSegment" => "add-up-order",
						"Title" => "Add Up Order",
						"MenuTitle" => "Add Up Order",
						"ShowInMenus" => 1,
						"ShowInSearch" => 1,
						"Content" => "<p>Choose your products below and continue through to the checkout...</p>",
					),
					array(
						"ClassName" => "PriceListPage",
						"URLSegment" => "price-list",
						"Title" => "Price List",
						"MenuTitle" => "Price List",
						"ShowInMenus" => 1,
						"ShowInSearch" => 1,
						"NumberOfProductsPerPage" => 100,
						"Content" => "<p>please review all our prices below...</p>"
					),
				)
			),
			array(
				"ClassName" => "AnyPriceProductPage",
				"URLSegment" => "donation",
				"Title" => "Make a donation",
				"MenuTitle" => "Donate",
				"Content" => "<p>You can try out our <i>Any Price Product</i> below, by entering a value you want to <i>Donate</i>. This page can be used to allow customers to make payments such as donations or wherever they can determine the price.  You can send them a link to this page with an amount like this: <i>/donate/setamount/11.11</i></p>",
				"Price" => 0,
				"Featured" => 0,
				"InternalItemID" => "DONATE"
			),
			array(
				"ClassName" => "Page",
				"URLSegment" => "about-us",
				"Title" => "about us",
				"MenuTitle" => "about us",
				"ShowInMenus" => 1,
				"ShowInSearch" => 1,
				"Content" => "
					<p>
						This demo e-commerce website has been developed by <a href=\"http://www.sunnysideup.co.nz\">Sunny Side Up</a> for evaluation and testing.
						If you would like help in building an e-commerce website using the Silverstripe CMS then do not hesitate to contact us.
						We charge <a href=\"http://www.xe.com/ucc/convert/?Amount=150&From=NZD&To=EUR\">NZD150</a> per hour for any e-commerce development work.
						In many cases, we have provided the back-bone (PHP + Javascript) for sites, with our clients taking care of the front-end (HTML + CSS).
						Here are our estimated charges for e-commerce websites:
					</p>
					<ul>
						<li>install only: <a href=\"http://www.xe.com/ucc/convert/?Amount=700&From=NZD&To=EUR\">NZD700</a>.</li>
						<li>small-size site, PHP + Javascript only: <a href=\"http://www.xe.com/ucc/convert/?Amount=1200&From=NZD&To=EUR\">NZD1200</a>.</li>
						<li>small-size site, PHP + Javascript + CSS: <a href=\"http://www.xe.com/ucc/convert/?Amount=2400&From=NZD&To=EUR\">NZD2400</a>.</li>
						<li>small-size site, PHP + Javascript + CSS + Design: <a href=\"http://www.xe.com/ucc/convert/?Amount=4800&From=NZD&To=EUR\">NZD4800</a>.</li>
						<li>medium-size site, PHP + Javascript only: <a href=\"http://www.xe.com/ucc/convert/?Amount=3600&From=NZD&To=EUR\">NZD3600</a>.</li>
						<li>medium-size site, PHP + Javascript + CSS: <a href=\"http://www.xe.com/ucc/convert/?Amount=7200&From=NZD&To=EUR\">NZD7200</a>.</li>
						<li>medium-size site, PHP + Javascript + CSS + Design: <a href=\"http://www.xe.com/ucc/convert/?Amount=10800&From=NZD&To=EUR\">NZD10800</a>.</li>
						<li>large-size site, PHP + Javascript only: <a href=\"http://www.xe.com/ucc/convert/?Amount=10800&From=NZD&To=EUR\">NZD10800</a>.</li>
						<li>large-size site, PHP + Javascript + CSS: <a href=\"http://www.xe.com/ucc/convert/?Amount=21600&From=NZD&To=EUR\">NZD21600</a>.</li>
						<li>large-size site, PHP + Javascript + CSS + Design: <a href=\"http://www.xe.com/ucc/convert/?Amount=43200&From=NZD&To=EUR\">NZD43200</a>.</li>
					</ul>
					<h2>Track Record</h2>
					<p>
						We are one of a few companies who have actually built a solid number of e-comemrce sites using the Silverstripe CMS.
						Our work includes:
					</p>
					<ul>
						<li><a href=\"http://www.photowarehosue.co.nz\">photowarehouse</a> - a large retailer site with over four thousand products</li>
						<li><a href=\"http://www.kprcatering.co.nz\">kpr catering</a> - a catering company</li>
						<li><a href=\"http://www.resumetemplates-usa.com\">resume templates usa</a> - a US based company selling curriculum vitae (resume) templates</li>
						<li><a href=\"http://www.guyton.co.nz\">guytons</a> - a famous fish shop</li>
					</ul>
					<p>
						Feel free to contact us by phone: +64 4 889 2773 or email: ecommerce [at] sunnysideup [dot] co [dot] nz for more information.
					</p>
				"
			),
			array(
				"ClassName" => "TypographyTestPage",
				"URLSegment" => "typo",
				"Title" => "Typography Test page",
				"MenuTitle" => "Typo Page",
				"ShowInMenus" => 0,
				"ShowInSearch" => 0,
			)
		);
	}

	private function getProductGroups($numberOfGroups = 7) {
		$numberOfGroups--;
		$array = array();
		for($j = 1; $j < $numberOfGroups; $j++) {
			$parentCode = $this->randomName();
			if(($j == 1) && ($numberOfGroups > 3) ) {
				$children1 = $this->getProductGroups($numberOfGroups);
				$children2 = $this->getProductGroups($numberOfGroups);
				$children = array_merge($children1, $children2);
			}
			else {
				$children = $this->getProducts($parentCode);
			}
			$levelOfProductsToShow = rand(0, 5);
			$defaultSortOrder = ($j % 2) ? "title" : "featured";
			$array[$j] = array(
				"ClassName" => "ProductGroup",
				"URLSegment" => "product-group-".$parentCode,
				"Title" => "Product Group ".$parentCode,
				"MenuTitle" => "Product group ".$parentCode,
				"Content" => "<p>Please review our products below.</p>",
				"LevelOfProductsToShow" => $levelOfProductsToShow,
				"NumberOfProductsPerPage" => $levelOfProductsToShow + 5,
				"DefaultSortOrder" => $defaultSortOrder,
				"Content" =>
					'<p>
						This product group page has the following characteristics:
					</p>
					<ul>
						<li>level of products to show: '.$levelOfProductsToShow.'</li>
						<li>number of products per page: '.($levelOfProductsToShow + 5).'</li>
						<li>default sort order: '.$defaultSortOrder.'</li>
					</ul>
					',
				"ProductsAlsoInOthersGroups" => "Boolean",
				"Children" =>  $children,
			);
		}
		return $array;
	}

	private function getProducts($parentCode) {
		$endPoint = rand(3, 15);
		for($j = 0; $j < $endPoint; $j++) {
			$i = rand(1, 500);
			$price = $i < 475 ? $i + ($i / 100) : 0;
			$weight = ($i % 3) ? 0 : 1.234;
			$model = ($i % 4) ? "" : "model $i";
			$quantifier = ($i % 5) ? "" : "per month";
			$featured = ($i % 5) ? FALSE : TRUE;
			$allowPurchase = ($i % 9) ? TRUE : FALSE;
			$numberSold = $i;
			$array[$i] = array(
				"ClassName" => "Product",
				"URLSegment" => "product-$parentCode-$i",
				"Title" => "Product $parentCode $i",
				"MenuTitle" => "Product $i",
				"Content" => "<p>
					Description for Product $i ... It has the following hidden characteristics:
				<p>
				<ul>
					<li>Weight: ".$weight.";</li>
					<li>Model: ".$model.";</li>
					<li>Featured Product: ".$featured.";</li>
					<li>Quantifier (e.g. per dozen): ".$quantifier.";</li>
					<li>Purchase Allowed: ".$allowPurchase.";</li>
					<li>Number Sold: ".$numberSold.";</li>
				</ul>",
				"Price" => $price,
				"InternalItemID" => "AAA".$i,
				'Weight' => $weight,
				'Model' => $model,
				'Quantifier' => $quantifier,
				"FeaturedProduct" => $featured,
				'AllowPurchase' => $allowPurchase ,
				'NumberSold' => $numberSold
			);
		}
		return $array;
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
		$products = DataObject::get("Product", "ClassName = 'Product'", "RAND()", "", "2");
		$this->addExamplePages("products with variations (size, colour, etc...)", $products);
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
				$this->addToTitle($product, "with variation", false);
				$product->writeToStage('Stage');
				$product->Publish('Stage', 'Live');
				$product->Status = "Published";
				$product->flushCache();
				$descriptionOptions = array("", "Per Month", "", "", "Per Year", "This option has limited warranty");
				if(!DataObject::get("ProductVariation", "ProductID  = ".$product->ID)) {
					foreach($variationCombos as $variationCombo) {
						$productVariation = new ProductVariation();
						$productVariation->ProductID = $product->ID;
						$productVariation->Price = $product->Price * 2;
						$productVariation->Description = $descriptionOptions[rand(0, 5)];
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


	private function AddMyModifiers() {
		$this->addExamplePages("Delivery charges", DataObject::get_one("CheckoutPage"));
		if(!DataObject::get_one("PickUpOrDeliveryModifierOptions", "Code = 'pickup'")) {
			$obj = new PickUpOrDeliveryModifierOptions();
			$obj->IsDefault = 1;
			$obj->Code = "pickup";
			$obj->Name = "pickup from Store";
			$obj->MinimumDeliveryCharge = 0;
			$obj->MaximumDeliveryCharge = 0;
			$obj->MinimumOrderAmountForZeroRate= 0;
			$obj->WeightMultiplier= 0;
			$obj->WeightUnit= 0;
			$obj->Percentage= 0;
			$obj->FixedCost= 3;
			$obj->Sort= 0;
			$obj->write();
		}
		$obj = null;
		if(!DataObject::get_one("PickUpOrDeliveryModifierOptions", "Code = 'delivery'")) {
			$obj = new PickUpOrDeliveryModifierOptions();
			$obj->IsDefault = 0;
			$obj->Code = "delivery";
			$obj->Name = "delivery via Courier Bob";
			$obj->MinimumDeliveryCharge = 0;
			$obj->MaximumDeliveryCharge = 0;
			$obj->MinimumOrderAmountForZeroRate= 0;
			$obj->WeightMultiplier= 0;
			$obj->WeightUnit= 0;
			$obj->Percentage= 0;
			$obj->FixedCost= 13;
			$obj->Sort= 100;
			$obj->write();
		}
		$obj = null;
		if(!DataObject::get_one("GSTTaxModifierOptions", "Code = 'GST'")) {
			$obj = new GSTTaxModifierOptions();
			$obj->CountryCode = "NZ";
			$obj->Code = "GST";
			$obj->Name = "Goods and Services Tax";
			$obj->InclusiveOrExclusive = "Inclusive";
			$obj->Rate = 0.15;
			$obj->PriceSuffix = "";
			$obj->AppliesToAllCountries = false;
			$obj->write();
		}
		$obj = null;
		if(!DataObject::get_one("GSTTaxModifierOptions", "Code = 'ACT'")) {
			$obj = new GSTTaxModifierOptions();
			$obj->CountryCode = "AU";
			$obj->Code = "ACT";
			$obj->Name = "Australian Carbon Tax";
			$obj->InclusiveOrExclusive = "Exclusive";
			$obj->Rate = 0.05;
			$obj->PriceSuffix = "";
			$obj->AppliesToAllCountries = false;
			$obj->write();
		}
	}

	function createTags(){
		$products = DataObject::get("Product", "ClassName = 'Product'", "RAND()", "", "2");
		$this->addExamplePages("Product Tags", $products);
		foreach($products as $product){
			$idArray[] = $product->ID;
			$titleArray[] = $product->MenuTitle;
			$this->addToTitle($product, "with tag", true);
		}
		$page = DataObject::get_one("Page", "\"URLSegment\" = 'tag-explanation'");
		$t1 = new EcommerceProductTag();
		$t1->Title = "TAG 1";
		$t1->ExplanationPageID = $page->ID;
		$t1->Explanation = "explains Tag 1";
		$t1->write();
		$existingProducts = $t1->Products();
		$existingProducts->addMany($idArray);
		DB::alteration_message("Creating tag: ".$t1->Title." for ".implode(",", $titleArray), "created");
		$t2 = new EcommerceProductTag();
		$t2->Title = "TAG 2";
		$t2->ExplanationPageID = $page->ID;
		$t2->Explanation = "explains Tag 2";
		$t2->write();
		$existingProducts = $t2->Products();
		$existingProducts->addMany($idArray);
		DB::alteration_message("Creating tag: ".$t2->Title." for ".implode(",", $titleArray), "created");
	}

	function createRecommendedProducts(){
		$products = DataObject::get("Product", "ClassName = 'Product'", "RAND()", "", "2");
		$this->addExamplePages("Products with recommended <i>additions</i>.", $products);
		foreach($products as $product){
			$idArrayProducts[] = $product->ID;
			$this->addToTitle($product, "with recommendations", true);
		}
		$recommendedProducts = DataObject::get("Product", " SiteTree.ID NOT IN (".implode(",",$idArrayProducts).") AND ClassName = 'Product'", "RAND()", "", "3");
		foreach($recommendedProducts as $product){
			$idArrayRecommendedProducts[] = $product->ID;
		}
		foreach($products as $product) {
			$existingRecommendations = $product->EcommerceRecommendedProducts();
			$existingRecommendations->addMany($idArrayRecommendedProducts);
			DB::alteration_message("adding recommendations for: ".$product->Title." (".implode(",",$idArrayRecommendedProducts).")", "created");
		}
	}


	function addStock(){
		$extension = "";
		if(Versioned::current_stage() == "Live") {
			$extension = "_Live";
		}
		$products = DataObject::get("Product", "SiteTree{$extension}.ClassName = 'Product' AND ProductVariation.ID IS NULL", "RAND()", "LEFT JOIN ProductVariation ON ProductVariation.ProductID = Product{$extension}.ID", "0, 3");
		$i = 0;
		$idArray = array();
		foreach($products as $product) {
			$i++;
			$idArray[$product->ID] = $product->ID;
			if($i == 1) {
				$this->addExamplePages("Minimum quantity per order", $product);
				$product->MinQuantity = 12;
				$this->addToTitle($product, "minimum per order of 12", true);
				DB::alteration_message("adding minimum quantity for: ".$product->Title, "created");
			}
			if($i == 2) {
				$this->addExamplePages("Maxiumum quantity per order", $product);
				$product->MaxQuantity = 12;
				$this->addToTitle($product, "maximum per order of 12", true);
				DB::alteration_message("adding maximum quantity for: ".$product->Title, "created");
			}
			if($i == 3) {
				$this->addExamplePages("Limited stock product", $product);
				$product->setActualQuantity(1);
				$product->UnlimitedStock = 0;
				$this->addToTitle($product, "limited stock", true);
				DB::alteration_message("adding limited stock for: ".$product->Title, "created");
			}
		}
		$variations = DataObject::get("ProductVariation", "ClassName = 'ProductVariation'", "RAND()", "", "0, 3");
		$i = 0;
		foreach($variations as $variation) {
			$i++;
			if($i == 1) {
				$variation->MaxQuantity = 12;
				$variation->Description = " - min quantity per order 12!";
				$variation->write();
				$variation->writeToStage("Stage");
				$this->addExamplePages("Minimum quantity product variation (colour / size / etc... option)", $variation->Product());
				DB::alteration_message("adding limited quantity for: ".$variation->Title, "created");
			}
			if($i == 2) {
				$variation->MaxQuantity = 12;
				$variation->Description = " - max quantity per order 12!";
				$variation->write();
				$variation->writeToStage("Stage");
				$this->addExamplePages("Maximum quantity product variation (colour / size / etc... option)", $variation->Product());
				DB::alteration_message("adding limited quantity for: ".$variation->Title, "created");
			}
			if($i == 3) {
				$variation->setActualQuantity(1);
				$variation->Description = " - limited stock!";
				$variation->UnlimitedStock = 0;
				$variation->write();
				$variation->writeToStage("Stage");
				$this->addExamplePages("Limited stockproduct variation (colour / size / etc... option)", $variation->Product());
				DB::alteration_message("adding limited quantity for: ".$variation->Title, "created");
			}
		}
	}


	protected function addSpecialPrice(){
		$group = new Group();
		$group->Title = "Discount Customers";
		$group->Code = "discountcustomers";
		$group->ParentID = EcommerceRole::get_customer_group()->ID;
		$group->write();
		$member = new Member();
		$member->FirstName = 'Bob';
		$member->Surname = 'Jones';
		$member->Email = 'bob@silverstripe-ecommerce.com';
		$member->Password = 'test123';
		$member->write();
		$member->Groups()->add($group);
		$products = DataObject::get("Product", "ClassName = 'Product'", "RAND()", "", "2");
		$this->addExamplePages("Special price for particular customers", $products);
		$i = 0;
		foreach($products as $product) {
			$i++;
			$complexObjectPrice = new ComplexPriceObject();
			if($i == 1) {
				$complexObjectPrice->Price = $product->Price - 1.5;
			}
			elseif($i == 2) {
				$complexObjectPrice->Percentage = 10;
				$complexObjectPrice->Reduction = 2.5;
			}
			else {
				$complexObjectPrice->Price = $product->Price - 1.5;
				$complexObjectPrice->Percentage = 10;
				$complexObjectPrice->Reduction = 2.5;
			}
			$complexObjectPrice->From = date("Y-m-d h:n:s", strtotime("now"));
			$complexObjectPrice->Until = date("Y-m-d h:n:s", strtotime("next year"));
			$complexObjectPrice->ProductID = $product->ID;
			$complexObjectPrice->write();
			$complexObjectPrice->Groups()->add($group);
			$product->Content = "<p><a href=\"Security/login/?BackURL=".$product->Link()."\">Login</a> as bob@silverstripe-ecommerce.com, password: test123 to get a special price. You can then <a href=\"Security/logout/?BackURL=".$product->Link()."\">log out</a> again to see the original price.</p>";
			$this->addToTitle($product, "member price", true);
		}
		$variations = DataObject::get("ProductVariation", "ClassName = 'ProductVariation'", "RAND()", "", "2");
		$i = 0;
		foreach($variations as $variation) {
			$i++;
			$complexObjectPrice = new ComplexPriceObject();
			if($i == 1) {
				$complexObjectPrice->Price = $product->Price - 1.5;
			}
			elseif($i == 2) {
				$complexObjectPrice->Percentage = 10;
				$complexObjectPrice->Reduction = 2.5;
			}
			else {
				$complexObjectPrice->Price = $product->Price - 1.5;
				$complexObjectPrice->Percentage = 10;
				$complexObjectPrice->Reduction = 2.5;
			}
			$complexObjectPrice->Price = $variation->Price - 1.5;
			$complexObjectPrice->From = date("Y-m-d h:n:s", strtotime("now"));
			$complexObjectPrice->Until = date("Y-m-d h:n:s", strtotime("next year"));
			$complexObjectPrice->ProductVariationID = $variation->ID;
			$complexObjectPrice->write();
			$complexObjectPrice->Groups()->add($group);
			$product = $variation->Product();
			$this->addExamplePages("Special price for particular customers for product variations", $product);
			$product->Content = "<p><a href=\"Security/login/?BackURL=".$product->Link()."\">Login</a> as bob@jones.com, password: test123 to get a special price</p>";
			$this->addToTitle($product, "member price", true);
		}
	}

	protected function productsInManyGroups(){
		$products = DataObject::get("Product", "\"ClassName\" = 'Product'", "RAND()", null, 2);
		$productGroups = DataObject::get("ProductGroup", "\"ClassName\" = 'ProductGroup'", "RAND()", null, 3);
		foreach($products as $product) {
			$groups = $product->ProductGroups();
			foreach($productGroups as $productGroup) {
				$groups->add($productGroup);
			}
			$this->addExamplePages("Product shown in more than one Product Group", $product);
		}
	}

	protected function createShopAdmin() {
		$member = new Member();
		$member->FirstName = 'Shop';
		$member->Surname = 'Admin';
		$member->Email = 'shop@silverstripe-ecommerce.com';
		$member->Password = 'test123';
		$member->write();
		$group = EcommerceRole::get_admin_group();
		$member->Groups()->add($group);
	}

	protected function collateExamplePages(){
		$this->addExamplePages("Checkout Page", DataObject::get_one("CheckoutPage"));
		$this->addExamplePages("Delivery options (add product to cart first)", DataObject::get_one("CheckoutPage"));
		$this->addExamplePages("Taxes (NZ based GST - add product to cart first", DataObject::get_one("CheckoutPage"));
		$this->addExamplePages("Order Confirmation Page", DataObject::get_one("OrderConfirmationPage"));
		$this->addExamplePages("Cart Page (review cart without checkout)", DataObject::get_one("CartPage"));
		$this->addExamplePages("Account Page", DataObject::get_one("AccountPage"));
		$this->addExamplePages("Donation page", DataObject::get_one("AnyPriceProductPage"));
		$this->addExamplePages("Quick Add Page", DataObject::get_one("AddToCartPage"));
		$this->addExamplePages("Corporate Account Order Page", DataObject::get_one("AddUpProductsToOrderPage"));
		$this->addExamplePages("Products with zero price", DataObject::get_one("Product", "\"Price\" = 0 AND ClassName = 'Product'"));
		$this->addExamplePages("Products that can not be sold", DataObject::get_one("Product", "\"AllowPurchase\" = 0 AND ClassName = 'Product'"));
		$html = '<h2>examples shown on this demo site</h2><ul>';
		foreach($this->examplePages as $examplePages) {
			$html .= '<li><span class="exampleTitle">'.$examplePages["Title"].'</span>'.$examplePages["List"].'</li>';
		}
		$html .= '</ul>
		<p>At any time you can <a href="/shoppingcart/clear/">reset the shopping cart</a> to start a new order.</p>
		<p>Also, do not hesitate to review <a href="http://sunny.svnrepository.com/svn/sunny-side-up-general/ecommerce_test/mysite/_config.php">the settings for this project</a> - as shown in the mysite/_config.php file.</p>
		<h2>Modules included are:</h2>
		<ul>
			<li>ecommerce_alsorecommended</li>
			<li>ecommerce_alternativeproductgroup</li>
			<li>ecommerce_anypriceproduct</li>
			<li>ecommerce_brandbrowsing</li>
			<li>ecommerce_complex_pricing</li>
			<li>ecommerce_corporate_account</li>
			<li>ecommerce_coupon</li>
			<li>ecommerce_custom_order_number</li>
			<li>ecommerce_delivery</li>
			<li>ecommerce_digitalproducts</li>
			<li>ecommerce_dimensions</li>
			<li>ecommerce_discount_coupon</li>
			<li>ecommerce_googleanalytics</li>
			<li>ecommerce_modifier_example</li>
			<li>ecommerce_modifiers</li>
			<li>ecommerce_multicurrency</li>
			<li>ecommerce_nl_connectivity</li>
			<li>ecommerce_nz_connectivity</li>
			<li>ecommerce_product_tags</li>
			<li>ecommerce_product_variation</li>
			<li>ecommerce_quick_add</li>
			<li>ecommerce_repeatorders</li>
			<li>ecommerce_reports</li>
			<li>ecommerce_shipping_fastwaynz</li>
			<li>ecommerce_statistics</li>
			<li>ecommerce_stockcontrol</li>
			<li>ecommerce_tax</li>
			<li>ecommerce_vote</li>
			<li>payment_directcredit</li>
			<li>payment_dps</li>
			<li>payment_epaydk</li>
			<li>payment_instore</li>
			<li>payment_ogone</li>
			<li>payment_paymate</li>
			<li>payment_paymentexpress</li>
			<li>payment_paypal</li>
			<li>payment_paystation_hosted</li>
			<li>payment_securatech</li>
		</ul>
		';
		$homePage = DataObject::get_one("Page", "URLSegment = 'home'");
		$homePage->Content .= $html;
		$homePage->writeToStage('Stage');
		$homePage->Publish('Stage', 'Live');
		$homePage->Status = "Published";
		$homePage->flushCache();
	}


	//====================================== ASSISTING FUNCTIONS =========================

	private function MakePage($fields, $parentPage = null) {
		$page = DataObject::get_one("SiteTree", "\"URLSegment\" = '".$fields["URLSegment"]."'");
		if(!$page) {
			if(isset($fields["ClassName"])) {
				$className = $fields["ClassName"];
			}
			else {
				$className = "Page";
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

	private function UpdateMyRecords() {
		$array = array(
			array("T" => "SiteConfig", "F" => "Title", "V" => "Silverstripe Ecommerce Demo", "W" => ""),
			array("T" => "SiteConfig", "F" => "Tagline", "V" => "Built by Sunny Side Up", "W" => ""),
			array("T" => "SiteConfig", "F" => "Theme", "V" => "main", "W" => ""),
			array("T" => "SiteConfig", "F" => "PostalCodeURL", "V" => "http://tools.nzpost.co.nz/tools/address-postcode-finder/APLT2008.aspx", "W" => ""),
			array("T" => "SiteConfig", "F" => "PostalCodeLabel", "V" => "Check Code", "W" => ""),
			array("T" => "SiteConfig", "F" => "ReceiptEmail", "V" => "demo-orders@sunnysideup.co.nz", "W" => ""),
			array("T" => "SiteConfig", "F" => "ReceiptEmail", "V" => "demo-orders@sunnysideup.co.nz", "W" => ""),
			array("T" => "CartPage", "F" => "CheckoutPageID", "V" => DataObject::get_one("CheckoutPage", "ClassName = 'CheckoutPage'")->ID, "W" => ""),
			array("T" => "CartPage", "F" => "ContinuePageID", "V" => DataObject::get_one("ProductGroup")->ID, "W" => ""),
			array("T" => "CartPage_Live", "F" => "CheckoutPageID", "V" => DataObject::get_one("CheckoutPage", "ClassName = 'CheckoutPage'")->ID, "W" => ""),
			array("T" => "CartPage_Live", "F" => "ContinuePageID", "V" => DataObject::get_one("ProductGroup")->ID, "W" => ""),
		);
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
	}


	private function addToTitle($page, $toAdd, $save = false) {
		$title = $page->Title;
		$newTitle = $title . " - ". $toAdd;
		$page->Title = $newTitle;
		$page->MenuTitle = $newTitle;
		$page->MetaTitle = $newTitle;
		if($save) {
			$page->writeToStage('Stage');
			$page->Publish('Stage', 'Live');
			$page->Status = "Published";
			$page->flushCache();
		}
	}

	private function addExamplePages($name, $pages) {
		$html = '<ul>';
		if($pages instanceof DataObjectSet) {
			foreach($pages as $page) {
				$html .= '<li><a href="'.$page->Link().'">'.$page->Title.'</a></li>';
			}
		}
		elseif($pages instanceof SiteTree) {
			$html .= '<li><a href="'.$pages->Link().'">'.$pages->Title.'</a></li>';
		}
		$html .= '</ul>';
		$i = count($this->examplePages);
		$this->examplePages[$i]["Title"] = $name;
		$this->examplePages[$i]["List"] = $html;
	}

	private $fruitArray = array("Apple", "Crabapple", "Hawthorn", "Pear", "Apricot", "Peach", "Nectarines", "Plum", "Cherry", "Blackberry", "Raspberry", "Mulberry", "Strawberry", "Cranberry", "Blueberry", "Barberry", "Currant", "Gooseberry", "Elderberry", "Grapes", "Grapefruit", "Kiwi fruit", "Rhubarb", "Pawpaw", "Melon", "Watermelon", "Figs", "Dates", "Olive", "Jujube", "Pomegranate", "Lemon", "Lime", "Key Lime", "Mandarin", "Orange", "Sweet Lime", "Tangerine", "Avocado", "Guava", "Kumquat", "Lychee", "Passion Fruit", "Tomato", "Banana", "Gourd", "Cashew Fruit", "Cacao", "Coconut", "Custard Apple", "Jackfruit", "Mango", "Neem", "Okra", "Pineapple", "Vanilla", "Carrot");

	private function randomName() {
		return array_pop($this->fruitArray);
	}

}



