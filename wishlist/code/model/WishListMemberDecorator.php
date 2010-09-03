<?php
/**
 *
 * @author romain[at]sunnysideup.co.nz
 * @description: adds functions to the shopping cart
 *
 **/

class EcommerceWishListMemberDecorator extends DataObjectDecorator {


	function extraDBFields() {
		return array(
			'db' => array(
				// We use Ecommerce fields for City, Suburb and Phone number
				'EcommerceWishList' => 'Text'
			)
		);
	}


}
