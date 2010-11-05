<?php

/**
 *@author nicolaas [at] sunnysideup.co.nz
 *
 *
 **/

class CampaignMonitorMemberDOD extends DataObjectDecorator {


	function extraStatics() {
		return array(
			'many_many' => array(
				'CampaignMonitorSubscriptions' => 'CampaignMonitorSignupPage',
			)
		);
	}

	function onAfterWrite() {
		parent::onAfterWrite();
    $CMWrapper = new CampaignMonitorWrapper();
    if ($this->owner->CampaignMonitorSubscription) {
      $CMWrapper->setListID ($this->owner->CampaignMonitorSubscription);
    }
    else {
      $fields = $this->owner->getChangedFields();
      if (isset ($fields['CampaignMonitorSubscription']['before'])) {
        $list_id = $fields['CampaignMonitorSubscription']['before'];
        $CMWrapper->setListID ($list_id);
        if (!$CMWrapper->subscriberUnsubscribe($this->owner->Email)) {
          user_error(_t('CampaignMonitorMemberDOD.GETCMSMESSAGEUNSUBSATTEMPTFAILED', 'Unsubscribe attempt failed: ') . $CMWrapper->lastErrorMessage, E_USER_WARNING);
				}
      }
    }
	}

  public function addCampaignMonitorList($page) {
    //internal database
		$existingLists = $this->owner->CampaignMonitorSubscriptions();
		if(!$existingLists) {
			user_error("can find relationship", E_USER_NOTICE);
		}
		else {
	    $existingLists->add($page->ID);
		}
		//external database
		$CMWrapper = new CampaignMonitorWrapper();
		$CMWrapper->setListID ($page->ListID);
		if(!$CMWrapper->subscriberIsUnconfirmed($this->owner->Email)) {
			if (!$CMWrapper->subscriberAdd($this->owner->Email, $this->owner->getName())) {
				user_error(_t('CampaignMonitorMemberDOD.GETCMSMESSAGESUBSATTEMPTFAILED', 'Subscribe attempt failed: ') . $CMWrapper->lastErrorMessage, E_USER_WARNING);
			}
		}
  }

  public function removeCampaignMonitorList($page) {
    //internal database
		$existingLists = $this->owner->CampaignMonitorSubscriptions();
		if(!$existingLists) {
			//nothing to do...
		}
		else {
	    $existingLists->remove($page->ID);
		}
		//external database
		$CMWrapper = new CampaignMonitorWrapper();
		$CMWrapper->setListID ($page->ListID);
		if (!$CMWrapper->subscriberUnsubscribe($this->owner->Email)) {
			user_error(_t('CampaignMonitorMemberDOD.GETCMSMESSAGEUNSUBSATTEMPTFAILED', 'Unsubscribe attempt failed: ') . $CMWrapper->lastErrorMessage, E_USER_WARNING);
		}

  }

}
