<?php
/**
 *@author nicolaas [at] sunnysideup.co.nz
 *@description: adds dataobject sorting functionality
 *
 *@package: dataobjectsorter
 **/

class DataObjectSorterDOD extends DataObjectDecorator {

	static function set_also_update_sort_field($b) {user_error("This method has been depreciated. You can remove this notice by removing DataObjectSorterDOD::set_also_update_sort_field from your _config file.", E_USER_NOTICE);}

	static function set_do_not_add_alternative_sort_field($b) {user_error("This method has been depreciated. You can remove this notice by removing DataObjectSorterDOD::set_do_not_add_alternative_sort_field from your _config file. ", E_USER_NOTICE);}

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
							UPDATE \"".$baseDataClass."\"
							SET \"".$baseDataClass."\".\"".$field."\" = ".$position. "
							WHERE \"".$baseDataClass."\".\"ID\" = ".$id."
								AND (\"".$baseDataClass."\".\"".$field."\" <> ".$position." )
							LIMIT 1;";
						//echo $sql .'<hr />';
						DB::query($sql);
						if("SiteTree" == $baseDataClass) {
							$sql_Live = str_replace("\"SiteTree\"", "\"SiteTree_Live\"", $sql);
							//echo $sql_Live .'<hr />';
							DB::query($sql_Live);
						}
					}
				}
			}
			return _t("DataObjectSorter.UPDATEDRECORDS", "Updated record(s)");
		}
		else {
			return _t("DataObjectSorter.NOACCESS", "You do not have access rights to make these changes");
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


