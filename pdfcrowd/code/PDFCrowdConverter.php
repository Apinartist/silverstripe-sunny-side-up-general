<?php


class PDFCrowdConverter extends Object {

	protected static $third_party_file = "pdfcrowd/thirdparty/github/pdfcrowd.php";
		public static function set_third_party_file($s) {self::$third_party_file = $s;}

	protected static $username = "";
		public static function set_username($s) {self::$username = $s;}
		public static function get_username() {return self::$username;}

	protected static $api_key = "";
		public static function set_api_key($s) {self::$api_key = $s;}
		public static function get_api_key() {return self::$api_key;}

	protected static $singleton = null;

	protected static $save_pdfs_here = 'assets/pdfs'; //e.g. assets/pdfs
		public static function set_save_pdfs_here($s) {self::$save_pdfs_here = $s;}
		public static function get_save_pdfs_here() {return self::$save_pdfs_here;}
		
	public $pdf = null;

	/**
	 * Allows access to the cart from anywhere in code.
	 *@return ShoppingCart Object
	 */
	public static function singleton(){
		if(!self::$singleton){
			self::$singleton = new PDFCrowdConverter();
		}
		return self::$singleton;
	}

	function __construct() {
		require_once(Director::baseFolder() . '/' .self::$third_party_file);
		$this->pdf = new Pdfcrowd(self::$username, self::$api_key);
		parent::__construct();
	}

	public function ConvertURL($url, $filename, $useCacheIfAvailable = true) {
		$folderFilename = '';
		if(isset($_GET["flush"])) {
			$useCacheIfAvailable = false;
		}
		if($folderFilename = $this->file2FolderFilename($filename) && $useCacheIfAvailable) {
			if(file_exists($folderFilename)) {
				//read file
				$fh = fopen($folderFilename, 'r');
				$pdf = fread($fh, filesize($folderFilename));
				fclose($fh);
				//output
				return $this->outputPDF($pdf, $filename);
			}
		}
		try {   
			$pdf = $this->pdf->convertURI($url);
		}
		catch(PdfcrowdException $e) {
			return "Pdfcrowd Error: " . $e->getMessage();
		}
		if($folderFilename = $this->file2FolderFilename($filename)  && $pdf) {
			try {
				$this->removeCachedPDF($filename);
				$fh = fopen($folderFilename, 'w');
				fwrite($fh, $pdf);
				fclose($fh);
			}
			catch(Exception $e) {
				user_error("can't write pdf'", E_USER_WARNING);
			}
		}
		return $this->outputPDF($pdf, $filename);
	}

	public function ConvertPage($page) {
		return $this->convert(Director::AbsoluteURL($page->Link), $page->URLSegment.".pdf");
	}

	public function removeCachedPDF($filename) {
		if($folderFilename = $this->file2FolderFilename($filename)) {
			if(file_exists($folderFilename)) {				
				unlink($folderFilename);
			}
		}
	}

	protected function file2FolderFilename($filename) {
		if(self::$save_pdfs_here) {
			$folder = Director::baseFolder()."/".self::$save_pdfs_here."/";
			Folder::findOrMake($folder);
			$folderFilename = $folder . $filename;
			return $folderFilename;
		}
	}


	public static function outputPDF($pdfAsString, $filename) {
		// set HTTP response headers
		header("Content-Type: application/pdf");
		header("Cache-Control: no-cache");
		header("Accept-Ranges: none");
		header("Content-Disposition: attachment; filename=\"$filename\"");
		// send the generated PDF 
		echo $pdfAsString;
	}

}


