<?php
/**
 * ExpiryDate field, contains validation and formspec for expirydate fields.
 * @package forms
 * @subpackage fields-formattedinput
 */
class ExpiryDateField extends TextField {

	function Field() {
		$parts = explode("\n", chunk_split($this->value,2,"\n"));
		$parts = array_pad($parts, 2, "");
		$field = "
			<span id=\"{$this->name}_Holder\" class=\"expiryDateField\">
				<select class=\"expiryDate expiryDateFirst\" autocomplete=\"off\" name=\"{$this->name}[0]\" value=\"$parts[0]\" " . $this->getTabIndexHTML(0) . ">
					<option value=\"\">Month</option>".$this->makeSelectList($this->monthArray())."
				</select>
				<select class=\"expiryDate expiryDateLast\"autocomplete=\"off\" name=\"{$this->name}[1]\" value=\"$parts[1]\" " . $this->getTabIndexHTML(1) . " />
					<option value=\"\">Year</option>".$this->makeSelectList($this->yearArray())."
				</select>
			</span>";
		return $field;
	}
	function dataValue() {
		if(is_array($this->value)) return implode("", $this->value);
		else return $this->value;
	}

	function jsValidation() {
		$formID = $this->form->FormName();
		$error1 = _t('ExpiryDateField.VALIDATIONJS1', 'Please ensure you have entered the');
		$error2 = _t('ExpiryDateField.VALIDATIONJS2', 'expiry date correctly.');
		$first = _t('ExpiryDateField.FIRST', 'first');
		$second = _t('ExpiryDateField.SECOND', 'second');
		$jsFunc =<<<JS
Behaviour.register({
	"#$formID": {
		validateExpiryDate: function(fieldName) {
			if(!$(fieldName + "_Holder")) return true;

			// Creditcards are split into multiple values, so get the inputs from the form.
			var expiryParts = $(fieldName + "_Holder").getElementsByTagName('input');

			var cardisnull = true;
			var i=0;

			for(i=0; i < expiryParts.length ; i++ ){
				if(expiryParts[i].value == null || expiryParts[i].value == "")
					cardisnull = cardisnull && true;
				else
					cardisnull = false;
			}
			if(!cardisnull){
				// Concatenate the string values from the parts of the input.
				for(i=0; i < expiryParts.length ; i++ ){
					// The expirydate cannot be null, nor have less than 2 digits.
					if(
						expiryParts[i].value == null || expiryParts[i].value == "" ||
						expiryParts[i].value.length < 2 ||
						!expiryParts[i].value.match(/[0-9]{2}/)
					){
						switch(i){
							case 0: number = "$first"; break;
							case 1: number = "$second"; break;
						}
						validationError(expiryParts[i],"$error1 " + number + " $error2","validation",false);
					return false;
					}
				}
			}
			return true;
		}
	}
});
JS;
		Requirements :: customScript($jsFunc, 'func_validateExpiryDate');

		return "\$('$formID').validateExpiryDate('$this->name');";
	}

	function validate($validator){
		// If the field is empty then don't return an invalidation message
		if(!trim(implode("", $this->value))) return true;

		$i=0;
		if($this->value) foreach($this->value as $part){
			if(!$part || !(strlen($part) == 2) || !ereg("([0-9]{2})",$part)){
				switch($i){
				  case 0: $number = _t('ExpiryDateField.FIRST', 'first'); break;
					case 1: $number = _t('ExpiryDateField.SECOND', 'second'); break;
				}
				$validator->validationError(
					$this->name,
					sprintf(
						_t('Form.VALIDATIONEXPIRYDATE', "Please ensure you have entered the %s expiry date correctly."),
						$number
					),
					"validation",
					false
				);
				return false;
			}
		$i++;
		}
	}

	protected function yearArray() {
		$list = array();
		$i = 0;
		for($i = 0; $i < 12; $i++) {
			$ts = strtotime("+".$i." year");
			$list[Date("y", $ts)] = Date("Y", $ts);
		}
		return $list;
	}

	protected function makeSelectList() {
		$array = $this->yearArray();
		$string = '';
		foreach($array as $key => $value) {
			$string .= '<option value="'.$key.'">'.$value.'</option>';
		}
		return $string;
	}

	protected function monthArray() {
		return array(
			"01" => "January",
			"02" => "February",
			"03" => "March",
			"04" => "April",
			"05" => "May",
			"06" => "June",
			"07" => "July",
			"08" => "August",
			"09" => "September",
			"10" => "October",
			"11" => "November",
			"12" => "December"
		);
	}

}
