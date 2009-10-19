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
		$stats[] = "Count: ".$this->statistic("count");
		$stats[] = "Sum: ".$this->currencyFormat($this->statistic("sum"));
		$stats[] = "Avg: ".$this->currencyFormat($this->statistic("avg"));
		$stats[] = "Min: ".$this->currencyFormat($this->statistic("min"));
		$stats[] = "Max: ".$this->currencyFormat($this->statistic("max"));
		$fields->addFieldToTab("Root.Report", new LiteralField("stats", '<h2>Payment Statistics</h2><ul><li>'.implode('</li><li>', $stats).'</li></ul>'));
		if($humanWhere = Session::get("SearchableOrderReport.humanWhere")) {
			$fields->addFieldToTab("Root.Report", new LiteralField("humanWhere", "<p>Current Search: ".$humanWhere."</p>"), "ReportDescription");
			$fields->removeByName("ReportDescription");
			$fields->addFieldToTab("Root.Search", new FormAction('clearSearch', 'Clear Search'));
		}
		$fields->addFieldToTab("Root.Search", new CheckboxSetField("Status", "Order Status", singleton('Order')->dbObject('Status')->enumValues(true)));
		$fields->addFieldToTab("Root.Search", new NumericField("OrderID", "Order ID"));
		$fields->addFieldToTab("Root.Search", new CalendarDateField("From", "From..."));
		$fields->addFieldToTab("Root.Search", new CalendarDateField("Until", "Until..."));
		$fields->addFieldToTab("Root.Search", new TextField("Email", "Email"));
		$fields->addFieldToTab("Root.Search", new TextField("FirstName", "First Name"));
		$fields->addFieldToTab("Root.Search", new TextField("Surname", "Surname"));
		$fields->addFieldToTab("Root.Search", new NumericField("HasMinimumPayment", "Has Minimum Payment of ..."));
		$fields->addFieldToTab("Root.Search", new NumericField("HasMaximumPayment", "Has Minimum Payment of ..."));
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
					case "From":
						$d = new Date("date");
						$d->setValue($value);
						$where[] = ' `Order`.`Created` <= "'.$d->format("Y-m-d").'"';
						$humanWhere[] = ' Order after or on '.$d->long();
						break;
					case "Until":
						$d = new Date("date");
						$d->setValue($value);
						$where[] = ' `Order`.`Created` >= "'.$d->format("Y-m-d").'"';
						$humanWhere[] = ' Order before or on '.$d->long();
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
						$subWhere = array();
						foreach($value as $item) {
							$subWhere[] = ' `Order`.`Status` = "'.$item.'"';
							$humanWhere[] = ' Order Status equals "'.$item.'"';
						}
						if(count($subWhere)) {
							$where[] = implode(" OR ", $subWhere);
						}
						break;
					case "HasMinimumPayment":
						$where[] = ' `RealPayments` > '.intval($value);
						$humanWhere[] = ' Real Payment of at least '.$this->currencyFormat($value);
						break;
					case "HasMaximumPayment":
						$where[] = ' `RealPayments` < '.intval($value);
						$humanWhere[] = ' Real Payment of no more than '.$this->currencyFormat($value);
						break;
					default:
					 break;
				}
			}
		}
		Session::set("SearchableOrderReport.where", implode(" AND ", $where));
		Session::set("SearchableOrderReport.humanWhere", implode(", ", $humanWhere));
		return "ok";
	}

	function getCustomQuery() {
			//buildSQL($filter = "", $sort = "", $limit = "", $join = "", $restrictClasses = true, $having = "")
		$query = singleton('Order')->buildSQL(Session::get("SearchableOrderReport.where"), 'Order.Created DESC', "", $join = " INNER JOIN Member on Member.ID = Order.MemberID");
		$query->groupby[] = 'Order.Created';
		$query->select[] = 'SUM(`Payment`.`Amount`) AS RealPayments';
		$query->leftJoin("Payment", '`Payment`.`OrderID` = `Order`.`ID`');
		return $query;
	}

	protected function currencyFormat($v) {
		$c = new Currency("date");
		$c->setValue($v);
		return $c->Nice();
	}

}


