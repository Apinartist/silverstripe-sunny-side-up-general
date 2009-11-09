<?php
/**
* @author Nicolaas [at] sunnysideup.co.nz
* @package: ecommerce
* @sub-package: ecommercextras
* @description: main class for ecommercextras that kicks things into place.
*/


class OrderReportDecorator extends Extension {

	function canView($member) {
		return false;
	}

}