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
				'CampaignMonitorSubscribe' => 'Boolean',
			)
		);
	}

	function onAfterWrite() {
		parent::onAfterWrite();
	}

}
