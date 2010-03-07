<?php

/**
 *@author nicolaas [at] sunnysideup.co.nz
 *@package ecommerce
 *@subpackage ecommercextras
 *@description adds extra functionality to the Order Class
 **/

class PaymentDecorator extends DataObjectDecorator {


	function canCreate($member = null) {
		return false;
	}

	function canDelete($member = null) {
		return false;
	}

	function extraStatics() {
		return array(
			'searchable_fields' => array(
				'OrderID' => array('title' => 'Order ID'),
				'Amount' => array('title' => 'Amount'),
				'IP' => array('title' => 'Customer Email', 'filter' => 'PartialMatchFilter'),
				'Status'
			)
		);
	}

	function updateSummaryFields(&$fields){
		$fields['Created'] = 'Date';
		$fields['OrderID'] = 'OrderID';
		$fields['Amount'] = 'Amount';
		$fields['IP'] = 'Amount';
		$fields['Total'] = 'Total';
	}
}


