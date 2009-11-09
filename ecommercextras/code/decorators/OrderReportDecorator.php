<?php
/**
* @author Nicolaas [at] sunnysideup.co.nz
* @package: ecommerce
* @sub-package: ecommercextras
* @description: hack!
*/


class OrderReportDecorator extends Extension {

	function __construct() {
		$isAdmin = false;
		$m = Member::currentUser();
		if($m) {
			$IsAdmin = $m->isAdmin();
		}
		if(!$isAdmin) {
			Security::permissionFailure($this, _t('Security.PERMFAILURE',' This page is secured and you need administrator rights to access it. Enter your credentials below and we will send you right along.'));
		}
	}


}