<?php
/**
 *
 * @package ecommercextras
 * @author nicolaas[at]sunnysideup.co.nz
 */
class SearchableOrderReport extends SalesReport {

	protected $title = 'Searchable Orders';

	protected $description = 'This shows all orders with the ability to search them';

	/**
	 * Return a {@link ComplexTableField} that shows
	 * all Order instances that are not printed. That is,
	 * Order instances with the property "Printed" value
	 * set to "0".
	 *
	 * @return ComplexTableField
	 */


	function getCMSFields() {
		$fields = parent::getCMSFields();
		$fields->addFieldToTab("Root.Search", new CheckboxSetField("Status", "Order Status", singleton('Order')->dbObject('Status')->enumValues(true)));
		$fields->addFieldToTab("Root.Search", new TextField("Email", "Email"));
		$fields->addFieldToTab("Root.Search", new NumericField("Value", "Value"));
		$fields->addFieldToTab("Root.Search", new CalendarDateField("StartDate", "Start Date"));
		$fields->addFieldToTab("Root.Search", new CalendarDateField("EndDate", "End Date"));
		$fields->addFieldToTab("Root.Search", new FormAction('doSearch', 'Start Search'));
		return $fields;
	}

	function processform() {
		print_r($_REQUEST);
		return "ok";
	}

	function getCustomQuery() {
			//buildSQL($filter = "", $sort = "", $limit = "", $join = "", $restrictClasses = true, $having = "")
		$query = singleton('Order')->buildSQL(Session::get("SearchableOrderReport.where"), 'Order.Created DESC', "", $join = " INNER JOIN Member on Member.ID = Order.MemberID");
		$query->groupby[] = 'Order.Created';
		return $query;
	}


}


