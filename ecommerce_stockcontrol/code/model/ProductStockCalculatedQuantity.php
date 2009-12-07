<?php
/**
 *@author: Nicolaas [at] Sunny Side Up . Co . Nz
 *@description: works out the quantity available for each product
 *
 *
 **/

class ProductStockCalculatedQuantity extends DataObject {

	static $db = array(
		"Name" => "Varchar(255)",
		"BaseQuantity" => "Int",
		"ProductPresent" => "Boolean"
	);

	static $has_one = array(
		"Product" => "Product",
	);

	static $has_many = array(
		"ProductStockOrderEntry" => "ProductStockOrderEntry",
		"ProductStockManualUpdate" => "ProductStockManualUpdate"
	);

	static $defaults = array(
		"ProductPresent" => 1
	);

	//MODEL ADMIN STUFF
	public static $searchable_fields = array(
		"BaseQuantity",
		"ProductPresent",
		"Name"
	);

	public static $field_labels = array(
		"BaseQuantity" => "Calculated Quantity On Hand",
		"ProductPresent" => "Product Present",
		"ProductID" => "Product ID",
		"LastEdited" => "Last Calculated"
	);

	public static $summary_fields = array(
		"Name",
		"BaseQuantity",
		"LastEdited"
	);

	public static $default_sort = "ProductPresent DESC, Name ASC";

	public static $singular_name = "Product Stock Calculated Quantity";

	public static $plural_name = "Product Stock Calculated Quantities";

	protected static $calculation_done = array();

	public function canCreate() {return false;}

	public function canEdit() {return false;}

	public function canDelete() {return false;}

	public function canView() {return $this->canDoAnything();}

	protected function canDoAnything() {
		if(!Permission::check("ADMIN")) {
			Security::permissionFailure($this, _t('Security.PERMFAILURE',' This page is secured and you need administrator rights to access it. Enter your credentials below and we will send you right along.'));
		}
		return true;
	}

	function index() {
		die("ok");
		self::add_all_products();
	}

	//END MODEL ADMIN STUFF

	static function add_all_products() {
		//add ones that have not been added yet
		$sql = '
			INSERT INTO `ProductStockCalculatedQuantity` (ProductID, BaseQuantity, Name)
			SELECT `Product`.`ID` AS ProductID, 0 AS BaseQuantity, `SiteTree`.`Title` AS Name
			FROM `Product`
				INNER JOIN `SiteTree` ON `Product`.`ID` = `SiteTree`.`ID`
				LEFT JOIN `ProductStockCalculatedQuantity` ON `ProductStockCalculatedQuantity`.`ProductID` = `Product`.`ID`
			WHERE `ProductStockCalculatedQuantity`.`ID` IS NULL;';
		DB::query($sql);
		//delete ones that are no longer required
		$sql = '
			UPDATE `ProductStockCalculatedQuantity`
				LEFT JOIN `Product` ON `Product`.`ID` = `ProductStockCalculatedQuantity`.`ProductID`
			SET `ProductStockCalculatedQuantity`.`ProductPresent` = 0
			WHERE `Product`.`ID` IS NULL;';
		DB::query($sql);
	}

	static function get_quantity_by_product_id($productID) {
		$value = 0;
		$item = self::get_by_product_id($productID);
		if($item) {
			$value = $item->calculatedBaseQuantity();
			if($value < 0) {
				$value = 0;
			}
		}
		return $value;
	}

	static function get_by_product_id($productID) {
		if($obj = DataObject::get_one("ProductStockCalculatedQuantity", "ProductID = ".intval($productID))) {
			//do nothing
		}
		else {
			$obj = new ProductStockCalculatedQuantity();
			$obj->ProductID = $productID;
		}
		if($obj) {
			$obj->write();
			return $obj;
		}
		user_error("Could not find / create ProductStockCalculatedQuantity for product with ID: ".$id, E_WARNING);
	}

	function calculatedBaseQuantity() {
		$this->write();
		if(!$this->ID) {
			return 0;
		}
		else {
			return $this->getField("BaseQuantity");
		}
	}

