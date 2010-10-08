<?php
/**
 * BusinessPage.php: Sub-class of Page
 * Contains info about a business
 * @created 14/10/2008
 */

class BusinessPage extends Page {

	static $icon = "business_directory/images/treeicons/BusinessPage";

	static $db = array (
		"IntroParagraph" => "Text",
		"Phone" => "Varchar",
		"Email" => "Varchar",
		"Skype" => "Varchar",
		"IM" => "Varchar",
		"ListingEmail" => "Varchar",
		"MailingAddress" => "Text",
		"Notes" => "Text",
		"CertificationYear" => "Int",
		"Website" => "Varchar",
		"FirstName" => "Varchar(255)",
		"LastName" => "Varchar(255)",
		"AlternativeContactDetails" => "Text",
		"OrganisationDescription" => "Text",
		'ReasonForFounding' => 'Text',
		'LastEmailSent' => 'SSDatetime'
	);

	static $has_one = array (
		'Image1' => 'Image',
		'Image2' => 'Image',
		'Image3' => 'Image'
	);

	static $many_many = array (
		'Certifications' => 'CertificationPage',
		'ProductCategories' => 'ProductCategoryPage',
		'Members' => 'Member'
	);

	//static $default_parent = "BrowseRegionsPage"; - SHOULD BE URL SEGMENT - DOES NOT WORK!

	static $can_be_root = false;

	//permissions and actions
 	static $need_permission = array('ADMIN','CMS_ACCESS_BusinessAdmin','ACCESS_FORUM','ACCESS_BUSINESS'); //List of permission codes a user can have to allow a user to create a page of this type.

	static $defaults = array (
		"CertificationYear" => "0",
		"HasGeoInfo" => 1,
		"ProvideComments" => true
	);

	static $allowed_children = "none";

	static $default_child = "ProductPage";

	static $member_group = "listing-member";

	static $member_title = "Business Members";

	static $access_code = "ACCESS_BUSINESS";

	static $casting = array(
		"HiddenEmail" => "Varchar",
		"DescriptiveEmail" => "Varchar"
	);
	/**
	 * Add default records to database
	 *
	 * This function is called whenever the database is built, after the
	 * database tables have all been created.
	 */
	public function requireDefaultRecords() {
		parent::requireDefaultRecords();

		if(!$businessGroup = DataObject::get_one("Group", 'Code = "'.self::$member_group.'"')) {
			$group = new Group();
			$group->Code = self::$member_group;
			$group->Title = self::$member_title;
			$group->write();
      Permission::grant( $group->ID, self::$access_code);
      Database::alteration_message(self::$member_group.' group created','created');
    }
    elseif(DB::query("SELECT COUNT(*) FROM Permission WHERE GroupID = ".$businessGroup->ID." AND Code LIKE '".self::$access_code."'")->value() == 0 ) {
      Permission::grant($businessGroup->ID, self::$access_code);
    }

	}

	function canEditCurrentPage() {
		$currentUser = Member::currentUser();
		if($currentUser && (Permission::check('ADMIN') || $this->Members('Member.ID = '.$currentUser->ID))) {
			return true;
		}
	}

	public function canEdit($member = null) {
		if(!$member) $member = Member::currentUser();
		if(!$member) return false;

		if(Permission::checkMember($member, "ADMIN")) return true;

		// decorated access checks
		$results = $this->extend('canEdit', $member);
		if($results && is_array($results)) if(!min($results)) return false;

		// if page can't be viewed, don't grant edit permissions
		if(!$this->canView()) return false;

		// check for empty spec
		if(!$this->CanEditType || $this->CanEditType == 'Anyone') return true;

		// check for inherit
		if($this->CanEditType == 'Inherit') {
			if($this->ParentID) return $this->Parent()->canEdit($member);
			else return Permission::checkMember($member, 'CMS_ACCESS_CMSMain');
		}

		// check for any logged-in users
		if($this->CanEditType == 'LoggedInUsers' && Permission::checkMember($member, 'CMS_ACCESS_CMSMain')) return true;

		// check for specific groups
		if($this->CanEditType == 'OnlyTheseUsers' && $member && $member->inGroups($this->EditorGroups())) return true;

		if($this->Members('MemberID = '.$member->ID)) return true;
		//Debug::message('About to fail');
		return false;
	}

