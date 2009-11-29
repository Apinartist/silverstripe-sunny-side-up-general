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
		"Product" => "Product"
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

	public static $singular_name = "Product Stock Calculated Quantity";

	public static $plural_name = "Product Stock Calculated Quantities";

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

	function init() {
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

	static function get_quantity_by_product_id($id) {
		$value = 0;
		$item = DataObject::get_one("ProductStockCalculatedQuantity", "`ProductID` = ".$id);
		if($item) {
			$value = $item->getBaseQuantity();
			if($value < 0) {
				$value = 0;
			}
		}
		return $value;
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
				INNER JOIN `Payment` ON `Payment`.`ID` = `Order`.`ID`
				INNER JOIN `ProductStockOrderEntry` On `ProductStockOrderEntry`.`OrderID` = `Order`.`ID`
			GROUP BY `Order`.`ID`, `ProductID`
			HAVING
				(`Product_OrderItem`.`ProductID` = '.(intval($this->ProductID) + 0).')
				')
				;//				(`ProductStockOrderEntry`.`Quantity` <> QuantitySum) AND
		foreach($data as $row) {
			$ProductStockOrderEntry = new ProductStockOrderEntry();
			$ProductStockOrderEntry->OrderID = $row["OrderID"];
			$ProductStockOrderEntry->Quantity = $row["QuantitySum"];
			$ProductStockOrderEntry->ParentID = $this->ID;
			$ProductStockOrderEntry->IncludeInCurrentCalculation = 1;
			$ProductStockOrderEntry->write();
		}
		//find last adjustment
		$LatestManualUpdate = DataObject::get_one("ProductStockManualUpdate","ParentID = ".$this->ID. true, "LastEdited DESC");
		//nullify order quantities that were entered before last adjustment
		if($LatestManualUpdate) {
			$LatestManualUpdateQuantity = $LatestManualUpdate->Quantity;
			DB::query("UPDATE `ProductStockOrderEntry` SET `IncludeInCurrentCalculation` = 0 WHERE `LastEdited` < ".$LatestManualUpdate->LastEdited." AND `ParentID` = ".$this->ID);
		}
		else {
			$LatestManualUpdateQuantity = 0;
		}
		//work out additional purchases
		$sqlQuery = new SQLQuery(
			 "SUM(`Quantity`)", // Select
			 "ProductStockOrderEntry", // From
			 "`ParentID` = ".$this->ID." AND `IncludeInCurrentCalculation` = 1" // Where (optional)
		);
		$OrderQuantityToDeduct = $sqlQuery->execute()->value();
		//work out base total
		$this->BaseQuantity = $LatestManualUpdateQuantity - $OrderQuantityToDeduct;
		parent::onBeforeWrite();
	}


}
