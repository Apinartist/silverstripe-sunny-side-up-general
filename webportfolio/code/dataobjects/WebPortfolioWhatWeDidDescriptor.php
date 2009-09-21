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
			$link = $page->Link().'show/'.$page->generateURLSegment($this->Name);
		}
		return $link;
	}

}