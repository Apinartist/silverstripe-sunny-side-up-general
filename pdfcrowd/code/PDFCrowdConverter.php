<?php


class PDFCrowdConverter extends Object {

	protected static $third_party_file = "pdfcrowd/thirdparty/github/pdfcrowd.php";
		public static set_third_party_file($s) {self::$third_party_file = $s;}

	protected static $username = "";
		public static set_username($s) {self::$username = $s;}

	protected static $api_key = "";
		public static set_api_key($s) {self::$api_key = $s;}

	public static function convert($url, $filename) {
		require_once(Director::baseFolder() . '/' .self::$third_party_file)
		try {   
			// create an API client instance
			$client = new Pdfcrowd(self::$username, self::$api_key);
			// convert a web page and store the generated PDF into a $pdf variable
			$pdf = $client->convertURI($url);
			// set HTTP response headers
			header("Content-Type: application/pdf");
			header("Cache-Control: no-cache");
			header("Accept-Ranges: none");
			header("Content-Disposition: attachment; filename=\"$filename\"");
			// send the generated PDF 
			echo $pdf;
		}
		catch(PdfcrowdException $e) {
			echo "Pdfcrowd Error: " . $e->getMessage();
		}
	}

}
