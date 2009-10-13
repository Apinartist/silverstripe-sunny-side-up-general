<?php
/**
 *
 * @package ecommercextras
 * @author nicolaas[at]sunnysideup.co.nz
 */
class SearchableOrderReport extends OrderReport {

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

}


class SearchableOrderReport_Handler extends Controller {

	function submit() {
		$where = 'Member.Email = "nfrancken@gmail.com"';
		Session::set("SearchableOrderReport.wherePhrase", $where);
		return "ok";
	}

}