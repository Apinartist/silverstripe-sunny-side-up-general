<?php

class BusinessAdmin extends CMSMain {

	static $url_segment = 'business';

	// Maintain a lower priority than other administration sections
	// so that Director does not think they are actions of CMSMain
	static $url_priority = 100;

	static $menu_priority = 10;

	// These variables are 2.3-specific; in 2.2.x you will need to edit _config.php
	static $menu_title = 'Business Content';

	public function getMenuTitle() {
		return _t('BusinessAdmin.BUSINESSADMIN', 'Business Admin', PR_HIGH, 'Menu title');
	}

	/*public function Link($action=null) {
		//if(!$action) $action = "index";
		return "admin/business/$action";
	}*/

	public function init() {
		parent::init();
		global $lang;
		$lang['en_US']['BusinessAdmin_left.ss'] = true;

		Requirements::javascript('business_directory/javascript/BusinessAdmin.js');
		Requirements::javascript('business_directory/javascript/BusinessAdmin_left.js');
	}

	function SiteTreeAsUL () {
		$member = Member::currentUser();
		$roots = $member->BusinessPages();

		if ((!$roots || $roots->Count() == 0 ) && $member->isAdmin()) {
			$roots = DataObject::get('BusinessPage');
		}

		$siteTree = null;
		if ($roots && $roots->Count() > 0 ) {
			$subsitetrees = array();
			foreach ($roots as $root) {
				$rootLink = $this->Link() . $root->ID;
				$rootId = $root->ID;
				$treeTitle = $root->Title;

				$subsitetrees[] = "<li id=\"record-$root->ID\" class=\"" . $root->CMSTreeClasses($this) . "\">" .
					"<a href=\"" . Director::link(substr($this->Link(),0,-1), "show", $root->ID) . "\" class=\"" . $root->CMSTreeClasses($this) . "\" title=\"" . _t('LeftAndMain.PAGETYPE','Page type: ') . $root->class."\" >" .
					($root->TreeTitle()) .
					"</a>".$this->SubSiteTreeAsUL($root)."</li>";
			}

			$siteTree = implode("\n",$subsitetrees);
		}

		// This code is copied from getSiteTreeFor(), because getSiteTreeFor has it hard-coded to only generate if rootID = 0
		$rootLink = $this->Link() . '0';

		if($this->hasMethod('getCMSTreeTitle')) $treeTitle = $this->getCMSTreeTitle();
		else $treeTitle =  _t('LeftAndMain.SITECONTENTLEFT',"Site Content",PR_HIGH,'Root node on left');

		$siteTree = "<ul id=\"sitetree\" class=\"tree unformatted\"><li id=\"record-0\" class=\"Root nodelete\"><a href=\"$rootLink\"><strong>$treeTitle</strong></a><ul>". $siteTree . "</ul></li></ul>";

		return $siteTree;
	}

	function SubSiteTreeAsUL($businessRoot = null) {
		// This piece of code just gets us a root ID to use
		if ($businessRoot == null) {
			$businessRoot = DataObject::get_one("BrowseWorldPage");
		}

		if(!$businessRoot) {
			$businessRoot = new BrowseWorldPage();
			$businessRoot->write();
		}

		// This code is what you will need to do to make a subtree version of CMSMain
		$this->generateDataTreeHints();
		$this->generateTreeStylingJS();

		// blogRoot->ID is your root node
		$siteTree = $this->getSiteTreeFor("SiteTree", $businessRoot->ID);

		return $siteTree;
	}
}
