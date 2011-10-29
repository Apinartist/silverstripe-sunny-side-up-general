<?php
/**
 * @author Nicolaas [at] sunnysideup.co.nz
 * @package Webquote
 * @sub-package Webquote
 */

class WebPortfolioWhatWeDidDescriptor extends DataObject {

	public static $db = array(
		"Name" => "Varchar(255)",
		"Description" => "Text",
		"SortNumber" => "Int"
	);

	public static $belongs_many_many = array(
		"WebPortfolioItem" => "WebPortfolioItem"
	);

	public static $default_sort = "SortNumber";

	public static $searchable_fields = array(
		"Name" => "PartialMatchFilter",
		"SortNumber"
	);

	public static $summary_fields = array(
		"Name",
		"SortNumber"
	);

	public static $singular_name = "What We Did Descriptor";

	public static $plural_name = "What We Did Descriptors";

	function Link() {
		$link = '';
		if($page = DataObject::get_one("WebPortfolioPage")) {
			$link = $page->Link().'show/'.$page->generateURLSegment($this->Name)."/";
		}
		return $link;
	}

	function getCMSFields() {
		$fields = parent::getCMSFields();

		if($this->ID) {
			$dos = DataObject::get("WebPortfolioWhatWeDidDescriptor", "WebPortfolioWhatWeDidDescriptor.ID <> ".$this->ID);
			if($dos) {
				$dosArray = $dos->toDropDownMap("ID", "Name", "-- do not merge --");
				$fields->addFieldToTab("Root.Merge", new DropdownField("MergeID", "Merge <i>$this->Name</i> into:", $dosArray));
			}
		}
		return $fields;
	}


	protected $mergeInto = null;

	function onAfterWrite(){
		parent::onAfterWrite();
		if($this->mergeInto) {
			DB::query("UPDATE \"WebPortfolioItem_WhatWeDid\" SET \"WebPortfolioWhatWeDidDescriptorID\" = ".$this->mergeInto->ID." WHERE \"WebPortfolioWhatWeDidDescriptorID\"  = ".$this->ID);
			$this->delete();
		}
		if(isset($_REQUEST["MergeID"])) {
			unset($_REQUEST["MergeID"]);
		}
		$this->mergeInto = null;
	}

	function onBeforeWrite() {
		parent::onBeforeWrite();
		if(isset($_REQUEST["MergeID"])) {
			$mergeID = intval($_REQUEST["MergeID"]);
			if($mergeID) {
				$this->mergeInto = DataObject::get_by_id("WebPortfolioWhatWeDidDescriptor", $mergeID);
			}
		}
	}


}
