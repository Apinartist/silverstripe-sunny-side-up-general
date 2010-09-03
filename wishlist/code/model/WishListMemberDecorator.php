<?php
/**
 *
 * @author romain[at]sunnysideup.co.nz
 * @description: adds functions to the shopping cart
 *
 **/

class WishListMemberDecorator extends DataObjectDecorator {


	function extraDBFields() {
		return array(
			'db' => array(
				// We use  fields for City, Suburb and Phone number
				'WishList' => 'Text'
			)
		);
	}


}
