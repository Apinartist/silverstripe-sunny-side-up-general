<?php
/**
 *@author nicolaas [at] sunnysideup.co.nz
 *@description: adds dataobject sorting functionality
 *
 *@package: dataobjectsorter
 **/

class DataObjectSorterDOD extends DataObjectDecorator {

	static function set_also_update_sort_field($b) {user_error("This method has been depreciated", E_USER_NOTICE);}

	static function set_do_not_add_alternative_sort_field($b) {user_error("This method has been depreciated", E_USER_NOTICE);}

	function extraStatics(){
		//this is not actually working because in dev/build, this statement is executed BEFORE the settting above is applied!
		//maybe add field in another way????
		return array(
			'db' =>   array(
				"Sort" => "Int"
			)
		);
	}



	function dodataobjectsort() {
		$bt = defined('DB::USE_ANSI_SQL') ? "\"" : "`";
		if(!Permission::check("CMS_ACCESS_CMSMain")) {
			Security::permissionFailure($this, _t('Security.PERMFAILURE',' This page is secured and you need administrator rights to access it. Enter your credentials below and we will send you right along.'));
		}
		$i = 0;
		if($this->owner->canEdit()) {
			$extraSet = '';
			$extraWhere = '';
			$field = "Sort";
			$baseDataClass = ClassInfo::baseDataClass($this->owner->ClassName);
			if($baseDataClass) {
				if(isset ($_REQUEST["dos"])) {
					foreach ($_REQUEST['dos'] as $position => $id) {
						$i++;
						$position = intval($position);
						$id = intval($id);
						$sql = "
							UPDATE {$bt}".$baseDataClass."{$bt}
							SET {$bt}".$baseDataClass."{$bt}.{$bt}".$field."{$bt} = ".$position. "
							WHERE {$bt}".$baseDataClass."{$bt}.{$bt}ID{$bt} = ".$id."
								AND ({$bt}".$baseDataClass."{$bt}.{$bt}".$field."{$bt} <> ".$position." )
							LIMIT 1;";
						//echo $sql .'<hr />';
						DB::query($sql);
						if("SiteTree" == $baseDataClass) {
							$sql_Live = str_replace("{$bt}SiteTree{$bt}", "{$bt}SiteTree_Live{$bt}", $sql);
							//echo $sql_Live .'<hr />';
							DB::query($sql_Live);
						}
					}
				}
			}
			return "Updated record(s)";
		}
		else {
			return "please log-in as an administrator to make changes to the sort order";
		}
	}

	/**
	 *legacy function
	 **/

	function dataObjectSorterPopupLink($filterField = '', $filterValue = '') {
		return DataObjectSorterController::popup_link($this->owner->ClassName, $filterField, $filterValue, $linkText = "Sort ".$this->owner->plural_name());
	}

	function updateCMSFields(&$fields) {
		$fields->removeFieldFromTab("Root.Main", "Sort");
		$link = self::dataObjectSorterPopupLink();
		$fields->addFieldToTab("Root.Sort", new LiteralField("DataObjectSorterPopupLink", $link));
	}

}


