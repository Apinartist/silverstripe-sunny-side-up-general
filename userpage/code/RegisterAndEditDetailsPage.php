<?php

/**
	* Page containing an edit details form
	* Uses Member::getMemberFormFields() to know what to make available for editing
	*/
class RegisterAndEditDetailsPage extends Page {


	static $icon = "userpage/images/treeicons/RegisterAndEditDetailsPage";

	static $can_be_root = false;

	static $db = array(
		"ThankYouTitle" => "Varchar(255)",
		"ThankYouContent" => "HTMLText",
		"WelcomeTitle" => "Varchar(255)",
		"WelcomeContent" => "HTMLText",
		"TitleLoggedIn" => "Varchar(255)",
		"MenuTitleLoggedIn" => "Varchar(255)",
		"MetaTitleLoggedIn" => "Varchar(255)",
		"ContentLoggedIn" => "HTMLText"
	);

	static $register_group_title = "Registered users";

	static $register_group_code = "registrations";

	static $register_group_access_key = "REGISTRATIONS";

	public function getTitle() {if($this->showLoggedInFields()) {$field = "TitleLoggedIn";}else {$field = "Title";} return $this->getField($field);}
	public function getMenuTitle() {if($this->isCMS()) {return "Register"; } elseif($this->showLoggedInFields()) {$field = "MenuTitleLoggedIn";}else {$field = "MenuTitle";} return $this->getField($field);}
	public function getMetaTitle() {if($this->isCMS()) {return "Register"; } elseif($this->showLoggedInFields()) {$field = "MetaTitleLoggedIn";}else {$field = "MetaTitle";} return $this->getField($field);}
	public function getContent() {if($this->showLoggedInFields()) {$field = "ContentLoggedIn";}else {$field = "Content";}return $this->getField($field);}

	private function showLoggedInFields() {
		if(!$this->isCMS() && Member::currentUser()  ) {
			return true;
		}
	}

	private function isCMS () {
		$actions = Director::urlParams();
		if(Director::is_ajax() || "CMSMain" == $actions["Controller"]) {
			return true;
		}
	}



	public function getCMSFields($cms) {
		$fields = parent::getCMSFields($cms);
		$fields->addFieldToTab('Root.Content.LoggedIn', new TextField('TitleLoggedIn', 'Title when user is Logged In'));
		$fields->addFieldToTab('Root.Content.LoggedIn', new TextField('MenuTitleLoggedIn', 'Navigation Label when user is Logged In'));
		$fields->addFieldToTab('Root.Content.LoggedIn', new HTMLEditorField('ContentLoggedIn', 'Content when user is Logged In'));
		$fields->addFieldToTab('Root.Content.Welcome', new TextField('WelcomeTitle', 'Welcome Title (afer user creates an account)'));
		$fields->addFieldToTab('Root.Content.Welcome', new HTMLEditorField('WelcomeContent', 'Welcome message (afer user creates an account)'));

		$fields->addFieldToTab('Root.Content.ThankYou', new TextField('ThankYouTitle', 'Thank you Title (afer user updates their details)'));
		$fields->addFieldToTab('Root.Content.ThankYou', new HTMLEditorField('ThankYouContent', 'Thank you message (afer user updates their details)'));
		return $fields;
	}

	public function canCreate() {
		$bt = defined('DB::USE_ANSI_SQL') ? "\"" : "`";
		return !DataObject::get_one("SiteTree", "{$bt}ClassName{$bt} = 'RegisterAndEditDetailsPage'");
	}

	public function canDelete() {
		false;
	}

	public function requireDefaultRecords() {
		parent::requireDefaultRecords();
		$bt = defined('DB::USE_ANSI_SQL') ? "\"" : "`";
		if(!$group = DataObject::get_one("Group", "{$bt}Code{$bt} = '".self::$register_group_code."'")) {
			$group = new Group();
			$group->Code = self::$register_group_code;
			$group->Title = self::$register_group_title;
			$group->write();
			Permission::grant( $group->ID, self::$register_group_access_key);
			DB::alteration_message("GROUP: ".self::$register_group_code.' ('.self::$register_group_title.')' ,"created");
		}
		elseif(DB::query("SELECT * FROM Permission WHERE {$bt}GroupID{$bt} = ".$group->ID." AND {$bt}Code{$bt} = '".self::$register_group_access_key."'")->numRecords() == 0) {
			Permission::grant($group->ID, self::$register_group_access_key);
		}
	}
}

