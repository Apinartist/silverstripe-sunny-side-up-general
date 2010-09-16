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
		"ContentLoggedIn" => "HTMLText",
		"ErrorEmailAddressAlreadyExists" => "Varchar(50)",
		"ErrorPasswordDoNotMatch" => "Varchar(50)",
		"ErrorMustSupplyPassword" => "Varchar(50)"
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

	public function getCMSFields() {
		$fields = parent::getCMSFields();
		$fields->addFieldToTab('Root.Content.LoggedIn', new TextField('TitleLoggedIn', 'Title when user is Logged In'));
		$fields->addFieldToTab('Root.Content.LoggedIn', new TextField('MenuTitleLoggedIn', 'Navigation Label when user is Logged In'));
		$fields->addFieldToTab('Root.Content.LoggedIn', new HTMLEditorField('ContentLoggedIn', 'Content when user is Logged In'));
		$fields->addFieldToTab('Root.Content.Welcome', new TextField('WelcomeTitle', 'Welcome Title (afer user creates an account)'));
		$fields->addFieldToTab('Root.Content.Welcome', new HTMLEditorField('WelcomeContent', 'Welcome message (afer user creates an account)'));
		$fields->addFieldToTab('Root.Content.UpdatingDetails', new TextField('ThankYouTitle', 'Thank you Title (afer user updates their details)'));
		$fields->addFieldToTab('Root.Content.UpdatingDetails', new HTMLEditorField('ThankYouContent', 'Thank you message (afer user updates their details)'));
		$fields->addFieldToTab('Root.Content.ErrorMessages', new TextField('ErrorEmailAddressAlreadyExists', 'Error shown when email address is already registered'));
		$fields->addFieldToTab('Root.Content.ErrorMessages', new TextField('ErrorPasswordDoNotMatch', 'Error shown when passwords do not match'));
		$fields->addFieldToTab('Root.Content.ErrorMessages', new TextField('ErrorMustSupplyPassword', 'Error shown when new user does not supply password'));
		return $fields;
	}

	public function canCreate() {
		$bt = defined('DB::USE_ANSI_SQL') ? "\"" : "`";
		return !DataObject::get_one("SiteTree", "{$bt}ClassName{$bt} = 'RegisterAndEditDetailsPage'");
	}

	public function requireDefaultRecords() {
		parent::requireDefaultRecords();
		$bt = defined('DB::USE_ANSI_SQL') ? "\"" : "`";
		$update = array();
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
		$page = DataObject::get_one("RegisterAndEditDetailsPage");
		if(!$page) {
			$page = new RegisterAndEditDetailsPage();
			$page->Title = "Register";
			$page->MetaTitle = "Register";
			$page->URLSegment = "register";
			$page->MenuTitle = "Register";
			$update[] = "created RegisterAndEditDetailsPage";
		}
		if($page) {
			if(!$page->ThankYouTitle){$page->ThankYouTitle = "Thank you for updating your details"; $update[] =  "updated ThankYouTitle";}
			if(strlen($page->ThankYouContent) < 17){$page->ThankYouContent = "<p>Thank you for updating your details. </p>"; $update[] =  "updated ThankYouContent";}
			if(!$page->WelcomeTitle){$page->WelcomeTitle = "Thank you for registering"; $update[] =  "updated WelcomeTitle";}
			if(strlen($page->WelcomeContent) < 17){$page->WelcomeContent = "<p>Thank you for registration. Please make sure to remember your username and password.</p>"; $update[] =  "updated WelcomeContent";}
			if(!$page->TitleLoggedIn){$page->TitleLoggedIn = "Welcome back"; $update[] =  "updated TitleLoggedIn";}
			if(!$page->MenuTitleLoggedIn){$page->MenuTitleLoggedIn = "Welcome back"; $update[] =  "updated MenuTitleLoggedIn";}
			if(!$page->MetaTitleLoggedIn){$page->MetaTitleLoggedIn = "Welcome back"; $update[] =  "updated MetaTitleLoggedIn";}
			if(strlen($page->ContentLoggedIn) < 17){$page->ContentLoggedIn = "<p>Welcome back - you can do the following ....</p>"; $update[] =  "updated ContentLoggedIn";}
			if(!$page->ErrorEmailAddressAlreadyExists){$page->ErrorEmailAddressAlreadyExists = "Sorry, that email address is already in use by someone else. You may have setup an account in the past or mistyped your email address."; $update[] =  "updated ErrorEmailAddressAlreadyExists";}
			if(!$page->ErrorPasswordDoNotMatch){$page->ErrorPasswordDoNotMatch = "Your passwords do not match. Please try again."; $update[] =  "updated ErrorPasswordDoNotMatch";}
			if(!$page->ErrorMustSupplyPassword){$page->ErrorMustSupplyPassword = "Your must supply a password."; $update[] =  "updated ErrorMustSupplyPassword";}
			if(count($update)) {
				$page->writeToStage('Stage');
				$page->publish('Stage', 'Live');
				DB::alteration_message($page->ClassName." created/updated: <ul><li>".implode("</li><li>", $update)."</li></ul>", 'created');
			}
		}
	}
}

