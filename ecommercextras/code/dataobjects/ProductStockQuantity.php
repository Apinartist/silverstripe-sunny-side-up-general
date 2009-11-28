<?php
/**
 *@author: Nicolaas [at] Sunny Side Up . Co . Nz
 *@description: works out the quantity available for each product
 *
 *
 **/

class ProductStockQuantity extends DataObject {

	static $db = array(
		"BaseQuantity" => "Int",
	);

	static $has_one = array(
		"Parent" => "Product",
	);

	static $has_many = array(
		"ProductOrderStockEntry" => "ProductOrderStockEntry"
	)

	static function get_quantity_by_product_id($id) {
		$value = 0;
		$item = DataObject::get_one("ProductStockQuantity", "`ParentID` = ".$id);
		if($item) {
			$value = $item->getBaseQuantity();
			if($value < 0) {
				$value = 0;
			}
		}
		return $value;
	}


	function getBaseQuantity() {
		$this->write();
		return $this->BaseQuantity;
	}

	function WorkOutQuantities($products = null) {
		if($products) {
			foreach($products as $product) {
				$ProductStockQuantityRecord = DataObject::get_one("ProductStockQuantity", "ParentID = ".$product->ID);
				if(!$ProductStockQuantityRecord && $LatestUpdate) {
					$ProductStockQuantityRecord = new ProductStockQuantity();
					$ProductStockQuantityRecord->ParentID = $product->ID;
				}
				if($ProductStockQuantityRecord) {
					$ProductStockQuantityRecord->write();
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
					LEFT JOIN ProductOrderStockEntry On `ProductOrderStockEntry`.`OrderID` = `Order`.`ID`
				Group By `Order`.`ID`, `ProductID`
				Where
					(`ProductOrderStockEntry`.`ID` IS NULL OR `ProductOrderStockEntry`.`Quantity` <> QuantitySum) AND
					(`ProductID` = '.$product->ID.')';
		foreach($data as $row) {
			$ProductOrderStockEntry = new ProductOrderStockEntry();
			$ProductOrderStockEntry->OrderID = $row["OrderID"];
			$ProductOrderStockEntry->Quantity = $row["QuantitySum"];
			$ProductOrderStockEntry->ParentID = $this->ID;
			$ProductOrderStockEntry->NoLongerRelevant = 0;
			$ProductOrderStockEntry->write();
		}
		//find last adjustment
		$LatestManualUpdate = DataObject::get_one("ProductStockUpdate","LastEdited DESC AND ParentID = ".$this->ID);
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
