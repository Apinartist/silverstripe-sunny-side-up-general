<?php
/**
 *
 * @package ecommercextras
 * @author nicolaas[at]sunnysideup.co.nz
 */
class SearchableOrderReport extends SalesReport {

	protected $title = 'Searchable Orders';

	protected $description = 'Search all orders';

	protected static $default_from_time = "11:00 am";
		static function set_default_from_time($v) { self::$default_from_time = $v;}
		static function get_default_from_time() { return self::$default_from_time;}
		static function get_default_from_time_as_full_date_time() {return date("Y-m-d",time()) . " " . date("H:i",strtotime(self::get_default_from_time()));}
	protected static $default_until_time = "10:00 pm";
		static function set_default_until_time($v) { self::$default_until_time = $v;}
		static function get_default_until_time() { return self::$default_until_time;}
		static function get_default_until_time_as_full_date_time() {return date("Y-m-d",time()) . " " . date("H:i",strtotime(self::get_default_until_time()));}

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
		$fields->addFieldToTab("Root.Search", new DropdownTimeField("FromTime", "Start time...", self::get_default_from_time_as_full_date_time(), "H:i a"));
		$fields->addFieldToTab("Root.Search", new CalendarDateField("Until", "Until..."));
		$fields->addFieldToTab("Root.Search", new DropdownTimeField("UntilTime", "End time...", self::get_default_until_time_as_full_date_time(), "H:i a"));
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
						$t = new Time("time");
						$cleanTime = trim(preg_replace('/([ap]m)/', "", Convert::raw2sql($_REQUEST["FromTime"])));
						$t->setValue($cleanTime); //
						$exactTime = strtotime($d->format("Y-m-d")." ".$t->Nice24());
						$where[] = ' UNIX_TIMESTAMP(`Order`.`Created`) >= "'.$exactTime.'"';
						$humanWhere[] = ' Order on or after '.Date("r", $exactTime);//r = Example: Thu, 21 Dec 2000 16:01:07 +0200 // also consider: l jS \of F Y H:i Z(e)
						break;
					case "Until":
						$d = new Date("date");
						$d->setValue($value);
						$t = new Time("time");
						$cleanTime = trim(preg_replace('/([ap]m)/', "", Convert::raw2sql($_REQUEST["FromTime"])));
						$t->setValue($cleanTime); //
						$exactTime = strtotime($d->format("Y-m-d")." ".$t->Nice24());
						$where[] = ' UNIX_TIMESTAMP(`Order`.`Created`) <= "'.$exactTime.'"';
						$humanWhere[] = ' Order before or on '.Date("r", $exactTime);//r = Example: Thu, 21 Dec 2000 16:01:07 +0200 // also consider: l jS \of F Y H:i Z(e)
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
					//this has been included for SearchableProductSalesReport
					case "Product":
						$where[] = " IF(ProductVariationsForVariations.Title IS NOT NULL, CONCAT(ProductSiteTreeForVariations.Title,' : ', ProductVariationsForVariations.Title), IF(SiteTreeForProducts.Title IS NOT NULL, SiteTreeForProducts.Title, OrderAttribute.ClassName)) LIKE \"%".$value."%\"";
						$humanWhere[] = ' Product includes the phrase '.$value.'"';
						break;

					default:
					 break;
				}
			}
		}
		return $this->saveProcessedForm($having, $where, $humanWhere);
	}

	protected function saveProcessedForm($having, $where, $humanWhere) {
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
			$join = "
				INNER JOIN `Member` ON `Member`.`ID` = `Order`.`MemberID`
				LEFT JOIN Payment ON `Payment`.`OrderID` = `Order`.`ID`
			"
		);
		$query->select[] = 'SUM(`Payment`.`Amount`) RealPayments';
		if($having = Session::get("SearchableOrderReport.having")) {
			$query->having($having);
		}
		return $query;
	}

	function getExportFields() {

		$fields = array(
			"Order.ID" => "Order ID",
			"Order.Created" => "Order date and time",
			"Payment.Message" => " Reference",
			"RealPayments" => "Total Payment",
			"Member.FirstName" => "Customer first name",
			"Member.Surname" => "Customer last name",
			"Member.HomePhone" => "Customer home phone",
			"Member.MobilePhone" => "Customer mobile phone",
			"Member.Email" => "Customer phone",
			"Member.Address" => "Customer address 1",
			"Member.AddressLine2" => "Customer address 2",
			"Member.City" => "Customer City",
			"Order.Status" => "Order Status"
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
				$join = "
					INNER JOIN `Member` on `Member`.`ID` = `Order`.`MemberID`
					LEFT JOIN PAYMENT ON `Payment`.`OrderID` = `Order`.`ID`
				"
			);
			$fieldArray = $this->getExportFields();
			if(is_array($fieldArray)) {
				if(count($fieldArray)) {
					foreach($fieldArray as $key => $field) {
						$query->select[] = $key;
					}
				}
			}
			$query->select[] = "SUM(IF(Payment.Status = 'Success',`Payment`.`Amount`, 0)) RealPayments";
			if($having = Session::get("SearchableOrderReport.having")) {
				$query->having($having);
			}
			return $query;
		}
	}

	protected function currencyFormat($v) {
		$c = new Currency("currency");
		$c->setValue($v);
		return $c->Nice();
	}






}


