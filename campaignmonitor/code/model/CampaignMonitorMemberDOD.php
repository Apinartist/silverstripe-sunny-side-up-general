<?php

/**
 *@author nicolaas [at] sunnysideup.co.nz
 *TO DO: only apply the on afterwrite to people in the subscriber group.
 *
 **/

class CampaignMonitorMemberDOD extends DataObjectDecorator {


	function extraStatics() {
	}

	function onAfterWrite() {
		parent::onAfterWrite();
		$this->synchroniseCMDatabase();
	}

	protected function synchroniseCMDatabase() {
		$lists = DataObject::get("CampaignMonitorSignupPage");
		if($lists) {
			foreach($lists as $list) {

				if($list->GroupID) {
					//external database
					$CMWrapper = new CampaignMonitorWrapper();
					$CMWrapper->setListID ($list->ListID);
					$userIsUnconfirmed = $CMWrapper->subscriberIsUnconfirmed($this->owner->Email);
					if($userIsUnconfirmed && $userIsUnconfirmed != "?") {
						// do nothing
					}
					else {
						$userIsSubscribed = $CMWrapper->subscriberIsSubscribed($this->owner->Email);

						if(!$this->owner->inGroup($list->GroupID, $strict = TRUE)) {
							//not in group, but is subscribed.... unsubscribe....
							if($userIsSubscribed && $userIsSubscribed != "?"){
								if (!$CMWrapper->subscriberUnsubscribe($this->owner->Email)) {
									user_error(_t('CampaignMonitorMemberDOD.GETCMSMESSAGESUBSATTEMPTFAILED', 'Unsubscribe attempt failed: ') .$this->owner->Email.", ". $CMWrapper->lastErrorMessage, E_USER_WARNING);
								}
							}
						}
						else {
							//
							$userIsUnsubscribed = $CMWrapper->subscriberIsUnsubscribed($this->owner->Email);
							if((!$userIsSubscribed && $userIsSubscribed != "?") && (!$userIsUnsubscribed || $userIsUnsubscribed =! "?" )) {
								if (!$CMWrapper->subscriberAdd($this->owner->Email, $this->owner->getName())) {
									user_error(_t('CampaignMonitorMemberDOD.GETCMSMESSAGESUBSATTEMPTFAILED', 'Subscribe attempt failed: ') .$this->owner->Email.", ". $CMWrapper->lastErrorMessage, E_USER_WARNING);
								}
							}
						}
					}
				}
			}
		}
	}

  public function addCampaignMonitorList($page, $alsoSynchroniseCMDatabase = false) {
    //internal database
		if($page->GroupID) {
			if($gp = DataObject::get_by_id("Group", $page->GroupID)) {
				$groups = $this->owner->Groups();
				if($groups) {
					$this->owner->Groups()->add($gp);
					if($alsoSynchroniseCMDatabase) {
						$this->synchroniseCMDatabase();
					}
				}
			}
		}
  }

  public function removeCampaignMonitorList($page, $alsoSynchroniseCMDatabase = false) {
    //internal database
		if($page->GroupID) {
			if($gp = DataObject::get_by_id("Group", $page->GroupID)) {
				$groups = $this->owner->Groups();
				if($groups) {
					$this->owner->Groups()->remove($gp);
					if($alsoSynchroniseCMDatabase) {
						$this->synchroniseCMDatabase();
					}
				}
			}
		}
  }

	function CampaignMonitorSubscriptionsPageIdList() {
		$array = Array();
		if($set = $this->owner->Groups()) {
			$idList = $set->getIdList();
			if(is_array($idList) && count($idList)) {
				$pages = DataObject::get("CampaignMonitorSignupPage", "GroupID IN(".implode(",", $idList).")");
				if($pages) {
					foreach($pages as $page) {
						$array[$page->ID] = $page->ID;
					}
				}
			}
		}
		return $array;
	}

}
