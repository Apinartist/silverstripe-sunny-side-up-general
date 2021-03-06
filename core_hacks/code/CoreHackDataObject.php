<?php

class CoreHackDataObject extends DataObject {

	static $db = array(
		"AbsoluteFile" => "Text",
		"From" => "Text",
		"To" => "Text"
	);

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
		parent::requireDefaultRecords();
		if($m = Member::currentUser()) {
			if($m->IsAdmin()) {
				$array = self::get_change_array();
				if(is_array($array)) {
					if(count($array)) {
						foreach($array as $change) {
							$hasBeenChanged = false;
							$error = '';
							$fullFile = Director::getAbsFile($change["folderAndFile"]);
							if(file_exists($fullFile)) {
								$handle = fopen($fullFile, "r");
								if($handle) {
									if(is_writable($fullFile)) {
										$string = fread($handle, filesize($fullFile));
										fclose($handle);
										if($string) {
											$oldStringIsPresent = strpos($string, $change["fromString"]);
											$newStringIsPresent = strpos($string, $change["toString"]);
											if(!$oldStringIsPresent && $newStringIsPresent) {
												$error .= "<br />OK: replacement has been made";
											}
											elseif(!$oldStringIsPresent && !$newStringIsPresent) {
												$error .= "<br />ERROR:  -- from -- string is not available, but neither is --- to ---";
											}
											elseif($oldStringIsPresent && !$newStringIsPresent) {
												$newString = str_replace($change["fromString"], $change["toString"], $string);
												if($newString) {
													if(Director::isDev()) {
														$handle = fopen($fullFile, "w");
														if($handle) {
															if(!fwrite($handle, $newString)) {
																$error .= "<br />ERROR:  could not be written";
															}
															else {
																$error .= "<br /><hr />SUCCESS";
																$hasBeenChanged = true;
															}
															fclose($handle);
														}
														else {
															$error .= '<br /><hr />it is recommend that you replace '.$change["fromString"].' with '.$change["toString"].' in '.$fullFile.'.';
														}
													}
													else {
														$error .= "<br />ERROR:  could not be loaded";
													}
												}
											}
											elseif($oldStringIsPresent && $newStringIsPresent) {
												$error .= "<br />ERROR:  both --- from --- and --- to ---- are in the file.";
											}
											if($hasBeenChanged) {
												$handle = fopen($fullFile, "r");
												if($handle) {
													$string = fread($handle, filesize($fullFile));
													fclose($handle);
													if($string) {
														$newString = str_replace($change["fromString"], $change["toString"], $string);
														if(!strpos($string, $change["toString"])) {
															$error .= "<br />ERROR: Could NOT make change even though it seemed that way";
														}
														else {
															$error .= "<br />SUCCESS: double-check OK";
														}
													}
													else {
														$error .= "<br />ERROR:  does not contain any text";
													}
												}
												else {
													$error .= "<br />ERROR:  can not be loaded for checking purposes";
												}
											}
										}
										else {
											$error .= "<br />ERROR:  does not contain any text";
										}
									}
									else {
										$error .= "<br />ERROR:  can not be written";
									}
								}
								else {
									$error .= "<br />ERROR:  can not be loaded";
								}
							}
							else {
								$error .= "<br />ERROR:  does not exist";
							}
							DB::alteration_message("<hr /><h3>core hack!</h3>trying to change $fullFile from: <br /><i>".$change["fromString"]."</i> to <br /><i>".$change["toString"]."</i>: <br /><b>".$error."</b>", "deleted");
							$obj = new CoreHackDataObject();
							$obj->From = $change["fromString"];
							$obj->To = $change["toString"];
							$obj->AbsoluteFile = $fullFile;
							$obj->write();
						}
					}
					$newString = null;
					$string = null;
				}
			}
		}
	}

}
