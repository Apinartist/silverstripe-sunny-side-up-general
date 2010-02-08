<?php

/**
 * @author nicolaas [at] sunny side up . co . nz
 * @description: adds all members to an "All Users" group.
 */
class MemberManagementGroupCleanup extends HourlyTask {

	function process() {
		$this->cleanup();
	}

	protected static $name_for_all_users_group = "All Users";
		static function set_name_for_all_users_group($v) {self::$name_for_all_users_group = Convert::raw2sql($v);}
		static function get_name_for_all_users_group() {return self::$name_for_all_users_group;}

	//must be set to TRUE exactly, just to make it a bit safer (this is a pretty powerful command)
	protected static $automatically_delete_members_without_group = false;
		static function set_automatically_delete_members_without_group ($v) {self::$automatically_delete_members_without_group = $v;}
		static function get_automatically_delete_members_without_group () {return self::$automatically_delete_members_without_group === TRUE;}

	function cleanup() {

		//basic cleanup of useless entries in Group_Members
		$sql = 'DELETE `Group_Members` FROM `Group_Members` LEFT JOIN `Member` ON `Group_Members`.`MemberID` = `Member`.`ID` WHERE `Member`.`ID` IS NULL;';
		DB::query($sql);
		$sql = 'DELETE `Group_Members` FROM `Group_Members` LEFT JOIN `Group` ON `Group_Members`.`GroupID` = `Group`.`ID` WHERE `Group`.`ID` IS NULL;';
		DB::query($sql);

		$allUsersGroup = DataObject::get_one("Group", "Title = '".self::$name_for_all_users_group."'");
		if(!$allUsersGroup) {
			$allUsersGroup = new Group();
			$allUsersGroup->Title = self::$name_for_all_users_group;
			$allUsersGroup->Sort = 999999;
			$allUsersGroup->write();
			Database::alteration_message("created ".self::$name_for_all_users_group." group", "created");
		}
		$allUsersGroupID = $allUsersGroup->ID;

		//delete members without group
		if(self::get_automatically_delete_members_without_group()) {
			$query = new SQLQuery('*', array('Group_Members'), 'GroupID = ' . $allUsersGroupID);
			$query->delete = true;
			$query->execute();
			$sql = "Delete Group_Members From Group_Members  WHERE GroupID = ". $allUsersGroupID;
			DB::query($sql);
			$unlistedMembers = DataObject::get(
				"Member",
				$where = "Group_Members.ID IS NULL",
				$sort = null,
				$join = "LEFT JOIN Group_Members ON Group_Members.MemberID = Member.ID"
			);
			if($unlistedMembers) {
				foreach($unlistedMembers as $member) {
					Database::alteration_message("Deleting Member: <i>".$member->getName()."</i> as he/she is not listed in any groups.", "deleted");
					$member->delete();
				}
			}
		}

		//load current combos
		$groupMemberCombos = array();
		$groupMemberCombosToDelete = array();
		$groupMemberCombosToAdd = array();
		$allCombos = DB::query("Select ID, MemberID, GroupID from Group_Members;");
		//make an array of all combos
		foreach($allCombos as $combo) {
			if(isset($groupMemberCombos[$combo["MemberID"]][$combo["GroupID"]])) {
				$groupMemberCombosToDelete[] = $combo["ID"];
			}
			$groupMemberCombos[$combo["MemberID"]][$combo["GroupID"]] = $combo["ID"];
		}
		$allCombos = $combo = null;
		//find all members that are not listed in all-users groups
		foreach($groupMemberCombos as $memberID => $memberGroup) {
			if(!isset($memberGroup[$allUsersGroupID])) {
				$groupMemberCombosToAdd[] = $memberID;
			}
		}

		//add all members that are not listed in any groups
		$extraWhere = '';
		if(count($groupMemberCombosToAdd)) {
			$extraWhere = "OR MemberID IN (".implode(",",$groupMemberCombosToAdd).")";
		}
		$unlistedMembers = DataObject::get(
			"Member",
			$where = "Group_Members.ID IS NULL ".$extraWhere,
			$sort = null,
			$join = "LEFT JOIN Group_Members ON Group_Members.MemberID = Member.ID"
		);

		//add combos
		if($unlistedMembers) {
			$existingMembers = $allUsersGroup->Members();
			foreach($unlistedMembers as $member) {
				$existingMembers->add($member);
			}
		}

		//delete double-entries
		if($number = count($groupMemberCombosToDelete)) {
			$query = new SQLQuery('*', array('Group_Members'), 'ID IN('.implode(",",$groupMemberCombosToDelete).')');
			$query->delete = true;
			$query->execute();
			Database::alteration_message("deleted double entries (".$count.") in group members table", "deleted");
		}



	}


}

