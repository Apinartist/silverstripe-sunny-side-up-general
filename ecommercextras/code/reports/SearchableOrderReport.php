<?php
/**
 *
 * @package ecommercextras
 * @author nicolaas[at]sunnysideup.co.nz
 */
class SearchableOrderReport extends SalesReport {

	protected $title = 'Searchable Orders';

	protected $description = 'This shows all orders with the ability to search them';

	protected static $default_from_time = "00:00";
		static function set_default_from_time($v) { self::$default_from_time = $v;}
		static function get_default_from_time() { return self::$default_from_time;}
	protected static $default_until_time = "23:59";
		static function set_default_until_time($v) { self::$default_until_time = $v;}
		static function get_default_until_time() { return self::$default_until_time;}

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
		if($this->statistic("count") > 3) {
			$fields->addFieldToTab("Root.Report", new LiteralField("stats", '<h2>Payment Statistics</h2><ul><li>'.implode('</li><li>', $stats).'</li></ul>'));
		}
		if($humanWhere = Session::get("SearchableOrderReport.humanWhere")) {
			$fields->addFieldToTab("Root.Report", new LiteralField("humanWhere", "<p>Current Search: ".$humanWhere."</p>"), "ReportDescription");
			$fields->removeByName("ReportDescription");
			$fields->addFieldToTab("Root.Search", new FormAction('clearSearch', 'Clear Search'));
		}
		$fields->addFieldToTab("Root.Search", new CheckboxSetField("Status", "Order Status", OrderDecorator::get_order_status_options()));
		$fields->addFieldToTab("Root.Search", new NumericField("OrderID", "Order ID"));
		$fields->addFieldToTab("Root.Search", new CalendarDateField("From", "From..."));
		$fields->addFieldToTab("Root.Search", new DropdownTimeField("ExportUntilTime", "End time...", self::get_default_from_time()));
		$fields->addFieldToTab("Root.Search", new CalendarDateField("Until", "Until..."));
		$fields->addFieldToTab("Root.Search", new DropdownTimeField("ExportFromTime", "Start time...", self::get_default_from_time()));
		$fields->addFieldToTab("Root.Search", new TextField("Email", "Email"));
		$fields->addFieldToTab("Root.Search", new TextField("FirstName", "First Name"));
		$fields->addFieldToTab("Root.Search", new TextField("Surname", "Surname"));
		$fields->addFieldToTab("Root.Search", new NumericField("HasMinimumPayment", "Has Minimum Payment of ..."));
		$fields->addFieldToTab("Root.Search", new NumericField("HasMaximumPayment", "Has Maximum Payment of ..."));
		$fields->addFieldToTab("Root.Search", new FormAction('doSearch', 'Apply Search'));
		//$fields->addFieldToTab("Root.ExportDetails", $this->getExportTable());

		$fields->addFieldToTab("Root.ExportDetails", new FormAction('doExport', 'Export Now'));
		return $fields;
	}

	function processform() {
		$where = array();
		$having = array();
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
						$where[] = ' `Order`.`Created` >= "'.$d->format("Y-m-d").'"';
						$humanWhere[] = ' Order after or on '.$d->long();
						break;
					case "Until":
						$d = new Date("date");
						$d->setValue($value);
						$where[] = ' `Order`.`Created` <= "'.$d->format("Y-m-d").'"';
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
						$having[] = ' RealPayments > '.intval($value);
						$humanWhere[] = ' Real Payment of at least '.$this->currencyFormat($value);
						break;
					case "HasMaximumPayment":
						$having[] = ' RealPayments < '.intval($value);
						$humanWhere[] = ' Real Payment of no more than '.$this->currencyFormat($value);
						break;
					default:
					 break;
				}
			}
		}
		Session::set("SearchableOrderReport.having", implode(" AND ", $having));
		Session::set("SearchableOrderReport.where",implode(" AND", $where));
		Session::set("SearchableOrderReport.humanWhere", implode(", ", $humanWhere));
		return "ok";
	}

	function getCustomQuery() {
			//buildSQL($filter = "", $sort = "", $limit = "", $join = "", $restrictClasses = true, $having = "")
		$where = Session::get("SearchableOrderReport.where");
		if(trim($where)) {
		 $where = " ( $where ) AND ";
		}
		$where .= '(`Payment`.`Status` = "Success" OR `Payment`.`Status` = "Pending" OR  `Payment`.`Status` IS NULL)';
		$query = singleton('Order')->buildSQL(
			$where,
			$sort = '`Order`.`Created` DESC',
			$limit = "",
			$join = " INNER JOIN `Member` on `Member`.`ID` = `Order`.`MemberID`"
		);
		$query->select[] = 'SUM(`Payment`.`Amount`) RealPayments';
		if($having = Session::get("SearchableOrderReport.having")) {
			$query->having($having);
		}
		$query->leftJoin("Payment", '`Payment`.`OrderID` = `Order`.`ID`');
		return $query;
	}

	function getExportFields() {

		$fields = array(
			"Order.ID" => "Order ID",
			"Order.Created" => "Order date and time",
			"Payment.Message" => " Reference",
			//"Total" => "Total Order Amount",
			"Member.FirstName" => "Customer first name",
			"Member.Surname" => "Customer last name",
			"Member.HomePhone" => "Customer home phone",
			"Member.MobilePhone" => "Customer mobile phone",
			"Member.Email" => "Customer phone",
			"Member.Address" => "Customer address 1",
			"Member.AddressLine2" => "Customer address 2",
			"Member.City" => "Customer City",
			"Order.Status" => "Order Status"
			//"PlaintextProductSummary" => "Products"
			//"PlaintextModifierSummary" => "Additions",
			//"PlaintextLogDescription" => "Dispatch Notes"
		);
		return $fields;
	}

	function getExportQuery() {
		if("SalesReport" == $this->class) {
			user_error('Please implement getExportFields() on ' . $this->class, E_USER_ERROR);
		}
		else {
			//buildSQL($filter = "", $sort = "", $limit = "", $join = "", $restrictClasses = true, $having = "")
			$where = Session::get("SearchableOrderReport.where");
			if(trim($where)) {
			 $where = " ( $where ) AND ";
			}
			$where .= '(`Payment`.`Status` = "Success" OR `Payment`.`Status` = "Pending" OR  `Payment`.`Status` IS NULL)';
			$query = singleton('Order')->buildSQL(
				$where,
				$sort = '`Order`.`Created` DESC',
				$limit = "",
				$join = " INNER JOIN `Member` on `Member`.`ID` = `Order`.`MemberID`"
			);
			$fieldArray = $this->getExportFields();
			if(is_array($fieldArray)) {
				if(count($fieldArray)) {
					foreach($fieldArray as $key => $field) {
						$query->select[] = $key;
					}
				}
			}
			$query->select[] = 'SUM(`Payment`.`Amount`) RealPayments';
			if($having = Session::get("SearchableOrderReport.having")) {
				$query->having($having);
			}
			$query->leftJoin("Payment", '`Payment`.`OrderID` = `Order`.`ID`');
			return $query;
		}
	}

	protected function currencyFormat($v) {
		$c = new Currency("date");
		$c->setValue($v);
		return $c->Nice();
	}






}


