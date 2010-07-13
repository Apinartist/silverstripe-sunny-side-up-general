<?php
/**
 * Add a field to each SiteTree object and it's subclasses to enable "follow us on ...", this can be a blog, twitter, facebook or whatever else.
 * it uses the SocialNetworkingLinksDataObject to get a list of icons.
 * @author nicolaas [at] sunnysideup.co.nz
 *
 **/


class SocialNetworkingLinks extends DataObjectDecorator {

	/**
	* Include on all pages no matter what
	* @var boolean
	*/

	protected static $always_include = 0;
		static function set_always_include($value) {self::$always_include = $value;}

	/**
	* Include on all pages by default
	* @var boolean
	*/

	protected static $include_by_default = 0;
		static function set_include_by_default($value) {self::$include_by_default = $value;}

	/**
	* show bookmark title next to icon
	* @var boolean
	*/

	protected static $show_title_with_icon = 0;
		static function set_show_title_with_icon($value) {self::$show_title_with_icon = $value;}

	/**
	* specify alternative icons in the form of array($key => $filename, $key => $filename)
	* @var array
	*/

	protected static function clean_boolean_value($value) {
		if($value == false) {
			$value = 0;
		}
		if($value == true) {
		 $value = 1;
		}
		if($value == 1 || $value == 0) {
			return $value;
		}
		Debug::show("$value should be a 0 or 1");
	}

	function extraStatics(){
		if(self::$always_include) {
			return array();
		}
		else {
			return array(
				'db' => array('HasSocialNetworkingLinks' => 'Boolean' ),
				'defaults' => array('HasSocialNetworkingLinks' => self::$include_by_default),
			);
		}
	}


	function updateCMSFields(FieldSet &$fields) {
		if(!self::$always_include) {
			$fields->addFieldToTab("Root.Behaviour", new CheckboxField("HasSocialNetworkingLinks","Show Social Networking Links on this Page (e.g. follow us on Twitter) - make sure to specify social networking links!"));
			$fields->addFieldToTab("Root.Behaviour", new LiteralField("HasSocialNetworkingLinksExplanation","<p>Social Networking links (e.g. a link to your facebook page) need to be <a href=\"/admin/social/\">added first</a>.</p>"));


		}
		return $fields;
	}

	/**
	* At the moment this method does nothing.
	*/

	function augmentSQL(SQLQuery &$query) {
	}


	/**
	* At the moment this method does nothing.
	*/
	function augmentDatabase() {
	}

	public function ThisPageHasSocialNetworkingLinks() {
		if($this->owner) {
			if(self::$always_include) {
				return true;
			}
			elseif(isset($this->owner->HasSocialNetworkingLinks)) {
				return $this->owner->HasSocialNetworkingLinks;
			}
		}
		return false;
	}

	public function SocialNetworkingLinksDataObjects(){
		if($this->ThisPageHasSocialNetworkingLinks()) {
			if($objects = DataObject::get("SocialNetworkingLinksDataObject")) {
				Requirements::themedCSS("SocialNetworking");
				return $objects;
			}
		}
	}

}
