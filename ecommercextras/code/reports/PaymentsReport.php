<?php
/**
 * @author Nicolaas [at] sunnysideup.co.nz
 * @package ecommercextras
 */
class PaymentsReport extends SSReport {

	protected $title = 'All Payments';

	protected static $sales_array = array();

	protected $description = 'Show all payments.';

	/**
	 * Return a {@link ComplexTableField} that shows
	 * all payment instances that are not printed. That is,
	 * payment instances with the property "Printed" value
	 * set to "0".
	 *
	 * @return ComplexTableField
	 */
	function getReportField() {
		Requirements::javascript(THIRDPARTY_DIR."/jquery/plugins/livequery/jquery.livequery.js");
		Requirements::javascript(THIRDPARTY_DIR."/jquery/plugins/form/jquery.form.js");
		Requirements::javascript("ecommercextras/javascript/PaymentsReport.js");
		Requirements::customScript('var PaymentsReportURL = "'.Director::baseURL()."PaymentsReport_Handler".'/";', 'PaymentsReport_Handler_Base_URL');
		$fields = array(
			'Created' => "Created",
			'Status' => "Status",
			'Amount' => "Amount",
			'Currency' => "Currency",
			'IP' => "IP",
			'ProxyIP' => "ProxyIP",
			'OrderID' => "OrderID"
		);
		$table = new TableListField(
			'Payments',
			'Payment',
			$fields
		);
		$payments = DataObject::get("Payment", "", "Created DESC");
		$table->setCustomSourceItems($payments);
		$table->setFieldCasting(array(
			'Created' => 'Date',
			'Amount' => 'Currency->Nice',
		));

		$table->setPermissions(array(
			'edit',
			'show',
			'export',
		));
		$table->setPageSize(250);
		return $table;
	}

	function getCustomQuery() {
			//buildSQL($filter = "", $sort = "", $limit = "", $join = "", $restrictClasses = true, $having = "")
		$query = singleton('Payment')->buildSQL('', 'Payment.Created DESC');
		return $query;
	}


}

class PaymentsReport_Handler extends Controller {

	function setstatus() {
		$id = $this->urlParams['ID'];
		if(!is_numeric($id)) {
			return "could not update payment status";
		}
		$payment = DataObject::get_by_id('Payment', $id);
		if($payment) {
			$oldStatus = $payment->Status;
			$newStatus = $this->urlParams['OtherID'];
			if($oldStatus != $newStatus) {
				$payment->Status = $newStatus;
				$payment->write();
				$paymentlog = new OrderStatusLog();
				$paymentlog->OrderID = $payment->OrderID;
				$paymentLog->Status = "Payment Status changed from ".$oldStatus." to ".$newStatus.".";
				$paymentlog->write();
			}
			else {
				return "no change";
			}
		}
		else {
			return "payment not found";
		}
		return "updated to ".$newStatus;
	}
}