<?php

/**
 *@author nicolaas [at] sunnysideup.co.nz
 *
 *
 **/

class SiteConfigExtras extends DataObjectDecorator {

	function extraStatics(){
		return array(
			'db' => array(
				"CopyrightNotice" => "HTMLText",
			),
			'has_one' => array(
				"BackgroundImage" => "Image"
			)
		);
	}


	function updateCMSFields(FieldSet &$fields) {
		$fields->addFieldToTab("Root.PageElements", new HTMLEditorField($name = "CopyrightNotice", $title = "Copyright notice.", 2));
		$fields->addFieldToTab("Root.PageElements", new ImageField($name = "BackgroundImage", $title = "Background Image", null, null, null, "backgroundimage"));
		return $fields;
	}

	function onBeforeWrite() {
		parent::onBeforeWrite();
	}

	function requireDefaultRecords() {
		parent::requireDefaultRecords();
		$update = array();
		$siteConfig = DataObject::get_one("SiteConfig");

		if($siteConfig) {
			if(strlen($siteConfig->CopyrightNotice) < 17) {
				$siteConfig->CopyrightNotice = '<p>&copy; '.date("Y").' website owner</p>';
				$update[]= "created default entry for CopyrightNotice";
			}
			if(count($update)) {
				$siteConfig->write();
				DB::alteration_message($siteConfig->ClassName." created/updated: ".implode(" --- ",$update), 'created');
			}
		}
	}

}
