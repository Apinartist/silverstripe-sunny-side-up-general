<?php

/**
 *@author nicolaas [at] sunnysideup.co.nz
 *@description: this class can be used to associate sidebar elements with templates
 *
 *
 **/

class SideBarOption extends DataObject{

	protected static $side_bar_options = array();
		static function set_side_bar_options ($v) {self::$side_bar_options = $v;}
		static function get_side_bar_options () {return self::$side_bar_options;}

	static $db = array(
		"Code" => "Varchar(50)",
		"Title" => "Varchar(50)"
	);

	static $many_many = array(
		"TemplateOverviewDescription" => "TemplateOverviewDescription"
	);


	function requireDefaultRecords() {
		parent::requireDefaultRecords();
		$bt = defined('DB::USE_ANSI_SQL') ? "\"" : "`";
		$array = self::get_side_bar_options();
		if(count($array)) {
			foreach($array as $key => $option) {
				if(!DataObject::get("SideBarOption", "{$bt}Code{$bt} = '$key'")) {
					$obj = new SideBarOption();
					$obj->Code = $key;
					$obj->Title = $option;
					$obj->write();
					DB::alteration_message($obj->ClassName." created new entry: ".$obj->Title, 'created');
				}
			}
		}
	}


}
