<?php

class TrainingPage extends Page {

	static $icon = "mysite/images/treeicons/TrainingPage";

	static $db = array(
		"Date" => "Date",
		"EndDate" => "Date",
		"Location" => "Varchar(255)",
		"Price" => "Currency",
		"IsOpenForBookings" => "Boolean",
		"PlacesAvailable" => "Int",
		"PeopleSignedUp" => "Int",
		"MoreInformation" => "HTMLText",
		"Options" => "Text"
	);

	static $has_one = array(
		"DownloadFile" => "File"
	);

	static $many_many = array(
		"Attendees" => "Member"
	);

	static $many_many_extraFields = array(
		 "Attendees" => array(
				"SelectedOption" => "Varchar(255)",
				"BookingCode" => "Varchar(255)"
		 )
	);

	//parents and children in sitetree
	static $allowed_children = "none"; //can also be "none";
	static $default_parent = "TrainingHolder";
	static $can_be_root = false; //default is true

	public function getCMSFields() {
		$fields = parent::getCMSFields();
		$fields->addFieldToTab("Root.Content.WhoWhereWhat", new CalendarDateField("Date", "Start Date"));
		$fields->addFieldToTab("Root.Content.WhoWhereWhat", new CalendarDateField("EndDate", "End Date - can be left blank for one day events"));
		$fields->addFieldToTab("Root.Content.WhoWhereWhat", new TextField("Location"));
		$fields->addFieldToTab("Root.Content.WhoWhereWhat", new CurrencyField("Price"));
		$fields->addFieldToTab("Root.Content.MoreInformation", new FileIFrameField("DownloadFile","Download File"));
		$fields->addFieldToTab("Root.Content.MoreInformation", new HTMLEditorField("MoreInformation","More Information", 12));
		$fields->addFieldToTab("Root.Content.Bookings", new TextareaField("Options", "Options available (separate by comma) - if any (e.g. venues)"));
		$fields->addFieldToTab("Root.Content.Bookings", new CheckboxField("IsOpenForBookings", "Is Open For Bookings"));
		$fields->addFieldToTab("Root.Content.Bookings", new NumericField("PlacesAvailable", "Places Available"));
		$fields->addFieldToTab("Root.Content.Bookings", new NumericField("PeopleSignedUp","People Signed Up (excluding the ones signed up on this website)"));
		$fields->addFieldToTab(
			"Root.Content.Bookings",
			$this->MemberField()
		);
		return $fields;
	}

	function MemberField() {
		$memberField = new ManyManyComplexTableField(
			$controller = $this,
			$name = "Attendees",
			$sourceClass = "Member",
			$fieldList = null,
			$detailFormFields = null,
			$sourceFilter = "`TrainingPageID` = ".$this->ID,
			$sourceSort = "`TrainingPage_Attendees`.`ID` DESC",
			$sourceJoin = ""
		);
		$memberField->setAddTitle("Attendees");
		$memberField->setPermissions(array("show", "edit", "export"));
		return $memberField;
	}

	function addAttendee($member) {
		$existingMembers = $this->Attendees();
		$existingMembers->add($member);
	}

	function DifferentEndDate() {
		if($this->Date != $this->EndDate && $this->EndDate) {
			return true;
		}
	}

	function DifferentEndMonth() {
		if($this->DifferentEndDate()) {
			if(Date("F",$this->Date) || Date("F",$this->EndDate)) {
				return true;
			}
		}
	}

}

class TrainingPage_Controller extends Page_Controller {

	function SignUpForm() {

		$form = new TrainingSignupForm($this, "SignUpForm", "Sign-Up for ".$this->Title);
		if("thankyou" == Director::URLParam("Action")) {
			return false;
		}
		return $form;
	}

	function thankyou () {
		$this->Title = "Thank You";
		$this->Content = "We will be in touch soon";
		return array();
	}

}
