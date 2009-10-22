<?php
/**
 * @author Nicolaas [at] sunnysideup.co.nz
 * @package ecommercextras
 */
class SalesReport extends SSReport {

	protected $title = 'All orders';

	protected static $sales_array = array();

	protected $description = 'Show all orders in the system.';

	/**
	 * Return a {@link ComplexTableField} that shows
	 * all Order instances that are not printed. That is,
	 * Order instances with the property "Printed" value
	 * set to "0".
	 *
	 * @return ComplexTableField
	 */
	function getReportField() {
		Requirements::javascript("jsparty/jquery/plugins/livequery/jquery.livequery.js");
		Requirements::javascript("jsparty/jquery/plugins/form/jquery.form.js");
		Requirements::javascript("ecommercextras/javascript/SalesReport.js");
		Requirements::customScript('var SalesReportURL = "'.Director::baseURL()."SalesReport_Handler".'/processform/";', 'SalesReport_Handler');
		$list = singleton('Order')->dbObject('Status')->enumValues(true);
		$js = '';
		foreach($list as $key => $value) {
			if($key && $value) {
				$js .= '
					SalesReport.dropdownStatusArray[] = "'.$value.'";';
			}
		}
		Requirements::customScript($js);
		// Get the fields used for the table columns
		$fields = Order::$table_overview_fields;

		// Add some fields specific to this report
		$fields['Invoice'] = '';
		$fields['PackingSlip'] = '';
		$fields['ChangeStatus'] = '';

		$table = new TableListField(
			'Orders',
			'Order',
			$fields
		);

		// Customise the SQL query for Order, because we don't want it querying
		// all the fields. Invoice and Printed are dummy fields that just have some
		// text in them, which would be automatically queried if we didn't specify
		// a custom query.

		$table->setCustomQuery($this->getCustomQuery());

		// Set the links to the Invoice and Print fields allowing a user to view
		// another template for viewing an Order instance
		$table->setFieldFormatting(array(
			'Invoice' => '<a href=\"OrderReport_Popup/invoice/$ID\" class=\"makeIntoPopUp\">Invoice</a>',
			'PackingSlip' => '<a href=\"OrderReport_Popup/packingslip/$ID\" class=\"makeIntoPopUp\">Packing Slip</a>',
			'ChangeStatus' => '<span class=\"statusDropdownChange\" rel=\"$Status,$ID\">$Status</span>'
		));

		$table->setFieldCasting(array(
			'Created' => 'Date',
			'Total' => 'Currency->Nice'
		));

		$table->setPermissions(array(
			'edit',
			'show',
			'export',
			'delete',
		));
		$table->setPageSize(250);
		return $table;
	}

	function getCustomQuery() {
		if("SalesReport" == $this->class) {
			user_error('Please implement getCustomQuery() on ' . $this->class, E_USER_ERROR);
		}
		else {
				//buildSQL($filter = "", $sort = "", $limit = "", $join = "", $restrictClasses = true, $having = "")
			$query = singleton('Order')->buildSQL('', 'Order.Created DESC');
			$query->groupby[] = 'Order.Created';
			return $query;
		}
	}

	protected function statistic($type) {
		if(!count(self::$sales_array)) {
			$data = $this->getCustomQuery()->execute();
			if($data) {
				$array = array();
				foreach($data as $row) {
					if($row["RealPayments"]) {
						self::$sales_array[] = $row["RealPayments"];
					}
				}
			}
		}
		if(count(self::$sales_array)) {
			switch($type) {
				case "count":
					return count(self::$sales_array);
					break;
				case "sum":
					return array_sum(self::$sales_array);
					break;
				case "avg":
					return array_sum(self::$sales_array) / count(self::$sales_array);
					break;
				case "min":
					asort(self::$sales_array);
					foreach(self::$sales_array as $item) {return $item;}
					break;
				case "max":
					arsort(self::$sales_array);
					foreach(self::$sales_array as $item) {return $item;}
					break;
				default:
					user_error("Wrong statistic type speficied in SalesReport::statistic", E_USER_ERROR);
			}
		}
		return -1;
	}

	function processform() {
		if("SalesReport" == $this->class) {
			user_error('Please implement processform() on ' . $this->class, E_USER_ERROR);
		}
		else {
			die($_REQUEST);
		}
	}

}

class SalesReport_Handler extends Controller {

	function processform() {
		$ClassName = Director::URLParam("ID");
		$object = new $ClassName;
		return $object->processform();
	}

	function getstatusoptions() {
		die("works");
	}

	function setstatusoptions() {

	}

}