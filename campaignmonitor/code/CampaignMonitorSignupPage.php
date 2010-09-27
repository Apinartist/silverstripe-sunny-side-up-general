<?php

/**
 * Main Holder page for Recipes
 *
 */
class CampaignMonitorSignupPage extends Page {

	static $icon = "campaignmonitor/images/treeicons/CampaignMonitorSignupPage";

	static $db = array(
    'ListID' => 'Varchar(32)',
		'ThankYouMessage' => 'HTMLText',
		'AlternativeTitle' => 'Varchar(255)',
		'AlternativeMenuTitle' => 'Varchar(255)',
		'AlternativeMetaTitle' => 'Varchar(255)',
		'SignUpHeader' => 'Varchar(100)',
		'SignUpIntro' => 'HTMLText',
		'SignUpButtonLabel' => 'Varchar(20)',
	);

	public function CanCreate() {
		return !DataObject::get_one("CampaignMonitorSignupPage");
	}

	function getCMSFields() {
		$fields = parent::getCMSFields();

		$fields->addFieldToTab('Root.Content.Main', new TextField('ListID', 'CampaignMonitor\'s ListID'), 'MenuTitle');
		$fields->addFieldToTab('Root.Content.StartForm', new LiteralField('StartFormExplanation', 'A start form is a form where people are just required to enter their email address and nothing else.  After completion they go through to another page (the actual CampaignMonitorSignUpPage) to complete all the details.'));
		$fields->addFieldToTab('Root.Content.StartForm', new TextField('SignUpHeader', 'Sign up header (e.g. sign up now)'));
		$fields->addFieldToTab('Root.Content.StartForm', new HTMLEditorField('SignUpIntro', 'Sign up form intro (e.g. sign up for our monthly newsletter ...'));
		$fields->addFieldToTab('Root.Content.StartForm', new TextField('SignUpButtonLabel', 'Sign up button label for start form (e.g. register now)'));

		$fields->addFieldToTab('Root.Content.ThankYou', new ReadonlyField('ReturnURL', 'Return URL after form is submitted - supply this to Campaign Monitor', Director::absoluteBaseURL().$this->URLSegment.'/thankyou/#CampaignMonitorSignupPageThankYou'));
		$fields->addFieldToTab('Root.Content.ThankYou', new TextField('AlternativeTitle', 'AlternativeTitle'));
		$fields->addFieldToTab('Root.Content.ThankYou', new TextField('AlternativeMenuTitle', 'AlternativeMenuTitle'));
		$fields->addFieldToTab('Root.Content.ThankYou', new TextField('AlternativeMetaTitle', 'AlternativeMetaTitle'));
		$fields->addFieldToTab('Root.Content.ThankYou', new HTMLEditorField('ThankYouMessage', 'Thank You Message after Submitting Form'));
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

  // Return a properly setup instance of the wrapper class
  public function newCMWrapper () {
    $CMWrapper = new CampaignMonitorWrapper();
		if(!$this->ListID) {
			$lists = $CMWrapper->clientGetLists();
			if(!$lists) {
				user_error("you will need to specify a list on this page first...", E_USER_WARNING);
			}
			if(is_array($lists) && isset($lists["anyType"]["List"]["ListID"])) {
				$this->ListID = $lists["anyType"]["List"]["ListID"];
			}
			if(is_int($lists)) {
				$this->ListID = $lists;
			}
		}
    $CMWrapper->setListID ($this->ListID);
    return $CMWrapper;
  }


	function onBeforeWrite() {
		$CMWrapper = new $this->newCMWrapper();
		parent::onBeforeWrite();
	}

	function requireDefaultRecords() {
		parent::requireDefaultRecords();
		$update = array();
		$page = DataObject::get_one("CampaignMonitorSignupPage");

		if($page) {
			$CMWrapper = new $this->newCMWrapper();
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

	// Subscription form

	function FormHTML() {
    // Create fields
    $fields = new FieldSet(
      new TextField('Name', 'Name'),
      new EmailField('Email', 'Email')
    );
    // Create action
    $actions = new FieldSet(
      new FormAction('subscribe', 'Subscribe')
    );
    // Create Validators
    $validator = new RequiredFields('Name', 'Email');
    return new Form($this, 'FormHTML', $fields, $actions, $validator);
	}

	function subscribe($data, $form) {
		$CMWrapper = $this->newCMWrapper();
		$member = Member::currentMember();
		if(!$member) {
			if($existingMember = DataObject::get_one("Member", "Email = '".Convert::raw2sql($data["Email"])."'")) {
				$form->addErrorMessage('Email', _t("CAMPAIGNMONITORSIGNUPPAGE_EMAIL_EXISTS", "This email is already in use. Please log in for this email or try another email address"), 'warning');
				Director::redirectBack();
				return;
			}
			$member = new Member();
		}
		if($CMWrapper->subscriberIsUnsubscribed($data["Email"])) {
			$form->addErrorMessage('Email', _t("CAMPAIGNMONITORSIGNUPPAGE_EMAIL_PREVIOUSLY_UNSUBSCRIBED", "This email is already unsubscribed and can not be added again. Please contact the website owners for more information."), 'warning');
			Director::redirectBack();
			return;
		}
		$form->saveInto($member);
    // TODO: why do we do this? Wouldn't it be better to query CM with the email address of the member? And do you need to be a member before you can subscribe??
		$member->CampaignMonitorSubscription = $this->ListID;
		// Write it to the database.  This needs to happen before we add it to a group
		$member->SetPassword = true;
		$member->Password = Member::create_new_password();
		$member->write();
    if (!$CMWrapper->subscriberAdd($data['Email'], $data['Name']))
      user_error('Subscribe attempt failed: ' . $CMWrapper->lastErrorMessage, E_USER_WARNING);
    else
      Director::redirect($this->Link().'thankyou/');
	}

  // Unsubscribe immediately...
  function unsubscribe() {
		$member = Member::currentMember();
    if ($member) {
      $member->CampaignMonitorSubscription = '';
      $member->write();
    }
  }

	function thankyou() {
		$this->ShowThankYouMessage = true; // TODO: what does this var do???
		if($this->AlternativeTitle) {$this->MetaTitle = $this->AlternativeTitle;}
		if($this->AlternativeMenuTitle) {$this->MetaTitle = $this->AlternativeMenuTitle;}
		if($this->AlternativeMetaTitle) {$this->MetaTitle = $this->AlternativeMetaTitle;}
    // TODO: this does not return/set/show the thank you message. Nicolaas to complete.
		return array();
	}

	//we use this if you reach the form with an email already...

	function CampaignMonitorStarterFormStartAction(SS_HTTPRequest $request){
		Requirements::javascript(THIRDPARTY_DIR . '/jquery/jquery.js');
		$data = $request->requestVars();
		if(isset($data["Email"])) {
			$email = $data["Email"];
			if($email) {
				Requirements::customScript('
					jQuery(document).ready(
						function() {
							jQuery("'.self::get_email_field_selector().'").val("'.Convert::raw2js($email).'");
						}
					);
				');
			}
		}
		return array();
	}

	function test() {
		//add user to CM and check results
		//to run this test go to http://www.mysite.com/NameOfPage/test/
		if(Permission::check("Admin")) {

			//run tests here
      $CMWrapper = $this->newCMWrapper();
      if (!$CMWrapper->testConnection())
        user_error('Cannot connect to CampaignMonitor: ' . $CMWrapper->lastErrorMessage, E_USER_WARNING);
      if (!$CMWrapper->testListSetup())
        user_error('List not setup: ' . $CMWrapper->lastErrorMessage, E_USER_WARNING);

      // Test connection with CM

			//returning array will show page as normal...
			return array();
		}
		else {
			Security::permissionFailure($this, _t("CAMPAIGNMONITORSIGNUPPAGE.TESTFAILURE", "This function is only available for administrators"));
		}

	}

}
