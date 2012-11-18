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
					$object = DataObject::get_by_id($item[0], $item[1]);
					if($object) {
						$links[] = "<a href=\"".$object->Link()."\">".$object->Title."</a>";
					}
					else {
						$links[] = "error in retrieving object ".implode(", ", $item);
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
			$members = DataObject::get("Member", "WishList <> '' AND WishList IS NOT NULL AND Member.ID = 720");
			if($members) {
				foreach($members as $member) {
					$change = false;
					$wishList = unserialize($member->WishList);
					if(is_array($wishList)) {
						if(count($wishList)) {
							foreach($wishList as $key => $array) {
								$newKey = null;
								$newArray = null;
								$keyExploded = explode(".", $key);
								if(intval($keyExploded[0]) && class_exists($keyExploded[1])) {
									$newKey = $keyExploded[1].".".$keyExploded[0];
								}
								if(intval($array[0]) && class_exists($array[1])) {
									$newArray = array( 0 => $array[1], 1 => $array[0]);
								}
								if($newArray && $newKey) {
									DB::alteration_message( "changing ".$key." to ".$newKey.", new value = ".print_r($newArray, 1) );
									$change = true;
									$wishList[$newKey] = $newArray;
									unset($wishList[$key]);
								}
								elseif(!strpos($key, ".")) {
									$change = true;
									$newKey = str_replace("ProductVariation", "ProductVariation.", $key);
									$newKey = str_replace("KahuvetProduct", "KahuvetProduct.", $key);
									$wishList[$newKey] = $array;
									unset($wishList[$key]);
								}
							}
							if($change) {
								$member->WishList = serialize($wishList);
								$member->write();
							}
						}
					}
				}
			}
		}
	}


}
