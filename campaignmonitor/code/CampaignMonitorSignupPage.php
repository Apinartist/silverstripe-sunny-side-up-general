<?php

/**
 * Main Holder page for Recipes
 *
 */
class CampaignMonitorSignupPage extends Page {

	static $icon = "campaignmonitor/images/treeicons/CampaignMonitorSignupPage";

	static $db = array(
		"FormHTML" => "HTMLText",
		"ThankYouMessage" => "HTMLText"
	);

	public function CanCreate() {
		return !DataObject::get_one("CampaignMonitorSignupPage");
	}

	function getCMSFields() {
		$fields = parent::getCMSFields();
		$fields->addFieldToTab('Root.Content.FormCode', new TextareaField("FormHTML", "Copy form html code here (use CTRL+V)"));
		$fields->addFieldToTab('Root.Content.ThankYou', new HTMLEditorField("ThankYouMessage", "Thank You Message after Submitting Form"));
		$fields->addFieldToTab('Root.Content.ThankYou', new ReadonlyField("ReturnURL", "Return URL after form is submitted - supply this to Campaign Monitor", Director::absoluteBaseURL().$this->URLSegment.'/thankyou/'));
		return $fields;
	}


}

class CampaignMonitorSignupPage_Controller extends Page_Controller {

	static $allowed_actions = array("thankyou");

	var $ShowThankYouMessage = false;

	function init() {
		parent::init();
	}

	function thankyou() {
		$this->ShowThankYouMessage = true;
	}

}

