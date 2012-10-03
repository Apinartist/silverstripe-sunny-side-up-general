<?php

class DefaultRecordsForEcommerce extends DataObject {

	protected $examplePages = array(
		0 => Array("Title" => "Basics", "List" => array()),
		1 => Array("Title" => "Products and Product Groups", "List" => array()),
		2 => Array("Title" => "Checkout Options", "List" => array()),
		3 => Array("Title" => "Stock Control", "List" => array()),
		4 => Array("Title" => "Pricing", "List" => array()),
		5 => Array("Title" => "Other", "List" => array())
	);

	function requireDefaultRecords() {

		parent::requireDefaultRecords();

		$this->checkreset();

		$this->runEcommerceDefaults();

		$this->createImages();

		$this->CreatePages();

		$this->AddVariations();

		$this->AddComboProducts();

		$this->AddMyModifiers();

		$this->UpdateMyRecords();

		$this->createCurrencies();

		$this->createTags();

		$this->createRecommendedProducts();

		$this->addStock();

		$this->addSpecialPrice();

		$this->productsInManyGroups();

		$this->productsWithSpecialTax();

		$this->createShopAdmin();

		$this->createOrder();

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

				<h2>What is this?</h2>
				<p>
					This is a demo site for the Silverstripe E-commerce, developed by <a href=\"http://www.sunnysideup.co.nz\">Sunny Side Up</a>.
					It showcases the <a href=\"http://code.google.com/p/silverstripe-ecommerce\">Silverstripe e-commerce project</a>.
					It <a href=\"/home/features/\">features</a> all the core e-commerce functionality as well as a selection of <i>add-on</i> modules - such as tax and delivery.
					For the <i>theme</i>, or visual presentation, we have used the default Sunny Side Up theme.
					Of course, the idea is that you add your own theme if you are using e-commerce for any of your projects.
					Please use the menu below to browse this site.
					<strong>
						This site is for testing only so try anything you like.
						Any feedback is appreciated and, where practicable, will be implemented.
					</strong>
					Please feel free to starting <a href=\"/shop/\">shopping</a>.
				</p>

				<h2>Testing</h2>
				<p>
					This site can reset itself so please go ahead and try whatever you want.
					At any time you can <a href=\"/shoppingcart/clear/\">reset the shopping cart</a> to start a new order.
					Also, make sure to <a href=\"admin/shop/\">open the cms</a> (see login details above).
					If you have some feedback then please <a href=\"/about-us/\">contact us</a>.
					<a href=\"http://www.sunnysideup.co.nz\">Sunny Side Up</a> is also available for <a href=\"/about-us/\">paid support</a>.
				</p>
				<h3 id=\"LoginDetails\">Log in details</h3>
				<p>
					You can <a href=\"admin/shop/\">log-in</a> as follows: shop@silverstripe-ecommerce.com / test123.
				</p>

				<h2>For developers</h2>
				<p>
					You can install an identical copy of this site (including test data) on your own development server by checking out this SVN repository: <br />
					<a href=\"http://sunny.svnrepository.com/svn/sunny-side-up-general/ecommerce_test/\">http://sunny.svnrepository.com/svn/sunny-side-up-general/ecommerce_test/</a>.
				</p>
				<h3>downloads, svn and git</h3>
				<p>
					Please log in (see <a href=\"#LoginDetails\">login details</a> above) to the <a href=\"home/downloads/\">the Git / SVN / Download page </a> to fork / checkout / download the source code.
				<p>
					This demo is based on the <a href=\"https://silverstripe-ecommerce.googlecode.com/svn/trunk/\">trunk</a> of e-commerce, as well as a bunch of complementary modules.
				</p>
				<h3>themes</h3>
				<p>
					This website showcases the Sunny Side Up theme.
					We are also implementing the Silverstripe 3.0 simple theme.
					Use the links below to switch themes:
				</p>
				<ul>
					<li><a href=\"/home/settheme/main/\">View Sunny Side Up Theme</a></li>
					<li><a href=\"/home/settheme/simple/\">View Simple Theme</a></li>
				</ul>
				<p>
					<strong>
						If you would like to contribute a theme to e-commerce then we would be delighted.
						Please contact us for more information.
					</strong>
				</p>
				<h3>data model</h3>
				<p>Please review <a href=\"/ecommerce/docs/en/SSUE-commerceDataModel.png\">our latest data model</a></p>
				<h3>Customising your own copy of e-commerce</h3>
				<p>We have created a PDF that shows a flow-chart of steps you take to <a href=\"https://silverstripe-ecommerce.googlecode.com/svn/trunk/docs/en/CustomisingEcommerce.pdf\">customise your own e-commerce module</a>.</p>

				<h2>Thank you</h2>
				<p>
					Thank you <a href=\"http://www.silverstripe.org\">Silverstripe Community</a> for the Silverstripe foundation.
					A big <i>kia ora</i> also to all the developers who contributed to <a href=\"http://code.google.com/p/silverstripe-ecommerce/\">the Silverstripe E-commerce Project</a>, especially <a href=\"http://www.burnbright.co.nz/\">Jeremy</a>.
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
					),
					array(
						"URLSegment" => "features",
						"MetaTitle" => "Silverstripe E-Commerce Features",
						"Title" => "Silverstripe E-Commerce Features",
						"MenuTitle" => "Features",
						"ShowInMenus" => true,
						"ShowInSearch" => true,
						"Content" => "",
					),
					array(
						"URLSegment" => "others",
						"MetaTitle" => "Other Silverstripe E-Commerce Applications",
						"Title" => "Other Silverstripe E-Commerce Applications",
						"MenuTitle" => "Also See",
						"ShowInMenus" => true,
						"ShowInSearch" => true,
						"Content" => "

									<p>Below are three other Silverstripe E-commerce Solutions (information updated 1 Oct 2012)</p>
									<table style=\"width: 100%\">
										<tbody>
											<tr>
												<th style=\"width: 10%;\" scope=\"col\">&nbsp;</th>
												<th style=\"width: 30%;\" scope=\"col\">
													<a href=\"\"><a href=\"https://github.com/burnbright/silverstripe-shop\">Shop</a>
												</th>
												<th style=\"width: 30%;\" scope=\"col\">
													<a href=\"https://github.com/frankmullenger/silverstripe-swipestripe\">Swipestripe</a>
												</th>
												<th style=\"width: 30%;\" scope=\"col\">
													<a href=\"https://bitbucket.org/silvercart/\">Silvercart</a>
												</th>
											</tr>

											<tr>
												<th scope=\"row\">Lead developer</th>
												<td>Jeremy</td>
												<td>Frank</td>
												<td>Roland</td>
											</tr>

											<tr>
												<th scope=\"row\">Strength (among others)</th>
												<td>
													<ul>
														<li>Easily extendable</li>
														<li>Excellent documentation</li>
														<li>Innovative Concepts</li>
														<li>Silverstripe Best Practice</li>
														<li>Tests</li>
													</ul>
												</td>
												<td>
													<ul>
														<li>Extensive Tests</li>
														<li>3.0 ready</li>
														<li>Reliable</li>
													</ul>
												</td>
												<td>
													<ul>
														<li>Most polished of all e-commerce applications</li>
														<li>Internationalisation</li>
													</ul>
												</td>
											</tr>

											<tr>
												<th scope=\"row\">Cost</th>
												<td>Free</td>
												<td>USD250ish + module cost</td>
												<td>Free</td>
											</tr>

											<tr>
												<th scope=\"row\">Demo</th>
												<td><a href=\"http://demo.ss-shop.org/\">demo.ss-shop.org/</a></td>
												<td>
													<a href=\"http://demo.swipestripe.com/\">demo.swipestripe.com/</a>
													<a href=\"http://ss3.swipestripe.com/\">ss3.swipestripe.com/</a>
												</td>
												<td><a href=\"http://demo.trysilvercart.com\">demo.trysilvercart.com</a></td>
											</tr>

											<tr>
												<th scope=\"row\">Features</th>
												<td>
													<ul>
														<li>Product catalog that can be stored in a categorized  tree hierarchy, or flat via model admin</li>
														<li>Account page for members to view past orders, and pay for incomplete orders</li>
														<li>Simplistic code base, intended to scale</li>
														<li>Integrates with payment module</li>
														<li>Product variations/options</li>
														<li>Address book</li>
														<li>Single page checkout</li>
														<li>35 unit tests running on TravisCI</li>
														<li>Self contained installer</li>
														<li>Discount coupon codes</li>
														<li>Zoned shipping system</li>
														<li>Product enquiry form</li>
														<li>Product searching</li>
														<li>Google analytics recording</li>
														<li>Bootstrap theme</li>
													</ul>
												</td>
												<td>
													<p>Aiming for a small and simple core codebase which is flexible enough to be extended easily in order to add features like product galleries, shipping, tax, coupon codes etc. Currently have a small selection of extensions (http://swipestripe.com/products/extensions/) that can be purchased seperately and provide common features for shopping carts such as:</p>
													<ul>
														<li>Product gallery with fancy box</li>
														<li>Xero integrations</li>
														<li>Virtual (downloadable) products</li>
														<li>Region based shipping</li>
														<li>Weight and region based shipping</li>
														<li>Region based tax rates</li>
														<li>Featured products</li>
														<li>Courier codes for orders</li>
													</ul>
												</td>
												<td>
													<ul>
														<li>Master and slave products (product variations can be grouped while the variations can be independently maintained)</li>
														<li>A products stock status can be displayed as quantity or with a traffic light as a symbol.</li>
														<li>Products can be provided with multiple images or a gallery.</li>
														<li>Unlimited quantity of products and product groups.</li>
														<li>Products can be mirrored in several product groups.</li>
														<li>Each product can have multiple files assigned (PDF,DOC,XLS, etc.)</li>
														<li>Drag and drop reordering</li>
														<li>manufacturer logo and link maintainable</li>
														<li>various possibilities to filter products</li>
														<li>Class diagram and API documentation</li>
														<li>Integrated update mechanism</li>
														<li>Import and export via CSV</li>
														<li>User account area with order history</li>
														<li>B2B or B2C</li>
													</ul>
												</td>
											</tr>

											<tr>
												<th scope=\"row\">Planned</th>
												<td>
													<ul>
														<li>SubShops - allowing members to create their own semi-branded store, and set profits</li>
														<li>SilverStripe 3 upgrade</li>
														<li>US/International tax support</li>
														<li>Multi-currency</li>
														<li>Stock control</li>
													</ul>
												</td>
												<td>
													<ul>
														<li>Using new SilverStripe 3.0 compatible payment module</li>
														<li>Multiple currency</li>
														<li>Documentation</li>
														<li>More comprehensive reporting</li>
														<li>Further 3rd party integrations</li>
													</ul>
												</td>
												<td>
													<ul>
														<li>Amazon module</li>
														<li>Groupon module</li>
														<li>Saferpay payment module</li>
														<li>Sofort banking module</li>
													</ul>
												</td>
											</tr>

											<tr>
												<th scope=\"row\">Support</th>
												<td>
													<ul>
														<li>Online: <a href=\"http://demo.ss-shop.org/docs/developer/en\">docs</a>, <a href=\"http://api.ss-shop.org/\">api</a></li>
														<li>mysite.com/dev/shop - tools and tasks for development</li>
														<li>Moderate forum support (shared with ecommerce module)</li>
														<li>Ecommerce mailing list (shared with ecommerce module)</li>
													</ul>
												</td>
												<td>
													<ul>
														<li><a href=\"http://docs.swipestripe.com/\">API documentation</a></li>
														<li><a href=\"http://swipestripe.com/docs\">Installation documentation</a></li>
													</ul>
												</td>
												<td>
													SilverCart uses the YAML CSS framework and customhtmlform module which takes care of SilverStripes HTML limitations for forms.
													<a href=\"http://www.silvercart.org/forum/\">support forum</a></td>
											</tr>

										</tbody>
									</table>


						",
					),
					array(
						"ClassName" => "DownloadPage",
						"URLSegment" => "downloads-git-svn",
						"Title" => "downloads / git / svn",
						"MenuTitle" => "downloads / git / svn",
						"ShowInMenus" => true,
						"ShowInSearch" => true,
						"Content" => "
						<p>
							Below is a full list of source code used on this site.
							Downloads are only available to users who are logged in as shop administrator (you can find out how you can log in on the homepage).
						</p>",
					)
				)
			),
			array(
				"ClassName" => "ProductGroup",
				"URLSegment" => "shop",
				"Title" => "Shop",
				"MenuTitle" => "Shop",
				"Content" => "
					<p>For each product group you can set the products that should be included.&nbsp; The options are:</p>
					<ul>
					<li>None, just like this page.</li>
					<li>Direct Children only</li>
					<li>Child and Grand Child Products</li>
					<li>etc...</li>
					<li>All products on the site.&nbsp;</li>
					</ul>
					<p>You can also setup a \"custom\" list of products by using one of the Product Group extension pages.</p>
				",
				"Children" => array(
					array(
						"ClassName" => "ProductGroup",
						"URLSegment" => "browse-products",
						"Title" => "Browse All Products",
						"MenuTitle" => "Browse Products",
						"ShowInMenus" => 1,
						"ShowInSearch" => 1,
						"NumberOfProductsPerPage" => 70,
						"Content" => "<p>In this section you can browse all the products...</p>",
						"Children" => $this->getProductGroups()
					),
					array(
						"ClassName" => "Page",
						"URLSegment" => "alternative-views",
						"Title" => "Alternative Views of Product and Product Groups",
						"MenuTitle" => "Alternative Views",
						"ShowInMenus" => 1,
						"ShowInSearch" => 1,
						"Content" => "<p>In this section we present a number of alternative ways to view products and product groups. Please use the sidebar menu to browse through the options.</p>",
						"Children" => array(
							array(
								"ClassName" => "ProductGroupWithTags",
								"URLSegment" => "shop-by-tag",
								"Title" => "Shop by Tag",
								"MenuTitle" => "Shop by Tag",
								"Content" => "<p>Please use the tags to find the products you are after.</p>",
							),
							array(
								"ClassName" => "AddUpProductsToOrderPage",
								"URLSegment" => "add-up-order",
								"Title" => "Add Up Order",
								"MenuTitle" => "Add Up Order",
								"ShowInMenus" => 1,
								"ShowInSearch" => 1,
								"Content" => "
									<p>
										Choose your products below and continue through to the checkout...
										This page helps customers who want to put a <i>name</i> or <i>identifier</i> with each order item - for example a group of people purchasing together.
									</p>",
							),
							array(
								"ClassName" => "PriceListPage",
								"URLSegment" => "price-list",
								"Title" => "Price List",
								"MenuTitle" => "Price List",
								"ShowInMenus" => 1,
								"ShowInSearch" => 1,
								"LevelOfProductsToShow" => -1,
								"NumberOfLevelsToHide" => 99,
								"Content" => "<p>please review all our prices below...</p>"
							),
							array(
								"ClassName" => "AddToCartPage",
								"URLSegment" => "quick-add",
								"Title" => "Quick Add",
								"MenuTitle" => "Quick Add",
								"ShowInMenus" => 1,
								"ShowInSearch" => 1,
								"Content" => "<p>Choose your products below and continue through to the checkout...</p>",
							)
						)
					),
					array(
						"ClassName" => "AnyPriceProductPage",
						"URLSegment" => "donation",
						"Title" => "Make a donation (example!)",
						"MenuTitle" => "Donate (example!)",
						"Content" => "<p>You can try out our <i>Any Price Product</i> below, by entering a value you want to <i>Donate</i>. This page can be used to allow customers to make payments such as donations or wherever they can determine the price.  You can send them a link to this page with an amount like this: <i>/donate/setamount/11.11</i></p>",
						"Price" => 0,
						"Featured" => 0,
						"InternalItemID" => "DONATE"
					)
				)
			),
			array(
				"ClassName" => "CheckoutPage",
				"URLSegment" => "checkout",
				"Title" => "Checkout",
				"MenuTitle" => "Checkout",
				"Content" => "
					<p>
						For further information on our terms of trade, please visit ....
						To test the tax, set your country to New Zealand (GST inclusive) or Australia (exclusive tax).
					</p>
				",
				"InvitationToCompleteOrder" => "<p>Please complete your details below to finalise your order.</p>",
				"CurrentOrderLinkLabel" => "View current order",
				"SaveOrderLinkLable" => "Save order",
				"NoItemsInOrderMessage" => "<p>There are no items in your current order</p>",
				"NonExistingOrderMessage" => "<p>We are sorry, but we can not find this order.</p>",
				"LoginToOrderLinkLabel" => "Log in now to checkout order",
				"HasCheckoutSteps" => 1,
				"ContinueShoppingLabel" => "Continue Shopping",
				"CurrentOrderLinkLabel" => "View Current Order",
				"LoadOrderLinkLabel" => "Load order",
				"DeleteOrderLinkLabel" => "Delete order",
				"Children" => array(
					array(
						"ClassName" => "OrderConfirmationPage",
						"URLSegment" => "confirmorder",
						"Title" => "Order Confirmation",
						"MenuTitle" => "Order Confirmation",
						"ShowInMenus" => 0,
						"ShowInSearch" => 0,
						"Content" => "<p>Please review your order below.</p>",
						"CurrentOrderLinkLabel" => "View current order",
						"SaveOrderLinkLable" => "Save order",
						"NoItemsInOrderMessage" => "<p>There are no items in your current order</p>",
						"NonExistingOrderMessage" => "<p>We are sorry, but we can not find this order.</p>",
						"LoginToOrderLinkLabel" => "Log in now to checkout order",
						"ContinueShoppingLabel" => "Continue Shopping",
						"CurrentOrderLinkLabel" => "View Current Order",
						"LoadOrderLinkLabel" => "Load order",
						"DeleteOrderLinkLabel" => "Delete order",
					),
					array(
						"ClassName" => "AccountPage",
						"URLSegment" => "my account",
						"Title" => "My Account",
						"MenuTitle" => "My Account",
						"ShowInMenus" => 1,
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
						<li><a href=\"http://www.photowarehouse.co.nz\">photowarehouse</a> - a large retailer site with over four thousand products - contracted by <a href=\"http://www.msodesign.com\">MSO Design</a> to develop back-end only</li>
						<li><a href=\"http://www.kprcatering.co.nz\">kpr catering</a> - a catering company - contracted by <a href=\"http://www.msodesign.com\">MSO Design</a> to develop back-end only</li>
						<li><a href=\"http://www.resumetemplates-usa.com\">resume templates usa</a> - a US based company selling curriculum vitae (resume) templates</li>
						<li><a href=\"http://www.kahuvet.co.nz\">kahu vet</a> - an NZ based company selling products to Vets - contracted by <a href=\"http://www.studiolief.co.nz\">studio lief</a> to build back-end</li>
						<li><a href=\"http://www.regalsalmon.co.nz\">Regal Salmon</a> - an NZ based company selling salmon - contracted by three-sixty media to build back-end</li>
						<li><a href=\"http://www.cathaven.com.au\">Cat Haven</a> - an AU based organisation helping cats - contracted by <a href=\"http://www.designcity.com.au\"design city</a> to build back-end</li>
						<li><a href=\"http://www.avelonshop.me\">Avelon Fashion</a> - an NL based fasion design company</li>
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
			$levelOfProductsToShow = (rand(1, 3));
			$filterNumber = (rand(0, 4));
			switch ($filterNumber % 4) {
				case 0:
					$filter = "inherit";
					break;
				case 1:
					$filter = "";
					break;
				case 2:
					$filter = "featuredonly";
					break;
				case 3:
					$filter = "nonfeaturedonly";
					break;
				default:
					$filter = "";
			}
			$styleNumber = (rand(0, 4));
			switch ($styleNumber % 4) {
				case 0:
					$style = "inherit";
					$numberOfProductsPerPage = 0;
					$sortOrder = "inherit";
					break;
				case 1:
					$style = "Short";
					$numberOfProductsPerPage = 50;
					$sortOrder = "price";
					break;
				case 2:
					$style = "";
					$numberOfProductsPerPage = 9;
					$sortOrder = "";
					break;
				case 3:
					$style = "MoreDetail";
					$numberOfProductsPerPage = 5;
					$sortOrder = "title";
					break;
			}
			$array[$j] = array(
				"ClassName" => "ProductGroup",
				"URLSegment" => "product-group-".$parentCode,
				"Title" => "Product Group ".$parentCode,
				"MenuTitle" => "Product group ".$parentCode,
				"LevelOfProductsToShow" => $levelOfProductsToShow,
				"NumberOfProductsPerPage" => $numberOfProductsPerPage,
				"DefaultSortOrder" => $sortOrder,
				"DefaultFilter" => $filter,
				"DisplayStyle" => $style,
				"ImageID" => $this->getRandomImageID(),
				"Content" =>
					'<p>
						'.$this->lipsum().'
						<br /><br />For testing purposes - the following characteristics were added to this product group:
					</p>
					<ul>
						<li>level of products to show: '.$levelOfProductsToShow.'</li>
						<li>number of products per page: '.($levelOfProductsToShow + 5).'</li>
						<li>sort order: '.($sortOrder ? $sortOrder : "[default]").'</li>
						<li>filter: '.($filter ? $filter : "[default]").'</li>
						<li>display style: '.($style ? $style : "[default]").'</li>
					</ul>
					',
				"Children" =>  $children,
			);
		}
		return $array;
	}

	private function getProducts($parentCode) {
		$endPoint = rand(10, 20);
		for($j = 0; $j < $endPoint; $j++) {
			$i = rand(1, 500);
			$q = rand(1, 500); $price = $q < 475 ? $q + ($q / 100) : 0;
			$q = rand(1, 500); $weight = ($q % 3) ? 0 : 1.234;
			$q = rand(1, 500); $model = ($q % 4) ? "" : "model $i";
			$q = rand(1, 500); $featured = ($q % 9) ? "NO" : "YES";
			$q = rand(1, 500); $quantifier = ($q % 7) ? "" : "per month";
			$q = rand(1, 500); $allowPurchase = ($q % 17) ? "YES" : "NO";
			$imageID = $this->getRandomImageID();
			DB::query("Update \"File\" SET \"ClassName\" = 'Product_Image' WHERE ID = ".$imageID);
			$numberSold = $i;
			$array[$i] = array(
				"ClassName" => "Product",
				"ImageID" => $imageID,
				"URLSegment" => "product-$parentCode-$i",
				"Title" => "Product $parentCode $i",
				"MenuTitle" => "Product $i",
				"Content" => "<p>
					Description for Product $i ...
					".$this->lipsum()."
					<br /><br />For testing purposes - the following characteristics were added to this product:
				<p>
				<ul>
					<li>weight: <i>".($weight == 0 ?"[none]" : $weight." grams")."</i> </li>
					<li>model: <i>".($model ? $model : "[none]")."</i></li>
					<li>featured: <i>$featured</i></li>
					<li>quantifier: <i>".($quantifier ? $quantifier : "[none]")."</i></li>
					<li>allow purchase: <i>$allowPurchase</i></li>
					<li>number sold: <i>".$numberSold."</i></li>
				</ul>",
				"Price" => $price,
				"InternalItemID" => "AAA".$i,
				"Weight" => $weight ? "1.234" : 0,
				"Model" => $model ? "model $i" : "",
				"Quantifier" => $quantifier,
				"FeaturedProduct" => $featured == "YES"  ? 1 : 0,
				"AllowPurchase" => $allowPurchase == "YES" ? 1 : 0,
				"NumberSold" => $numberSold
			);
		}
		return $array;
	}

	private function AddComboProducts(){
		$pages = new DataObjectSet();
		$productGroups = DataObject::get("ProductGroup", "ParentID > 0", "\"Sort\" DESC", null, 3);
		foreach($productGroups as $productGroup) {
			$i = rand(1, 500);
			$imageID = $this->getRandomImageID();
			DB::query("Update \"File\" SET \"ClassName\" = 'Product_Image' WHERE ID = ".$imageID);
			$numberSold = $i;
			$fields = array(
				"ClassName" => "CombinationProduct",
				"ImageID" => $imageID,
				"URLSegment" => "combo-product-$i",
				"Title" => "Combination Product $i",
				"MenuTitle" => "Combi Product $i",
				"ParentID" => $productGroup->ID,
				"Content" => "<p>
					This is a combination Product.
					Aenean tincidunt nisl id ante pretium quis ornare libero varius. Nam cursus, mi quis dignissim laoreet, mauris turpis molestie ligula, et luctus urna nibh et ligula. Morbi in arcu ante, sit amet fermentum lacus. Cras elit lacus, feugiat sit amet faucibus quis, condimentum a leo. Donec molestie lacinia nisl a ullamcorper.
					For testing purposes - the following characteristics were added to this product:
				<p>
				<ul>
					<li>featured: <i>YES</i></li>
					<li>allow purchase: <i>YES</i></li>
					<li>number sold: <i>".$numberSold."</i></li>
				</ul>",
				"InternalItemID" => "combo".$i,
				"FeaturedProduct" => 1,
				"AllowPurchase" => 1,
				"NumberSold" => $numberSold,
				"NewPrice" => 10
			);
			$this->MakePage($fields);
			$page = DataObject::get_one("CombinationProduct", "ParentID = ".$productGroup->ID);
			$includedProducts = $page->IncludedProducts();
			$products = DataObject::get("Product", "\"AllowPurchase\" = 1", null, null, 3);
			foreach($products as $product) {
				$includedProducts->add($product);
			}
			$page->writeToStage('Stage');
			$page->Publish('Stage', 'Live');
			$page->Status = "Published";
			$pages->push($page);
		}
		$this->addExamplePages(1, "Combination Products", $pages);
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
				$redObject->RGBCode = "ff0000";
				$redObject->ContrastRGBCode = "BFC1C1";
				$redObject->TypeID = $colourObject->ID;
				$redObject->Sort = 100;
				$redObject->write();
			}
			$blueObject = DataObject::get_one("ProductAttributeValue", "\"Value\" = 'blue'");
			if(!$blueObject) {
				$blueObject = new ProductAttributeValue();
				$blueObject->Value = "blue";
				$blueObject->RGBCode = "0000ff";
				$blueObject->ContrastRGBCode = "BFC1C1";
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
		$this->addExamplePages(1, "products with variations (size, colour, etc...)", $products);
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
				$product->Content .= "<p>On this page you can see two example of how you customers can add variations to their products (form / table)... In a real-life shop you would probably choose one or the other.</p>";
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
						$productVariation->ImageID = rand(0, 1) ? 0 : $this->getRandomImageID();
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
		if(!DataObject::get_one("GSTTaxModifierOptions", "Code = 'ADD'")) {
			$obj = new GSTTaxModifierOptions();
			$obj->CountryCode = "";
			$obj->Code = "ADD";
			$obj->Name = "Additional Tax";
			$obj->InclusiveOrExclusive = "Inclusive";
			$obj->Rate = 0.65;
			$obj->PriceSuffix = "";
			$obj->DoesNotApplyToAllProducts = true;
			$obj->AppliesToAllCountries = true;
			$obj->write();
		}
		$obj = null;
		if(!DataObject::get_one("DiscountCouponOption", "\"Code\" = 'AAA'")) {
			$obj = new DiscountCouponOption();
			$obj->Title = "Example Coupon";
			$obj->Code = "AAA";
			$obj->StartDate = date("Y-m-d", strtotime("Yesterday"));
			$obj->EndDate = date("Y-m-d", strtotime("Next Year"));
			$obj->DiscountAbsolute = 10;
			$obj->DiscountPercentage = 7.5;
			$obj->CanOnlyBeUsedOnce = false;
			$obj->write();
		}
	}

	function createCurrencies(){
		$obj = new EcommerceCurrency();
		$obj->Code = "USD";
		$obj->Name = "US Dollars";
		$obj->InUse = 1;
		$obj->write();
		DB::alteration_message("created USD currency", "created");
		$obj = DataObject::get_one("EcommerceCurrency", "\"Code\" = 'EUR'");
		if(!$obj) {
			$obj = new EcommerceCurrency();
		}
		$obj->Code = "EUR";
		$obj->Name = "Euros";
		$obj->InUse = 1;
		$obj->write();
		DB::alteration_message("created EUR currency", "created");
		$obj = new EcommerceCurrency();
		$obj->Code = "AUD";
		$obj->Name = "Australian Dollar";
		$obj->InUse = 0;
		$obj->write();
		DB::alteration_message("created AUD currency", "created");
	}

	function createTags(){
		$products = DataObject::get("Product", "ClassName = 'Product'", "RAND()", "", "4");
		$this->addExamplePages(1, "Product Tags", $products);
		foreach($products as $pos => $product){
			$idArray[$pos] = $product->ID;
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
		$existingProducts->addMany(array($idArray[0],$idArray[1]));
		DB::alteration_message("Creating tag: ".$t1->Title." for ".implode(",", $titleArray), "created");
		$t2 = new EcommerceProductTag();
		$t2->Title = "TAG 2";
		$t2->ExplanationPageID = $page->ID;
		$t2->Explanation = "explains Tag 2";
		$t2->write();
		$existingProducts = $t2->Products();
		$existingProducts->addMany(array($idArray[2],$idArray[3]));
		DB::alteration_message("Creating tag: ".$t2->Title." for ".implode(",", $titleArray), "created");
		$productGroupWithTags = DataObject::get_one("ProductGroupWithTags");
		$existingTags = $productGroupWithTags->EcommerceProductTags();
		$existingTags->addMany(array($t1->ID, $t2->ID));
	}

	function createRecommendedProducts(){
		$products = DataObject::get("Product", "ClassName = 'Product'", "RAND()", "", "2");
		$this->addExamplePages(1, "Products with recommended <i>additions</i>.", $products);
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
				$this->addExamplePages(3, "Minimum quantity per order", $product);
				$product->MinQuantity = 12;
				$this->addToTitle($product, "minimum per order of 12", true);
				DB::alteration_message("adding minimum quantity for: ".$product->Title, "created");
			}
			if($i == 2) {
				$this->addExamplePages(3, "Maxiumum quantity per order", $product);
				$product->MaxQuantity = 12;
				$this->addToTitle($product, "maximum per order of 12", true);
				DB::alteration_message("adding maximum quantity for: ".$product->Title, "created");
			}
			if($i == 3) {
				$this->addExamplePages(3, "Limited stock product", $product);
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
				$this->addExamplePages(3, "Minimum quantity product variation (colour / size / etc... option)", $variation->Product());
				DB::alteration_message("adding limited quantity for: ".$variation->Title, "created");
			}
			if($i == 2) {
				$variation->MaxQuantity = 12;
				$variation->Description = " - max quantity per order 12!";
				$variation->write();
				$variation->writeToStage("Stage");
				$this->addExamplePages(3, "Maximum quantity product variation (colour / size / etc... option)", $variation->Product());
				DB::alteration_message("adding limited quantity for: ".$variation->Title, "created");
			}
			if($i == 3) {
				$variation->setActualQuantity(1);
				$variation->Description = " - limited stock!";
				$variation->UnlimitedStock = 0;
				$variation->write();
				$variation->writeToStage("Stage");
				$this->addExamplePages(3, "Limited stock for product variation (colour / size / etc... option)", $variation->Product());
				DB::alteration_message("adding limited quantity for: ".$variation->Title, "created");
			}
		}
	}


