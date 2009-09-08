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
		'RulesApplied' => 'Varchar(255)'
	);

	protected static $rule_array = array();

// 					 *** static functions

	static function set_rule($nameModifierA, $functionOrVariableNameA, $valueOrValueArrayA, $nameModifierB, $functionOrVariableNameB, $valueB) {
		$rule_array[] = array(
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
		$this->applyRules();
		return false;
	}

	function __construct() {
		parent::__construct();
		$this->applyRules();
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

	function Description() {
		if($this->ID) {
			return $this->Description;
		}
		else {
			return $this->applyRules();
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
				if($modifier->class == $rule["NameModifierA"]) {
					//get value from A
					$valueA = self::getValueFromClass($modifier, $rule["FunctionOrVariableNameA"]);
					//does value A match the rule conditional value
					if( (is_array($rule["ValueOrValueArrayA"]) && in_array($valueA, $rule["ValueOrValueArrayA"]) ) || ($valueA == $rule["ValueOrValueArrayA"]))
						//add rules
						$rulesToBeApplied[$key] = $key;
					}
				}
			}
		}
		foreach($modifierArray as $modifier) {
			foreach($rulesToBeApplied as $ruleNumberToApply) {
				$rule = self::$rule_array[$ruleNumberToApply];
				if($modifier->class == $rule["NameModifierB"]) {
					self::setValueInClass($modifier, $rule['FunctionOrVariableNameB'], $rule["valueB"]);
				}
			}
		}
		return implode(","$rulesToBeApplied);
	}

	function getValueFromClass($classObject, $functionName) {
		//static method
		if(method_exists($classObject->class,$functionName)) {
			return $classObject::$functionName();
		}
		//method
		elseif(method_exists($classObject,$functionName))
			return $classObject->$functionName();
		}
		//variable
		elseif($classObject->$functionName) {
			return $classObject->$functionName;
		}
		//static variable
		else {
			$classObject::$functionName;
		}
		return "";
	}

	function setValueInClass($classObject, $functionName, $value) {
	//NOTE: property_exists allows to check for static variables, but only available from PHP 5.3
		if(method_exists($classObject->class,$functionName)) {
			$classObject::$functionName($value);
		}
		elseif(method_exists($classObject,$functionName))
			$classObject->$functionName($value);
		}
		elseif($classObject->$functionName) {
			$classObject->$functionName = $value;
		}
		else {
			$classObject::$functionName = $value;
		}
	}


// 					 *** database functions
	public function onBeforeWrite() {
		parent::onBeforeWrite();
		$this->RulesApplied = $this->applyRules();
		$this->Name = $this->LiveName();
	}
}



