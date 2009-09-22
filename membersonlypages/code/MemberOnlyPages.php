<?php

class MembersOnlyPage extends Page {

	static $add_action = 'Intranet Page';

	static $icon = 'mysite/images/treeicons/MembersOnlyPage';

	static $default_parent = 'MembersOnlyPage';

	static $allowed_children = array("MembersOnlyPage");

	protected static $group_code = "intranet-members";

	protected static $group_name = "intranet members";

	protected static $permission_code = "INTRANET_USERS";

	static $defaults = array(
		"ProvideComments" => 1,
		"ShowInSearch" => 0
	);


	public function getCMSFields() {
		$fields = parent::getCMSFields();
		return $fields;
	}

	public function canView() {
		if ($member = Member::currentUser()) {
			if($member->isAdmin() || Permission::checkMember($member, self::$permission_code)) {
				return true;
			}
		}
		return false;
	}

	public function getShowInMenus() {
		return $this->canView();
	}

	public function ShowInMenus() {
		return $this->getShowInMenus();
	}

	public function getShowInSearch() {
		return $this->canView();
	}

	public function ShowInSearch() {
		return $this->getShowInMenus();
	}

	public function requireDefaultRecords() {
		parent::requireDefaultRecords();
		if(!$intranetGroup = DataObject::get_one("Group", "Code = 'intranet-members'")) {
			$group = new Group();
			$group->Code = self::$group_code;
			$group->Title = self::$group_name;
			$group->write();

			Permission::grant( $group->ID, self::$permission_code );
			Database::alteration_message(self::$group_name.'group created',"created");
		}
		else if(DB::query("SELECT * FROM Permission WHERE `GroupID` = '".$intranetGroup->ID."' AND `Code` LIKE '".self::$permission_code."'")->numRecords() == 0 ) {
			Permission::grant($intranetGroup->ID, self::$permission_code);
		}
		if(!DataObject::get_one("MembersOnlyPage", "`ParentID` < 1")) {
			$intranetholder = new MembersOnlyPage();
			$intranetholder->Title = "Intranet";
			$intranetholder->URLSegment = "Intranet";
			$intranetholder->Content = "<p>Welcome to your Intranet</p>";
			$intranetholder->Status = "Published";
			$intranetholder->write();
			$intranetholder->publish("Stage", "Live");
		}
	}

}

class MembersOnlyPage_Controller extends Page_Controller {

	public function init() {
		parent::init();
	}


}

