<?php
/*
 *@author nicolaas [at] sunnysideup.co.nz
 *
 *@also see: http://www.campaignmonitor.com/api/
 *
 *what should we be able to do
 *1. check if client exists
 *2. if not, create cllient with data from CampaignMonitorPage (client editable stuff) + _config (web developer editable stuff
 *3. link from CampaignMonitorPage to CampaignMonitor Website
 *4. see list of campaigns
 *5. see list of lists
 *5a. synchronise list members with Silverstripe users
 *5b. update list config: listTitle,. unsubscribePage, confirmationSuccessPage, confirmOptIn
  **/

class CampaignMonitorWrapper extends Object {

	//basic basics
	protected static $cm = null;
		public static function set_cm($v) {self::$cm = $v;}
		public static function get_cm() {return self::$cm;}

	protected static $campaign_monitor_url = "http://yourcompany.createsend.com/";
		public static function set_campaign_monitor_url($v) {self::$campaign_monitor_url = $v;}
		public static function get_campaign_monitor_url() {return self::$campaign_monitor_url;}

	//basic configs
	protected static $api_key = '';
		public static function set_api_key($v) {self::$api_key = $v;}
		public static function get_api_key() {return self::$api_key;}

	protected static $client_ID = '';
		public static function set_client_ID($v) {self::$client_ID = $v;}
		public static function get_client_ID() {return self::$client_ID;}

	//__________client config... ONLY set by web developer
	// $accessLevel = '63';
	// $username = 'apiusername';
	// $password = 'apiPassword';
	// $billingType = 'ClientPaysWithMarkup';
	// $currency = 'USD';
	// $deliveryFee = '7';
	// $costPerRecipient = '3';
	// $designAndSpamTestFee = '10';
	//__________client config... editable by client
	//companyName = 'Created From API';
	//contactName = 'Joe Smith';
	//emailAddress = 'joe@domain.com';
	//country = 'United States of America';
	//timeZone = '(GMT-05:00) Eastern Time (US & Canada)';

	//campaign
	protected $campaignID = '';
		public function setCampaignID($v) {$this->campaignID = $v;}
		public function getCampaignID() {return $this->campaignID;}
	// $campaignName = 'March newsletter';
	// $subject = 'March newsletter';
	// $fromName = 'John Smith';
	// $fromEmail = 'john@smith.com';
	// $replyEmail = 'john@smith.com';
	// $confirmationEmail = 'joe@domain.com';
	// $sendDate = '2089-02-15 09:00:00';
	// $htmlContent = 'http://www.campaignmonitor.com/uploads/templates/previews/template-1-left-sidebar/index.html';
	// $textContent = 'http://www.campaignmonitor.com/uploads/templates/previews/template-1-left-sidebar/textversion.txt';
	// $subcriberListIDArray = array('xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx');
	// $subscriberSegments = "";

	//template
	protected $templateID = '';
		public function setTemplateID($v) {$this->templateID = $v;}
		public function getTemplateID() {return $this->templateID;}
	// $templateName = 'Updated Template Name';
	// $htmlURL = "http://notarealdomain.com/templates/test/index.html";
	// $zipURL = "http://notarealdomain.com/templates/test/images.zip";
	// $screenshotURL = "http://notarealdomain.com/templates/test/screenshot.jpg";

	//list
	protected $listID = '';
		public function setListID($v) {$this->listID = $v;}
		public function getListID() {return $this->listID;}
	// $listTitle = 'Updated API Created List';
	// $unsubscribePage = '';
	// $confirmOptIn = 'false';
	// $confirmationSuccessPage = '';

	function __construct() {
		if(!self::$api_key) {user_error("You need to set an $api_key in your configs.", E_USER_WARNING);}
		if(!self::$client_ID) {user_error("You need to set a $client_ID in your configs.", E_USER_WARNING);}
		self::$cm = new CampaignMonitor( self::$api_key, self::$client_ID);
	}

	// -------------------- CAMPAIGN SECTION --------------------