	function onBeforeWrite() {
		// If first write then add current member to Business members
		/*$currentUser = Member::currentUser();
		if(!$this->ID && !Permission::check('ADMIN')) {
		} else {
			// Check the user is admin or a member
			if(!$this->Members('Member.ID = '.$currentUser->ID) && !Permission::check('ADMIN')) {
				user_error('You do not have permission to edit this operator', E_USER_ERROR);
				exit();
			}
		}*/

		$emails = array($this->Email, $this->ListingEmail);

		foreach ($emails as $e) {
		  if ($e) {
				$member = DataObject::get_one('Member', "Email = '$e'");

		    if (!$member) {
		      $member = new Member();
					$member->FirstName = $this->FirstName;
					$member->Surname = $this->LastName;
					$member->Nickname = $this->FirstName;
		      $member->Email = $e;
					$pwd = Member::create_new_password();
		      $member->Password = $pwd;

		      //$member->sendInfo('signup', array('Password' => $pwd));

					$emaildata = array('Password' => $pwd);
					$e = new BusinessMember_SignupEmail();
					$e->populateTemplate($member);
					/* if(is_array($emaildata)) {
						foreach($emaildata as $key => $value)
							$e->$key = $value;
					} */
					$e->populateTemplate($emaildata);
					$e->from = "do-not-reply@localorganics.net";
					//Debug::show($e->debug());
					$e->send();
					$member->write();
				}
				elseif (round((abs(time() - strtotime($this->LastEmailSent)))/60) > 60) { // Check we haven't sent an email in the last hour
					// If some fields have changed then send an update
			    $from = Email::getAdminEmail();
			    $to = $this->Email . "," . $this->ListingEmail;
			    $subject = "Your businesses details were updated on localorganics.net";
					if(!$this->URLSegment) {
						$this->URLSegment = $this->generateURLSegment($this->Title);
					}
			    $url = Director::absoluteBaseURL().$this->URLSegment;
					$body = '
					<h1>Hello, $member.FirstName.</h1>
					<p>The details of your business '.$this->Title.' were updated on localorganics.net</p>

					<h3>View your details at
						<a href="'.$url.'">'.$url.'</a>
					</h3>';
			    $email = new Email($from, $to, $subject, $body);
					$email->from = $from;
					$email->to = $to;
					$email->subject = $subject;
					$email->body = $body;
			    $email->populateTemplate(array(
						'business' => $this,
						'member' => $member
					));
					//Debug::show($email->debug());
		      $email->send();
					$email->to = "nfrancken@gmail.com";
					$email->send();
					$this->LastEmailSent = date('Y-m-d H:i:s', strtotime('now'));
				}
				// Add user as BusinessMember - CHECK IF THIS GETS DONE MANY TIMES RATHER THAN JUST ONES
				$this->Members()->add($member);
				Group::addToGroupByName($member, self::$member_group);
		  }
		}

		//Delete old members
		$members = $this->Members('Email != \''.$this->Email .'\' AND Email !=\'"'.$this->ListingEmail.'\'');
		foreach ($members as $m) {
			if ($m->Email != $this->Email && $m->Email != $this->ListingEmail) {
				$m->delete();
			}
		}
	  parent::onBeforeWrite();
	}

	public function getCMSFields( $cms = null ) {
		//'Requirements::javascript( 'business_directory/javascript/BusinessPage_CMS.js' );
		$generalFields = new FieldSet(
			new HeaderField("MainDetails", "Main Details", 3),
			new TextField("Title", "Business Name"),
			new TextareaField("IntroParagraph", "Introduction, one paragraph (two or three sentences)", 4),
			new TextareaField("OrganisationDescription", "Organisation Description, one paragraph (two or three sentences)", 4),
			new TextareaField("ReasonForFounding", "Reason For Founding, one paragraph (two or three sentences)", 4),

			// Contact tab
			new HeaderField("ContactDetails", "Contact Details", 3),
			new TextField("FirstName", "First Name"),
			new TextField("LastName", "Last Name"),
			new TextField("Phone", "Phone (country code, area code, number, extension)", $this->Phone, true,true,true),
			new EmailField("Email", "Administrator Email (not public)"),
			new EmailField("ListingEmail", "Listing Email (public)"),
			new TextField("Skype", "Skype Address"),
			new TextField("IM", "Instant Messaging ID (MSN, Gmail, etc...)"),
			new TextareaField("MailingAddress", "Mailing Address", 4),
			new TextareaField("Notes", "Notes (only use this field if needed) - SHOWN TO GENERAL PUBLIC",5),
			new TextField("Website", "Website"),
			new TextAreaField("AlternativeContactDetails", "Alternative Contact Details - NOT SHOWN TO PUBLIC", 4),
			new NumericField("CertificationYear", "Certification Year")
		);
		if($cms) {
			$fields = parent::getCMSFields( $cms );
			// Extra content fields
			$fields->removeFieldFromTab("Root.Content.Main","Content");
			$fields->removeFieldFromTab("Root.Content", "MainImage" );
			//$fields->removeFieldFromTab($rootTabname."Main","Title");
			$fields->removeFieldFromTab("Root.Content.Main","MenuTitle"); // Leave this as automatic
			$rootTabname = "Root.Content.";
			// Image fields
			$fields->addFieldToTab($rootTabname."Images", new ImageField("Image1", "Image 1",null,null,null,'BusinessImages/'.$this->ID) );
			$fields->addFieldToTab($rootTabname."Images", new ImageField("Image2", "Image 2",null,null,null,'BusinessImages/'.$this->ID) );
			$fields->addFieldToTab($rootTabname."Images", new ImageField("Image3", "Image 3",null,null,null,'BusinessImages/'.$this->ID) );
			$fields->addFieldToTab($rootTabname."General", $generalFields );
			return $fields;
		}
		else {
			// Image fields
			$generalFields->push(new HeaderField("Images", "Images",3) );
			$generalFields->push(new SimpleImageField("Image1", "Image 1",null,null,null,'BusinessImages/'.$this->ID) );
			$generalFields->push(new SimpleImageField("Image2", "Image 2",null,null,null,'BusinessImages/'.$this->ID) );
			$generalFields->push(new SimpleImageField("Image3", "Image 3",null,null,null,'BusinessImages/'.$this->ID) );
			return $generalFields;
		}
	}

