<?php


class DataIntegrityTest extends DatabaseAdmin {

	protected static $test_array = array(
		"In SiteTree_Live but not in SiteTree" =>
    	"SELECT SiteTree.ID, SiteTree.Title FROM SiteTree_Live RIGHT JOIN SiteTree ON SiteTree_Live.ID = SiteTree.ID WHERE SiteTree.ID IS NULL;",
		"ParentID does not exist in SiteTree" =>
			"SELECT SiteTree.ID, SiteTree.Title FROM SiteTree RIGHT JOIN SiteTree Parent ON SiteTree.ParentID = Parent.ID Where SiteTree.ID IS NULL and SiteTree.ParentID <> 0;",
		"ParentID does not exists in SiteTree_Live" =>
			"SELECT SiteTree_Live.ID, SiteTree_Live.Title FROM SiteTree_Live RIGHT JOIN SiteTree_Live Parent ON SiteTree_Live.ParentID = Parent.ID Where SiteTree_Live.ID IS NULL and SiteTree_Live.ParentID <> 0;",
	);

	/**
	*@param array = should be provided as follows: array("Member.UselessField1", "Member.UselessField2", "SiteTree.UselessField3")
	*/
	protected static $fields_to_delete = array();
		static function set_fields_to_delete($array) {self::$fields_to_delete = $array;}

	function index() {
		echo "<h2>Database Administration Helpers</h2>";
		echo "<p><a href=\"obsoletefields\">Prepare a list of obsolete fields.</a></p>";
		echo "<p><a href=\"deletefields\">Delete marked fields.</a></p>";
	}

	public function obsoletefields() {
		$dataClasses = ClassInfo::subclassesFor('DataObject');
		//remove dataobject
		array_shift($dataClasses);
		foreach($dataClasses as $dataClass) {
			// Check if class exists before trying to instantiate - this sidesteps any manifest weirdness
			if(class_exists($dataClass)) {
				$dataObject = singleton($dataClass);
				$requireFields = $dataObject->databaseFields();
				$actualFields = DB::fieldList($dataClass);
				if($actualFields) {
					foreach($actualFields as $actualField) {
						if(!in_array($actualField, $requiredField)) {
							Database::alteration_message("$actualField in $dataClass might not be required. HOWEVER - THIS IS ONLY A GUESS, PLEASE DOUBLE-CHECK!", "deleted");
						}
					}
				}
			}
		}

	}

	public function deletefields() {
		foreach(self::$fields_to_delete as $key => $tableDotField) {
			$tableFieldArray = explode(".", $tableDotField);
			$this->deleteField($tableFieldArray[0], $tableFieldArray[1]);
		}
	}

	protected function deleteField($table, $field) {
		$fields = DB::fieldList($table);
		$tables = DB::tableList()
		if(!in_array($field, $fields)) {
			return false;
		}
		elseif(!in_array($table, $tables)) {
			return false;
		}
		elseif(!class_exists($table) {
			return false;
		}
		else {
			DB::query('ALTER TABLE `'.$table.'` DROP `'.$field.'`;');
			Database::alteration_message("Deleted $field in $table", "deleted");
			$obj = singleton($table);
			//to do: make this more reliable - checking for versioning rather than SiteTree
			if($obj instanceof SiteTree) {
				DB::query('ALTER TABLE `'.$table.'_Live` DROP `'.$field.'`;');
				Database::alteration_message("Deleted $field in {$table}_Live", "deleted");
				DB::query('ALTER TABLE `'.$table.'_versions` DROP `'.$field.'`;');
				Database::alteration_message("Deleted $field in {$table}_versions", "deleted");
			}
		}
	}

}