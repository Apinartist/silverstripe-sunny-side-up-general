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
    $CMWrapper = new CampaignMonitorWrapper();
    if ($this->owner->CampaignMonitorSubscription) {
      $CMWrapper->setListID ($this->owner->CampaignMonitorSubscription);
      if (!$CMWrapper->subscriberAdd($this->owner->Email, $this->owner->getName()))
        user_error('Subscribe attempt failed: ' . $CMWrapper->lastErrorMessage, E_USER_WARNING);
    }
    else {
      $fields = $this->owner->getChangedFields();
      if (isset ($fields['CampaignMonitorSubscription']['before'])) {
        $list_id = $fields['CampaignMonitorSubscription']['before'];
        $CMWrapper->setListID ($list_id);
        if (!$CMWrapper->subscriberUnsubscribe($this->owner->Email))
          user_error('Unsubscribe attempt failed: ' . $CMWrapper->lastErrorMessage, E_USER_WARNING);
      }
    }
	}

}