	public function getContinent () {
		return $this->getAncestorObject('BrowseContinentsPage');
	}

	public function getCountry () {
		return $this->getAncestorObject('BrowseCountriesPage');
	}

	public function getCurrency () {
		return $this->getCountry->Currency;
	}

	public function getRegion () {
		return $this->getAncestorObject('BrowseRegionsPage');
	}

	public function getCity () {
		return $this->getAncestorObject('BrowseCitiesPage');
	}

	private function getAncestorObject($type, $obj = null) {
		if(!$obj) {
			$child = $this;
		}
		else {
			$child = $obj;
		}
		$parent = $child->parent();
		if( $parent->ClassName == $type ) {
			return $parent;
		}
		else if ( $child->ID != 0 ) {
			return $this->getAncestorObject($type, $parent);
		}
		else {
			return false;
		}
	}

	function getHiddenEmail (){
		if($this->ListingEmail) {
			$array = explode("@",$this->ListingEmail);
			return "mailto/".$array[0]."/".$array[1].'/'.urlencode('enquiry from www.friars.co.nz')."/";
		}
	}

	function getDescriptiveEmail (){
		if($this->ListingEmail) {
			$array = explode("@",$this->ListingEmail);
			$array[1] = explode(".", $array[1]);
			return "".$array[0]." [at] ".implode(" . ",$array[1]);
		}
	}

	function setSidebarImage() {
		return false;
	}

}

class BusinessPage_Controller extends Page_Controller {

	static $allowed_actions = array(
		'showPagePointsMapXML',
		'updateMeXML',
		'EditForm'
	);

	public function init() {
		parent::init();
	}

	/*
		This function should be overriden in page-types where not random image is required
	*/

	function SidebarImage() {
		return false;
	}


	public function index($action) {
		//Debug::show($action);
		//Debug::show($this->owner->isAjax);
		if ($this->isAjax) {
			//$data = DataObject::get_by_id('BusinessPage', (int)$action['ID']);
			return $this->renderWith('AjaxBusinessDetails');
		}
		else {
			if($this->canEditCurrentPage()) {
				$this->addUpdateServerUrlDragend();
				$this->addMap("showPagePointsMapXML");
			}
			else {
				$this->addUpdateServerUrlDragend();
				$this->addMap("showPagePointsMapXML");
			}
			return Array();
		}
	}

	function getGeoPoints() {
		return DataObject::get("GoogleMapLocationsObject", "ParentID = ".$this->ID);
	}

	function EditForm() {
		if($this->canEditCurrentPage()) {
			$fields = $this->getCMSFields();
			$resultAction = new FormAction("savedata","save");
			$actions = new FieldSet(
				 $resultAction
			);
			$validator = new RequiredFields();
			$form = new Form($this, "EditForm", $fields, $actions, $validator);
			$form->loadDataFrom($this);
			return $form;
		}
	}

	function savedata($data = null, $form = null){
		$business = DataObject::get_by_id("BusinessPage",$this->ID) ;
		$form->saveInto($business);
		$business->writeToStage('Stage');$business->publish('Stage', 'Live');
		Director::redirectBack();
	}

}
