<?php
/**
 * @author Nicolaas [at] sunnysideup.co.nz
 * @package Webquote
 * @sub-package Webquote
 */

class WebQuote extends DataObject {

	public static $db = array(
		"Name" => "Varchar(255)",
		"IDSelection" => "Text",
		"SavedQuote" => "HTMLText",
		"NotesAndQuestions" => "Text",
		"TemplatesRequired" => "Text",
		"Goals" => "Text",
		"DeadLine" => "Text"
	);

	public static $casting = array();

	public static $indexes = array(
		"Name" => true
	);

	public static $defaults = array();

	public static $default_records = array();

	public static $has_one = array(
		"CurrencyConversion" => "WebQuoteCurrencyConversion",
		"DesignOption" => "WebQuoteDesignOption",
		"TimeframeOption" => "WebQuoteTimeframeOption",
		"WeDeliverYouDeliver" => "WebQuoteWeDeliverYouDeliver",
		"Member" => "Member",
		"Parent" => "WebQuotePage"
	);

	public static $has_many = null;

	public static $many_many = array(
		"SalesItem" => "WebQuoteSalesItem"
	);

	public static $many_many_extraFields = null;

	public static $belongs_many_many = null;

	public static $default_sort = "Code";


	public static $searchable_fields = array(
		"Code",
		"Name" => "PartialMatchFilter"
	);

	public static $field_labels = array(
		"Code" => "Code",
		"Name" => "Long Name",
		"DefaultValue" => "exchange rate if no live value is available",
		"Multiplier" => "additional mupliplier to account for additional cost (e.g. 1.1)"
	);

	public static $summary_fields = array(
		"Code",
		"Name",
		"DefaultValue",
		"Multiplier"
	);

	public static $singular_name = "Web Quote";

	public static $plural_name = "Web Quotes";

	public function onBeforeWrite() {
		if(!$this->ParentID) {
			if($parent = DataObject::get_one("WebQuotePage");
				$this->ParentID = $parent->ID;
			}
		}
		parent::onBeforeWrite();
	}

}