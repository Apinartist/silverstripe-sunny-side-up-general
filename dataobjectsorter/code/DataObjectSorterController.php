<?php
/**
 *@author nicolaas [at] sunnysideup.co.nz
 *@description: allows you to sort dataobjects, you need to provide them in this way: http://www.mysite.com/dataobjectsorter/[dataobjectname]/
 *
 *@package: dataobjectsorter
 **/

class DataObjectSorterController extends Controller{

	static $allowed_actions = array("sort", "startsort", "dodataobjectsort" );

	function sort() {
		return array();
	}

	function startsort() {
		return array();
	}

	function dodataobjectsort() {
		$class = Director::URLParam("ID");
		if($class) {
			if(class_exists($class)) {
				$obj = DataObject::get_one($class);
				return $obj->dodataobjectsort();
			}
			else {
				user_error("$class does not exist", E_USER_WARNING);
			}
		}
		else {
			user_error("Please make sure to provide a class to sort e.g. http://www.sunnysideup.co.nz/dataobjectsorter/MyLongList - where MyLongList is the DataObject you want to sort.", E_USER_WARNING);
		}
	}

	public function Children() {
		$bt = defined('DB::USE_ANSI_SQL') ? "\"" : "`";
		$class = Director::URLParam("ID");
		if($class) {
			if(class_exists($class)) {
				$where = '';
				$filterField = Convert::raw2sql(Director::URLParam("OtherID"));
				$filterValue = Convert::raw2sql(Director::URLParam("ThirdID"));
				$titleField = Convert::raw2sql(Director::URLParam("FourthID"));
				if($filterField && $filterValue) {
					$array = explode(",",$filterValue);
					if(is_array($array) && count($array)) {
						$where = "{$bt}$filterField{$bt} IN ($filterValue)";
					}
					else {
						$where = "{$bt}$filterField{$bt} = '$filterValue'";
					}
				}
				elseif(is_numeric($filterField)) {
					$where = "{$bt}ParentID{$bt} = '$filterField'";
				}
				$sort = "{$bt}Sort{$bt} ASC";
				$objects = DataObject::get($class, $where, $sort);
				if($objects && $objects->count()) {
					foreach($objects as $obj) {
						if($titleField) {
							$method = "get".$titleField;
							if($obj->hasMethod($method)) {
								$obj->SortTitle = $obj->$method();
							}
							else {
								$method = $titleField;
								if($obj->hasMethod($method)) {
									$obj->SortTitle = $obj->$method();
								}
								else {
									$obj->SortTitle = $obj->$titleField;
								}
							}
						}
						else {
							$obj->SortTitle = $obj->getTitle();
						}
					}
					if(!$obj->hasField("Sort") && !$obj->hasField("AlternativeSortNumber")) {
						user_error("No field Sort or AlternativeSortNumber was found on data object: ".$class, E_USER_WARNING);
					}
					self::add_requirements($class);
					return $objects;
				}
				else {
					return null;
				}
			}
			else {
				user_error("$class does not exist", E_USER_WARNING);
			}
		}
		else {
			user_error("Please make sure to provide a class to sort e.g. http://www.sunnysideup.co.nz/dataobjectsorter/MyLongList - where MyLongList is the DataObject you want to sort.", E_USER_WARNING);
		}
	}


	function add_requirements($className) {
		Requirements::javascript(THIRDPARTY_DIR."/jquery/jquery.js");
		Requirements::javascript("dataobjectsorter/javascript/jquery-ui-1.7.2.custom.min.js");
		Requirements::javascript("dataobjectsorter/javascript/dataobjectsorter.js");
		Requirements::themedCSS("dataobjectsorter");
		Requirements::customScript('var DataObjectSorterURL = "'.Director::absoluteURL("dataobjectsorter/dodataobjectsort/".$className."/").'";', 'initDataObjectSorter');
	}

	/**
	 * returns a link for sorting objects. You can use this in the CMS like this....
	 * <code>
	 * if(class_exists("DataObjectSorterController")) {
	 * 	$fields->addFieldToTab("Root.Position", new LiteralField("AdvertisementsSorter", DataObjectSorterController::popup_link("Advertisement", $filterField = "", $filterValue = "", $linkText = "sort ".Advertisement::$plural_name, $titleField = "FullTitle")));
	 * }
	 * else {
	 * 	$fields->addFieldToTab("Root.Position", new NumericField($name = "Sort", "Sort index number (the lower the number, the earlier it shows up"));
	 * }
	 * </code>
	 *
	 * @param String $className - DataObject Class Name you want to sort
	 * @param String | Int $filterField - Field you want to filter for OR ParentID number (i.e. you are sorting children of Parent with ID = $filterField)
	 * @param String $filterValue - filter field should be equal to this integer OR string. You can provide a list of IDs like this: 1,2,3,4 where the filterFiel is probably equal to ID or MyRelationID
	 * @param String $linkText - text to show on the link
	 * @param String $titleField - field to show in the sort list. This defaults to the DataObject method "getTitle", but you can use "name" or something like that.
	 * @return String
	 */
	function popup_link($className, $filterField = "", $filterValue = "", $linkText = "sort this list", $titleField = "") {
		$obj = singleton($className);
		if($obj->canEdit()) {
			$link = 'dataobjectsorter/sort/'.$className."/";
			if($filterField) {
				$link .= $filterField.'/';
			}
			if($filterValue) {
			 $link .= $filterValue.'/';
			}
			if($titleField) {
				$link .= $titleField.'/';
			}
			return '
			<a href="'.$link.'" onclick="window.open(\''.$link.'\', \'sortlistFor'.$className.$filterField.$filterValue.'\',\'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=1,width=600,height=600,left = 440,top = 200\'); return false;">'.$linkText.'</a>';
		}
	}



}
