<?php
/**
 *@author: Nicolaas [at] Sunny Side Up . Co . Nz
 *
 *
 **/

class ProductStockVariationCalculatedQuantity extends ProductStockCalculatedQuantity {

	static $db = array(
		"ProductVariationPresentPresent" => "Boolean"
	);

	static $has_one = array(
		"ProductVariation" => "ProductVariation"
	);

	static $defaults = array(
		"ProductVariationPresent" => 1
	);

	//MODEL ADMIN STUFF
	public static $searchable_fields = array(
		"BaseQuantity",
		"ProductVariationPresent",
		"Name"
	);

	public static $field_labels = array(
		"BaseQuantity" => "Calculated Quantity On Hand",
		"ProductVariationPresent" => "Variation Present",
		"ProductVariationID" => "Product Variation ID",
		"LastEdited" => "Last Calculated"
	);


	public static $default_sort = "ProductVariationPresent DESC, Name ASC";

	public static $singular_name = "Product Stock Variation Calculated Quantity";

	public static $plural_name = "Product Stock Variation Calculated Quantities";


	//END MODEL ADMIN STUFF

	static function add_all_product_variations() {
		//add ones that have not been added yet
		$sql = '
			INSERT INTO `ProductStockVariationCalculatedQuantity` (ProductVariationID, BaseQuantity, Name)
			SELECT `Product`.`ID` AS ProductID, 0 AS BaseQuantity, `ProductVariation`.`Title` AS Name
			FROM `ProductVariation`
				LEFT JOIN `ProductStockVariationCalculatedQuantity` ON `ProductStockVariationCalculatedQuantity`.`ProductVariationID` = `ProductVariation`.`ID`
			WHERE `ProductStockVariationCalculatedQuantity`.`ID` IS NULL;';
		DB::query($sql);
		//delete ones that are no longer required
		$sql = '
			UPDATE `ProductStockVariationCalculatedQuantity`
				LEFT JOIN `ProductVariation` ON `ProductVariation`.`ID` = `ProductStockVariationCalculatedQuantity`.`ProductVariationID`
			SET `ProductStockVariationCalculatedQuantity`.`ProductVariationPresent` = 0
			WHERE `ProductVariation`.`ID` IS NULL;';
		DB::query($sql);
	}

	static function get_quantity_by_product_variation_id($productVariationID) {
		$value = 0;
		$item = self::get_by_product_variation_id($productVariationID);
		if($item) {
			$value = $item->calculatedBaseQuantity();
			if($value < 0) {
				$value = 0;
			}
		}
		return $value;
	}

	static function get_by_product_variation_id($productVariationID) {
		if($obj = DataObject::get_one("ProductStockVariationCalculatedQuantity", "ProductVariationID = ".intval($productVariationID))) {
			$obj = $obj;
		}
		else {
			$obj = new ProductStockVariationCalculatedQuantity();
			$obj->ProductVariationID = $productVariationID;
		}
		if($obj) {
			$obj->write();
			return $obj;
		}
		user_error("Could not find / create ProductStockVariationCalculatedQuantity for Product Variation with ID: ".$id, E_WARNING);
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

	function WorkOutQuantities($productVariations = null) {
		if($productVariations) {
			foreach($productVariations as $productVariation) {
				$ProductStockVariationCalculatedQuantityRecord = DataObject::get_one("ProductStockVariationCalculatedQuantity", "ProductVariationID = ".$productVariation->ID);
				if(!$ProductStockVariationCalculatedQuantityRecord && $LatestUpdate) {
					$ProductStockVariationCalculatedQuantityRecord = new ProductStockVariationCalculatedQuantity();
					$ProductStockVariationCalculatedQuantityRecord->ProductVariationID = $productVariation->ID;
				}
				if($ProductStockVariationCalculatedQuantityRecord) {
					$ProductStockVariationCalculatedQuantityRecord->write();
				}
			}
		}
	}

	function onBeforeWrite() {
		if($this->ProductVariationID && $this->ID) {
			if($productVariation = DataObject::get_by_id("ProductVariation", $this->ProductVariationID)) {
				//set name
				$this->Name = $productVariation->Title;

				//add total order quantities
				$data = DB::query('
					SELECT
						`ProductVariation_OrderItem`.`ProductVariationID`,
						Sum(`OrderItem`.`Quantity`)+0 QuantitySum,
						`Order`.`ID` OrderID
					FROM
						`Order`
						INNER JOIN `OrderAttribute` ON `OrderAttribute`.`OrderID` = `Order`.ID
						INNER JOIN `OrderItem` ON `OrderAttribute`.`ID` = `OrderItem`.`ID`
						INNER JOIN `ProductVariation_OrderItem` ON `ProductVariation_OrderItem`.`ID` = `OrderAttribute`.`ID`
						INNER JOIN `Payment` ON `Payment`.`ID` = `Order`.`ID`
						INNER JOIN `ProductStockOrderEntry` On `ProductStockOrderEntry`.`OrderID` = `Order`.`ID`
					GROUP BY
						`Order`.`ID`, `ProductID`
					HAVING
						(`ProductVariation_OrderItem`.`ProductVariationID` = '.(intval($this->productVariationID) + 0).')
				');
				foreach($data as $row) {
					$ProductStockOrderEntry = new ProductStockOrderEntry();
					$ProductStockOrderEntry->OrderID = $row["OrderID"];
					$ProductStockOrderEntry->Quantity = $row["QuantitySum"];
					$ProductStockOrderEntry->ParentID = $this->ID;
					$ProductStockOrderEntry->IncludeInCurrentCalculation = 1;
					$ProductStockOrderEntry->write();
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
					DB::query("UPDATE `ProductStockOrderEntry` SET `IncludeInCurrentCalculation` = 0 WHERE `LastEdited` < ".$LatestManualUpdate->LastEdited." AND `ParentID` = ".$this->ID);
				}
				else {
					$LatestManualUpdateQuantity = 0;
				}

				//work out base total
				$this->BaseQuantity = $LatestManualUpdateQuantity - $OrderQuantityToDeduct;
			}
		}
		parent::onBeforeWrite();
	}


}