	public function campaignCreate(
		$campaignName = 'March newsletter',
		$subject = 'March newsletter',
		$fromName = 'John Smith',
		$fromEmail = 'john@smith.com',
		$replyEmail = 'john@smith.com',
		$confirmationEmail = 'joe@domain.com',
		$sendDate = '2089-02-15 09:00:00',
		$htmlContent = 'http://www.campaignmonitor.com/uploads/templates/previews/template-1-left-sidebar/index.html',
		$textContent = 'http://www.campaignmonitor.com/uploads/templates/previews/template-1-left-sidebar/textversion.txt',
		$subcriberListIDArray = array('xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx')
	) {
		return self::$cm->campaignCreate( self::$client_ID, $campaignName, $subject, $fromName, $fromEmail, $replyEmail, $htmlContent, $textContent, $subcriberListIDArray, "" );
	}

	public function campaignDelete() {
		if(!$this->campaignID) {user_error("You need to set a campaignID for this function to work.", E_USER_WARNING);}
		return self::$cm->campaignDelete($this->campaignID);
	}

	public function campaignGetBounces() {
		user_error("this function has not been implemented yet!", E_USER_ERROR);
	}

	public function campaignGetLists() {
		user_error("this function has not been implemented yet!", E_USER_ERROR);
	}

	public function campaignGetOpens() {
		user_error("this function has not been implemented yet - low priority - shows users who opened campaign!", E_USER_ERROR);
	}

	public function campaignGetSubscriberClicks() {
		user_error("this function has not been implemented yet - low priority!", E_USER_ERROR);
	}

	public function campaignGetSummary() {
		user_error("this function has not been implemented yet - low priority", E_USER_ERROR);
	}

	public function campaignGetUnsubscribes() {
		user_error("this function has not been implemented yet - low priority", E_USER_ERROR);
	}


	public function campaignSend($confirmationEmail = 'joe@domain.com',$sendDate = '2029-02-15 09:00:00') {
		if(!$this->campaignID) {user_error("You need to set a campaignID for this function to work.", E_USER_WARNING);}
		return self::$cm->campaignSend( $this->campaignID, $confirmationEmail, $sendDate );
	}


	// -------------------- CLIENT SECTION --------------------

	public function clientCreate($companyName = 'Created From API',$contactName = 'Joe Smith',$emailAddress = 'joe@domain.com',$country = 'United States of America',$timeZone = '(GMT-05:00) Eastern Time (US & Canada)') {
		return self::$cm->clientCreate( $companyName, $contactName, $emailAddress, $country, $timeZone );
	}

	public function clientGetCampaigns() {
		return self::$cm->clientGetCampaigns( self::$client_ID );
	}

	public function clientGetDetail() {
		return self::$cm->clientGetDetail( self::$client_ID );
	}

	public function clientGetLists() {
		user_error("this function has not been implemented yet", E_USER_ERROR);
	}

	public function clientGetSegments() {
		user_error("this function has not been implemented yet - low priority", E_USER_ERROR);
	}

	public function clientGetSuppressionList{
		return self::$cm->clientGetSuppressionList( self::$client_ID );
	}

	public function clientGetTemplates{
		return self::$cm->clientGetTemplates( self::$client_ID );
	}

	public function clientUpdateAccessAndBilling(
		$accessLevel = '63',
		$username = 'apiusername',
		$password = 'apiPassword',
		$billingType = 'ClientPaysWithMarkup',
		$currency = 'USD',
		$deliveryFee = '7',
		$costPerRecipient = '3',
		$designAndSpamTestFee = '10'
	) {
		return self::$cm->clientUpdateAccessAndBilling( self::$client_ID, $accessLevel, $username, $password, $billingType, $currency, $deliveryFee, $costPerRecipient, $designAndSpamTestFee );
	}

	public function clientUpdateBasics($companyName = 'Created From API',$contactName = 'Joe Smith',$emailAddress = 'joe@domain.com',$country = 'United States of America',$timeZone = '(GMT-05:00) Eastern Time (US & Canada)') {
		return self::$cm->clientUpdateBasics( self::$client_ID, $companyName, $contactName, $emailAddress, $country, $timeZone );
	}


	// -------------------- LIST SECTION --------------------

	public function listCreate($listTitle = 'Updated API Created List',$unsubscribePage = '',$confirmOptIn = 'false',$confirmationSuccessPage = '') {
		return self::$cm->listCreate( self::$client_ID, $listTitle, $unsubscribePage, $confirmOptIn, $confirmationSuccessPage );
	}

	public function listDelete() {
		if(!$this->listID) {user_error("You need to set a listID for listDelete to work.", E_USER_WARNING);}
		return self::$cm->listDelete( $this->listID );
	}

