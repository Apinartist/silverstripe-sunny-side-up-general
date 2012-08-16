<?php

 /**
 * @author Nicolaas [at] sunnysideup.co.nz
 *
 *
 *
 *
 *
 *
 */

class WebPortfolioItem extends DataObject {

	static $db = array(
		"WebAddress" => "Varchar(255)",
		"NoLongerActive" => "Boolean",
		"NotPubliclyAvailable" => "Boolean",
		"Favourites" => "Boolean",
		"Notes" => "Varchar(255)",
		"Client" => "Varchar(255)",
		"Design" => "Varchar(255)",
		"CodingFrontEnd" => "Varchar(255)",
		"CodingBackEnd" => "Varchar(255)",
		"Copy" => "Varchar(255)",
		"Photography" => "Varchar(255)",
		"ScreenshotTaken" => "Date",
		"StartDate" => "Date",
		"EndDate" => "Date"
	);

	static $has_one = array(
		"Agent" => "WebPortfolioAgent",
		"Screenshot" => "Image",
	);

	static $many_many = array(
		"WhatWeDid" => "WebPortfolioWhatWeDidDescriptor",
	);

	static $belongs_many_many = array(
		"WhatWeDid" => "WebPortfolioWhatWeDidDescriptor",
	);

	static $defaults = array(
		"WebAddress" => "http",
		"NoLongerActive" => false,
		"Favourites" => false
	);

	public static $default_sort = "Favourites DESC, Created DESC";

	public static $searchable_fields = array(
		"WebAddress",
		"Client",
		"NoLongerActive",
		"NotPubliclyAvailable",
		"Favourites",
		"Agent.Name"
	);

	public static $summary_fields = array(
		"WebAddress",
		"Client"
	);

	public static $casting = array(
		"Title" => "Varchar"
	);

	public static $singular_name = "Item";

	public static $plural_name = "Items";


	function getCMSFields() {
		$fields = parent::getCMSFields();
		$dos = DataObject::get("WebPortfolioWhatWeDidDescriptor");
		if($dos && $this->ID) {
			$dosArray = $dos->toDropDownMap();
			$fields->addFieldsToTab(
				"Root.WhatWeDid",
				array(
					new CheckboxSetField("WhatWeDid", "Select work done", $dosArray),
					new TextField("AddWhatWeDid", "Add a job")
				)
			);
		}
		return $fields;
	}

	protected $newWhatWeDid = null;

	function onAfterWrite(){
		parent::onAfterWrite();
		if($this->newWhatWeDid) {
			$this->newWhatWeDid->WebPortfolioItem()->add($this);
			$this->WhatWeDid()->add($this->newWhatWeDid);
		}
		if(isset($_REQUEST["AddWhatWeDid"])) {
			unset($_REQUEST["AddWhatWeDid"]);
		}
		$this->newWhatWeDid = null;
	}

	function onBeforeWrite() {
		parent::onBeforeWrite();
		if(isset($_REQUEST["AddWhatWeDid"])) {
			$name = Convert::raw2sql($_REQUEST["AddWhatWeDid"]);
			if($name) {
				$this->newWhatWeDid = DataObject::get_one("WebPortfolioWhatWeDidDescriptor", "\"Name\" = '$name'");
				if(!$this->newWhatWeDid) {
					$this->newWhatWeDid = new WebPortfolioWhatWeDidDescriptor();
					$this->newWhatWeDid->Name = $name;
					$this->newWhatWeDid->write();
					//TO DO - does not work!!!
				}
			}
		}
	}

	function Title() {return $this->getTitle();}
	function getTitle() {
		return $this->WebAddress;
	}

}
