<?php

class CMSHelp extends ContentController {

	/**
	 *@var String name of the directory in which the help files are kept
	 *
	 */
	protected static $help_file_directory_name = "_help";
		static function set_help_file_directory_name($s) {self::$help_file_directory_name = $s;}
		static function get_help_file_directory_name() {return self::$help_file_directory_name;}


	/**
	 *@var String urlsegment for the controller
	 *
	 */
	protected static $url_segment = "admin/help";
		static function set_url_segment($s) {self::$url_segment = $s;}
		static function get_url_segment() {return self::$url_segment;}


	/**
	 * standard SS Method
	 *
	 */
	function index() {
		Requirements::themedCSS("typography");
		Requirements::javascript(THIRDPARTY_DIR."/jquery/jquery.js");
		return array();
	}

	/**
	 * @return Object - DataObjectSet of help files
	 *
	 *
	 */
	function HelpFiles(){
		$dos = new DataObjectSet();
		$fileArray = $this->getListOfHelpFiles();
		if($fileArray && count($fileArray)) {
			$linkArray = array();
			foreach($fileArray as $file) {
				$dos->push(new ArrayData($file));
			}
		}
		return $dos;
	}

	/**
	 * @return String - title for project
	 *
	 *
	 */
	function SiteTitle(){
		$sc = SiteConfig::current_site_config();
		if($sc && $sc->Title) {
			return $sc->Title;
		}
		return Director::absoluteURL();
	}


	/**
	 * @return Array - array of help files
	 *
	 *
	 */
	private function getListOfHelpFiles() {
		$fileArray = array();
		$directory = "/".self::get_help_file_directory_name()."/";
		$baseDirectory = Director::baseFolder().$directory;
		//get all image files with a .jpg extension.
		$images = $this->getDirectoryList($baseDirectory , array("png", "jpg"));
		//print each file name
		if($images && count($images)) {
			foreach($images as $key => $image){
				if($image) {
					if(file_exists($baseDirectory.$image)) {
						$fileArray[$key]["Link"] = $directory.$image;
						$fileArray[$key]["Title"] = $this->spaceBeforeCapital($image);
					}
				}
			}
		}
		return $fileArray;
	}


	/**
	 * @param String $directory - location of the directory
	 * @param Array $extensionArray - array of extensions to include (e.g. Array("png", "mov");)
	 *
	 * @return Array - list of all files in a directory
	 */
	private function getDirectoryList ($directory, $extensionArray) {
		//	create an array to hold directory list
		$results = array();
		// create a handler for the directory
		$handler = opendir($directory);
		//if(!is_dir($directory)) {
		//	return false;
		//}
		// open directory and walk through the filenames
		while ($file = readdir($handler)) {
			// if file isn't this directory or its parent, add it to the results
			if ($file != "." && $file != ".." && !is_dir($file)) {
				//echo $file;
				$extension = substr(strrchr($file, '.'), 1);
				if(in_array($extension, $extensionArray)) {
					$results[] = $file;
				}
			}
		}
	// tidy up: close the handler
		closedir($handler);
		// done!
		asort($results);
		return $results;
	}

	/**
	 * returns the Link to the controller
	 *
	 * @return String -
	 */
	function Link() {
		return "/".self::$url_segment."/";
	}



	/**
	 * returns the Link to the controller
	 * @param String $string - input
	 * @return String
	 */
	private function spaceBeforeCapital($string){
		$string = preg_replace('/(?<!\ )[A-Z\-]/', ' $0', $string);
		$extension = substr(strrchr($string, '.'), 0);
		$string = str_replace(array('-', $extension, '.'),"", $string);
		return $string;
	}


}