	public function listCreateCustomField($fieldName = 'Nickname', $dataType = 'Text', $options = '') {
		/*
		// Below are examples for the other possible field types
		// Number field example
		$fieldName = 'Age';
		$dataType = 'Number';
		$options = '';
		// Multi-option select one example
		$fieldName = 'Sex';
		$dataType = 'MultiSelectOne';
		$options = 'Male||Female';
		// Multi-option select many example
		$fieldName = 'Hobby';
		$dataType = 'MultiSelectMany';
		$options = 'Surfing||Reading||Snowboarding';
		*/
		if(!$this->listID) {user_error("You need to set a listID for listCreateCustomField to work.", E_USER_WARNING);}
		return self::$cm->listCreateCustomField( $this->listID, $fieldName, $dataType, $options );
	}

	public function listDeleteCustomField($key = '[CustomFieldKey]') {
		if(!$this->listID) {user_error("You need to set a listID for this function to work.", E_USER_WARNING);}
		return self::$cm->listDeleteCustomField( $this->listID, $key );
	}

	public function listGetCustomFields() {
		if(!$this->listID) {user_error("You need to set a listID for this function to work.", E_USER_WARNING);}
		return self::$cm->listGetCustomFields( $this->listID );
	}

	public function listGetDetail() {
		if(!$this->listID) {user_error("You need to set a listID for this function to work.", E_USER_WARNING);}
		return self::$cm->listGetDetail( $this->listID );
	}


	public function listGetStats() {
		//Gets statistics for a subscriber list
		user_error("this function has not been implemented yet", E_USER_ERROR);
	}

	public function listUpdate($listTitle = 'Updated API Created List',$unsubscribePage = '',$confirmOptIn = 'false',$confirmationSuccessPage = '') {
		if(!$this->listID) {user_error("You need to set a listID for this function to work.", E_USER_WARNING);}
		return self::$cm->listUpdate( $this->listID, $listTitle, $unsubscribePage, $confirmOptIn, $confirmationSuccessPage );
	}


	// -------------------- SUBSCRIBER SECTION --------------------

	public function subscriberAdd($subscriberEmail, $subscriberName) {
		//Sample using the CMBase.php wrapper to call Subscriber.AddWithCustomFields from any version of PHP
		//Relative path to CMBase.php. This example assumes the file is in the same folder
		//Your API Key. Go to http://www.campaignmonitor.com/api/required/ to see where to find this and other required keys
		if(!$this->campaignID) {user_error("You need to set a campaignID for this function to work.", E_USER_WARNING);}
		if(!$this->listID) {user_error("You need to set a listID for this function to work.", E_USER_WARNING);}
		$tempCM = new CampaignMonitor(self::$api_key, self::$client_ID, $this->campaignID, $this->listID );
		//
		//passing email address, name.
		$result = $tempCM->subscriberAdd($subscriberEmail, $subscriberEmailName);
		if($result['Result']['Code'] == 0) {
			return 'Success';
		}
		else {
			return 'Error : ' . $result['Result']['Message'];
		}
	}


	public function subscriberAddAndResubscribe() {
		user_error("this function has not been implemented yet", E_USER_ERROR);
	}

	public function subscriberAddAndResubscribeWithCustomFields() {
		user_error("this function has not been implemented yet", E_USER_ERROR);
	}

