<?php

/**
 *nicolaas [at] sunnysideup.co.nz
 *
 *
 **/

class CampaignMonitorMemberDOD extends DataObjectDecorator {


	function extraStatics() {
		return array(
			'db' => array(
				'CampaignMonitorSubscription' => 'Varchar(32)',
			)
		);
	}

	function onAfterWrite() {
		parent::onAfterWrite();

    $wrapper = new CampaignMonitorWrapper();
    if ($this->owner->CampaignMonitorSubscription) {
      $wrapper->setListID ($this->owner->CampaignMonitorSubscription);
      if (!$wrapper->subscriberAdd($this->owner->Email, $this->owner->getName()))
        user_error('Subscribe attempt failed: ' . $wrapper->lastErrorMessage, E_USER_WARNING);
    }
    else {
      $fields = $this->owner->getChangedFields();
      if (isset ($fields['CampaignMonitorSubscription']['before'])) {
        $list_id = $fields['CampaignMonitorSubscription']['before'];
        $wrapper->setListID ($list_id);
        if (!$wrapper->subscriberUnsubscribe($this->owner->Email))
          user_error('Unsubscribe attempt failed: ' . $wrapper->lastErrorMessage, E_USER_WARNING);
      }
    }
	}

}
