<?php
/*
	@ see http://code.google.com/p/swfobject/wiki/documentation
	@ see http://www.swffix.org/swfobject/generator/
*/

class FlashObjectDOD extends DataObjectDecorator {

	function extraDBFields(){
		return array(
			'db' =>   array(
				"Title" => "Varchar(255)"
			),
			'has_one' => array(
				"FlashFile" => "File"
			),
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
		if( ! FlashObject::has_external_flash_file()) {
			$fields->addFieldToTab("Root.Content.FlashObject", new FileIFrameField('FlashFile', 'File'));
			$fields->addFieldToTab("Root.Content.FlashObject", new TextField('Title', 'Title'));
		}
		return $fields;
 }

	public function CreateFlashObject() {
		if($this->owner->FlashFileID) {
			$obj = new FlashObject();
			$flashFile = $this->owner->FlashFile();
			return $obj->CreateFlashObject($this->owner->Title, null, $flashFile->Filename);
		}
		else {
			return new DataObjectSet();
		}
  }

	/*
	legacy function
	*/

	public function FlashObjectData() {
		return $this->CreateFlashObject();
	}

}



