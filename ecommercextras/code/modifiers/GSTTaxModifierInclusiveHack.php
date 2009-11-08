<?php

/**
 * @author Nicolaas [at] sunnysideup.co.nz
 * @package: ecommerce
 * @sub-package: ecommercextras
 * @description: dirty hack to resolve situations where tax is inclusive and purchaser is from above
 *
 */

class GSTTaxModifierInclusiveHack extends GSTTaxModifier {

	protected static $is_chargable = false;

	function IsExclusive() {
		return false;
	}


	protected function LiveRate() {
		if($this->LiveCountry()  != "NZ") {
			return floatval(1/9);
		}
		return 0;
	}

	protected function LiveName() {
		if($this->LiveCountry()  != "NZ") {
			return "GST Reduction (based on sales outside New Zealand)";
		}
		else {
			return "&nbsp;";
		}
	}

	protected function LiveIsExclusive() {
		return false;
	}

	function AddedCharge() {
		return $this->Charge();
	}

	function Charge() {
		return $this->TaxableAmount() * $this->Rate();
	}

	function getAmount() {
		if($this->ID) {
			return $this->Amount;
		}
		else {
			return $this->LiveAmount();
		}
	}

	function TaxableAmount() {
		$order = $this->Order();
		return $order->SubTotal() + $order->ModifiersSubTotal(array("GSTTaxModifier", "GSTTaxModifierInclusiveHack"));
	}


// ajax  NEED TO OVERRIDE THE STANDARD ONE..
	function updateForAjax(array &$js) {
		$js[] = array('id' => $this->CartTotalID(), 'parameter' => 'innerHTML', 'value' => $this->Charge());
		$js[] = array('id' => $this->TableTotalID(), 'parameter' => 'innerHTML', 'value' => $this->TableValue());
		$js[] = array('id' => $this->TableTitleID(), 'parameter' => 'innerHTML', 'value' => $this->TableTitle());
	}


}
