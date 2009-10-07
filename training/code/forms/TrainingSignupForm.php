<?php

class TrainingSignupForm extends Form {

	function __construct($controller, $name, $title = "Training") {
		if($member = Member::currentUser()) {
		}
		else {
			$member = new Member();
		}
		$fields = new FieldSet(
			new HeaderField($title)
		);
		$extraFields = $member->getTrainingFields();
		foreach($extraFields as $field) {
			if("Password" == $field->title() && $member->ID) {

			}
			elseif("Password" == $field->title() ) {
				$fields->push(new ConfirmedPasswordField("Password"));
			}
			else {
				$fields->push($field);
			}
		}
		$actions = new FieldSet(
				new FormAction("doSave", "Sign Up Now")
		);
		$requiredFields = new RequiredFields(
			"FirstName",
			"Surname",
			"Email",
			"Password"
		);
		parent::__construct($controller, $name, $fields, $actions, $requiredFields);
		$this->loadNonBlankDataFrom($member);
		return $this;
	}

	function doSave($data, $form) {
		if(isset($data['Password']) && is_array($data['Password'])) {
			$data['Password'] = $data['Password']['_Password'];
		}

		// We need to ensure that the unique field is never overwritten
		$uniqueField = Member::get_unique_identifier_field();
		if(isset($data[$uniqueField])) {
			$SQL_unique = Convert::raw2xml($data[$uniqueField]);
			$existingUniqueMember = DataObject::get_one('Member', "$uniqueField = '{$SQL_unique}'");
			if($existingUniqueMember && $existingUniqueMember->exists()) {
				if(Member::currentUserID() != $existingUniqueMember->ID) {
					die("current member does not match enrolled member");
					return false;
				}
			}
		}
		$member = Member::currentUser();
		if(!$member) {
			$member = new Member();
		}

		$member->update($data);
		$member->write();
		$this->controller->addAttendee($member);
		Director::redirect($this->controller->Link()."thankyou");
		return;

	}



}




