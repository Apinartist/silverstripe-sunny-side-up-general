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

		$componentSet = $this->owner->CampaignMonitorSubscriptions();
		$campaignMonitorSubscriptions = $componentSet->getIdList();
		$lists = DataObject::get("CampaignMonitorSignupPage");
		if($lists) {
			foreach($lists as $list) {
				//external database
				$CMWrapper = new CampaignMonitorWrapper();
				$CMWrapper->setListID ($list->ListID);
				$userIsUnconfirmed = $CMWrapper->subscriberIsUnconfirmed($this->owner->Email);
				if($userIsUnconfirmed && $userIsUnconfirmed != "?") {
					// do nothing
				}
				else {
					$userIsSubscribed = $CMWrapper->subscriberIsSubscribed($this->owner->Email);
					if(!isset($campaignMonitorSubscriptions[$list->ID])) {
						if($userIsSubscribed && $userIsSubscribed != "?"){
							if (!$CMWrapper->subscriberUnsubscribe($this->owner->Email)) {
								user_error(_t('CampaignMonitorMemberDOD.GETCMSMESSAGESUBSATTEMPTFAILED', 'Unsubscribe attempt failed: ') .$this->owner->Email.", ". $CMWrapper->lastErrorMessage, E_USER_WARNING);
							}
						}
					}
					else {
						$userIsUnsubscribed = $CMWrapper->subscriberIsUnsubscribed($this->owner->Email);
						if(!$userIsSubscribed && !$userIsUnsubscribed && $userIsUnsubscribed =! "?" && $userIsSubscribed != "?") {
							if (!$CMWrapper->subscriberAdd($this->owner->Email, $this->owner->getName())) {
								user_error(_t('CampaignMonitorMemberDOD.GETCMSMESSAGESUBSATTEMPTFAILED', 'Subscribe attempt failed: ') .$this->owner->Email.", ". $CMWrapper->lastErrorMessage, E_USER_WARNING);
							}
						}
					}
				}
			}
		}
	}

  public function addCampaignMonitorList($page, $alsoSynchroniseCMDatabase = false) {
    //internal database
		$existingLists = $this->owner->CampaignMonitorSubscriptions();
		if(!$existingLists) {
			user_error("can find relationship", E_USER_NOTICE);
		}
		else {
	    $existingLists->add($page->ID);
		}
		if($alsoSynchroniseCMDatabase) {
			$this->synchroniseCMDatabase();
		}

  }

  public function removeCampaignMonitorList($page, $alsoSynchroniseCMDatabase = false) {
    //internal database
		$existingLists = $this->owner->CampaignMonitorSubscriptions();
		if(!$existingLists) {
			//nothing to do...
		}
		else {
	    $existingLists->remove($page->ID);
		}
		if($alsoSynchroniseCMDatabase) {
			$this->synchroniseCMDatabase();
		}

  }

}
