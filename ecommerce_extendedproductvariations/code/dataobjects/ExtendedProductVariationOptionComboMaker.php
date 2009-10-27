<?php


class ExtendedProductVariationOptionComboMaker extends Object {

	protected $fullArray = array();

	protected $optionArray = array();

	protected $groupCount = 0;


	//init:
	function addGroups($groups) {
		$this->groupCount = 0;
		$item = array();
		foreach($groups as $group) {
			if($group->IncludeOptionsAsCombinedProductVariations) {
				$options = DataObject::get("ExtendedProductVariationOption", "`ParentID` = ".$group->ID);
				if($options) {
					foreach($options as $option) {
						$this->fullArray[$this->groupCount][] = $option->ID;
					}
					$this->groupCount++;
				}
			}
		}
	}

	function groupCount() {
		return $this->groupCount;
	}

	function meltOptions($number = 0, $startIDString = '') {
		if(count($this->fullArray)) {
			foreach($this->fullArray[$number] as $option) {
				$next = $number + 1;
				if(isset($this->fullArray[$next])) {
					$option .= ",";
					$this->meltOptions($next, $startIDString.$option);
				}
				else {
					$this->optionArray[] = $startIDString.$option;
				}
			}
		}
	}

	function finalise() {
		$this->meltOptions();
		return $this->optionArray;
	}

}