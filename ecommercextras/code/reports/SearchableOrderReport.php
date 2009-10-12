<?php
/**
 *
 * @package ecommercextras
 * @author nicolaas[at]sunnysideup.co.nz
 */
class SearchableOrderReport extends SSReport {

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
		Requirements::javascript("ecommercextras/javascript/SearchableOrderReport.js");
		Requirements::javascript("jsparty/jquery/plugins/form/jquery.form.js");
		Requirements::customScript('var SearchableOrderReportURL = "'.Director::baseURL()."SearchableOrderReport_Handler".'/submit/";', 'SearchableOrderReport_Handler');
		$fields = parent::getCMSFields();
		$fields->addFieldToTab("Root.Search", new CheckboxSetField("Status", "Order Status", singleton('Order')->dbObject('Status')->enumValues(true)));
		$fields->addFieldToTab("Root.Search", new TextField("Email", "Email"));
		$fields->addFieldToTab("Root.Search", new NumericField("Value", "Value"));
		$fields->addFieldToTab("Root.Search", new CalendarDateField("StartDate", "Start Date"));
		$fields->addFieldToTab("Root.Search", new CalendarDateField("EndDate", "End Date"));
		$fields->addFieldToTab("Root.Search", new FormAction('doSearch', 'Start Search'));
		return $fields;
	}


	function doSearch($data, $form) {
		die("ok");

		Director::redirectBack();
	}

	function EditForm() {
		die("ok2");
	}


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
		$wherePhrase = Session::get("SearchableOrderReport.wherePhrase");
		$query = singleton('Order')->buildSQL(Session::get("SearchableOrderReport.wherePhrase"), 'Order.Created DESC');
		$query->groupby[] = 'Order.Created';
		$table->setCustomQuery($query);

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

}


class SearchableOrderReport_Handler extends Controller {

	function submit() {
		$where = 'Member.Email = "nfrancken@gmail.com"';
		Session::set("SearchableOrderReport.wherePhrase", $where);
		return "ok";
	}

}