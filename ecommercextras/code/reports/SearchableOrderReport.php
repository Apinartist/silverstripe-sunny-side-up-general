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
		if($humanWhere = Session::get("SearchableOrderReport.humanWhere")) {
			$fields->addFieldToTab("Root.Report", new LiteralField("humanWhere", "<p>Current Search: ".$humanWhere."</p>"), "ReportDescription");
			$fields->removeByName("ReportDescription");
			$fields->addFieldToTab("Root.Search", new FormAction('clearSearch', 'Clear Search'));
		}
		$fields->addFieldToTab("Root.Search", new CheckboxSetField("Status", "Order Status", singleton('Order')->dbObject('Status')->enumValues(true)));
		$fields->addFieldToTab("Root.Search", new NumericField("OrderID", "Order ID"));
		$fields->addFieldToTab("Root.Search", new TextField("Email", "Email"));
		$fields->addFieldToTab("Root.Search", new TextField("FirstName", "First Name"));
		$fields->addFieldToTab("Root.Search", new TextField("Surname", "Surname"));
		$fields->addFieldToTab("Root.Search", new FormAction('doSearch', 'Apply Search'));
		return $fields;
	}

	function processform() {
		$where = array();
		$humanWhere = array();
		foreach($_REQUEST as $key => $value) {
			$value = Convert::raw2sql($value);
			if($value) {
				switch($key) {
					case "OrderID":
						$where[] = ' `Order`.`ID` = '.intval($value);
						$humanWhere[] = ' OrderID equals '.intval($value);
						break;
					case "Email":
						$where[] = ' `Member`.`Email` = "'.$value.'"';
						$humanWhere[] = ' Customer Email equals "'.$value.'"';
						break;
					case "FirstName":
						$where[] = ' `Member`.`FirstName` LIKE "%'.$value.'%"';
						$humanWhere[] = ' Customer First Name equals '.$value.'"';
						break;
					case "Surname":
						$where[] = ' `Member`.`Surname` LIKE "%'.$value.'%"';
						$humanWhere[] = ' Customer Surname equals "'.$value.'"';
						break;
					case "Status":
						foreach($value as $item) {
							$where[] = ' `Order`.`Status` = "'.$item.'"';
							$humanWhere[] = ' Order Status equals "'.$item.'"';
						}
						break;
					default:
					 break;
				}
			}
		}
		Session::set("SearchableOrderReport.where", implode(" OR ", $where));
		Session::set("SearchableOrderReport.humanWhere", implode(", ", $humanWhere));
		return "ok";
	}

	function getCustomQuery() {
			//buildSQL($filter = "", $sort = "", $limit = "", $join = "", $restrictClasses = true, $having = "")
		$query = singleton('Order')->buildSQL(Session::get("SearchableOrderReport.where"), 'Order.Created DESC', "", $join = " INNER JOIN Member on Member.ID = Order.MemberID");
		$query->groupby[] = 'Order.Created';
		return $query;
	}

	function filterDescription() {

	}

}


