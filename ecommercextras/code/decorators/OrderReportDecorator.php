<?php
/**
* @author Nicolaas [at] sunnysideup.co.nz
* @package: ecommerce
* @sub-package: ecommercextras
* @description: hack!
*/


class OrderReportDecorator extends Extension {

	function canView($member = null) {
		if($member) {
			return $member->isAdmin();
		}
		$isAdmin = false;
		$m = Member::currentUser();
		if($m) {
			$isAdmin = $m->isAdmin();
		}
		if(!$isAdmin) {
			Security::permissionFailure($this->getOwner(), _t('Security.PERMFAILURE',' This page is secured and you need administrator rights to access it. Enter your credentials below and we will send you right along.'));
		}
		else {
			return true;
		}
	}


}