class RegisterAndEditDetailsPage_Controller extends Page_Controller {

	protected static $fields_to_remove = array("Locale","DateFormat", "TimeFormat");
		static function set_fields_to_remove($v) {self::$fields_to_remove = $v;}
		static function get_fields_to_remove() {return self::$fields_to_remove;}

	protected static $required_fields = array("FirstName","Email");
		static function set_required_fields($v) {self::$required_fields = $v;}
		static function get_required_fields() {return self::$required_fields;}

	function index() {
		if($this->isAjax()) {
			return $this->renderWith(array("RegisterAndEditDetailsPageAjax", "RegisterAndEditDetailsPage"));
		}
		return array();
	}

	function Form() {
		if(isset($_REQUEST["BackURL"])){
			Session::set('BackURL', $_REQUEST["BackURL"]);
		}
		$member = Member::currentUser();
		$fields = new FieldSet();

		$passwordField = null;
		if($member) {
			$name = $member->getName();
			//if($member && $member->Password != '') {$passwordField->setCanBeEmpty(true);}
			$action = new FormAction("submit", "Update your details");
			$action->addExtraClass("updateButton");
			$actions = new FieldSet($action);
		}
		else {
			$passwordField = new ConfirmedPasswordField("Password", "Password");
			$action = new FormAction("submit", "Register");
			$action->addExtraClass("registerButton");
			$actions = new FieldSet($action);
			$member = new Member();
		}
		$memberFormFields = $member->getMemberFormFields();

		if($memberFormFields) {
			if(is_array(self::$fields_to_remove) && count(self::$fields_to_remove)) {
				foreach(self::$fields_to_remove as $fieldName) {
					$memberFormFields->removeByName($fieldName);
				}
			}
			$fields->merge($memberFormFields);
		}
		if($passwordField) {
			$fields->push($passwordField);
		}
		foreach(self::$required_fields as $fieldName) {
			$fields->fieldByName($fieldName)->addExtraClass("RequiredField");
		}
		$requiredFields = new CustomRequiredFields(self::$required_fields);
		$form = new Form($this, "Form", $fields, $actions, $requiredFields);
		// Load any data avaliable into the form.
		if($member) {
			$form->loadDataFrom($member);
		}
		$data = Session::get("FormInfo.Form_Form.data");
		if(is_array($data)) {
			$form->loadDataFrom($data);
		}

		// Optional spam protection
		if(class_exists('SpamProtectorManager')) {
			SpamProtectorManager::update_form($form);
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
		Session::set("FormInfo.Form_Form.data", $data);
		if(!$member) {
			$newPerson = true;
			$member = Object::create('Member');
			$form->sessionMessage($this->WelcomeTitle, 'good');
			$id = 0;
		}
		else {
			$form->sessionMessage($this->ThankYouTitle, 'good');
			$id = $member->ID;
		}

		//validation
		if($existingMember = DataObject::get_one("Member", "{$bt}Email{$bt} = '". Convert::raw2sql($data['Email']) . "' AND {$bt}Member{$bt}.{$bt}ID{$bt} <> $id")) {
			$form->addErrorMessage("Blurb",$this->ErrorEmailAddressAlreadyExists,"bad");
			Director::redirectBack();
			return;
		}
		// check password fields are the same before saving
		if($data["Password"]["_Password"] != $data["Password"]["_ConfirmPassword"]) {
			$form->addErrorMessage("Password", $this->ErrorPasswordDoNotMatch,"bad");
			return Director::redirectBack();
		}

		if(!$id && !$data["Password"]["_Password"]) {
			$form->addErrorMessage("Password", $this->ErrorMustSupplyPassword,"bad");
			return Director::redirectBack();
		}
		$form->saveInto($member);
		$member->write();
		//adding to group
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
