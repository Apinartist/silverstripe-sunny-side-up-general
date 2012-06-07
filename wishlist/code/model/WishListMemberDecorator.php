<?php
/**
 *
 * @author romain[at]sunnysideup.co.nz
 * @description: adds functions to the shopping cart
 *
 **/

class WishListMemberDecorator extends DataObjectDecorator {

	/**
	 * Define extra database fields for member object.
	 * @return array
	 */
	function extraStatics() {
		return array(
			'db' => array(
				//Wish list will be stored as a serialised array.
				'WishList' => 'Text'
			)
		);
	}

	/**
	 * standard SS function - we dont need to show the Wish List field in the CMS.
	 */
	function updateCMSFields(&$fields) {
		$fields->removeByName("WishList");
	}


}
