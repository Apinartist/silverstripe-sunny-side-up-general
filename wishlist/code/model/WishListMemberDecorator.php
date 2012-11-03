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
		$member = Member::currentUser();
		if($member && $member->IsAdmin()) {
			$html = "";
			$array = unserialize($this->owner->WishList);
			$links = array();
			if(is_array($array) && count($array)) {
				foreach($array as $item) {
					$object = DataObject::get_by_id($item[1], $item[0]);
					if($object) {
						$links[] = "<a href=\"".$object->Link()."\">".$object->Title."</a>";
					}
					else {
						$links[] = "error in retrieving object ".implode($item);
					}
				}
			}
			else {
				$links[] = "no items on wishlist";
			}
			$html = "<ul><li>".implode("</li><li>", $links)."</li></ul>";
			$field = new LiteralField(
				"WishListOverview",
				$html
			);
			$fields->addFieldToTab("Root.WishList", $field);
		}
		else {
			$fields->removeByName("WishList");
		}
	}

	function requireDefaultRecords() {
		if(isset($_GET["updatewishlist"])) {
			DB::alteration_message("updating wishlists", "created");
			$member = DataObject::get("Member", "WishList <> ''");
			$wishList = unserialize($member->WishList);
			if(is_array($wishList)) {
				if(count($wishList)) {
					foreach($wishList as $key => $array) {
						$newKey = null;
						$newArray = null;
						$keyExploded = explode(".", $key);
						if(intval($keyExploded[0]) && class_exists($keyExploded[1])) {
							$newKey = $keyExploded[1].$keyExploded[0];
						}
						if(intval($array[0]) && class_exists($array[1])) {
							$newArray = array( 0 => $array[1], 1 => $array[0]);
						}
						if($newArray && $newKey) {
							$wishList[$newKey] = $newArray;
							unset($wishList[$key]);
						}
					}
					$this->WishList = serialize($wishList);
					$this->owner->write();
				}
			}
		}
	}


}
