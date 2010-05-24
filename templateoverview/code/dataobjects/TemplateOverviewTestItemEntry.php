<?php

class TemplateOverviewTestItemEntry extends DataObject {

	static $status = array(
		"Completed" => "Boolean",
		"PositiveResult" => "Boolean"
	);

	static $has_one = array(
		"Member" => "Member",
		"TemplateOverviewTestItem" => "TemplateOverviewTestItem"
	);

	public static $searchable_fields = array(
		"TemplateOverviewTestItem",
		"Member",
		"Completed",
		"PositiveResult"
	);
	public static $field_labels = array(
		"Completed" => "Test done",
		"PositiveResult" => "Test OK",
		"Member" => "Tested By",
		"TemplateOverviewTestItem" => "Item Tested"
	);

	public static $singular_name = "Test Item Entry";

	public static $plural_name = "Test Item Entries";
	//CRUD settings
	public static $default_sort = "LastEdited DESC, Created DESC";


	function onBeforeWrite() {
		parent::onBeforeWrite();
		$this->MemberID = Member::currentMemberID();
	}

}