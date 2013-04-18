<?php

class ValidationDE extends DataExtension {
	
	public function validate(ValidationResult $validationResult) {
		$requiredFields = Config::inst()->get($this->owner->class, 'required_fields', Config::INHERITED);
		if($requiredFields) {
			foreach($requiredFields as $name) {
				$error = false;
				if($this->owner->hasMethod($name)) {
					$object = $this->owner->$name();
					if(! $object->exists()) {
						$error = true;
					}
				}
				else if(! $this->owner->$name) {
					$error = true;
				}
				if($error) {
					$label = $this->owner->fieldLabel($name);
					$validationResult->error(sprintf(_t('Form.FIELDISREQUIRED', '%s is required'), "\"$label\""), $name);
				}
			}
		}
		return $validationResult;
	}
}