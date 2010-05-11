<?php

class CoreHackDataObject extends DataObject {

	protected static $change_array = array();
		static function add_change_array($folderAndFile, $fromString, $toString) {
			self::$change_array[] =
				array(
					"folderAndFile" => $folderAndFile,
					"fromString" => $fromString,
					"toString" => $toString
				);
		}
		static function get_change_array() {return self::$change_array;}


	function requireDefaultRecords() {
		parent::requireDefaultRecords;
		$array = self::get_change_array();
		if(is_array($array)) {
			if(count($array)) {
				foreach($array as $change) {
					$fullFile = Director::getAbsFile($change["folderAndFile"]);
					$handle = fopen($fullFile, "w");
					if($handle) {
						$string = '';
						while($string .= fread($handle)) {
							//do nothing
						}
						if($string) {
							$newString = str_replace($change["fromString"], $change["toString"], $string);
							fwrite($handle, $string);
							Database::alterationMessage("change $fullFile from: <i>".$change["fromString"].'</i> to  <i>'.$change["toString"].'</i>', "created");
						}
						else {
							user_error("$fullFile does not contain any text", E_USER_ERROR);
						}
					}
					else {
						user_error("$fullFile can not be read", E_USER_ERROR);
					}
				}
			}

	}










}