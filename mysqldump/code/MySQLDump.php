<?php

/**
	* based on a class MySQLDump by Marcus Vinícius <mv[at]cidademais.com.br>
	*/
class MySQLDump extends DatabaseAdmin {


	/**
		* Dump data and structure from MySQL database
		*
		* @param string $database
		* @return string
		*/

	static $tables_excluded = array("Page_view", "regions", "cities", "continents", "countries", "Page_old");

	static $table_endings_exluded = array("_versions", "_old");

	static $table_startings_exluded = array("_obsolete");

	static $rows_per_insert = 10;



	function export() {
		$bt = defined('DB::USE_ANSI_SQL') ? "\"" : "`";
		global $databaseConfig;

		$database = $databaseConfig["database"];

		header('Content-Type: text/html; charset=utf-8');

		$fileName = 'mysql_dump'.$database.date("Y-M-D").'.sql';

		// It will be called downloaded.pdf
		header('Content-Disposition: attachment; filename="'.$fileName.'"');

		// The PDF source is in original.pdf
		//readfile($fileName);

		// Get all table names from database
		$c = 0;
		$result = mysql_list_tables($database);
		$excludeTables = array();
		for($x = 0; $x < mysql_num_rows($result); $x++) {
			$table = mysql_tablename($result, $x);
			$exclude = false;
			if (!empty($table)) {
				if(in_array($table, self::$tables_excluded)) {
					$exclude = true;
					echo "-- excluding excluding $table - exact match \n";
				}
				foreach(self::$table_startings_exluded as $starting) {
					$len = strlen($starting);
					if($len) {
						$tableStart = substr($table, 0, $len);
						if($starting == $tableStart) {
							$exclude = true;
							echo "-- excluding excluding $table - head match \n";
						}
					}
				}
				foreach(self::$table_endings_exluded as $ending) {
					$len = strlen($ending);
					if($len) {
						$tableEnd = substr($table, -$len);
						if($ending == $tableEnd) {
							$exclude = true;

							echo "-- excluding $table - tail match \n";
						}
					}
				}
				if(!$exclude) {
					$arr_tables[$c] = mysql_tablename($result, $x);
					$c++;
				}
				else {
					$excludeTables[] = $table;
				}
			}
		}

		// List tables
		$structure = '';
		$data = '';
		for ($y = 0; $y < count($arr_tables); $y++){

			$structure = '';
			// DB Table name
			$table = $arr_tables[$y];

			// Structure Header
			$structure .= "-- \n";
			$structure .= "-- Table structure for table {$bt}{$table}{$bt} \n";
			$structure .= "-- \n\n";

			// Dump Structure
			$structure .= "DROP TABLE IF EXISTS {$bt}{$table}{$bt}; \n";
			$structure .= "CREATE TABLE {$bt}{$table}{$bt} (\n";
			$result = mysql_db_query($database, "SHOW FIELDS FROM {$bt}{$table}{$bt}");
			while($row = mysql_fetch_object($result)) {

				$structure .= "  {$bt}{$row->Field}{$bt} {$row->Type}";
				$structure .= (!empty($row->Default)) ? " DEFAULT '{$row->Default}'" : false;
				$structure .= ($row->Null != "YES") ? " NOT NULL" : false;
				$structure .= (!empty($row->Extra)) ? " {$row->Extra}" : false;
				$structure .= ",\n";

			}

			$structure = ereg_replace(",\n$", "", $structure);

			// Save all Column Indexes in array
			unset($index);
			$result = mysql_db_query($database, "SHOW KEYS FROM {$bt}{$table}{$bt}");
			while($row = mysql_fetch_object($result)) {

				if (($row->Key_name == 'PRIMARY') AND ($row->Index_type == 'BTREE')) {
					$index['PRIMARY'][$row->Key_name] = $row->Column_name;
				}

				if (($row->Key_name != 'PRIMARY') AND ($row->Non_unique == '0') AND ($row->Index_type == 'BTREE')) {
					$index['UNIQUE'][$row->Key_name] = $row->Column_name;
				}

				if (($row->Key_name != 'PRIMARY') AND ($row->Non_unique == '1') AND ($row->Index_type == 'BTREE')) {
					$index['INDEX'][$row->Key_name] = $row->Column_name;
				}

				if (($row->Key_name != 'PRIMARY') AND ($row->Non_unique == '1') AND ($row->Index_type == 'FULLTEXT')) {
					$index['FULLTEXT'][$row->Key_name] = $row->Column_name;
				}

			}

			// Return all Column Indexes of array
			if (is_array($index)) {
				foreach ($index as $xy => $columns) {

					$structure .= ",\n";

					$c = 0;
					foreach ($columns as $column_key => $column_name) {

						$c++;

						$structure .= ($xy == "PRIMARY") ? "  PRIMARY KEY  ({$bt}{$column_name}{$bt})" : false;
						$structure .= ($xy == "UNIQUE") ? "  UNIQUE KEY {$bt}{$column_key}{$bt} ({$bt}{$column_name}{$bt})" : false;
						$structure .= ($xy == "INDEX") ? "  KEY {$bt}{$column_key}{$bt} ({$bt}{$column_name}{$bt})" : false;
						$structure .= ($xy == "FULLTEXT") ? "  FULLTEXT {$bt}{$column_key}{$bt} ({$bt}{$column_name}{$bt})" : false;

						$structure .= ($c < (count($index[$xy]))) ? ",\n" : false;

					}

				}

			}

			$structure .= "\n);\n\n";

			// Header
			$structure .= "-- \n";
			$structure .= "-- Dumping data for table {$bt}{$table}{$bt} \n";
			$structure .= "-- \n\n";

			// Dump data
			unset($data);
			$data = '';
			$result     = mysql_query("SELECT * FROM {$bt}{$table}{$bt}");
			$num_rows   = mysql_num_rows($result);
			$num_fields = mysql_num_fields($result);

			for ($i = 0; $i < $num_rows; $i++) {
				$startRowInstance = ( ($i / self::$rows_per_insert) == intval($i / self::$rows_per_insert) ) || 0 == $i;
				$row = mysql_fetch_object($result);
				if($startRowInstance) {
					if($i) {
						$data .= ';';
					}
					$data .= "INSERT INTO {$bt}{$table}{$bt} (";

					// Field names
					for ($x = 0; $x < $num_fields; $x++) {
						$field_name = mysql_field_name($result, $x);
						$data .= "{$bt}{$field_name}{$bt}";
						$data .= ($x < ($num_fields - 1)) ? ", " : false;
					}
					$data .= ") VALUES (";
				}
				else {
					$data .= ", (";
				}
				// Values
				for ($x = 0; $x < $num_fields; $x++) {
					$field_name = mysql_field_name($result, $x);
					$data .= "'" . str_replace('\"', '"', mysql_escape_string($row->$field_name)) . "'";
					$data .= ($x < ($num_fields - 1)) ? ", " : false;
				}
				$data .= ")\n";
			}

			$data .= ";\n";
			if($num_rows > 0) {
				$structure = "DELETE FROM {$bt}{$table}{$bt};";
				if(in_array($table."_versions", $excludeTables)) {
					$structure .= "DELETE FROM {$bt}{$table}_versions{$bt};";
				}
				echo $structure . $data;
			}
			else {
				echo "-- skipping $table --------------------------------------------------------\n\n";
			}
			echo "-- --------------------------------------------------------\n\n";
		}
	}

	public function import() {
		header("location: ../mysqldump/_third_party/BigDumpOriginal.php");
		die("to be implemented");
		$dir = dirname(getcwd());
		global $database;
		chdir("$dir/mysqldump/_third_party/");
		define("SS_DATABASE_NAME", $database);
		echo __FILE__;
		echo chroot();
		$_SERVER["PHP_SELF"] = "$dir/mysqldump/_third_party/";
		require_once("$dir/mysqldump/_third_party/BigDumpOriginal.php");
		if(file_exists("$dir/mysite/data/data.sql")) {
			define("SS_DATABASE_FILE", "../../mysite/data/data.sql");
		}
		else {
			die("no file found");
		}
	}

}
