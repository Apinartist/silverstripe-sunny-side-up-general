<?php
/**
 * MONTHLY SALES:
 * SELECT YEAR(OrderAttribute.Created), MONTH(OrderAttribute.Created), Sum(CalculatedTotal)
 * FROM OrderAttribute
 * 	INNER JOIN OrderItem ON OrderItem.ID = OrderAttribute.ID
 * 	INNER JOIN `Order` ON `Order`.ID = OrderAttribute.OrderID
 * WHERE `Order`.StatusID > 1
 * GROUP BY YEAR(OrderAttribute.Created), MONTH(OrderAttribute.Created)
 *
 */
class EcommerceStatistic extends DataObject {

	/**
	 * standard SS variable
	 * @var Array
	 */
	static $db = array(
		"Measure" => "Varchar(255)",
		"GroupBy" => "Varchar(255)",
		"Filter" => "Varchar(255)",
		"FilterValue" => "Text"
	);

	/**
	 * standard SS variable
	 * @var Array
	 */
	static $indexes = array(
		"Measure" => true,
		"GroupBy" => true,
		"Filter" => true
	);

	/**
	 * standard SS variable
	 * @var Array
	 */
	static $has_many = array(
		"EcommerceStatisticDataPoints" => "EcommerceStatisticDataPoint"
	);


	/**
	 *
	 * @return String
	 */
	function getTitle(){
		return self::$measure_options[$this->Measure]." ".
		self::$measure_options[$this->GroupBy]." ".
		self::$filter_options[$this->Filter]." ".
		$this->FilterValue;
	}

	/**
	 * @var Array
	 * FORMAT
	 * Code = array(
	 *    "Title"
	 *    "SelectSQL"
	 *    "JoinSQL"
	 *    "WhereSQL"
	 *    "GroupBySQL"
	 * )
	 */
	protected static $measure_options = array(
		"CountOfSales" = array(
			"Title" => "Order Count",
			"SelectSQL" => "COUNT(\"Order\".\"ID\") AS Measure",
			"JoinSQL" => "",
			"WhereSQL" => "",
			"GroupBySQL" => ""
		)
	);

	/**
	 * @var Array
	 * FORMAT
	 * Code = array(
	 *    "Title"
	 *    "SelectSQL"
	 *    "JoinSQL"
	 *    "WhereSQL"
	 *    "GroupBySQL"
	 * )
	 */
	 protected static $group_by_options = array(
		"year" => array(
			"Title" => "per Year",
			"SelectSQL" => "YEAR(\"LastEdited\") AS GroupBy",
			"JoinSQL" => "",
			"WhereSQL" => "",
			"GroupBySQL" => "YEAR(\"LastEdited\")",
		)
	);

	/**
	 * @var STRING
	 * Where statement that is included by Default
	 */
	protected static $default_where = "
		(\"Order\".\"CancelledByID\" = 0 OR \"Order\".\"CancelledByID\" IS NULL) AND SubmitLog.ClassName = 'OrderStatusLog_Submitted'";


	/**
	 * @var STRING
	 * JOIN statement that is included by Default
	 */
	protected static $default_join = "INNER JOIN \"OrderStatusLog\" AS SubmitLog ON OrderStatusLog.OrderID = Order.ID";

	function onBeforeWrite(){
		parent::onBeforeWrite();
		$query =
	}

}

