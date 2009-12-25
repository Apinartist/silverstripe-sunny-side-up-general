<?php

class SearchPlusPage extends Page {

	static $add_action = 'Search Plus Page';

	static $can_be_root = true;

	static $icon = 'mysite/images/treeicons/SearchPlusPage';

	public static $db = array();

	public function canCreate() {
		return !DataObject::get_one("SiteTree", 'ClassName = "SearchPlusPage"');
	}

	public function canDelete() {
		return false;
	}

	public function getCMSFields() {
		$fields = parent::getCMSFields();
		return $fields;
	}




}

class SearchPlusPage_Controller extends Page_Controller {

	public function init() {
		parent::init();
	}

	function popularsearchwords() {
		if(!Permission::check("ADMIN")) {
			Security::permissionFailure($this, _t('Security.PERMFAILURE',' This page is secured and you need administrator rights to access it. Enter your credentials below and we will send you right along.'));
			return;
		}
		$days = intval(Director::URLParam("ID"));
		if(!$days) {
			$days = 100;
		}
		$countMin = intval(Director::URLParam("OtherID")+0);
		$data = DB::query("SELECT COUNT(ID) count, Title FROM `SearchHistory` WHERE Created > ( NOW() - INTERVAL $days DAY ) GROUP BY `Title`  HAVING COUNT(ID) >= $countMin ORDER BY count DESC ");
		$do = new DataObject();
		$do->Data = new DataObjectSet();
		if(!$countMin) $countMin++;
		$v = "<h1>Search Phrases entered at least $countMin times during the last $days days</h1><table>";
		$list = array();
		foreach($data as $key => $row) {
			if(!$key) {
				$multiplier = 700 / $row["count"];
			}
			$multipliedWidth = floor($row["count"]*$multiplier);
			$list[$row["count"]."-".$key] = $row["Title"];
			$do->Data->push(new ArrayData($row));
			$v .=' <tr><td style="text-align: right; width: 350px;">'.$row["Title"].'</td><td style="background-color: grey"><div style="width: '.$multipliedWidth.'px; background-color: #0066CC;">'.$row["count"].'</div></td></tr>';
		}
		$v .= '</table>';
		asort($list);
		$v .= "<h1>A - Z</h1><table>";
		foreach($list as $key => $title) {
			$array = explode("-", $key);
			$multipliedWidth = $array[0]*$multiplier;
			$v .=' <tr><td style="text-align: right; width: 350px;">'.$title.'</td><td style="background-color: grey"><div style="width: '.$multipliedWidth.'px; background-color: #0066CC;">'.$array[0].'</div></td></tr>';
		}
		$v .= '</table>';
		return $v;
	}



}

