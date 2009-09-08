<?php


/**
* allows you to check rules like this
* if(Modifier A::variable = ... set Modifier B::variable to ...)
* variables can also be function
* it checks for the function first and then does the variable
*
*/

class ModifierRulesModifier extends OrderModifier {

// 					 *** static variables

	public static $db = array(
		'Name' => 'Varchar(255)',
		'RulesApplied' => 'Varchar(255)',
		'DebugMessage' => 'Text'
	);

	protected static $rule_array = array();

// 					 *** static functions

	static function set_rule($nameModifierA, $functionOrVariableNameA, $valueOrValueArrayA, $nameModifierB, $functionOrVariableNameB, $valueB) {
		self::$rule_array[] = array(
			"NameModifierA" => $nameModifierA,
			"FunctionOrVariableNameA" => $functionOrVariableNameA,
			"ValueOrValueArrayA" => $valueOrValueArrayA,
			"NameModifierB" => $nameModifierB,
			"FunctionOrVariableNameB" => $functionOrVariableNameB,
			"ValueB" => $valueB,
		);
	}

	static function show_form() {
		false;
	}

	static function get_form($controller) {
		return false;
	}

	function __construct() {
		parent::__construct();
		//$this->applyRules();
	}


// 					 *** display functions
	function CanRemove() {
		return false;
	}

	function ShowInTable() {
		return false;
	}

// 					 *** inclusive / exclusive functions
// 					 *** table values
	function LiveAmount() {
		$this->applyRules();
		return 0;
	}

	function TableValue() {
		return "";
	}


// 					 *** table titles
	function LiveName() {
		return "";
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


// 					 *** calculations
	protected function applyRules() {
		$rulesToBeApplied = Array();
		$modifierArray = ShoppingCart::get_modifiers();
		//go through all the modifiers
		foreach($modifierArray as $modifier) {
			//go through all the rules
			foreach(self::$rule_array as $key => $rule) {
				//does modifier match rule
				if($modifier->ClassName == $rule["NameModifierA"]) {
					//get value from A
					$valueA = self::getValueFromClass($modifier, $rule["FunctionOrVariableNameA"]);
					//does value A match the rule conditional value
					$this->debugMessage .="value is: $valueA";
					if( (is_array($rule["ValueOrValueArrayA"]) && in_array($valueA, $rule["ValueOrValueArrayA"]) ) || ($valueA == $rule["ValueOrValueArrayA"])) {
						//add rules
						$rulesToBeApplied[$key] = $key;
					}
				}
			}
		}
		if(is_array($rulesToBeApplied) && count($rulesToBeApplied)) {
			foreach($modifierArray as $modifier) {
				foreach($rulesToBeApplied as $ruleNumberToApply) {
					$rule = self::$rule_array[$ruleNumberToApply];
					if($modifier->class == $rule["NameModifierB"]) {
						self::setValueInClass($modifier, $rule['FunctionOrVariableNameB'], $rule["ValueB"]);
					}
				}
			}
			$rulesImploded = implode(",", $rulesToBeApplied);
			$this->debugMessage .= "rules: ".$rulesImploded;
			return $rulesImploded;
		}
		return "";
	}

	protected function getValueFromClass($classObject, $functionName) {
		$this->debugMessage .="Getting Value for {$classObject->ClassName} :: $functionName";
		//method
		$this->debugMessage .=$classObject->PickupOrDeliveryType();
		if(method_exists($classObject,$functionName)) {
			return $classObject->$functionName();
		}
		//static method: DOES NOT WORK
		elseif(method_exists($classObject->ClassName,$functionName)) {
			eval("return {$classObject->ClassName}::{$functionName}();");
			die("not implemented A $classObject->ClassName::$functionName"); //return $classObject::$functionName();
		}
		//variable
		elseif($classObject->$functionName) {
			return $classObject->$functionName;
		}
		//static variable
		else {
			eval("return {$classObject->ClassName}::{$functionName};");
			die("not implemented B"); //$classObject::$functionName;
		}
		return "";
	}

	protected function setValueInClass($classObject, $functionName, $value) {
		$this->debugMessage .="trying {$classObject->ClassName} :: $functionName setting it to $value";
		//method
		if(method_exists($classObject,$functionName)) {
			$this->debugMessage .="applying rule to  method";
			return $classObject->$functionName($value);
		}
		//static method: DOES NOT WORK
		elseif(method_exists($classObject->ClassName,$functionName)) {
			$this->debugMessage .="applying rule to  static method";
			eval("return {$classObject->ClassName}::{$functionName}($value);");
			die("not implemented A $classObject->ClassName::$functionName"); //return $classObject::$functionName();
		}
		//variable
		elseif($classObject->$functionName) {
			$this->debugMessage .="applying rule to variable";
			return $classObject->$functionName = $value;
		}
		//static variable
		else {
			$this->debugMessage .="applying rule to static variale ";
			eval("return {$classObject->ClassName}::{$functionName} = $value;");
			die("not implemented B"); //$classObject::$functionName;
		}
		return "";
	}


// 					 *** database functions
	public function onBeforeWrite() {
		parent::onBeforeWrite();
		$this->RulesApplied = $this->applyRules();
		$this->Name = $this->LiveName();
	}
}



