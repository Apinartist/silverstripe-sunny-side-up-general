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

	public function ConvertURL($url, $filename) {
		try {   
			$pdf = $this->pdf->convertURI($url);
			return $this->outputPDF($pdf, $filename);
		}
		catch(PdfcrowdException $e) {
			return "Pdfcrowd Error: " . $e->getMessage();
		}
	}

	public function ConvertPage($page) {
		return $this->convert(Director::AbsoluteURL($page->Link), $page->URLSegment.".pdf");
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


