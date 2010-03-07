<?php


/**
 * @author Nicolaas [at] sunnysideup.co.nz
 * @package: ecommerce
 * @sub-package: ecommercextras
 * @description: checks email address as soon as it is entered and alerts the person that they already have a login
 *
 */

class LiveEmailCheckModifier extends OrderModifier {

// --------------------------------------------------------------------*** static variables

	protected static $account_exists_message = "There is already an account setup for email: [email]. Would you like to login now?";
		static function set_account_exists_message($string) {self::$account_exists_message = $string;}
		static function get_account_exists_message() {return self::$account_exists_message;}

// --------------------------------------------------------------------*** static functions


	static function show_form() {
		Requirements::javascript("ecommercextras/javascript/LiveEmailCheckModifier.js");
		Requirements::customScript("jQuery(document).ready(function() { LiveEmailCheckModifier.init(); });", "LiveEmailCheckModifier_init");
		false;
	}

	static function get_form($controller) {
		return false;
	}

//--------------------------------------------------------------------*** variables

	protected $messsage = '';

//--------------------------------------------------------------------*** display functions

	function CanRemove() {
		return false;
	}

	function ShowInTable() {
	}

// -------------------------------------------------------------------- *** table values
	function LiveAmount() {
		return 0;
	}

	function TableValue() {
		return 0;
	}


//--------------------------------------------------------------------*** table titles

	function LiveName() {
		return "Email has been checked";
	}

	function Name() {
		if($this->ID) {
			return $this->Name;
		}
		else {
			return $this->LiveName();
		}
	}


	function TableTitle() {
		return $this->Name();
	}


//--------------------------------------------------------------------*** calculations



//-------------------------------------------------------------------- *** database functions
//-------------------------------------------------------------------- *** debug

	function DebugMessage () {
		if(Director::isDev()) {return $this->debugMessage;}
	}
}



class LiveEmailCheckModifier_handler extends Controller {

	public function checkemail(HTTPRequest $httprequest) {
		$email = trim(Convert::raw2sql($httprequest->getVar("email")));
		$currentUserEmail = '';
		if(!$this->checkEmailAddress($email)) {
			return "invalid";
		}
		$attemptedUser = DataObject::get_one("Member", "Email = '".$email."'");
		$loggedInUser = Member::CurrentUser();
		if(!$loggedInUser && $attemptedUser) {
			Session::set('SessionForms.MemberLoginForm.Email', $email);
			return $this->substituteEmailInPhrase($email, LiveEmailCheckModifier::get_account_exists_message());
		}
		return "ok";
	}

	function checkEmailAddress($email) {
		if(!ereg('^([a-zA-Z0-9_+\.\-]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$', $email)) {
			return false;
		}
		return true;
	}

	protected function substituteEmailInPhrase($email, $phrase) {
		return str_replace("[email]", $email, $phrase);
	}


}