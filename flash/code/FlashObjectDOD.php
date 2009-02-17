<?php
/*
	@ see http://code.google.com/p/swfobject/wiki/documentation
	@ see http://www.swffix.org/swfobject/generator/
*/

class FlashObjectDOD extends DataObjectDecorator {

	static $UseDynamicInsert = true;

	function extraDBFields(){
		return array(
			'db' =>   array("AlternativeContent" => "HTMLText", "Title" => "Varchar(255)"),
			'has_one' => array("FlashFile" => "File"),
		);
	}

	/**
	* At the moment augmentSQL does nothing.
	*/
	function augmentSQL(SQLQuery &$query) {	}

	/**
	* At the moment augmentDatabase does nothing.
	*/
	function augmentDatabase() {}

	function updateCMSFields(FieldSet &$fields) {
		if(!self::$externalFlashFile) {
			$fields->addFieldToTab("Root.Content.FlashObject", new FileIFrameField('FlashFile', 'File'));
			if(!self::$AlternativeContent) {
				$fields->addFieldToTab("Root.Content.FlashObject", new TextField('AlternativeContent', 'AlternativeContent'));
			}
		}
		return $fields;
 }

	public function CreateFlashObject() {
		$obj = new FlashObject();
		return $obj->CreateFlashObject($this->owner->Title, $this->owner->FlashFile->Filename, $this->owner->AlternativeContent);
  }


}



