<?php
/**
 *@author: Nicolaas [at] Sunny Side Up . Co . Nz
 *@description: works out the quantity available for each product
 *
 *
 **/

class ProductStockCalculatedQuantity extends DataObject {

	static $db = array(
		"BaseQuantity" => "Int",
	);

	static $has_one = array(
		"Parent" => "Product",
	);

	static $has_many = array(
		"ProductStockOrderEntry" => "ProductStockOrderEntry",
		"ProductStockManualUpdate" => "ProductStockManualUpdate"
	);

	static function get_quantity_by_product_id($id) {
		$value = 0;
		$item = DataObject::get_one("ProductStockCalculatedQuantity", "`ParentID` = ".$id);
		if($item) {
			$value = $item->getBaseQuantity();
			if($value < 0) {
				$value = 0;
			}
		}
		return $value;
	}

	static function add_all_products() {
		$sql = '
			INSERT INTO `ProductStockCalculatedQuantity` (ParentID, BaseQuantity)
			SELECT `Product`.`ID` AS ParentID, 0 AS BaseQuantity
			FROM Product
				LEFT JOIN `ProductStockCalculatedQuantity` ON `ProductStockCalculatedQuantity`.`ParentID` = `Product`.`ID`
			WHERE `ProductStockCalculatedQuantity`.`ID` IS NULL;';
		DB::query($sql);
	}

	function getBaseQuantity() {
		$this->write();
		return $this->BaseQuantity;
	}

	function WorkOutQuantities($products = null) {
		if($products) {
			foreach($products as $product) {
				$ProductStockCalculatedQuantityRecord = DataObject::get_one("ProductStockCalculatedQuantity", "ParentID = ".$product->ID);
				if(!$ProductStockCalculatedQuantityRecord && $LatestUpdate) {
					$ProductStockCalculatedQuantityRecord = new ProductStockCalculatedQuantity();
					$ProductStockCalculatedQuantityRecord->ParentID = $product->ID;
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
				Select
					`ProductID`,
					Sum(`Quantity`) QuantitySum,
					`Order`.`ID` OrderID
				From
					`Order`
					INNER JOIN `OrderAttribute` ON `OrderAttribute`.`OrderID` = `Order`.ID
					INNER JOIN `OrderItem` ON `OrderAttribute.ID = `OrderItem`.`ID`
					INNER JOIN `Product_OrderItem` ON `Product_OrderItem`.`ID` = `OrderAttribute`.`ID`
					INNER JOIN `Payment` ON `Payment`.`ID` = `Order`.`ID`
					LEFT JOIN ProductStockOrderEntry On `ProductStockOrderEntry`.`OrderID` = `Order`.`ID`
				Group By `Order`.`ID`, `ProductID`
				Where
					(`ProductStockOrderEntry`.`ID` IS NULL OR `ProductStockOrderEntry`.`Quantity` <> QuantitySum) AND
					(`ProductID` = '.$product->ID.')');
		foreach($data as $row) {
			$ProductStockOrderEntry = new ProductStockOrderEntry();
			$ProductStockOrderEntry->OrderID = $row["OrderID"];
			$ProductStockOrderEntry->Quantity = $row["QuantitySum"];
			$ProductStockOrderEntry->ParentID = $this->ID;
			$ProductStockOrderEntry->NoLongerRelevant = 0;
			$ProductStockOrderEntry->write();
		}
		//find last adjustment
		$LatestManualUpdate = DataObject::get_one("ProductStockManualUpdate","LastEdited DESC AND ParentID = ".$this->ID);
		//nullify order quantities that were entered before last adjustment
		DB::query("UPDATE `ProductStockEntry` SET `NoLongerRelevant` = 1 WHERE `LastEdited` < ".$LatestManualUpdate->LastEdited." AND `ParentID` = ".$this->ID);
		//work out additional purchases
		$sqlQuery = new SQLQuery(
			 "SUM(`Quantity`)", // Select
			 "ProductStockEntry", // From
			 "`ParentID` = ".$this->ID." AND `NoLongerRelevant` = 0" // Where (optional)
		);
		$OrderQuantityToDeduct = $sqlQuery->execute()->value();
		//work out base total
		$this->BaseQuantity = $LatestUpdate->Quantity - $OrderQuantityToDeduct;
		parent::onBeforeWrite();
	}


}
