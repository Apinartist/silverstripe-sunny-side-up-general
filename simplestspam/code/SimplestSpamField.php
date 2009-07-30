<?php
/**
 * Provides an {@link FormField} which allows form to validate for non-bot submissions
 * by giving them a challenge answer a question
 *
 * @module SimplestSpam
 */
class SimplestSpamField extends SpamProtectorField {

	protected static $questions_and_answers = array();

	protected static $default_questions_and_answers = array(
		array("The English name for the capital of Italy is ... ", "Rome"),
		array("The English name for the highest mountain the the world is Mount ... ", "Everest"),
		array("The Maori name for New Zealand is ... (try Aotearoa)", "Aotearoa")
	);

	protected static $explanation_message = "this question is here to prevent spam";

	protected static $wrong_answer_message = "please check anti-spam field to proceed";

	protected static $form_wrong_answer_message = "Submission was NOT successful. Please check anti-spam field.";

	protected static $has_been_initialised = false;

	static function set_question_and_answer($question, $answer) {
		self::$questions_and_answers[] = array($question, $answer);
	}

	static function set_explanation_message($v) {
		self::$explanation_message = $v;
	}

	static function set_wrong_answer_message($v) {
		self::$form_wrong_answer_message = $v;
	}

	static function set_form_wrong_answer_message($v) {
		self::$wrong_answer_message = $v;
	}


	protected function initialise() {
		if(!count(self::$questions_and_answers)) {
			self::$questions_and_answers = self::$default_questions_and_answers;
		}
		if(!self::$has_been_initialised && !isset($_REQUEST['SimplestSpam_challenge_field']) && count(self::$questions_and_answers)) {
			$randomNumber = rand(0, count(self::$questions_and_answers)-1);
			Session::set("SimplestSpamQuestion", $randomNumber + 1); // adding one to make it easier to work out if anything has been entered, i.e. 0 could be nothing or first question
			self::$has_been_initialised = true;
		}
	}

	public function Field() {
		$this->initialise();
		$html = '<input type="text" name="SimplestSpam_challenge_field" />';
		return $html;
	}

	function FieldHolder() {
		$this->initialise();
		$expected_question_answer_array = $this->getQuestionAnswerArray();
		$Question = $expected_question_answer_array[0];
		$Explanation = self::$explanation_message;
		if($Explanation) {
			$Explanation = "(".$Explanation.")";
		}
		$Title = $this->XML_val('Title');
		$Message = $this->XML_val('Message');
		$MessageType = $this->XML_val('MessageType');
		$Type = $this->XML_val('Type');
		$extraClass = $this->XML_val('extraClass');
		$Name = $this->XML_val('Name');
		$Field = $this->XML_val('Field');
		$messageBlock = (!empty($Message)) ? "<span class=\"message $MessageType\">$Message</span>" : "";
		return <<<HTML
<div id="$Name" class="field $Type $extraClass"><span class="spamquestion">{$Question} {$Explanation} </span>{$Field}{$messageBlock}</div>
HTML;
	}

	public function validate($validator) {
		// don't bother querying the SimplestSpam-service if fields were empty
		if(
			!isset($_REQUEST['SimplestSpam_challenge_field'])
			|| empty($_REQUEST['SimplestSpam_challenge_field'])
		) {
			$validator->validationError(
				$this->name,
				self::$wrong_answer_message,
				"validation",
				false
			);
			return false;
		}

		$response = $_REQUEST['SimplestSpam_challenge_field'];
		$expected_question_answer_array = $this->getQuestionAnswerArray();
		if(!isset($expected_question_answer_array[1])) {
			user_error("SimplestSpamField::validate(): could not find answer - sorry, please try again'", E_USER_ERROR);
			return false;
		}
		if($this->cleanupAnswer($expected_question_answer_array[1]) != $this->cleanupAnswer($response)) {
			// Internal error-string returned by SimplestSpam, e.g. "incorrect-captcha-sol".
			// Used to generate the new iframe-url/js-url after form-refresh.
			$error = "answer incorect";
			Session::set("FormField.{$this->form->FormName()}.{$this->Name()}", trim($error));
			$validator->validationError(
				$this->name,
				self::$wrong_answer_message,
				"validation",
				false
			);
			$this->form->sessionMessage(self::$form_wrong_answer_message, "bad");
			return false;
		}

		//passed all tests
		return true;
	}

	protected function cleanupAnswer($v) {
		return trim(strtolower($v));
	}

	protected function getQuestionAnswerArray() {
		$this->initialise();
		$number = Session::get("SimplestSpamQuestion");
		if($number > 0) {
			$number = $number - 1;
			if(isset(self::$questions_and_answers[$number])) {
				$expected_question_answer_array = self::$questions_and_answers[$number];
				return $expected_question_answer_array;
			}
			else {
				$error = "selected question not found: ".$number;
			}
		}
		else {
			$error = "no question selection made";
		}
		return array("Is this a spam entry - please answer no to submit", "no");
	}


}
