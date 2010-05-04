<?php
/**
 *
 * @package ecommercextras
 * @author nicolaas[at]sunnysideup.co.nz
 */
class SearchableProductSalesReport extends SearchableOrderReport {

	protected $title = 'Searchable Product Sales';

	protected $description = 'Search all products ordered';


	function getCMSFields() {
		$fields = parent::getCMSFields();
		$fields->addFieldToTab("Root.Search", new TextField("Product", "Product name (or part of the name)"), "OrderID");
		return $fields;
	}

	function getCustomQuery() {
			//buildSQL($filter = "", $sort = "", $limit = "", $join = "", $restrictClasses = true, $having = "")
		$where = Session::get("SearchableProductSalesReport.where");
		if(trim($where)) {
		 $where = " ( $where ) AND";
		}
		$where .= ' (OrderModifier.Amount <> 0 OR OrderModifier.Amount IS NULL)';
		$query = singleton('OrderAttribute')->buildSQL(
			$where,
			$sort = '`Order`.`Created` DESC',
			$limit = "",
			$join = "
				INNER JOIN `Order` ON `Order`.`ID` = `OrderAttribute`.`OrderID`
				INNER JOIN `Member` On `Order`.`MemberID` = `Member`.`ID`
				LEFT JOIN `Payment` ON `Payment`.`OrderID` = `Order`.`ID`
				LEFT JOIN `Product_versions` ProductForProducts ON `Product_OrderItem`.`ProductID` = ProductForProducts.`RecordID` AND `Product_OrderItem`.`ProductVersion` = ProductForProducts.`Version`
				LEFT JOIN `SiteTree_versions` SiteTreeForProducts ON SiteTreeForProducts.`RecordID` = `Product_OrderItem`.`ProductID` AND `Product_OrderItem`.`ProductVersion` = SiteTreeForProducts.`Version`
				LEFT JOIN `ProductVariation_versions` ProductVariationsForVariations ON `ProductVariation_OrderItem`.`ProductVariationID` = ProductVariationsForVariations.`RecordID` AND `ProductVariation_OrderItem`.`ProductVariationVersion` = ProductVariationsForVariations.`Version`
				LEFT JOIN `SiteTree_versions` ProductSiteTreeForVariations ON `ProductSiteTreeForVariations`.`RecordID` = `Product_OrderItem`.`ProductID` AND `Product_OrderItem`.`ProductVersion` = `ProductSiteTreeForVariations`.`Version`
			"
		);
		//make sure to do variations first
		$query->select[] = "SUM(IF(Payment.Status = 'Success',`Payment`.`Amount`, 0)) RealPayments";
		$query->select[] = "IF(ProductVariationsForVariations.Title IS NOT NULL, CONCAT(ProductSiteTreeForVariations.Title,' : ', ProductVariationsForVariations.Title), IF(SiteTreeForProducts.Title IS NOT NULL, SiteTreeForProducts.Title, OrderAttribute.ClassName)) ProductOrModifierName";
		$query->select[] = "IF(ProductSiteTreeForVariations.ID IS NOT NULL, ProductVariationsForVariations.Price, IF(ProductForProducts.Price IS NOT NULL, ProductForProducts.Price, IF(OrderModifier.Amount IS NOT NULL, OrderModifier.Amount, 'n/a'))) ProductOrModifierPrice";
		$query->select[] = "IF(OrderItem.Quantity IS NOT NULL, OrderItem.Quantity, 1)  ProductOrModifierQuantity";
		$query->select[] = "CONCAT('#', Order.ID, ' on ', Order.Created, ', ', Order.Status) as OrderDetails";
		$query->select[] = "CONCAT(Member.FirstName, ' ', Member.Surname, ' (', Member.Email,')') MemberDetails";
		if($having = Session::get("SearchableProductSalesReport.having")) {
			$query->having($having);
		}
		return $query;
	}

	function getReportField() {
		$fields = array(
			"OrderDetails" => "Order ID",
			"MemberDetails" => "Customer",
			"ProductOrModifierName" => "Name",
			"ProductOrModifierPrice" => "Price",
			"ProductOrModifierQuantity" => "Quantity",
			"RealPayments" => "Order Payment"
		);

		$table = new TableListField(
			'Orders',
			'Order',
			$fields
		);

		$table->setCustomQuery($this->getCustomQuery());

		$table->setFieldCasting(array(
			'Created' => 'Date',
			'Total' => 'Currency->Nice'
		));

		$table->setPermissions(array(
			'edit',
			'show',
			'export',
		));
		$table->setPageSize(250);


		$table->setFieldListCsv($this->getExportFields());

		$table->setCustomCsvQuery($this->getExportQuery());

		//$tableField->removeCsvHeader();

		return $table;
	}


	function getExportFields() {

		$fields = array(
			"Order.ID" => "Order ID",
			"Order.Created" => "Order date and time",
			"Payment.Message" => " Reference",
			//"Total" => "Total Order Amount",
			"Member.FirstName" => "Customer first name",
			"Member.Surname" => "Customer last name",
			"Member.HomePhone" => "Customer home phone",
			"Member.MobilePhone" => "Customer mobile phone",
			"Member.Email" => "Customer phone",
			"Member.Address" => "Customer address 1",
			"Member.AddressLine2" => "Customer address 2",
			"Member.City" => "Customer City",
			"Order.Status" => "Order Status"
			//"PlaintextProductSummary" => "Products"
			//"PlaintextModifierSummary" => "Additions",
			//"PlaintextLogDescription" => "Dispatch Notes"
		);
		return $fields;
	}

	function getExportQuery() {
		if("SalesReport" == $this->class) {
			user_error('Please implement getExportFields() on ' . $this->class, E_USER_ERROR);
		}
		else {
			return $this->getCustomQuery();
		}
	}




}