	function WorkOutQuantities($products = null) {
		if($products) {
			foreach($products as $product) {
				$ProductStockCalculatedQuantityRecord = DataObject::get_one("ProductStockCalculatedQuantity", "ProductID = ".$product->ID);
				if(!$ProductStockCalculatedQuantityRecord && $LatestUpdate) {
					$ProductStockCalculatedQuantityRecord = new ProductStockCalculatedQuantity();
					$ProductStockCalculatedQuantityRecord->ProductID = $product->ID;
				}
				if($ProductStockCalculatedQuantityRecord) {
					$ProductStockCalculatedQuantityRecord->write();
				}
			}
		}
	}

	function onBeforeWrite() {
		if($this->ProductID && $this->ID && !isset(self::$calculation_done[$this->ID])) {
			if($product = DataObject::get_by_id("Product", $this->ProductID)) {
				//set name
				$this->Name = $product->Title;

				//add total order quantities
				$data = DB::query('
					SELECT
						`Product_OrderItem`.`ProductID`,
						Sum(`OrderItem`.`Quantity`)+0 QuantitySum,
						`Order`.`ID` OrderID
					FROM
						`Order`
						INNER JOIN `OrderAttribute` ON `OrderAttribute`.`OrderID` = `Order`.ID
						INNER JOIN `OrderItem` ON `OrderAttribute`.`ID` = `OrderItem`.`ID`
						INNER JOIN `Product_OrderItem` ON `Product_OrderItem`.`ID` = `OrderAttribute`.`ID`
						INNER JOIN `Payment` ON `Payment`.`OrderID` = `Order`.`ID`
					GROUP BY
						`Order`.`ID`, `ProductID`
					HAVING
						(`Product_OrderItem`.`ProductID` = '.(intval($this->ProductID) + 0).')
				');
				if($data) {
					foreach($data as $row) {
						if($row["OrderID"] && $this->ID && $row["QuantitySum"]) {
							if($ProductStockOrderEntry = DataObject::get_one("ProductStockOrderEntry", "OrderID = ".$row["OrderID"]." AND ParentID = ".$this->ID)) {
								//do nothing
							}
							else {
								$ProductStockOrderEntry = new ProductStockOrderEntry();
								$ProductStockOrderEntry->OrderID = $row["OrderID"];
								$ProductStockOrderEntry->ParentID = $this->ID;
								$ProductStockOrderEntry->IncludeInCurrentCalculation = 1;
								$ProductStockOrderEntry->Quantity = 0;
							}
							if($ProductStockOrderEntry->Quantity != $row["QuantitySum"] && $ProductStockOrderEntry->IncludeInCurrentCalculation) {
								$ProductStockOrderEntry->Quantity = $row["QuantitySum"];
								$ProductStockOrderEntry->write();
							}
						}
					}
				}
				//work out additional purchases
				$sqlQuery = new SQLQuery(
					 "SUM(`Quantity`)", // Select
					 "ProductStockOrderEntry", // From
					 "`ParentID` = ".$this->ID." AND `IncludeInCurrentCalculation` = 1" // Where (optional)
				);
				$OrderQuantityToDeduct = $sqlQuery->execute()->value();

				//find last adjustment
				$LatestManualUpdate = DataObject::get_one("ProductStockManualUpdate","ParentID = ".$this->ID, "LastEdited DESC");

				//nullify order quantities that were entered before last adjustment
				if($LatestManualUpdate) {
					$LatestManualUpdateQuantity = $LatestManualUpdate->Quantity;
					DB::query("UPDATE `ProductStockOrderEntry` SET `IncludeInCurrentCalculation` = 0 WHERE `LastEdited` < '".$LatestManualUpdate->LastEdited."' AND `ParentID` = ".$this->ID);
				}
				else {
					$LatestManualUpdateQuantity = 0;
				}

				//work out base total
				$this->BaseQuantity = $LatestManualUpdateQuantity - $OrderQuantityToDeduct;
				if(isset($_GET["debug"])) {
					echo "<hr />";
					echo $this->Name;
					echo " | Manual SUM: ".$LatestManualUpdateQuantity;
					echo " | Order SUM: ".$OrderQuantityToDeduct;
					echo " | Total SUM: ".$this->BaseQuantity;
					echo "<hr />";
				}
			}
			self::$calculation_done[$this->ID] = $this->BaseQuantity;
		}
		parent::onBeforeWrite();
	}

}
