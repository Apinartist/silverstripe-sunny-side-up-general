<?php

class Page extends SiteTree {

	public static $db = array(
	);

	public static $has_one = array(
		"BackgroundImage" => "Image"	
	);

	function MyBackgroundImage() {
		if($this->BackgroundImageID) {
			if($image = $this->BackgroundImage()) {
				return $image;
			}
		}
		if($this->ParentID) {
			if($parent = DataObject::get_by_id("SiteTree", "ParentID = ".$this->ParentID)) {
				return $parent->MyBackgroundImage();
			}
		}
		if($siteConfig = SiteConfig::current_site_config()) {
			return $siteConfig->MyBackgroundImage();
		}
	}	

}
class Page_Controller extends ContentController {

	/**
	 * An array of actions that can be accessed via a request. Each array element should be an action name, and the
	 * permissions or conditions required to allow the user to access it.
	 *
	 * <code>
	 * array (
	 *     'action', // anyone can access this action
	 *     'action' => true, // same as above
	 *     'action' => 'ADMIN', // you must have ADMIN permissions to access this action
	 *     'action' => '->checkAction' // you can only access this action if $this->checkAction() returns true
	 * );
	 * </code>
	 *
	 * @var array
	 */
	public static $allowed_actions = array (
	);

	public function init() {
		parent::init();
		$this->addBasicMetatagRequirements();
	}

	function IsNotHome() {
		return $this->URLSegment != "home";
	}

}
