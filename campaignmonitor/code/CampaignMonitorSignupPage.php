<?php

/**
 * Main Holder page for Recipes
 *
 */
class CampaignMonitorSignupPage extends Page {

	static $icon = "campaignmonitor/images/treeicons/CampaignMonitorSignupPage";

	private static $remove_linebreaks_from_original_code = false;
		static function set_remove_linebreaks_from_original_code($v) { self::$remove_linebreaks_from_original_code = $v;}
		static function get_remove_linebreaks_from_original_code() { return self::$remove_linebreaks_from_original_code; }

	static $db = array(
		"FormHTML" => "HTMLText",
		"ThankYouMessage" => "HTMLText",
		"AlternativeTitle" => "Varchar(255)",
		"AlternativeMenuTitle" => "Varchar(255)",
		"AlternativeMetaTitle" => "Varchar(255)",
		"SignUpHeader" => "Varchar(100)",
		"SignUpIntro" => "HTMLText",
		"SignUpButtonLabel" => "Varchar(20)",
	);

	public function CanCreate() {
		return !DataObject::get_one("CampaignMonitorSignupPage");
	}

	function getCMSFields() {
		$fields = parent::getCMSFields();
		$fields->addFieldToTab('Root.Content.FormCode', new TextareaField("FormHTML", "Copy form html code here (use CTRL+V)"));

		$fields->addFieldToTab('Root.Content.StartForm', new LiteralField("StartFormExplanation", "A start form is a form where people are just required to enter their email address and nothing else.  After completion they go through to another page (the actual CampaignMonitorSignUpPage) to complete all the details."));
			$fields->addFieldToTab('Root.Content.StartForm', new TextField("SignUpHeader", "Sign up header (e.g. sign up now)"));
		$fields->addFieldToTab('Root.Content.StartForm', new HTMLEditorField("SignUpIntro", "Sign up form intro (e.g. sign up for our monthly newsletter ..."));
		$fields->addFieldToTab('Root.Content.StartForm', new TextFied("SignUpButtonLabel", "Sign up button label for start form (e.g. register now)"));

		$fields->addFieldToTab('Root.Content.ThankYou', new ReadonlyField("ReturnURL", "Return URL after form is submitted - supply this to Campaign Monitor", Director::absoluteBaseURL().$this->URLSegment.'/thankyou/#CampaignMonitorSignupPageThankYou'));
		$fields->addFieldToTab('Root.Content.ThankYou', new TextField("AlternativeTitle", "AlternativeTitle"));
		$fields->addFieldToTab('Root.Content.ThankYou', new TextField("AlternativeMenuTitle", "AlternativeMenuTitle"));
		$fields->addFieldToTab('Root.Content.ThankYou', new TextField("AlternativeMetaTitle", "AlternativeMetaTitle"));
		$fields->addFieldToTab('Root.Content.ThankYou', new HTMLEditorField("ThankYouMessage", "Thank You Message after Submitting Form"));
		return $fields;
	}

	/**
	* you can add this function to other pages to have a form that starts the basic after which the client needs to complete the rest.
	*
	**/

	static function CampaignMonitorStarterForm($controller) {
		$page = DataObject::get_one("CampaignMonitorSignupPage");
		$fields = new FieldSet(new TextField("Email", ""));
		$actions = new FieldSet(new FormAction("CampaignMonitorStarterFormStartAction", $page->SignUpButtonLabel));
		$form = new Form(
			$controller,
			"CampaignMonitorStarterForm",
			$fields,
			$actions
		);
		$form->setFormAction($page->Link("CampaignMonitorStarterFormStartAction"));
		return $form;
	}




	function onBeforeWrite() {
		parent::onBeforeWrite();
		$this->FormHTML = str_replace("<br>", " ", $this->FormHTML);
		$this->FormHTML = str_replace("<br />", " ", $this->FormHTML);
	}



	function requireDefaultRecords() {
		parent::requireDefaultRecords();
		$update = array();
		$page = DataObject::get_one("CampaignMonitorSignupPage");

		if($page) {
			if(!$page->SignUpHeader) {
				$page->SignUpHeader = 'Sign Up Now';
				$update[]= "created default entry for SignUpHeader";
			}
			if(strlen($page->SignUpIntro) < strlen("<p> </p>")) {
				$page->SignUpIntro = '<p>Enter your email to sign up for our newsletter</p>';
				$update[]= "created default entry for SignUpIntro";
			}
			if(!$page->SignUpButtonLabel) {
				$page->SignUpButtonLabel = 'Register Now';
				$update[]= "created default entry for SignUpButtonLabel";
			}
			if(count($update)) {
				$page->writeToStage('Stage');
				$page->publish('Stage', 'Live');
				DB::alteration_message($page->ClassName." created/updated: ".implode(" --- ",$update), 'created');
			}
		}
	}
}

class CampaignMonitorSignupPage_Controller extends Page_Controller {

	protected static $get_email_field_selector = "#Email";
		static function set_email_field_selector($v){self::$get_email_field_selector = $v;}
		static function get_email_field_selector(){return self::$get_email_field_selector;}

	var $ShowThankYouMessage = false;

	function init() {
		parent::init();
		Requirements::themedCSS("CampaignMonitorSignupPage");
	}

	function thankyou() {
		$this->ShowThankYouMessage = true;
		if($this->AlternativeTitle) {$this->MetaTitle = $this->AlternativeTitle;}
		if($this->AlternativeMenuTitle) {$this->MetaTitle = $this->AlternativeMenuTitle;}
		if($this->AlternativeMetaTitle) {$this->MetaTitle = $this->AlternativeMetaTitle;}
		$this->Content = $this->ThankYouMessage;
		return array();
	}

	function CampaignMonitorStarterFormStartAction(SS_HTTPRequest $request){
		Requirements::javascript(THIRDPARTY_DIR . '/jquery/jquery.js');
		$data = $request->requestVars();
		if(isset($data["Email"])) {
			$email = $data["Email"];
			if($email) {
				Requirements::customScript('jQuery("'.self::get_email_field_selector().'").val("'.Convert::raw2js($email).'")');
			}
		}
		return array();
	}

}

