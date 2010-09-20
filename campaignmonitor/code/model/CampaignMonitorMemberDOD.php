<?php

/**
 *nicolaas [at] sunnysideup.co.nz
 *
 *
 **/

class CampaignMonitorMemberDOD extends DataObjectDecorator {


	function extraDBFields() {
		return array(
			'db' => array(
				'CampaignMonitorSubscribe' => 'Boolean',
			)
		);
	}

	function onAfterWrite() {
		parent::onAfterWrite();
		//synchronise....
	}

}
