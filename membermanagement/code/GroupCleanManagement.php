<?php

/**
 * @author nicolaas [at] sunny side up . co . nz
 * @description: adds all members to an "All Users" group.
 */
class GroupCleanManagement extends DataObjectDecorator {

	protected static $name_for_all_users_group = "All Users";
	static function set_name_for_all_users_group($v) {
		self::$name_for_all_users_group = $v;
	}

	function requireDefaultRecords() {
		parent::requireDefaultRecords();
		$allUsersGroup = DataObject::get_one("Group", "Name = '".self::$name_for_all_users_group."'");
		if(!$group) {
			$allUsersGroup = new Group();
			$allUsersGroup->Title = self::$name_for_all_users_group;
			$allUsersGroup->write();
		}
		$allUsersGroupID = $group->ID;

		$groupMemberCombos = array();
		$groupMemberCombosToDelete = array();
		$groupMemberCombosToAdd = array();
		$allCombos = DB::query("Select ID, MemberID, GroupID from Group_Members;");
		//make an array of all combos
		foreach($allCombos as $combo) {
			if(isset($groupMemberCombos[$combo["MemberID"]][$combo["GroupID"]])) {
				$groupMemberCombosToDelete[] = $combo["ID"];
			}
			$groupMemberCombos[$row["MemberID"]][$row["GroupID"]] = $combo["ID"];
		}
		$allCombos = $combo = null;
		//find all members that are not listed in all-users groups
		foreach($groupMemberCombos as $memberID => $memberGroup) {
			if(!isset($memberGroup[$allUsersGroupID])) {
				$groupMemberCombosToAdd[] = $memberID;
			}
		}
		//add all members that are not listed in any groups
		$unlistedMembers = DataObject::get(
			"Member",
			$where = "Group_Members.ID IS NULL OR MemberID IN (".implode(",",$groupMemberCombosToAdd).")",
			$sort = null,
			$join = "LEFT JOIN Group_Members ON Group_Members.MemberID = Member.ID"
		);

		$existingMembers = $allUsersGroup->Members();
		// method 1: Add many by iteration
		foreach($unlistedMembers as $member) {
			$existingMembers->add($member);
		}

	}

}