	protected function addSpecialPrice(){

		$task = new CreateEcommerceMemberGroups();
		$task->run(false);
		$customerGroup = EcommerceRole::get_customer_group();
		if(!$customerGroup) {
			die("could not create customer group");
		}
		$group = new Group();
		$group->Title = "Discount Customers";
		$group->Code = "discountcustomers";
		$group->ParentID = $customerGroup->ID;
		$group->write();
		$member = new Member();
		$member->FirstName = 'Bob';
		$member->Surname = 'Jones';
		$member->Email = 'bob@silverstripe-ecommerce.com';
		$member->Password = 'test123';
		$member->write();
		$member->Groups()->add($group);
		$products = DataObject::get("Product", "ClassName = 'Product'", "RAND()", "", "2");
		$this->addExamplePages(4, "Special price for particular customers", $products);
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
			$this->addExamplePages(4, "Special price for particular customers for product variations $i", $product);
			$product->Content = "<p><a href=\"Security/login/?BackURL=".$product->Link()."\">Login</a> as bob@jones.com, password: test123 to get a special price</p>";
			$this->addToTitle($product, "member price", true);
		}
	}

	protected function productsWithSpecialTax(){
		$products = DataObject::get("Product", "\"ClassName\" = 'Product'", "RAND()", null, 2);
		$taxToAdd = DataObject::get_one("GSTTaxModifierOptions", "\"Code\" = 'ADD'");
		if($taxToAdd && $products) {
			foreach($products as $product) {
				$additionalTax = $product->AdditionalTax();
				$additionalTax->addMany(array($taxToAdd->ID));
			}
			$this->addExamplePages(2, "product with additional taxes (add to cart to see this feature in action)", $products);
		}
		$products = DataObject::get("AnyPriceProductPage");
		$allStandardTaxes = DataObject::get("GSTTaxModifierOptions", "\"DoesNotApplyToAllProducts\" = 0");
		if($allStandardTaxes && $products) {
			foreach($products as $product) {
				$excludedTax = $product->ExcludedFrom();
				foreach($allStandardTaxes as $taxToExclude) {
					$excludedTax->addMany(array($taxToExclude->ID));
				}
			}
			$this->addExamplePages(2, "product without taxes (add to cart to see this feature in action)", $products);
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
		}
		$this->addExamplePages(1, "Product shown in more than one Product Group", $products);
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

	protected function createOrder(){
		$order = new Order();
		$order->UseShippingAddress = true;
		$order->CustomerOrderNote = "THIS IS AN AUTO-GENERATED ORDER";
		$order->write();

		$member = new Member();
		$member->FirstName = 'Tom';
		$member->Surname = 'Cruize';
		$member->Email = 'tom@silverstripe-ecommerce.com';
		$member->Password = 'test123';
		$member->write();
		$order->MemberID = $member->ID;

		$billingAddress = new BillingAddress();
		$billingAddress->Prefix = "Dr";
		$billingAddress->FirstName = "Tom";
		$billingAddress->Surname = "Cruize";
		$billingAddress->Address = "Lamp Drive";
		$billingAddress->Address2 = "Linux Mountain";
		$billingAddress->City = "Apache Town";
		$billingAddress->PostalCode = "555";
		$billingAddress->Country = "NZ";
		$billingAddress->Phone = "555 5555555";
		$billingAddress->MobilePhone = "444 44444";
		$billingAddress->Email = "tom@silverstripe-ecommerce.com";
		$billingAddress->write();
		$order->BillingAddressID = $billingAddress->ID;

		$shippingAddress = new ShippingAddress();
		$shippingAddress->ShippingPrefix = "Dr";
		$shippingAddress->ShippingFirstName = "Tom";
		$shippingAddress->ShippingSurname = "Cruize";
		$shippingAddress->ShippingAddress = "Lamp Drive";
		$shippingAddress->ShippingAddress2 = "Linux Mountain";
		$shippingAddress->ShippingCity = "Apache Town";
		$shippingAddress->ShippingPostalCode = "555";
		$shippingAddress->ShippingCountry = "NZ";
		$shippingAddress->ShippingPhone = "555 5555555";
		$shippingAddress->ShippingMobilePhone = "444 44444";
		$shippingAddress->write();
		$order->ShippingAddressID = $shippingAddress->ID;

		//get a random product
		$extension = "";
		if(Versioned::current_stage() == "Live") {
			$extension = "_Live";
		}
		$count = 0;
		$noProductYet = true;
		$triedArray = array(0 => 0);
		while($noProductYet && $count < 50) {
			$product = DataObject::get_one("Product", "\"ClassName\" = 'Product' AND \"Product{$extension}\".\"ID\" NOT IN (".implode(",", $triedArray).") AND Price > 0");
			if($product) {
				if($product->canPurchase()) {
					$noProductYet = false;
				}
				else {
					$triedArray[] = $product->ID;
				}
			}
			$count++;
		}

		//adding product order item
		$item = new Product_OrderItem();
		$item->addBuyableToOrderItem($product, 7);
		$item->OrderID = $order->ID;
		$item->write();
		//final save
		$order->write();
		$order->tryToFinaliseOrder();

	}

	protected function collateExamplePages(){
		$this->addExamplePages(0, "Checkout page", DataObject::get_one("CheckoutPage"));
		$this->addExamplePages(0, "Order Confirmation page", DataObject::get_one("OrderConfirmationPage"));
		$this->addExamplePages(0, "Cart page (review cart without checkout)", DataObject::get_one("CartPage", "ClassName = 'CartPage'"));
		$this->addExamplePages(0, "Account page", DataObject::get_one("AccountPage"));
		$this->addExamplePages(1, "Donation page", DataObject::get_one("AnyPriceProductPage"));
		$this->addExamplePages(1, "Products that can not be sold", DataObject::get_one("Product", "\"AllowPurchase\" = 0 AND ClassName = 'Product'"));
		$this->addExamplePages(1, "Product group with short product display template", DataObject::get_one("ProductGroup", "\"DisplayStyle\" = 'Short'"));
		$this->addExamplePages(1, "Product group with medium length product display template", DataObject::get_one("ProductGroup", "\"DisplayStyle\" = ''"));
		$this->addExamplePages(1, "Product group with more detail product display template", DataObject::get_one("ProductGroup", "\"DisplayStyle\" = 'MoreDetail'"));
		$this->addExamplePages(1, "Quick Add page", DataObject::get_one("AddToCartPage"));
		$this->addExamplePages(1, "Shop by Tag page ", DataObject::get_one("ProductGroupWithTags"));
		$this->addExamplePages(2, "Delivery options (add product to cart first)", DataObject::get_one("CheckoutPage"));
		$this->addExamplePages(2, "Taxes (NZ based GST - add product to cart first)", DataObject::get_one("CheckoutPage"));
		$this->addExamplePages(2, "Discount Coupon (try <i>AAA</i>)", DataObject::get_one("CheckoutPage"));
		$this->addExamplePages(4, "Products with zero price", DataObject::get_one("Product", "\"Price\" = 0 AND ClassName = 'Product'"));
		$this->addExamplePages(5, "Corporate Account Order page", DataObject::get_one("AddUpProductsToOrderPage"));
		$html = '<h2>examples shown on this demo site</h2>';
		foreach($this->examplePages as $key => $exampleGroups) {
			$html .= "<h3>".$exampleGroups["Title"]."</h3><ul>";
			foreach($exampleGroups["List"] as $examplePages) {
				$html .= '<li><span class="exampleTitle">'.$examplePages["Title"].'</span>'.$examplePages["List"].'</li>';
			}
			$html .= "</ul>";
		}
		$html .= '
		<h2>adding an order programatically</h2>
		<p>As part of this demo, we automatically add an order - as follows:</p>
		<pre>
			$order = new Order();
			$order-&gt;UseShippingAddress = true;
			$order-&gt;CustomerOrderNote = "THIS IS AN AUTO-GENERATED ORDER";
			$order-&gt;write();

			$member = new Member();
			$member-&gt;FirstName = \'Tom\';
			$member-&gt;Surname = \'Cruize\';
			$member-&gt;Email = \'tom@silverstripe-ecommerce.com\';
			$member-&gt;Password = \'test123\';
			$member-&gt;write();
			$order-&gt;MemberID = $member-&gt;ID;

			$billingAddress = new BillingAddress();
			$billingAddress-&gt;Prefix = "Dr";
			$billingAddress-&gt;FirstName = "Tom";
			$billingAddress-&gt;Surname = "Cruize";
			$billingAddress-&gt;Address = "Lamp Drive";
			$billingAddress-&gt;Address2 = "Linux Mountain";
			$billingAddress-&gt;City = "Apache Town";
			$billingAddress-&gt;PostalCode = "555";
			$billingAddress-&gt;Country = "NZ";
			$billingAddress-&gt;Phone = "555 5555555";
			$billingAddress-&gt;MobilePhone = "444 44444";
			$billingAddress-&gt;Email = "tom@silverstripe-ecommerce.com";
			$billingAddress-&gt;write();
			$order-&gt;BillingAddressID = $billingAddress-&gt;ID;

			$shippingAddress = new ShippingAddress();
			$shippingAddress-&gt;ShippingPrefix = "Dr";
			$shippingAddress-&gt;ShippingFirstName = "Tom";
			$shippingAddress-&gt;ShippingSurname = "Cruize";
			$shippingAddress-&gt;ShippingAddress = "Lamp Drive";
			$shippingAddress-&gt;ShippingAddress2 = "Linux Mountain";
			$shippingAddress-&gt;ShippingCity = "Apache Town";
			$shippingAddress-&gt;ShippingPostalCode = "555";
			$shippingAddress-&gt;ShippingCountry = "NZ";
			$shippingAddress-&gt;ShippingPhone = "555 5555555";
			$shippingAddress-&gt;ShippingMobilePhone = "444 44444";
			$shippingAddress-&gt;write();
			$order-&gt;ShippingAddressID = $shippingAddress-&gt;ID;

			//get a random product
			$product = DataObject::get_one("Product");
			$triedArray = array($product-&gt;ID);
			$extension = "";
			if(Versioned::current_stage() == "Live") {
				$extension = "_Live";
			}
			$count = 0;
			while($product && !$product-&gt;canPurchase() && $count &lt; 50) {
				$product = DataObject::get_one("Product", "\"ClassName\" = \'Product\' AND \"Product{$extension}\".\"ID\" NOT IN (".implode(",", $triedArray).")");
				if($product) {
					$triedArray[] = $product-&gt;ID;
				}
				$count++;
			}

			//adding product order item
			$item = new Product_OrderItem();
			$item-&gt;addBuyableToOrderItem($product, 7);
			$item-&gt;OrderID = $order-&gt;ID;
			$item-&gt;write();
			//final save
			$order-&gt;write();
			$order-&gt;tryToFinaliseOrder();
		</pre>
		<h2>API Access</h2>
		<p>
			E-commerce allows you to access its model using the built-in Silverstripe API.
			This is great for communication with third party applications.
			Access examples are listed below:
		</p>
		<ul>
			<li><a href="/api/v1/Order/">view all orders</a></li>
			<li><a href="/api/v1/Order/1">view order with ID = 1</a></li>
		</ul>
		<p>
			For more information on the restful server API, you can visit the <a href="http://api.silverstripe.org/2.4/sapphire/api/RestfulServer.html">help documents</a> on this topic.
			In the help documents you can read that potentially orders could also be created through third-party gateways.
		</p>
		';
		$featuresPage = DataObject::get_one("Page", "URLSegment = 'features'");
		$featuresPage->Content .= $html;
		$featuresPage->writeToStage('Stage');
		$featuresPage->Publish('Stage', 'Live');
		$featuresPage->Status = "Published";
		$featuresPage->flushCache();
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
		if($page->Title  && !$page->MetaTitle) {
			$page->MetaTitle = $page->Title;
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
			array("T" => "SiteConfig", "F" => "CopyrightNotice", "V" => "This demo (not the underlying modules) are &copy; Sunny Side Up Ltd", "W" => ""),
			array("T" => "SiteConfig", "F" => "Theme", "V" => "main", "W" => ""),
			array("T" => "EcommerceDBConfig", "F" => "ShopClosed", "V" => "0", "W" => ""),
			array("T" => "EcommerceDBConfig", "F" => "ShopPricesAreTaxExclusive", "V" => "0", "W" => ""),
			array("T" => "EcommerceDBConfig", "F" => "ShopPhysicalAddress", "V" => "<address>The Shop<br />1 main street<br />Coolville 123<br />Landistan</address>", "W" => ""),
			array("T" => "EcommerceDBConfig", "F" => "ReceiptEmail", "V" => "\"Silverstrip E-comerce Demo\" <sales@silverstripe-ecommerce.com>", "W" => ""),
			array("T" => "EcommerceDBConfig", "F" => "PostalCodeURL", "V" => "http://tools.nzpost.co.nz/tools/address-postcode-finder/APLT2008.aspx", "W" => ""),
			array("T" => "EcommerceDBConfig", "F" => "PostalCodeLabel", "V" => "Check Code", "W" => ""),
			array("T" => "EcommerceDBConfig", "F" => "NumberOfProductsPerPage", "V" => "5", "W" => ""),
			array("T" => "EcommerceDBConfig", "F" => "OnlyShowProductsThatCanBePurchased", "V" => "0", "W" => ""),
			array("T" => "EcommerceDBConfig", "F" => "ProductsHaveWeight", "V" => "1", "W" => ""),
			array("T" => "EcommerceDBConfig", "F" => "ProductsHaveModelNames", "V" => "1", "W" => ""),
			array("T" => "EcommerceDBConfig", "F" => "ProductsHaveQuantifiers", "V" => "1", "W" => ""),
			array("T" => "EcommerceDBConfig", "F" => "ProductsAlsoInOtherGroups", "V" => "1", "W" => ""),
			array("T" => "EcommerceDBConfig", "F" => "EmailLogoID", "V" => $this->getRandomImageID(), "W" => ""),
			array("T" => "EcommerceDBConfig", "F" => "DefaultProductImageID", "V" => $this->getRandomImageID(), "W" => ""),

			array("T" => "CartPage", "F" => "ContinuePageID", "V" => DataObject::get_one("ProductGroup")->ID, "W" => ""),
			array("T" => "CartPage_Live", "F" => "ContinuePageID", "V" => DataObject::get_one("ProductGroup")->ID, "W" => ""),
		);
		foreach($array as $innerArray) {
			if(isset($innerArray["W"]) && $innerArray["W"]) {				$innerArray["W"] = " WHERE ".$innerArray["W"];
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

	private function addExamplePages($group, $name, $pages) {
		$html = '<ul>';
		if($pages instanceof DataObjectSet) {
			foreach($pages as $page) {
				$html .= '<li><a href="'.$page->Link().'">'.$page->Title.'</a></li>';
			}
		}
		elseif($pages instanceof SiteTree) {
			$html .= '<li><a href="'.$pages->Link().'">'.$pages->Title.'</a></li>';
		}
		else{
			$html .= '<li>not available yet</li>';
		}
		$html .= '</ul>';
		$i = count($this->examplePages[$group]["List"]);
		$this->examplePages[$group]["List"][$i]["Title"] = $name;
		$this->examplePages[$group]["List"][$i]["List"] = $html;
	}

	private $fruitArray = array("Apple", "Crabapple", "Hawthorn", "Pear", "Apricot", "Peach", "Nectarines", "Plum", "Cherry", "Blackberry", "Raspberry", "Mulberry", "Strawberry", "Cranberry", "Blueberry", "Barberry", "Currant", "Gooseberry", "Elderberry", "Grapes", "Grapefruit", "Kiwi fruit", "Rhubarb", "Pawpaw", "Melon", "Watermelon", "Figs", "Dates", "Olive", "Jujube", "Pomegranate", "Lemon", "Lime", "Key Lime", "Mandarin", "Orange", "Sweet Lime", "Tangerine", "Avocado", "Guava", "Kumquat", "Lychee", "Passion Fruit", "Tomato", "Banana", "Gourd", "Cashew Fruit", "Cacao", "Coconut", "Custard Apple", "Jackfruit", "Mango", "Neem", "Okra", "Pineapple", "Vanilla", "Carrot");

	private function randomName() {
		return array_pop($this->fruitArray);
	}

	private $imageArray = array();

	private function getRandomImageID(){
		if(!count($this->imageArray)) {
			$folder = Folder::findOrMake("randomimages");
			$images = DataObject::get("Image", "ParentID = ".$folder->ID, "RAND()");
			if($images) {
				$this->imageArray = $images->map("ID", "ID");
			}
			else {
				$this->imageArray = array(0 => 0);
			}
		}
		return array_pop($this->imageArray);
	}

	private function runEcommerceDefaults() {
		$request = true;
		$buildTask = new CreateEcommerceMemberGroups($request);
		$buildTask->run($request);
		$obj = new EcommerceDBConfig();
		$obj->Title = "Test Configuration";
		$obj->UseThisOne = 1;
		$obj->write();
	}

	private function createImages($width = 170, $height = 120) {
		$folder = Folder::findOrMake("randomimages");
		$folder->syncChildren();
		if($folder->Children()->count() < 250) {
			for($i = 0; $i < 10; $i++) {
				$r = mt_rand(0, 255);
				$g = mt_rand(0, 255);
				$b = mt_rand(0, 255);
				$im = @imagecreate($width, $height) or die("Cannot Initialize new GD image stream");
				$background_color = imagecolorallocate($im, $r, $g, $b);
				$baseFolderPath = Director::baseFolder();
				$fileName = $baseFolderPath."/assets/randomimages/img_".sprintf("%03d", $r)."_".sprintf("%03d", $g)."_".sprintf("%03d", $b).".png";
				if(!file_exists($fileName)) {
					imagepng($im, $fileName);
				}
				imagedestroy($im);
			}
		}
	}

	private function lipsum(){
		$str = "
			Suspendisse auctor eros non metus semper vel mattis enim auctor.
			Maecenas aliquam feugiat lectus, eu pretium neque imperdiet sed.
			Nullam semper velit quis velit condimentum ut hendrerit felis blandit.
			Phasellus quis massa vel dolor consectetur ornare vel in justo.
			Vivamus vel sem lacus, eget auctor nibh.
			Quisque a massa sit amet odio malesuada placerat.
			Cras ut nunc leo, eget bibendum diam.
			//
			Morbi feugiat leo ac mauris posuere dictum.
			Integer venenatis augue sit amet lectus auctor auctor.
			Integer rhoncus velit molestie sem vehicula mattis.
			Duis condimentum nunc a arcu ornare vitae fermentum ipsum egestas.
			Ut eget tellus ligula, id convallis dui.
			Pellentesque ultricies metus at nisi hendrerit ut fringilla sem fringilla.
			Donec sit amet sem risus, ac rutrum dolor.
			Maecenas nec elit quam, eget laoreet sem.
			Donec egestas dui et nibh pharetra in ullamcorper risus aliquet.
			Fusce vitae nibh quis erat cursus egestas non non turpis.
			Pellentesque ac nunc sed nisl sollicitudin gravida.
			Nulla sollicitudin velit consectetur lacus commodo lacinia non non mauris.
			Quisque dignissim ante et odio dictum sodales euismod eu tellus.
			Nunc rhoncus nibh vel augue posuere nec suscipit leo sagittis.
			//
			Sed et lorem turpis, eu hendrerit nunc.
			Vivamus varius faucibus orci, a gravida massa varius non.
			Quisque nec sem sed purus pretium malesuada.
			Suspendisse eget quam at justo tempus imperdiet eget nec purus.
			Nullam vel lacus sit amet sem volutpat rutrum vel at dui.
			Nulla scelerisque lorem a mi commodo vestibulum.
			Maecenas quis dui sed mauris mattis mollis.
			Morbi ac tortor id sapien ornare tincidunt ut quis enim.
			Fusce id nisi vulputate augue dictum volutpat molestie nec metus.
			Nunc ultricies iaculis ante, sed pellentesque nulla fringilla ut.
			Duis non nibh in tellus lobortis dapibus.
			//
			Vestibulum at est eu purus cursus semper.
			Ut eu neque et lectus auctor tempus sed vel libero.
			Donec in lorem at dolor facilisis vestibulum.
			Vivamus fermentum felis nec nisl accumsan faucibus.
			Maecenas at tellus ut nulla congue tempor vitae in nisi.
			Mauris vitae nulla in libero mollis semper.
			Cras ac eros lorem, in volutpat odio.
			Nullam tristique egestas turpis, accumsan fermentum turpis feugiat ac.
			Proin adipiscing turpis non nunc faucibus quis facilisis mauris fermentum.
			Praesent rhoncus leo sed libero cursus pellentesque.
			Cras luctus urna in sem scelerisque vestibulum scelerisque velit hendrerit.
			Maecenas eget diam eu quam congue pulvinar eu non neque.
			//
			Quisque consequat tellus quis eros facilisis lacinia.
			Morbi quis nibh eget elit vehicula vestibulum.
			Proin rutrum vestibulum dui, viverra rutrum nisl tempor non.
			Praesent quis nibh nec risus gravida sodales ac eu neque.
			Integer non leo non lectus convallis semper.
			Integer viverra sapien eu dui vehicula ultrices.
			Cras fermentum vulputate justo, ut pretium magna dignissim sed.
			Nam tristique tellus vel lacus condimentum ac lobortis tortor dapibus.
			Maecenas quis quam sapien, sed ullamcorper ligula.
			Sed eu urna quis libero porttitor euismod.
			//
			Proin eget enim quis diam rhoncus faucibus a in lorem.
			Phasellus vulputate volutpat tortor, consectetur convallis nisl mollis a.
			Vestibulum at turpis quis lacus commodo sollicitudin.
			Phasellus placerat molestie purus, sed elementum leo congue et.
			Sed volutpat massa id lacus sollicitudin vel vehicula magna imperdiet.
			Suspendisse sit amet dui lacus, id scelerisque sem.
			//
			Vestibulum bibendum nulla ac odio rutrum aliquet.
			Fusce pretium felis nec justo semper laoreet.
			Aenean porta turpis at metus dapibus non rutrum magna ullamcorper.
			Vivamus non orci risus, id commodo enim.
			Sed adipiscing felis a dui ultrices ornare.
			Praesent in risus nisl, id luctus sapien.
			//
			Nunc a risus sapien, a aliquet tellus.
			Cras non nisl non lectus volutpat elementum eu ac nisl.
			Donec mattis odio nec ante mollis mattis.
			//
			Integer auctor interdum nulla, ac semper velit aliquet sit amet.
			Pellentesque egestas ultrices metus, vehicula malesuada sem viverra a.
			Morbi blandit metus eu mi egestas imperdiet.
			Vivamus volutpat turpis et nibh ornare vitae blandit tellus egestas.
			Integer molestie dolor ut orci semper at luctus urna pharetra.
			Morbi feugiat dolor eget velit dignissim vel scelerisque ligula aliquam.
			Cras in lacus magna, sed gravida est.
			Duis vulputate eleifend erat, pellentesque eleifend purus cursus eget.
			Vivamus blandit egestas sem, sed gravida velit posuere sit amet.
			//
			Nam malesuada sollicitudin erat, eget egestas risus condimentum quis.
			In dictum sapien ut velit tincidunt lobortis.
			Nam nec lectus non leo vulputate rutrum et at erat.
			Nam varius tristique turpis, eget euismod nisl lacinia vitae.
			Maecenas euismod lorem sed mi dictum aliquam.
			Phasellus at dui vitae est eleifend dictum a vitae lorem.
			Fusce quis sapien et enim sollicitudin interdum sit amet imperdiet ligula.
			Proin egestas sem vel sapien pharetra at varius est malesuada.
			Quisque et lorem in dolor blandit suscipit.
			//
			Nullam congue est eu lectus laoreet euismod.
			Nulla eu sapien ligula, semper pellentesque neque.

			Nulla ut enim dui, nec pharetra leo.
			//
			Sed placerat ante vel enim convallis consectetur.
			Sed quis justo auctor arcu sagittis laoreet.
			Curabitur eu risus a eros ornare ullamcorper.
			Pellentesque posuere ante quis diam placerat sit amet eleifend purus volutpat.
			Aenean id tellus et nisl luctus rhoncus.
			Cras ut velit a diam lobortis tincidunt.
			Mauris sit amet libero ut magna lacinia suscipit.
			Nam ut risus nec mauris faucibus posuere pellentesque vitae est.
			Maecenas dignissim bibendum sapien, id viverra urna sollicitudin id.
			Aliquam non quam ac nulla convallis viverra quis eget nisi.
			Sed rhoncus lectus non nisi consequat semper fermentum non ipsum.
			Aenean tempus mauris ut lectus dictum eu eleifend purus ultrices.
			Etiam id erat nunc, sed mattis mauris.
			Suspendisse et metus orci, eget tempus urna.
			Ut eu ipsum a quam convallis scelerisque.
			//
			Nullam a nulla vitae mauris eleifend tristique.
			Pellentesque nec leo non nisl posuere cursus nec sit amet nisl.
			Aenean sit amet sapien ut ipsum porta condimentum.
			Nullam ullamcorper elementum augue, ut suscipit dolor adipiscing sed.
			Maecenas congue leo a mi faucibus congue.
			Sed imperdiet pharetra lorem, eget rhoncus sapien feugiat vitae.
			Nullam mollis nulla ut risus faucibus imperdiet.
			//
			Proin eu orci in nunc euismod fermentum et ac dolor.
			Duis nec risus in sapien tempus posuere sed nec massa.
			Cras commodo nulla sit amet orci pretium dapibus.
			Nulla sed elit eu justo hendrerit auctor.
			Pellentesque ut quam a metus consequat venenatis vitae ut mauris.
			Ut vulputate luctus arcu, at dapibus risus molestie ac.
			Etiam euismod est nec est iaculis tristique.
			Vivamus ornare felis quis leo aliquet elementum.
			Nunc pretium arcu convallis quam suscipit eu rutrum est auctor.
			//
			Phasellus eu ipsum ac lorem euismod vestibulum quis at metus.
			Donec aliquet condimentum mi, in consequat elit dignissim nec.
			Etiam eu ante vel libero congue vehicula.
			Suspendisse volutpat ante eu ligula semper vitae venenatis lectus imperdiet.
			Pellentesque porttitor nisl quam, a pretium nibh.
			Pellentesque nec sem in neque suscipit posuere ut id odio.
			//
			Integer sollicitudin enim sit amet leo lacinia varius.
			Fusce fermentum sem vel est iaculis eleifend.";
		$array = explode("//", $str);
		$length = count($array);
		$rand = rand(0, $length -1);
		return $array[$rand];
	}


}



