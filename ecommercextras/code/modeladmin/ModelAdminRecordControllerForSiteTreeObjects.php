<?php

class ModelAdminRecordControllerForSiteTreeObjects extends ModelAdmin_RecordController{

	protected static $actions_to_keep = array(
		"Back",
		"doDelete",
		"doSave"
	);

	/**
	 * Returns a form for editing the attached model
	 */
	public function EditForm() {
		$form = parent::EditForm();
		$oldActions = $form->Actions();
		//$form->unsetAllActions();
		//in order of appearance
		//$form->unsetActionByName("action_doDelete");
		$form->unsetActionByName("action_unpublish");
		$form->unsetActionByName("action_delete");
		$form->unsetActionByName("action_save");
		$form->unsetActionByName("action_publish");
		//$form->unsetActionByName("action_doSave");
		return $form;
	}

	function doSave($data, $form, $request) {
		$form->saveInto($this->currentRecord);
		$this->currentRecord->write();
		$this->currentRecord->writeToStage("Stage");
		$this->currentRecord->publish("Stage", "Live");
		$this->currentRecord->flushCache();

		// Behaviour switched on ajax.
		if(Director::is_ajax()) {
			return $this->edit($request);
		} else {
			Director::redirectBack();
		}
	}


	function doDelete() {
		user_error("this function has not been implemented yet", E_USER_NOTICE);
	}

}