<?php
/**
 * This class replaces the actions in modeladmin for loading pages into modeladmin
 * the "doSave" function saves and publishes
 * @package ecommercextras
 * @subpackage modeladmin
 */

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
		//in order of appearance
		//$form->unsetActionByName("action_doDelete"); - USEFUL TO KEEP
		$form->unsetActionByName("action_unpublish");
		$form->unsetActionByName("action_delete");
		$form->unsetActionByName("action_save");
		$form->unsetActionByName("action_publish");
		//$form->unsetActionByName("action_doSave"); - USEFUL TO KEEP
		return $form;
	}

	function doSave($data, $form, $request) {
		$form->saveInto($this->currentRecord);
		$this->currentRecord->writeToStage("Stage");
		$this->currentRecord->publish("Stage", "Live");
		$this->currentRecord->flushCache();
		if(Director::is_ajax()) {
			return $this->edit($request);
		} else {
			Director::redirectBack();
		}
	}


	function doDelete() {
		user_error("this function has not been implemented yet", E_USER_NOTICE);
		//might be prudent not to allow deletions as products should not be deleted, but rather be made "not for sale"
	}

}