	public function subscriberAddWithCustomFields($subscriberEmail, $subscriberName, $params) {
		//Sample using the CMBase.php wrapper to call Subscriber.AddWithCustomFields from any version of PHP
		//Relative path to CMBase.php. This example assumes the file is in the same folder
		//Your API Key. Go to http://www.campaignmonitor.com/api/required/ to see where to find this and other required keys
		if(!$this->campaignID) {user_error("You need to set a campaignID for this function to work.", E_USER_WARNING);}
		if(!$this->listID) {user_error("You need to set a listID for this function to work.", E_USER_WARNING);}
		$tempCM = new CampaignMonitor(self::$api_key, self::$client_ID, $this->campaignID, $this->listID );
		//
		//passing email address, name and custom fields. Custom fields should be added as an array as shown here with the Interests and Dog fields.
		//Multi-option field values are added as an array within this, as demonstrated for the Interests field.

		// TO DO ____________________ ! ____________________ ! ____________________ ! ____________________ ! ____________________ ! ____________________ ! ____________________ !
		//turn params into arguments for function!
		// TO DO ____________________ ! ____________________ ! ____________________ ! ____________________ ! ____________________ ! ____________________ ! ____________________ !

		$result = $tempCM->subscriberAddWithCustomFields($subscriberEmail, $subscriberName, $params));
		if($result['Code'] == 0) {
			return 'Success';
		}
		else {
			return 'Error : ' . $result['Message'];
		}
	}

	public function subscriberUnsubscribe($subscriberEmail) {
		//Sample using the CMBase.php wrapper to call Subscriber.AddWithCustomFields from any version of PHP
		//Relative path to CMBase.php. This example assumes the file is in the same folder
		//Your API Key. Go to http://www.campaignmonitor.com/api/required/ to see where to find this and other required keys
		if(!$this->campaignID) {user_error("You need to set a campaignID for this function to work.", E_USER_WARNING);}
		if(!$this->listID) {user_error("You need to set a listID for this function to work.", E_USER_WARNING);}
		$TEMPcm = new CampaignMonitor(self::$api_key, self::$client_ID, $this->campaignID, $this->listID );
		$result = $TEMPcm->subscriberUnsubscribe($subscriberEmail);
		if($result['Result']['Code'] == 0) {
			return 'Success';
		}
		else {
			return 'Error : ' . $result['Result']['Message'];
		}
	}


	public function subscriberGetActive() {
		//Gets a list of all active subscribers for a list that have been added since the specified date
		user_error("this function has not been implemented yet", E_USER_ERROR);
	}

	public function subscriberGetBounced() {
		//Gets a list of all subscribers for a list that have hard bounced since the specified date.
		user_error("this function has not been implemented yet", E_USER_ERROR);
	}

	public function subscriberGetIsSubscribed() {
		//Returns True or False as to the existence of the given email address in the list supplied.
		user_error("this function has not been implemented yet", E_USER_ERROR);
	}

	public function subscriberGetSingleSubscriber() {
		//This method returns the details of a particular subscriber, including email address, name, active/inactive status and all custom field data. If a subscriber with that email address does not exist in that list, an empty record is returned.
		user_error("this function has not been implemented yet", E_USER_ERROR);
	}

	public function subscriberGetUnsubscribed() {
		//Gets a list of all subscribers for a list that have unsubscribed since the specified date.
		user_error("this function has not been implemented yet", E_USER_ERROR);
	}

	// -------------------- TEMPLATE SECTION --------------------

	public function templateCreate(
		$templateName = 'Updated Template Name',
		$htmlURL = "http://notarealdomain.com/templates/test/index.html",
		$zipURL = "http://notarealdomain.com/templates/test/images.zip",
		$screenshotURL = "http://notarealdomain.com/templates/test/screenshot.jpg"
	) {
		return self::$cm->templateCreate(self::$client_ID, $templateName, $htmlURL, $zipURL, $screenshotURL);
	}

	public function templateDelete() {
		if(!$this->templateID) {user_error("You need to set a templateID for this function to work.", E_USER_WARNING);}
		return self::$cm->templateDelete($this->templateID);
	}

	public function templateGetDetail() {
		if(!$this->templateID) {user_error("You need to set a templateID for this function to work.", E_USER_WARNING);}
		return self::$cm->templateGetDetail($this->templateID);
	}

	public function templateUpdate(
		$templateName = 'Updated Template Name',
		$htmlURL = "http://notarealdomain.com/templates/test/index.html",
		$zipURL = "http://notarealdomain.com/templates/test/images.zip",
		$screenshotURL = "http://notarealdomain.com/templates/test/screenshot.jpg"
	) {
		if(!$this->templateID) {user_error("You need to set a templateID for this function to work", E_USER_WARNING);}
		return self::$cm->templateUpdate($this->templateID, $templateName, $htmlURL, $zipURL, $screenshotURL);
	}


		// -------------------- USER SECTION --------------------
	public function userGetApiKey() {
		return self::$cm->userGetApiKey();
	}

	public function userGetClients() {
		return self::$cm->userGetApiKey();
	}

	public function userGetCountries() {
		return self::$cm->userGetCountries();
	}

	public function userGetSystemDate() {
		return self::$cm->userGetSystemDate();
	}

	public function userGettimeZones() {
		return self::$cm->userGettimeZones();
	}

}
