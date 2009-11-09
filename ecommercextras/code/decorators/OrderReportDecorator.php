<?php
/**
* @author Nicolaas [at] sunnysideup.co.nz
* @package: ecommerce
* @sub-package: ecommercextras
* @description: hack!
*/


class OrderReportDecorator extends Extension {

	function canView($member) {
		return false;
	}

}