class RegisterAndEditDetailsPage_Controller extends Page_Controller {

	function index() {
		if($this->isAjax()) {
			return $this->renderWith(array("Thickbox", "RegisterAndEditDetailsPage"));
		}
		return array();
	}

	function Form() {
		if(isset($_REQUEST["BackURL"])){
			Session::set('BackURL', $_REQUEST["BackURL"]);
		}
		$member = Member::currentUser();
		$fields = new FieldSet();
		$passwordField = new ConfirmedPasswordField("Password", "Password");
		if($member) {
			$name = $member->FirstName;
			if($member->FirstName && $member->Surname) {
				$name .= ' ';
			}
			$name .= $member->Surname;
			$logoutField = new LiteralField('LogoutNote', '<p class="message good LogoutNoteStatus LoggedIn">You are currently logged-in as '.$name.'. Click <a href="Security/logout">here</a> to log-out or log-in as someone else.</p>');
			if($member && $member->Password != '') {
				$passwordField->setCanBeEmpty(true);
			}
			$actions = new FieldSet(new FormAction("submit", "Update your details"));
		}
		else {
			$logoutField = new LiteralField('LogoutNote', '<p class="message good LogoutNoteStatus NotLoggedInYet">You are currently not logged-in. Click <a href="Security/login?BackURL='.$this->Link().'">here</a> to log-in.</p>');
			$actions = new FieldSet(new FormAction("submit", "Register"));
		}
		$fields->push($logoutField);

		if($memberFormFields = Member::getMemberFormFields()) {
			$fields->merge($memberFormFields);
		}
		$fields->push($passwordField);

		$requiredFieldList = array(
			"FirstName",
			"Email",
		);
		foreach($requiredFieldList as $fieldName) {
			$fields->fieldByName($fieldName)->addExtraClass("RequiredField");
		}
		$requiredFields = new CustomRequiredFields($requiredFieldList);
		$form = new Form($this, "Form", $fields, $actions, $requiredFields);
		// Load any data avaliable into the form.
		if($member) {
			$form->loadNonBlankDataFrom($member);
		}
		return $form;
	}

	/**
		* Save the changes to the form
		*/
	function submit($data, $form) {
		$bt = defined('DB::USE_ANSI_SQL') ? "\"" : "`";
		$member = Member::currentUser();
		$newPerson = false;
		if(!$member) {
			$newPerson = true;
			$member = new Member();
			$form->sessionMessage($this->WelcomeTitle, 'good');
		}
		else {
			$form->sessionMessage($this->ThankYouTitle, 'good');
		}
		$form->saveInto($member);
		$member->write();

		$group = DataObject::get_one("Group", "{$bt}Code{$bt} = '".RegisterAndEditDetailsPage::$register_group_code."'");
		if($group) {
			$member->Groups()->add($group);
		}
		if($newPerson) {
			$member->logIn();
			$link = ContentController::join_links($this->Link() , 'welcome');
		}
		else {
			$link = ContentController::join_links($this->Link() , 'thanks');
		}
		if(!isset($_REQUEST["BackURL"]) && Session::get('BackURL')) {
			$_REQUEST["BackURL"] = Session::get('BackURL');
		}
		if(isset($_REQUEST["BackURL"])){
			$link = urldecode($_REQUEST["BackURL"]);
			Session::set('BackURL', '');
		}
		if($link) {
			Director::redirect($link);
		}
		return array();
	}

	function thanks() {
		if(!Member::currentUser()) {
			Director::redirect($this->Link());
		}
		$this->Title = $this->ThankYouTitle;
		$this->Content = $this->ThankYouContent;
		return array();
	}

	function welcome() {
		if(!Member::currentUser()) {
			Director::redirect($this->Link());
		}
		$this->Title = $this->WelcomeTitle;
		$this->Content = $this->WelcomeContent;
		return array();
	}

}
