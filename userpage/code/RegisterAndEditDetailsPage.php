<?php

/**
	* Page containing an edit details form
	* Uses Member::getMemberFormFields() to know what to make available for editing
	*/
class RegisterAndEditDetailsPage extends Page {


	static $icon = "registration/images/treeicons/RegisterAndEditDetailsPage";

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
		return !DataObject::get_one("RegisterAndEditDetailsPage");
	}

	public function canDelete() {
		false;
	}

	public function requireDefaultRecords() {
		parent::requireDefaultRecords();
		if(!$group = DataObject::get_one("Group", 'Code = "'.self::$register_group_code.'"')) {
			$group = new Group();
			$group->Code = self::$register_group_code;
			$group->Title = self::$register_group_title;
			$group->write();
			Permission::grant( $group->ID, self::$register_group_access_key);
			Database::alteration_message("GROUP: ".self::$register_group_code.' ('.self::$register_group_title.')' ,"created");
		}
		elseif(DB::query('SELECT * FROM Permission WHERE `GroupID` = '.$group->ID.' AND `Code` = "'.self::$register_group_access_key.'"')->numRecords() == 0) {
			Permission::grant($group->ID, self::$register_group_access_key);
		}
	}
}

class RegisterAndEditDetailsPage_Controller extends Page_Controller {

	function index() {
		if(isset($_GET["ajax"])) {
			return $this->renderWith(array("Thickbox", "RegisterPage"));
		}
		return array();
	}

	function Form() {
		$member = Member::currentUser();
		$fields = new FieldSet();
		$passwordField = new ConfirmedPasswordField("Password", "Password");
		if($member) {
			$logoutField = new LiteralField('LogoutNote', '<p class="message good">You are currently logged in as '. $member->FirstName . ' ' . $member->Surname . '. Click <a href="Security/logout" title="Click here to log out">here</a> to log out.</p>');
			if($member && $member->Password != '') {
				$passwordField->setCanBeEmpty(true);
			}
			$actions = new FieldSet(new FormAction("submit", "Update your details"));
		}
		else {
			$logoutField = new LiteralField('LogoutNote', '<p class="message good">You are currently not logged in. Click <a href="Security/login" title="Click here to log out" class="thickbox">here</a> to log-in.</p>');
			$actions = new FieldSet(new FormAction("submit", "Register"));
		}
		$fields->push($logoutField);

		if($memberFormFields = Member::getMemberFormFields()) {
			$fields->merge($memberFormFields);
		}
		$fields->push(new HeaderField("Login Details", 3));
		$fields->push($passwordField);

		$requiredFieldList = array(
			"FirstName",
			"Email",
		);
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
		$member = Member::currentUser();
		$newPerson = false;
		if(!$member) {
			$newPerson = true;
			$member = new Member();
			$form->sessionMessage('Thank you. You have been registered.', 'good');
		}
		else {
			$form->sessionMessage('Your details have been saved', 'good');

		}
		$form->saveInto($member);
		$member->write();

		$group = DataObject::get_one("Group", '`Code` = "'.RegisterAndEditDetailsPage::$register_group_code.'"');
		if($group) {
			$member->Groups()->add($group);
		}
		if($newPerson) {
			$member->logIn();
			Director::redirect($this->Link() . 'welcome');
		}
		else {
			Director::redirect($this->Link() . 'thanks');
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