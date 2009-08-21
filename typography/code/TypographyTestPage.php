<?php
/**
 * Add a page to your site that allows you to view all the html that can be used in the typography section - if applied correctly.
 *
 * TO DO: add a testing sheet with a list of checks to be made (e.g. italics, bold, paragraphy) - done YES / NO, a date and a person who checked it (member).
 */


class TypographyTestPage extends Page {

	static $icon = "typography/images/treeicons/TypographyTestPage";

	protected static $auto_include = false;

	static function setAutoInclude($value) {
		self::$auto_include = $value;
	}

	static $defaults = array(
		"URLSegment" => "typo",
		"ShowInMenus" => false,
		"ShowInSearch" => false,
		"Title" => "Typography Test",
		"Content" => 'auto-completed - do not alter',
	);

	public function requireDefaultRecords() {
		parent::requireDefaultRecords();
		if(self::$auto_include) {
			$check = DataObject::get_one("TypographyTestPage");
			if(!$check) {
				$page = new TypographyTestPage();
				$page->ShowInMenus = 0;
				$page->ShowInSearch = 0;
				$page->ShowInSearch = 0;
				$page->Title = "typography test page";
				$page->MetaTitle = "typography test page";
				$page->PageTitle = "typography test page";
				$page->Sort = 99999;
				$page->URLSegment = "typo";
				$page->writeToStage('Stage');
				$page->publish('Stage', 'Live');
				Database::alteration_message("TypographyTestPage","created");
			}
		}
		if($page = DataObject::get_one("SiteTree", 'ClassName <> "TypographyTestPage" AND URLSegment = "typo"')) {
			Database::alteration_message("WARNING: there is a page called typo - this is usually reserved for TypographyTestPage - but the page called typo is not a TypographyTestPage, but rather a ".$page->ClassName,"deleted");
		}
	}


}

class TypographyTestPage_Controller extends Page_Controller {

	static $allowed_actions = array("typographyhtml", "test");


	function init() {
		parent::init();

		Requirements::javascript("jsparty/jquery/jquery.js");
		Requirements::javascript('typography/javascript/typography.js');
	}

	public function index() {
		$this->Content = $this->renderWith("TypographySample");
		return array();
	}

	function Form() {
		$array = array();
		$array[] = "green";
		$array[] = "yellow";
		$array[] = "blue";
		$array[] = "pink";
		$array[] = "orange";
		$errorField = new TextField($name = "ErrorField", $title = "Text Field Example");
		$errorField->setError("there is an error", "required");
		$form = new Form(
			$controller = $this,
			$name = "TestForm",
			$fields = new FieldSet(
				// List the your fields here
				new HeaderField($name = "HeaderField1", $title = "HeaderField Level 1", 1),
				new TextField($name = "TextField", $title = "Text Field Example"),
				new HeaderField($name = "HeaderField2", $title = "Error Message", 2),
				$errorField,
				new TextareaField($name = "TextareaField", $title = "Textarea Field", 5, 5),
				new EmailField("EmailField", "Email address"),
				new HeaderField($name = "HeaderField2", $title = "HeaderField Level 2", 2),
				new DropdownField($name = "DropdownField",$title = "Dropdown Field",$source = Geoip::getCountryDropDown()),
				new OptionsetField($name = "OptionsetField",$title = "Optionset Field",$array),
				new CheckboxSetField($name = "CheckboxSetField",$title = "Checkbox Set Field",$array),
				new HeaderField($name = "HeaderField3", $title = "HeaderField Level 3", 3),
				new NumericField($name = "NumericField", $title = "Numeric Field "),
				new DateField($name = "DateField", $title = "Date Field"),
				new CheckboxField($name = "CheckboxField", $title = "Checkbox Field")
			),
			$actions = new FieldSet(
					// List the action buttons here
					new FormAction("signup", "Sign up")

			),
			$requiredFields = new RequiredFields(
					// List the required fields here: "Email", "FirstName"
			)
		);
		return $form;
	}

	function TestForm() {
		die("thank you for signing up to twenty years of free chocolate");
	}

	function typographyhtml() {
		return $this->renderWith("TypographySample");
	}

}


