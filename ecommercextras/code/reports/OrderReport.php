<?php
/**
 * An extension to {@link SSReport} that allows a user
 * to view all Order instances in the system.
 *
 * @package ecommerce
 */
class OrderReport extends SSReport {

	protected $title = 'All orders';

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
		// Get the fields used for the table columns
		$fields = Order::$table_overview_fields;

		// Add some fields specific to this report
		$fields['Invoice'] = '';
		$fields['Print'] = '';

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
			'Invoice' => '<a href=\"OrderReport_Popup/invoice/$ID\">Invoice</a>',
			'Print' => '<a target=\"_blank\" href=\"OrderReport_Popup/index/$ID?print=1\">Print</a>'
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

		return $table;
	}

	function getCustomQuery() {
		if("OrderReport" == $this->class) {
			user_error('Please implement getCustomQuery() on ' . $this->class, E_USER_ERROR);
		}
		else {
				//buildSQL($filter = "", $sort = "", $limit = "", $join = "", $restrictClasses = true, $having = "")
			$query = singleton('Order')->buildSQL('', 'Order.Created DESC');
			$query->groupby[] = 'Order.Created';
			return $query;
		}
	}


}
